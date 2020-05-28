<?php

namespace App\Imports;

use App\User;
use App\persona;
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
    {   //++$this->numRows; puede servir para comparar cuantos elemtos se guardaron

        if (!isset($row['nombres'])) {
            return null;
        }
 ++$this->numRows;

        return new persona([

            //importante

            'perso_nombre'     => $row['nombres'] ,
            'perso_apPaterno'  => $row['apellido_paterno']  ,
          
            //
        ]);

       /*  return new User([

            'rol_id'     => $row['rol_id'],
            'email'    =>  intval(preg_replace('/[^0-9]+/', '', $row['email']), 10),
            'password' =>('123456'),
            //
        ]); */
    }
    public function rules(): array
    {
        return [




        ];
    }
    public function getRowCount(): int
    {
        return $this->numRows;
    }

}
