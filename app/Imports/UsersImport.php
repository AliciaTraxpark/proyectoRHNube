<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel,WithHeadingRow, WithValidation
{   private $numRows = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    { ++$this->numRows;
        return new User([

            'rol_id'     => $row['rol_id'],
            'email'    => $row['email'],
            'password' =>('123456'),
            //
        ]);
    }
    public function rules(): array
    {
        return [
            'rol_id' => 'required|int',
            'email' => 'required|max:45',


        ];
    }
    public function getRowCount(): int
    {
        return $this->numRows;
    }
}
