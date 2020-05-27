<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all();

    }
    public function headings(): array
    {
        return [
            '#',
            'User',
            'Date',
            'd',
            'f',
            'ff',
            'fdf',
        ];
    }
    public function headings(): array
    {
        return [
            '#',
            'Id',
            'Nombre',
            'Email',
        ];
    }
}
