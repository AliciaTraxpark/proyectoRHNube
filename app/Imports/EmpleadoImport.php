<?php

namespace App\Imports;

use App\empleado;
use App\persona;
use App\ubigeo_peru_districts;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_departments;
use App\tipo_documento;
use App\cargo;
use App\area;
use App\centro_costo;
use App\tipo_contrato;
use App\local;
use App\nivel;
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

                //tipo_doc
                $tipoDoc = tipo_documento::where("tipoDoc_descripcion", "like", "%".$row['tipo_documento']."%")->first();
                if($row['tipo_documento']!=null){
                    $row['tipo_doc'] =  $tipoDoc->tipoDoc_id; } else{ $row['tipo_doc']=null; }

                //departamento
                $dep = ubigeo_peru_departments::where("name", "like", "%".$row['departamento']."%")->first();
                if($row['departamento']!=null){
                    $row['iddep'] = $dep->id; } else{ $row['iddep']=null; }


                //provincia
                $provi = ubigeo_peru_provinces::where("name", "like", "%".$row['provincia']."%")->first();
                if($row['provincia']!=null){
                    $row['idprov'] = $provi->id; } else{ $row['idprov']=null; }


               //distrito
                $idD = ubigeo_peru_districts::where("name", "like", "%".$row['distrito']."%")->where("province_id", "=",$provi->id)->first();
                if($row['distrito']!=null){
                $row['id'] = $idD->id;} else{$row['id'] = null; }

                //cargo
                $cargo = cargo::where("cargo_descripcion", "like", "%".$row['cargo']."%")->first();
                if($row['cargo']!=null){
                    if($cargo!=null){
                     $row['idcargo'] = $cargo->cargo_id;
                    } else{
                        $cargorow=new cargo();
                        $cargorow->cargo_descripcion=$row['cargo'];
                        $cargorow->save();
                        $row['idcargo']=$cargorow->cargo_id;
                    }
                 } else{ $row['idcargo']=null; }

                //area
                $area = area::where("area_descripcion", "like", "%".$row['area']."%")->first();
                if($row['area']!=null){
                    if($area!=null){
                     $row['idarea'] = $area->area_id;
                    } else{
                        $arearow = new area();
                        $arearow->area_descripcion=$row['area'];
                        $arearow->save();
                        $row['idarea']= $arearow->area_id;
                    }
                 } else{ $row['idarea']=null; }

                //centro_costo
                $centro_costo = centro_costo::where("centroC_descripcion", "like", "%".$row['centro_costo']."%")->first();
                if($row['centro_costo']!=null){
                    if($centro_costo!=null){
                     $row['idcentro_costo'] = $centro_costo->centroC_id;
                    } else{
                        $centrorow = new centro_costo();
                        $centrorow->centroC_descripcion=$row['centro_costo'];
                        $centrorow->save();
                        $row['idcentro_costo']= $centrorow->centroC_id;
                    }
                } else{ $row['idcentro_costo']=null; }

                //departamentoNac
                $depN = ubigeo_peru_departments::where("name", "like", "%".$row['departamento_nacimiento']."%")->first();
                if($row['departamento_nacimiento']!=null){
                $row['iddepartamento_nacimiento'] = $depN->id; } else{ $row['iddepartamento_nacimiento']=null; }

                //provinciaNac
                $proviN = ubigeo_peru_provinces::where("name", "like", "%".$row['provincia_nacimiento']."%")->first();
                if($row['provincia_nacimiento']!=null){
                $row['idprovincia_nacimiento'] = $proviN->id; } else{ $row['idprovincia_nacimiento']=null; }

               //distritoNac
                $idDN = ubigeo_peru_districts::where("name", "like", "%".$row['distrito_nacimiento']."%")->where("province_id", "=",$proviN->id)->first();
                if($row['distrito_nacimiento']!=null){
                $row['iddistrito_nacimiento'] = $idDN->id;} else{$row['distrito_nacimiento'] = null; }

                //tipo_contrato
                $tipo_contrato = tipo_contrato::where("contrato_descripcion", "like", "%".$row['tipo_contrato']."%")->first();
                if($row['tipo_contrato']!=null){
                    if($tipo_contrato!=null){
                        $row['idtipo_contrato'] = $tipo_contrato->contrato_id;
                    } else{
                        $tipoCrow = new tipo_contrato();
                        $tipoCrow->contrato_descripcion=$row['tipo_contrato'];
                        $tipoCrow->save();
                        $row['idtipo_contrato']= $tipoCrow->contrato_id;
                    }
                 } else{ $row['idtipo_contrato']=null; }

                //local
                $local = local::where("local_descripcion", "like", "%".$row['local']."%")->first();
                if($row['local']!=null){
                    if ($local!=null){
                        $row['idlocal'] = $local->local_id;
                    } else{
                        $localrow = new local();
                        $localrow->local_descripcion=$row['local'];
                        $localrow->save();
                        $row['idlocal'] = $localrow->local_id;

                    }
                 } else{ $row['idlocal']=null; }

                //nivel
                $nivel = nivel::where("nivel_descripcion", "like", "%".$row['nivel']."%")->first();
                if($row['nivel']!=null){
                    if ($nivel!=null){
                    $row['idnivel'] = $nivel->nivel_id;
                    } else{
                        $nivelrow = new nivel();
                        $nivelrow->nivel_descripcion=$row['nivel'];
                        $nivelrow->save();
                       $row['idnivel'] =  $nivelrow->nivel_id;
                   }
                 } else{ $row['idnivel']=null; }

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


                    'emple_tipoDoc'    => $row['tipo_doc'],

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

                    'emple_foto' =>'',



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
