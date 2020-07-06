<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
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
use App\modo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\EmpleadoImport;
use Maatwebsite\Excel\Facades\Excel;
class excelEmpleadoController extends Controller
{
    //

    //
    public function import(request $request)
    {
        $file = $request->file('file');



           if ($file == null) {

        return back()->with('alertE', 'No se ha cargado ningún archivo excel');

           }


        $import =new  EmpleadoImport();//del userimpor
        Excel::import($import,request()->file('file'));
        $filas=$import->getRowCount();
        $empleados=$import->dniE();
        //dd($dni);
        //$parameters =  ['numRows'=>$import->getRowCount(),'alert'=>$import->errors()];
        //return back()->with(['numRows'=>$import->getRowCount(),'alert'=>$import->errors()]);
       /*  $path = public_path() . '/files';
        $fileName =$file->getClientOriginalName();
        $file->move($path, $fileName); */

        return back()->with(['filas'=>$filas,'empleados'=>$empleados]);
    }
     public function guardarBD(request $request){
        $rows=$request->emplead;
        function escape_like(string $value, string $char = '\\')
        {
            return str_replace(
                [$char, '%', '_'],
                [$char.$char, $char.'%', $char.'_'],
                $value
            );
        }
        foreach ($rows as $row)
        {  $emp=$row['location'];
            if($emp[1]!= ""){
                //tipo_doc
                $tipoDoc = tipo_documento::where("tipoDoc_descripcion", "like", "%". escape_like($emp[0])."%")->first();
                if($emp[0]!=null){
                    if($tipoDoc!=null){
                          $row['tipo_doc'] =  $tipoDoc->tipoDoc_id;
                    }  else{return redirect()->back()->with('alert', 'No se encontro el tipo de documento:'.$emp[0].'.  El proceso se interrumpio '); $row['tipo_doc']=null;}
                   } else{ $row['tipo_doc']=null; }

                //departamento
                $cadDep=$emp[6];
                if(strlen($cadDep)>3){
                   $cadDep = substr ($cadDep, 0, -1);
                }
               ;
                if($emp[6]!=null){
                    $dep = ubigeo_peru_departments::where('name', 'like', "%".escape_like($cadDep)."%")->first();
                    if($dep!=null){
                    $row['iddep'] = $dep->id; }
                    else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$emp[6].'.  El proceso se interrumpio'); $row['iddep']=null;}
                }
                    else{ $row['iddep']=null; }


                //provincia
                $cadProv=$emp[7];
                if(strlen($cadProv)>3){
                    $cadProv = substr ($cadProv, 0, -1);
                }
                if($emp[7]!=null){
                    $provi = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProv)."%")->first();
                    if( $provi!=null){
                        $row['idprov'] = $provi->id; }
                        else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$emp[7].'.  El proceso se interrumpio '); $row['idprov']=null;}
                } else{ $row['idprov']=null; }


               //distrito
               $cadDist=$emp[8];
                if(strlen($cadDist)>3){
                    $cadDist = substr ($cadDist, 0, -1);
                }
                if($emp[8]!=null){
                    $idD = ubigeo_peru_districts::where("name", "like", "%".escape_like($cadDist)."%")->where("province_id", "=",$provi->id)->first();
                     if($idD!=null){
                        $row['id'] = $idD->id;
                     }
                     else{return redirect()->back()->with('alert', 'No se encontro el distrito:'.$emp[8].'.  El proceso se interrumpio '); $row['id']=null;}
                    } else{$row['id'] = null; }

                //cargo
                $cargo = cargo::where("cargo_descripcion", "like", "%".$emp[9]."%")->first();
                if($emp[9]!=null){
                    if($cargo!=null){
                     $row['idcargo'] = $cargo->cargo_id;
                    } else{
                        $cargorow=new cargo();
                        $cargorow->cargo_descripcion=$emp[9];
                        $cargorow->save();
                        $row['idcargo']=$cargorow->cargo_id;
                    }
                 } else{ $row['idcargo']=null; }

                //area
                $area = area::where("area_descripcion", "like", "%".$emp[10]."%")->first();
                if($emp[10]!=null){
                    if($area!=null){
                     $row['idarea'] = $area->area_id;
                    } else{
                        $arearow = new area();
                        $arearow->area_descripcion=$emp[10];
                        $arearow->save();
                        $row['idarea']= $arearow->area_id;
                    }
                 } else{ $row['idarea']=null; }

                //centro_costo
                $centro_costo = centro_costo::where("centroC_descripcion", "like", "%".$emp[11]."%")->first();
                if($emp[11]!=null){
                    if($centro_costo!=null){
                     $row['idcentro_costo'] = $centro_costo->centroC_id;
                    } else{
                        $centrorow = new centro_costo();
                        $centrorow->centroC_descripcion=$emp[11];
                        $centrorow->save();
                        $row['idcentro_costo']= $centrorow->centroC_id;
                    }
                } else{ $row['idcentro_costo']=null; }
                //departamentoNac
                $cadDepN=$emp[13];
                if(strlen($cadDepN)>3){
                    $cadDepN = substr ($cadDepN, 0, -1);
                }

                if($emp[13]!=null){
                $depN = ubigeo_peru_departments::where("name", "like", "%".escape_like($cadDepN)."%")->first();
                if($depN!=null){
                    $row['iddepartamento_nacimiento'] = $depN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$emp[13].'.  El proceso se interrumpio '); $row['iddepartamento_nacimiento']=null;}
                } else{ $row['iddepartamento_nacimiento']=null; }

                //provinciaNac
                $cadProvN=$emp[14];
                if(strlen($cadProvN)>3){
                    $cadProvN = substr ($cadProvN, 0, -1);
                }
                if($emp[14]!=null){
                $proviN = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProvN)."%")->first();
                if($proviN!=null){
                    $row['idprovincia_nacimiento'] = $proviN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$emp[14].'.  El proceso se interrumpio '); $row['idprovincia_nacimiento']=null;}
                 } else{ $row['idprovincia_nacimiento']=null; }

               //distritoNac
               $cadDistN=$emp[15];
               if(strlen($cadDistN)>3){
                   $cadDistN = substr ($cadDistN, 0, -1);
               }
                if($emp[15]!=null){
                $idDN = ubigeo_peru_districts::where("name", "like", "%".escape_like($cadDistN)."%")->where("province_id", "=",$proviN->id)->first();
                if($idDN!=null){
                    $row['iddistrito_nacimiento'] = $idDN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el distrito:'.$emp[15].'.  El proceso se interrumpio '); $row['iddistrito_nacimiento']=null;}
                } else{$row['iddistrito_nacimiento'] = null; }

                //tipo_contrato
                $tipo_contrato = tipo_contrato::where("contrato_descripcion", "like", "%".$emp[17]."%")->first();
                if($emp[17]!=null){
                    if($tipo_contrato!=null){
                        $row['idtipo_contrato'] = $tipo_contrato->contrato_id;
                    } else{
                        $tipoCrow = new tipo_contrato();
                        $tipoCrow->contrato_descripcion=$emp[17];
                        $tipoCrow->save();
                        $row['idtipo_contrato']= $tipoCrow->contrato_id;
                    }
                 } else{ $row['idtipo_contrato']=null; }

                //local
                $local = local::where("local_descripcion", "like", "%".$emp[18]."%")->first();
                if($emp[18]!=null){
                    if ($local!=null){
                        $row['idlocal'] = $local->local_id;
                    } else{
                        $localrow = new local();
                        $localrow->local_descripcion=$emp[18];
                        $localrow->save();
                        $row['idlocal'] = $localrow->local_id;

                    }
                 } else{ $row['idlocal']=null; }

                //nivel
                $nivel = nivel::where("nivel_descripcion", "like", "%".$emp[19]."%")->first();
                if($emp[19]!=null){
                    if ($nivel!=null){
                    $row['idnivel'] = $nivel->nivel_id;
                    } else{
                        $nivelrow = new nivel();
                        $nivelrow->nivel_descripcion=$emp[19];
                        $nivelrow->save();
                       $row['idnivel'] =  $nivelrow->nivel_id;
                   }
                 } else{ $row['idnivel']=null; }


            $personaId =persona::create([

                'perso_nombre'     => $emp[2] ,
                'perso_apPaterno'  =>$emp[3] ,
                'perso_apMaterno'  =>$emp[4],
                'perso_direccion'  => $emp[5] ,
                'perso_fechaNacimiento' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($emp[12]),
                    'perso_sexo'  => $emp[16] ,

               /*  $porciones = explode(" ", $row['apellido_paterno']),
                'perso_apPaterno'  => $porciones[0]  , */

                //
            ]);
            $empleadoId=empleado::create([
                'emple_persona'    => $personaId->perso_id,
                'emple_tipoDoc'    =>  $row['tipo_doc'],
                'emple_nDoc'       =>$emp[1],
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
                'emple_estado' =>'1',

                'emple_nivel'  =>$row['idnivel'],


                'emple_foto' =>'',
                'users_id'=> Auth::user()->id,
                'emple_pasword'=>Hash::make($emp[1]),



                //
            ]);
            modo::create([
                'idEmpleado'    => $empleadoId->emple_id,
                'idTipoModo'    => 1,
                'idTipoDispositivo'       =>1,

            ]);
            modo::create([
                'idEmpleado'    => $empleadoId->emple_id,
                'idTipoModo'    => 1,
                'idTipoDispositivo'       =>2,

            ]);
            }
        }
     }
    public function guardarBD1(request $request){
        $rows=$request->emplea;

        foreach ($rows as $row)
        {    $emp=$row['location'];

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
                    'emple_pasword'=>Hash::make($row['numero_documento']),


                    //
                ]);

            }
        }

    }

}
