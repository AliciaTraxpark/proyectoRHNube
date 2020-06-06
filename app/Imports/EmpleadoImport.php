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
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class EmpleadoImport implements ToCollection,WithHeadingRow, WithValidation, WithBatchInserts
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
            if($row['numero_documento']!= ""){

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

                //cargo
                $cargo = explode(" ", $row['cargo']);
                if($row['cargo']!=null){
                $row['idcargo'] = $cargo[0]; } else{ $row['idcargo']=null; }

                //area
                $area = explode(" ", $row['area']);
                if($row['area']!=null){
                $row['idarea'] = $area[0]; } else{ $row['idarea']=null; }

                //centro_costo
                $centro_costo = explode(" ", $row['centro_costo']);
                if($row['centro_costo']!=null){
                $row['idcentro_costo'] = $centro_costo[0]; } else{ $row['idcentro_costo']=null; }

                //departamentoNac
                $depN = explode(" ", $row['departamento_nacimiento']);
                if($row['departamento_nacimiento']!=null){
                $row['iddepartamento_nacimiento'] = $depN[0]; } else{ $row['iddepartamento_nacimiento']=null; }

                //provinciaNac
                $proviN = explode(" ", $row['provincia_nacimiento']);
                if($row['provincia_nacimiento']!=null){
                $row['idprovincia_nacimiento'] = $proviN[0]; } else{ $row['idprovincia_nacimiento']=null; }

               //distritoNac
                $idDN = ubigeo_peru_districts::where("name", "like", "%".$row['distrito_nacimiento']."%")->first();
                if($row['distrito_nacimiento']!=null){
                $row['iddistrito_nacimiento'] = $idDN->id;} else{$row['distrito_nacimiento'] = null; }

                //tipo_contrato
                $tipo_contrato = explode(" ", $row['tipo_contrato']);
                if($row['tipo_contrato']!=null){
                $row['idtipo_contrato'] = $tipo_contrato[0]; } else{ $row['idtipo_contrato']=null; }

                //local
                $local = explode(" ", $row['local']);
                if($row['local']!=null){
                $row['idlocal'] = $local[0]; } else{ $row['idlocal']=null; }

                //nivel
                $nivel = explode(" ", $row['nivel']);
                if($row['nivel']!=null){
                $row['idnivel'] = $nivel[0]; } else{ $row['idnivel']=null; }

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


                    'emple_cargo'      =>  $row['idcargo'],

                    'emple_area'       => $row['idarea'],

                    'emple_centCosto'  => $row['idcentro_costo'],

                    'emple_departamentoN' => $row['iddepartamento_nacimiento'],

                    'emple_provinciaN'  => $row['idprovincia_nacimiento'],

                    'emple_distritoN'   => $row['iddistrito_nacimiento'],

                    'emple_tipoContrato' => $row['idtipo_contrato'],

                    'emple_local' => $row['idlocal'],

                    'emple_nivel'  =>$row['idnivel'],



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
    public function batchSize(): int
    {
        return 2000;
    }
    public function getRowCount(): int
    {
        return $this->numRows;
    }

}
