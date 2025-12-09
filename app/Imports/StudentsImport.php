<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    /**
     * Process the entire collection of rows.
     * First validates that all students can be accommodated, then processes the import.
     *
     * @param Collection $rows
     * @return void
     * @throws \Exception
     */
    public function collection(Collection $rows)
    {
        $activeSY = SchoolYear::where('status', 'active')->first();
        if (!$activeSY) {
            throw new \Exception('No active school year found.');
        }

        // Filter out empty rows
        $validRows = $rows->filter(function ($row) {
            return !empty($row['first_name']) && !empty($row['last_name']) && !empty($row['section']);
        });

        if ($validRows->isEmpty()) {
            throw new \Exception('No valid student data found in the import file.');
        }

        // First, validate that all students can be accommodated
        $this->validateCapacity($validRows, $activeSY->id);

        // If validation passes, process all rows
        $this->processImport($validRows, $activeSY);
    }

    /**
     * Validate that all students in the import can be accommodated in their sections.
     * 
     * @param Collection $rows
     * @param int $schoolYearId
     * @return void
     * @throws \Exception
     */
    private function validateCapacity(Collection $rows, $schoolYearId)
    {
        // Group students by section
        $studentsBySection = [];
        $sectionMap = [];

        foreach ($rows as $row) {
            $sectionName = $row['section'] ?? null;
            if (!$sectionName) {
                continue;
            }

            // Get or cache section
            if (!isset($sectionMap[$sectionName])) {
                $section = Section::where('name', $sectionName)
                    ->where('school_year_id', $schoolYearId)
                    ->first();

                if (!$section) {
                    throw new \Exception("Section '{$sectionName}' not found for the active school year.");
                }

                $sectionMap[$sectionName] = $section;
            }

            $section = $sectionMap[$sectionName];
            $sectionId = $section->id;

            if (!isset($studentsBySection[$sectionId])) {
                $studentsBySection[$sectionId] = [
                    'section' => $section,
                    'count' => 0,
                ];
            }

            $studentsBySection[$sectionId]['count']++;
        }

        // Group sections by grade level to handle overflow across multiple sections
        $sectionsByGradeLevel = [];
        foreach ($studentsBySection as $sectionId => $data) {
            $gradeLevelId = $data['section']->gradelevel_id;
            if (!isset($sectionsByGradeLevel[$gradeLevelId])) {
                $sectionsByGradeLevel[$gradeLevelId] = [];
            }
            $sectionsByGradeLevel[$gradeLevelId][$sectionId] = $data;
        }

        // Check capacity for each grade level
        foreach ($sectionsByGradeLevel as $gradeLevelId => $sections) {
            $totalOverflow = 0;
            $overflowDetails = [];

            // First pass: calculate overflow for each section
            foreach ($sections as $sectionId => $data) {
                $section = $data['section'];
                $newStudentsCount = $data['count'];

                if (!$section->capacity) {
                    // No capacity limit, skip check
                    continue;
                }

                // Get current enrollment count
                $currentEnrolled = Enrollment::where('section_id', $sectionId)
                    ->where('school_year_id', $schoolYearId)
                    ->where('status', 'Enrolled')
                    ->count();

                // Check if we can accommodate all new students
                $totalAfterImport = $currentEnrolled + $newStudentsCount;

                if ($totalAfterImport > $section->capacity) {
                    $available = $section->capacity - $currentEnrolled;
                    $overflow = $newStudentsCount - $available;
                    $totalOverflow += $overflow;
                    $overflowDetails[] = [
                        'section' => $section,
                        'available' => $available,
                        'overflow' => $overflow,
                    ];
                }
            }

            // If there's overflow, check if alternative sections can accommodate it
            if ($totalOverflow > 0) {
                // Get all alternative sections (excluding the ones with overflow)
                $excludeSectionIds = array_keys($sections);
                $alternativeSections = Section::where('gradelevel_id', $gradeLevelId)
                    ->where('school_year_id', $schoolYearId)
                    ->whereNotIn('id', $excludeSectionIds)
                    ->get();

                $totalAlternativeCapacity = 0;
                foreach ($alternativeSections as $altSection) {
                    if (!$altSection->capacity) {
                        // No capacity limit, can accommodate unlimited
                        $totalAlternativeCapacity = PHP_INT_MAX;
                        break;
                    }

                    $altCurrentEnrolled = Enrollment::where('section_id', $altSection->id)
                        ->where('school_year_id', $schoolYearId)
                        ->where('status', 'Enrolled')
                        ->count();

                    $altAvailable = $altSection->capacity - $altCurrentEnrolled;
                    $totalAlternativeCapacity += max(0, $altAvailable);
                }

                if ($totalAlternativeCapacity < $totalOverflow) {
                    $details = [];
                    foreach ($overflowDetails as $detail) {
                        $details[] = "Section '{$detail['section']->name}' can only accept {$detail['available']} more, but {$detail['overflow']} will overflow";
                    }

                    throw new \Exception(
                        "Cannot accommodate all students. " .
                        implode('. ', $details) . ". " .
                        "Total overflow: {$totalOverflow} student(s), but only {$totalAlternativeCapacity} space(s) available in alternative sections."
                    );
                }
            }
        }
    }

    /**
     * Process the import after validation passes.
     * 
     * @param Collection $rows
     * @param SchoolYear $activeSY
     * @return void
     */
    private function processImport(Collection $rows, SchoolYear $activeSY)
    {
        $baseCode = '400655';
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $lastNumber = 0;
        if ($lastStudent && preg_match('/^' . $baseCode . '(\d{4,})$/', $lastStudent->student_number, $m)) {
            $lastNumber = (int) $m[1];
        }

        $studentNumberCounter = $lastNumber;

        foreach ($rows as $row) {
            $sectionName = $row['section'] ?? null;
            if (!$sectionName) {
                continue;
            }

            $section = Section::where('name', $sectionName)
                ->where('school_year_id', $activeSY->id)
                ->first();

            if (!$section) {
                continue;
            }

            // Check if section has capacity, if not find alternative
            $section = $this->findSectionForStudent($section, $activeSY->id);

            if (!$section) {
                throw new \Exception("No available section found for student: {$row['first_name']} {$row['last_name']}");
            }

            $email = strtolower($row['last_name'] . '.' . $row['first_name'] . '@mindware.edu.ph');

            DB::transaction(function () use ($row, $email, $section, $activeSY, $baseCode, &$studentNumberCounter) {
                // Generate student number inside transaction
                $studentNumberCounter++;
                $studentNumber = $baseCode . str_pad($studentNumberCounter, 4, '0', STR_PAD_LEFT);

                $cleanLast  = strtolower(preg_replace('/\s+/', '', $row['last_name']));
                $cleanFirst = strtolower(preg_replace('/\s+/', '', $row['first_name']));
                $lastFour   = substr($studentNumber, -4);

                $tempPassword = $cleanLast . $cleanFirst . $lastFour;

                // A. Create User
                $user = User::create([
                    'email' => $email,
                    'password' => Hash::make($tempPassword),
                    'role_id' => 4,
                    'status' => 'active',
                ]);

                // B. Create User Profile
                UserProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'last_name' => $row['last_name'],
                    'sex' => $row['sex'],
                    'birthdate' => $row['birthdate'],
                    'address' => $row['address'],
                    'contact_number' => $row['contact_number'] ?? null,
                    'guardian_name' => $row['guardian_name'] ?? null,
                ]);

                // C. Create Student Record
                $student = Student::create([
                    'user_id' => $user->id,
                    'student_number' => $studentNumber,
                    'section_id' => $section->id,
                ]);

                // D. Create Enrollment Record
                Enrollment::create([
                    'student_id' => $student->id,
                    'section_id' => $section->id,
                    'school_year_id' => $activeSY->id,
                    'status' => 'Enrolled',
                    'created_by' => Auth::id(),
                ]);
            });
        }
    }

    /**
     * Find the appropriate section for a student, checking capacity and finding alternatives if needed.
     * 
     * @param Section $requestedSection
     * @param int $schoolYearId
     * @return Section|null
     */
    private function findSectionForStudent($requestedSection, $schoolYearId)
    {
        // Check if requested section has capacity
        if ($requestedSection->capacity) {
            $enrolledCount = Enrollment::where('section_id', $requestedSection->id)
                ->where('school_year_id', $schoolYearId)
                ->where('status', 'Enrolled')
                ->count();

            if ($enrolledCount < $requestedSection->capacity) {
                return $requestedSection;
            }
        } else {
            // No capacity limit
            return $requestedSection;
        }

        // Requested section is full, find alternative
        return $this->findAvailableSection(
            $requestedSection->gradelevel_id,
            $schoolYearId,
            $requestedSection->id
        );
    }

    /**
     * Find an available section for the given grade level.
     * Does NOT create new sections - returns null if all are full.
     * 
     * @param int $gradeLevelId
     * @param int $schoolYearId
     * @param int|null $excludeSectionId Section ID to exclude from search
     * @return Section|null
     */
    private function findAvailableSection($gradeLevelId, $schoolYearId, $excludeSectionId = null)
    {
        $sections = Section::where('gradelevel_id', $gradeLevelId)
            ->where('school_year_id', $schoolYearId)
            ->when($excludeSectionId, function ($q) use ($excludeSectionId) {
                $q->where('id', '!=', $excludeSectionId);
            })
            ->get();

        foreach ($sections as $section) {
            if (!$section->capacity) {
                // No capacity limit, so it's available
                return $section;
            }

            $enrolledCount = Enrollment::where('section_id', $section->id)
                ->where('school_year_id', $schoolYearId)
                ->where('status', 'Enrolled')
                ->count();

            if ($enrolledCount < $section->capacity) {
                return $section;
            }
        }

        // No available sections found
        return null;
    }
}