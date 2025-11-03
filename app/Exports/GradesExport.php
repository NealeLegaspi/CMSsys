<?php

namespace App\Exports;

use App\Models\SubjectAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;

class GradesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SubjectAssignment::all();
    }
}
