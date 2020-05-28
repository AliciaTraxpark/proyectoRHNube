<?php

namespace App\Imports;

use App\empleado;
use App\persona;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmpleadoImport implements ToCollection,WithHeadingRow, WithValidation
{
    private $numRows = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            if($row->filter()->isNotEmpty()){
                ++$this->numRows;
                $personaId =persona::create([
                    'perso_nombre'     => $row['nombres'] ,
                   /*  $porciones = explode(" ", $row['apellido_paterno']),
                    'perso_apPaterno'  => $porciones[0]  , */

                    //
                ]);
                empleado::create([
                    'emple_persona'         => $personaId->perso_id,
                    'emple_nDoc'           =>('12345678'),

                    //
                ]);
            }
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
