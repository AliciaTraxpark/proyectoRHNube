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
use App\condicion_pago;
use App\contrato;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Imports\EmpleadoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\actividad;
use App\calendario;
use App\eventos_usuario;
use App\eventos_empleado;
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
                $cadDep=$emp[13];
                if(strlen($cadDep)>3){
                   $cadDep = substr ($cadDep, 0, -1);
                }
               ;
                if($emp[13]!=null){
                    $dep = ubigeo_peru_departments::where('name', 'like', "%".escape_like($cadDep)."%")->first();
                    if($dep!=null){
                    $row['iddep'] = $dep->id; }
                    else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$emp[13].'.  El proceso se interrumpio'); $row['iddep']=null;}
                }
                    else{ $row['iddep']=null; }


                //provincia
                $cadProv=$emp[14];
                if(strlen($cadProv)>3){
                    $cadProv = substr ($cadProv, 0, -1);
                }
                if($emp[14]!=null){
                    $provi = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProv)."%")->first();
                    if( $provi!=null){
                        $row['idprov'] = $provi->id; }
                        else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$emp[14].'.  El proceso se interrumpio '); $row['idprov']=null;}
                } else{ $row['idprov']=null; }


               //distrito
               $cadDist=$emp[15];
                if(strlen($cadDist)>3){
                    $cadDist = substr ($cadDist, 0, -1);
                }
                if($emp[15]!=null){
                    $idD = ubigeo_peru_districts::where("name", "like", "%".escape_like($cadDist)."%")->where("province_id", "=",$provi->id)->first();
                     if($idD!=null){
                        $row['id'] = $idD->id;
                     }
                     else{return redirect()->back()->with('alert', 'No se encontro el distrito:'.$emp[15].'.  El proceso se interrumpio '); $row['id']=null;}
                    } else{$row['id'] = null; }

                //cargo
                $cargo = cargo::where("cargo_descripcion", "like", "%".$emp[19]."%")->first();
                if($emp[19]!=null){
                    if($cargo!=null){
                     $row['idcargo'] = $cargo->cargo_id;
                    } else{
                        $cargorow=new cargo();
                        $cargorow->cargo_descripcion=$emp[19];
                        $cargorow->save();
                        $row['idcargo']=$cargorow->cargo_id;
                    }
                 } else{ $row['idcargo']=null; }

                //area
                $area = area::where("area_descripcion", "like", "%".$emp[20]."%")->first();
                if($emp[20]!=null){
                    if($area!=null){
                     $row['idarea'] = $area->area_id;
                    } else{
                        $arearow = new area();
                        $arearow->area_descripcion=$emp[20];
                        $arearow->save();
                        $row['idarea']= $arearow->area_id;
                    }
                 } else{ $row['idarea']=null; }

                //centro_costo
                $centro_costo = centro_costo::where("centroC_descripcion", "like", "%".$emp[21]."%")->first();
                if($emp[21]!=null){
                    if($centro_costo!=null){
                     $row['idcentro_costo'] = $centro_costo->centroC_id;
                    } else{
                        $centrorow = new centro_costo();
                        $centrorow->centroC_descripcion=$emp[21];
                        $centrorow->save();
                        $row['idcentro_costo']= $centrorow->centroC_id;
                    }
                } else{ $row['idcentro_costo']=null; }
                //departamentoNac
                $cadDepN=$emp[9];
                if(strlen($cadDepN)>3){
                    $cadDepN = substr ($cadDepN, 0, -1);
                }

                if($emp[9]!=null){
                $depN = ubigeo_peru_departments::where("name", "like", "%".escape_like($cadDepN)."%")->first();
                if($depN!=null){
                    $row['iddepartamento_nacimiento'] = $depN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$emp[9].'.  El proceso se interrumpio '); $row['iddepartamento_nacimiento']=null;}
                } else{ $row['iddepartamento_nacimiento']=null; }

                //provinciaNac
                $cadProvN=$emp[10];
                if(strlen($cadProvN)>3){
                    $cadProvN = substr ($cadProvN, 0, -1);
                }
                if($emp[10]!=null){
                $proviN = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProvN)."%")->first();
                if($proviN!=null){
                    $row['idprovincia_nacimiento'] = $proviN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$emp[10].'.  El proceso se interrumpio '); $row['idprovincia_nacimiento']=null;}
                 } else{ $row['idprovincia_nacimiento']=null; }

               //distritoNac
               $cadDistN=$emp[11];
               if(strlen($cadDistN)>3){
                   $cadDistN = substr ($cadDistN, 0, -1);
               }
                if($emp[11]!=null){
                $idDN = ubigeo_peru_districts::where("name", "like", "%".escape_like($cadDistN)."%")->where("province_id", "=",$proviN->id)->first();
                if($idDN!=null){
                    $row['iddistrito_nacimiento'] = $idDN->id;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el distrito:'.$emp[11].'.  El proceso se interrumpio '); $row['iddistrito_nacimiento']=null;}
                } else{$row['iddistrito_nacimiento'] = null; }

                //tipo_contrato
                $tipo_contrato = tipo_contrato::where("contrato_descripcion", "like", "%".$emp[16]."%")->first();
                if($emp[16]!=null){
                    if($tipo_contrato!=null){
                        $row['idtipo_contrato'] = $tipo_contrato->contrato_id;
                    } else{
                        $tipoCrow = new tipo_contrato();
                        $tipoCrow->contrato_descripcion=$emp[16];
                        $tipoCrow->save();
                        $row['idtipo_contrato']= $tipoCrow->contrato_id;
                    }
                 } else{ $row['idtipo_contrato']=null; }

                //local
                $local = local::where("local_descripcion", "like", "%".$emp[17]."%")->first();
                if($emp[17]!=null){
                    if ($local!=null){
                        $row['idlocal'] = $local->local_id;
                    } else{
                        $localrow = new local();
                        $localrow->local_descripcion=$emp[17];
                        $localrow->save();
                        $row['idlocal'] = $localrow->local_id;

                    }
                 } else{ $row['idlocal']=null; }

                //nivel
                $nivel = nivel::where("nivel_descripcion", "like", "%".$emp[18]."%")->first();
                if($emp[18]!=null){
                    if ($nivel!=null){
                    $row['idnivel'] = $nivel->nivel_id;
                    } else{
                        $nivelrow = new nivel();
                        $nivelrow->nivel_descripcion=$emp[18];
                        $nivelrow->save();
                       $row['idnivel'] =  $nivelrow->nivel_id;
                   }
                 } else{ $row['idnivel']=null; }
                 ////////////////Condicion de pago
                 $condicion_pago = condicion_pago::where("condicion", "like", "%".$emp[22]."%") ->where('organi_id', '=', session('sesionidorg'))->first();
                 if($emp[22]!=null){
                     if ($condicion_pago!=null){
                         $row['idcondicion_pago'] = $condicion_pago->id;
                     } else{
                         $condicion_pagorow = new condicion_pago();
                         $condicion_pagorow->condicion=$emp[22];
                         $condicion_pagorow->user_id=Auth::user()->id;
                         $condicion_pago->organi_id= session('sesionidorg');
                         $condicion_pagorow->save();
                         $row['idcondicion_pago'] =$condicion_pagorow->id;

                     }
                  } else{ $row['idcondicion_pago']=null; }
                  ///////////////


            $personaId =persona::create([

                'perso_nombre'     => $emp[2] ,
                'perso_apPaterno'  =>$emp[3] ,
                'perso_apMaterno'  =>$emp[4],
                'perso_direccion'  => $emp[12] ,
                'perso_fechaNacimiento' => $emp[8],
                    'perso_sexo'  => $emp[7] ,

               /*  $porciones = explode(" ", $row['apellido_paterno']),
                'perso_apPaterno'  => $porciones[0]  , */

                //
            ]);
            if($emp[6]!=null || $emp[6]!=''){
                $numcelular='+51'.$emp[6];
            } else { $numcelular='';}
            $empleadoId=empleado::create([
                'emple_persona'    => $personaId->perso_id,
                'emple_tipoDoc'    =>  $row['tipo_doc'],
                'emple_nDoc'       =>$emp[1],
                'emple_Correo'=>$emp[5],
                'emple_celular'=>$numcelular,

                'emple_telefono'=>'',
                'emple_departamento'=> $row['iddep'],
                'emple_provincia'  => $row['idprov'],
                'emple_distrito'   =>  $row['id'],
                'emple_cargo'      =>  $row['idcargo'],
                'emple_area'       => $row['idarea'],
                'emple_centCosto'  => $row['idcentro_costo'],

                'emple_departamentoN' => $row['iddepartamento_nacimiento'],

                'emple_provinciaN'  => $row['idprovincia_nacimiento'],

                'emple_distritoN'   => $row['iddistrito_nacimiento'],

                /* 'emple_tipoContrato' => $row['idtipo_contrato'], */

                'emple_local' => $row['idlocal'],
                'emple_estado' =>'1',

                'emple_nivel'  =>$row['idnivel'],
                'emple_foto' =>'',
                'users_id'=> Auth::user()->id,
                'organi_id'=> session('sesionidorg'),
                'emple_pasword'=>Hash::make($emp[1]),

                //
            ]);


                actividad::create([
                    'Activi_Nombre'=>"Tarea 01",
                    'empleado_emple_id'=>$empleadoId->emple_id,
                    'estado'    => 1,

                ]);
                if($row['idtipo_contrato']!='' || $row['idtipo_contrato']!=null){
                   contrato::create([
                'id_tipoContrato'=>$row['idtipo_contrato'],
                'id_condicionPago'=>$row['idcondicion_pago'],
                'monto'=>$emp[23],
                'idEmpleado'    => $empleadoId->emple_id,
                'estado'    => 1,


            ]);
                }

            $calendario=calendario::where('calendario_nombre','=','Perú')
            ->where('organi_id','=',session('sesionidorg'))->get()->first();
            $idcalendario=$calendario->calen_id;
            $eventos_usuario = eventos_usuario::where('organi_id', '=', session('sesionidorg'))
                ->where('id_calendario', '=', $idcalendario)->get();
            if ($eventos_usuario) {
                foreach ($eventos_usuario as $eventos_usuarios) {
                    $eventos_empleado_r = new eventos_empleado();
                    $eventos_empleado_r->id_empleado =  $empleadoId->emple_id;
                    $eventos_empleado_r->title = $eventos_usuarios->title;
                    $eventos_empleado_r->color = $eventos_usuarios->color;
                    $eventos_empleado_r->textColor = $eventos_usuarios->textColor;
                    $eventos_empleado_r->start = $eventos_usuarios->start;
                    $eventos_empleado_r->end = $eventos_usuarios->end;
                    $eventos_empleado_r->tipo_ev = $eventos_usuarios->tipo;
                    $eventos_empleado_r->id_calendario = $idcalendario;
                    $eventos_empleado_r->laborable =0;
                    $eventos_empleado_r->save();
                }
            }
            /*
            modo::create([
                'idEmpleado'    => $empleadoId->emple_id,
                'idTipoModo'    => 1,
                'idTipoDispositivo'       =>2,

            ]); */
            }
        }
     }


}
