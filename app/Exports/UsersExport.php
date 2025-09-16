<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::with('role')
            ->get()
            ->map(function ($user) {
                return [
                    'ID'        => $user->id,
                    'FirstName' => optional($user->profile)->first_name,
                    'LastName'  => optional($user->profile)->last_name,
                    'Email'     => $user->email,
                    'Role'      => optional($user->role)->name,
                    'Status'    => $user->status,
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'First Name', 'Last Name', 'Email', 'Role', 'Status'];
    }
}
