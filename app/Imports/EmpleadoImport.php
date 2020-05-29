<?php

namespace App\Imports;

use App\empleado;
use App\persona;
use App\ubigeo_peru_districts;
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

                //departamento
                $dep = explode(" ", $row['departamento']);
                if($row['departamento']!=null){
                    $row['iddep'] = $dep[0]; } else{ $row['iddep']=null; }


                //provincia
                $provi = explode(" ", $row['provincia']);
                if($row['provincia']!=null){
                    $row['idprov'] = $provi[0]; } else{ $row['idprov']=null; }


               //distrito
                $idD = ubigeo_peru_districts::where("name", "like", "%".$row['distrito']."%")->first();
                if($row['distrito']!=null){
                $row['id'] = $idD->id;} else{$row['id'] = null; }



                ++$this->numRows;
                $personaId =persona::create([

                    'perso_nombre'     => $row['nombres'] ,
                    'perso_apPaterno'  => $row['apellido_paterno'] ,
                    'perso_apMaterno'  => $row['apellido_materno'] ,
                    'perso_direccion'  => $row['direccion'] ,
                    'perso_fechaNacimiento' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_nacimiento']),
                    'perso_sexo'  => $row['sexo'] ,
                   /*  $porciones = explode(" ", $row['apellido_paterno']),
                    'perso_apPaterno'  => $porciones[0]  , */

                    //
                ]);


                empleado::create([
                    'emple_persona'    => $personaId->perso_id,

                    $tipoDoc = explode(" ", $row['tipo_documento']),
                    'emple_tipoDoc'    => $tipoDoc[0],

                    'emple_nDoc'       =>$row['numero_documento']
                    ,
                   
                    'emple_departamento'=> $row['iddep'],


                    'emple_provincia'  => $row['idprov'],


                    'emple_distrito'   =>  $row['id'],

                   /*  $cargo = explode(" ", $row['cargo']),
                    'emple_cargo'      => $cargo[0],

                    $area = explode(" ", $row['area']),
                    'emple_area'       => $area[0],

                    $ceCosto = explode(" ", $row['centro_costo']),
                    'emple_centCosto'  => $ceCosto[0],

                    $depN = explode(" ", $row['departamento_nacimiento']),
                    'emple_departamentoN' => $depN[0],

                    $provN = explode(" ", $row['provincia_nacimiento']),
                    'emple_provinciaN'  => $provN[0],

                    $distN = explode(" ", $row['distrito_nacimiento']),
                    'emple_distritoN'   => $distN[0],

                    $tipoC = explode(" ", $row['tipo_contrato']),
                    'emple_tipoContrato' => $tipoC[0],

                    $local = explode(" ", $row['local']),
                    'emple_local' => $local[0],

                    $nivel = explode(" ", $row['nivel']),
                    'emple_nivel'  => $nivel[0],
 */



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
