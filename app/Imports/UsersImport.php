<?php

namespace App\Imports;

use App\User;
use App\persona;
use App\empleado;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;


class UsersImport implements ToCollection,WithHeadingRow, WithValidation
{   private $numRows = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)

    {   //++$this->numRows; puede servir para comparar cuantos elemtos se guardaron

        foreach ($rows as $row)
        {

        if($row->filter()->isNotEmpty()){
            ++$this->numRows;
            $personaId =persona::create([
            'perso_nombre'     => $row['nombres'] ,
            'perso_apPaterno'  => $row['apellido_paterno']  ,

            //
        ]);
        empleado::create([
            'emple_persona'         => $personaId->perso_id,
            'emple_nDoc'           =>('12345678'),

            //
        ]);
         }

       /*  return new User([

            'rol_id'     => $row['rol_id'],
            'email'    =>  intval(preg_replace('/[^0-9]+/', '', $row['email']), 10),
            'password' =>('123456'),
            //
        ]); */
        }
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
