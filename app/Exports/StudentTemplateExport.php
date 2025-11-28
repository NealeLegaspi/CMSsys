<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'sex',
            'birthdate',
            'contact_number',
            'guardian_name',
            'address',
            'section',
        ];
    }

    public function array(): array
    {
        // Optional sample row (registrar can delete)
        return [
            [
                'Juan',
                'Dela',
                'Cruz',
                'Male',
                '2010-05-11',
                '09998887777',
                'Maria Cruz',
                'Brgy. Uno',
                'Grade 7 - Rizal'
            ]
        ];
    }
}
