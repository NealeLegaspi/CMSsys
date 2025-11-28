<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GradeTemplateExport implements FromArray, WithHeadings
{
    protected $rows;
    protected $heading;

    public function __construct(array $students, string $heading)
    {
        $this->rows = $students;
        $this->heading = $heading;
    }

    public function headings(): array
    {
        return [
            'student_number',
            'last_name',
            'first_name',
            $this->heading,
        ];
    }

    // students is array of rows
    public function array(): array
    {
        return $this->rows;
    }
}
