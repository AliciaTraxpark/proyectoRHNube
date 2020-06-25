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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\Importable;
class EmpleadoImport implements ToCollection,WithHeadingRow, WithValidation, WithBatchInserts, SkipsOnError
{    use Importable, SkipsErrors;
    private $numRows = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        function escape_like(string $value, string $char = '\\')
        {
            return str_replace(
                [$char, '%', '_'],
                [$char.$char, $char.'%', $char.'_'],
                $value
            );
        }
        foreach ($rows as $row)
        {
            if($row['numero_documento']!= ""){
               $filaA= $this->numRows;
               $filas= $filaA+2;
                //tipo_doc
                $tipoDoc = tipo_documento::where("tipoDoc_descripcion", "like", "%". escape_like($row['tipo_documento'])."%")->first();
                if($row['tipo_documento']!=null){
                    if($tipoDoc!=null){
                          $row['tipo_doc'] =  $tipoDoc->tipoDoc_id;
                    }  else{return redirect()->back()->with('alert', 'No se encontro el tipo de documento:'.$row['tipo_documento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['tipo_doc']=null;}
                   } else{ $row['tipo_doc']=null; }

                //departamento
                $cadDep=$row['departamento'];
                if(strlen($cadDep)>3){
                   $cadDep = substr ($cadDep, 0, -1);
                }
               ;
                if($row['departamento']!=null){
                    $dep = ubigeo_peru_departments::where('name', 'like', "%".escape_like($cadDep)."%")->first();
                    if($dep!=null){
                    $row['iddep'] = $dep->id; }
                    else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$row['departamento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['iddep']=null;}
                }
                    else{ $row['iddep']=null; }


                //provincia
                $cadProv=$row['provincia'];
                if(strlen($cadProv)>3){
                    $cadProv = substr ($cadProv, 0, -1);
                }
                if($row['provincia']!=null){
                    $provi = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProv)."%")->first();
                    if( $provi!=null){
                        $row['idprov'] = $provi->id; }
                        else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$row['provincia'].'.  El proceso se interrumpio en la fila:'.$filas); $row['idprov']=null;}
                } else{ $row['idprov']=null; }


               //distrito
               $cadDist=$row['distrito'];
                if(strlen($cadDist)>3){
                    $cadDist = substr ($cadDist, 0, -1);
                }
                if($row['distrito']!=null){
                    $idD = ubigeo_peru_districts::where("name", "like", "%".escape_like($cadDist)."%")->where("province_id", "=",$provi->id)->first();
                     if($idD!=null){
                        $row['id'] = $idD->id;
                     }
                     else{return redirect()->back()->with('alert', 'No se encontro el distrito:'.$row['distrito'].'.  El proceso se interrumpio en la fila:'.$filas); $row['id']=null;}
                    } else{$row['id'] = null; }

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
                $cadDepN=$row['departamento_nacimiento'];
                if(strlen($cadDepN)>3){
                    $cadDepN = substr ($cadDepN, 0, -1);
                }

                if($row['departamento_nacimiento']!=null){
                $depN = ubigeo_peru_departments::where("name", "like", "%".escape_like($cadDepN)."%")->first();
                if($depN!=null){
                    $row['iddepartamento_nacimiento'] = $depN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$row['departamento_nacimiento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['iddepartamento_nacimiento']=null;}
                } else{ $row['iddepartamento_nacimiento']=null; }

                //provinciaNac
                $cadProvN=$row['provincia_nacimiento'];
                if(strlen($cadProvN)>3){
                    $cadProvN = substr ($cadProvN, 0, -1);
                }
                if($row['provincia_nacimiento']!=null){
                $proviN = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProvN)."%")->first();
                if($proviN!=null){
                    $row['idprovincia_nacimiento'] = $proviN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$row['provincia_nacimiento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['idprovincia_nacimiento']=null;}
                 } else{ $row['idprovincia_nacimiento']=null; }

               //distritoNac
               $cadDistN=$row['distrito_nacimiento'];
               if(strlen($cadDistN)>3){
                   $cadDistN = substr ($cadDistN, 0, -1);
               }
                if($row['distrito_nacimiento']!=null){
                $idDN = ubigeo_peru_districts::where("name", "like", "%".escape_like($cadDistN)."%")->where("province_id", "=",$proviN->id)->first();
                if($idDN!=null){
                    $row['iddistrito_nacimiento'] = $idDN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el distrito:'.$row['distrito_nacimiento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['iddistrito_nacimiento']=null;}
                } else{$row['iddistrito_nacimiento'] = null; }

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
                    'users_id'=> Auth::user()->id,



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
    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }

}
