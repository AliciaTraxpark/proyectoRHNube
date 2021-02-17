<?php

namespace App\Imports;

use App\area;
use App\cargo;
use App\centro_costo;
use App\condicion_pago;
use App\local;
use App\nivel;
use App\tipo_contrato;
use App\tipo_documento;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_districts;
use App\ubigeo_peru_provinces;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
class EmpleadoImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts,WithChunkReading, SkipsOnError
{use Importable, SkipsErrors;
    private $numRows = 0;
    public $dnias = [];
    public $Ndoc = [];
    public $NCorreo = [];

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
                [$char . $char, $char . '%', $char . '_'],
                $value
            );

            //*VALIDAR FECHA
        }function validateDate($date, $format = 'Y-m-d H:i:s')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

        foreach ($rows as $row) {

            /* VALIDARMEOS DATOS DE CABECERA */
            if (isset($row['tipo_documento']) && isset($row['numero_documento']) && isset($row['codigo'])
                && isset($row['nombres']) && isset($row['apellido_paterno']) && isset($row['apellido_materno'])
                && isset($row['correo']) && isset($row['prefijo']) && isset($row['celular'])
                && isset($row['genero']) && isset($row['fecha_nacimiento']) && isset($row['distrito_nacimiento'])
                && isset($row['direccion']) && isset($row['distrito']) && isset($row['tipo_contrato'])
                && isset($row['inicio_contrato']) && isset($row['fin_contrato']) && isset($row['dias_notificacion'])
                && isset($row['local']) && isset($row['nivel'])
                && isset($row['cargo']) && isset($row['area'])
                && isset($row['codigo_centro_costo']) && isset($row['centro_costo'])
                && isset($row['condicion_pago']) && isset($row['monto_pago'])) {

            } else {
                return redirect()->back()->with('alert', 'Formato incorrecto, Porfavor descargue la plantilla y actualize sus datos');
            }
            /* ----------------------------- */
            if ($row['numero_documento'] != "") {
                $filaA = $this->numRows;

                //$this->dnias=$row['numero_documento'];
                $filas = $filaA + 2;
                //tipo_doc
                $tipoDoc = tipo_documento::where("tipoDoc_descripcion", "like", "%" . escape_like($row['tipo_documento']) . "%")->first();
                if ($row['tipo_documento'] != null) {
                    if ($tipoDoc != null) {
                        $row['tipo_doc'] = $tipoDoc->tipoDoc_id;
                        $row['tipo_docArray'] = $tipoDoc->tipoDoc_descripcion;
                    } else {return redirect()->back()->with('alert', 'No se encontro el tipo de documento:' . $row['tipo_documento'] . '.  El proceso se interrumpio en la fila:' . $filas);
                        $row['tipo_doc'] = null;}
                } else { $row['tipo_docArray'] = null;}
                //dni
                $empleadoAntiguo = DB::table('empleado')->where('emple_nDoc', '=', $row['numero_documento'])->where('empleado.organi_id', '=', session('sesionidorg'))
                    ->where('empleado.emple_estado', '=', 1)->first();
                if ($empleadoAntiguo != null) {
                    return redirect()->back()->with('alert', 'Número de documento ya esta  registrado: ' . $row['numero_documento'] . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');
                };
                $capturaD = [$row['numero_documento']];
                array_push($this->Ndoc, $capturaD);

                $lineal = Arr::flatten($this->Ndoc);
                $clave2 = array_splice($lineal, 0, $filaA);
                $clave = array_search($row['numero_documento'], $clave2);

                //dd($clave2,$clave);
                if ($clave !== false) {
                    //dd($clave2,$clave,$filaA);
                    return redirect()->back()->with('alert', 'numero de documento duplicado en la importacion: ' . $row['numero_documento'] . ' .El proceso se interrumpio en la fila ' . $filas . ' de excel');

                }
                $stringtipo = str_replace(" ", "", $row['tipo_documento']);
                //vaidar dni y tipo
                $length = Str::length($row['numero_documento']);
                if ($stringtipo == 'DNI' && $length != 8) {
                    return redirect()->back()->with('alert', 'numero de DNI ' . $row['numero_documento'] . ' invalido en la importacion(Debe tener 8 digitos)  .El proceso se interrumpio en la fila ' . $filas . ' de excel');
                }

                if ($stringtipo == 'Carnetextranjeria' && $length != 12) {
                    return redirect()->back()->with('alert', 'numero de Carnet extranjeria ' . $row['numero_documento'] . ' invalido en la importacion(Debe tener 12 digitos)  .El proceso se interrumpio en la fila ' . $filas . ' de excel');
                }
                if ($stringtipo == 'Pasaporte' && $length != 12) {
                    return redirect()->back()->with('alert', 'numero de Pasaporte ' . $row['numero_documento'] . ' invalido en la importacion(Debe tener 12 digitos)  .El proceso se interrumpio en la fila ' . $filas . ' de excel');
                }
                //correo
                $filaCorreo = $this->numRows;
                if ($row['correo'] != null || $row['correo'] != '') {

                    $correoAntiguo = DB::table('empleado')->join('persona as p', 'p.perso_id', '=', 'empleado.emple_persona')
                    ->select(
                            DB::raw('CONCAT(p.perso_apPaterno," ",p.perso_apMaterno) as nombre'),
                            'emple_nDoc as dni'
                        )
                    ->where('emple_Correo', '=', $row['correo'])
                    ->where('empleado.organi_id', '=', session('sesionidorg'))
                        ->where('empleado.emple_estado', '=', 1)->first();
                    if ($correoAntiguo != null) {
                        return redirect()->back()->with('alert', 'El correo: ' . $row['correo'] . '
                         ya se encuentra registrado previamente en RH nube, a nombre del empleado '. $correoAntiguo->nombre .
                          ' con documento:  '.$correoAntiguo->dni.'  .El proceso se interrumpio en la fila: ' . $filas . ' de excel');
                    };


                    }
                     //*VALIDANDO QUE NO ESTE EN OTRO EMPLEADO
                     $capturaCorreo = [$row['correo']];

                     array_push($this->NCorreo, $capturaCorreo);

                    $linealCorreo = Arr::flatten($this->NCorreo);
                    $clave2Correo = array_splice($linealCorreo, 0, $filaCorreo);
                    if($row['correo'] != null){
                    $claveCC = array_search($row['correo'], $clave2Correo);
                    if ($claveCC !== false) {
                        //dd($clave2,$clave,$filaA);
                        return redirect()->back()->with('alert', 'El correo está repetido en el archivo de carga: ' . $row['correo'] . ' .El proceso se interrumpio en la fila ' . $filas . ' de excel');

                    }
                }


                //PREFIJO
                if ($row['prefijo'] != null || $row['prefijo'] != '') {

                } else {
                    if ($row['celular'] != null || $row['celular'] != '') {
                        return redirect()->back()->with('alert', 'Debe especificar prefijo de numero celular' . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');
                    }
                }

                //celular
                if ($row['celular'] != null || $row['celular'] != '') {
                    if (is_numeric($row['celular']) == true) {
                        $lengthCelu = Str::length($row['celular']);
                        if ($lengthCelu > 16) {
                            return redirect()->back()->with('alert', 'el numero de celular: ' . $row['celular'] . ' debe tenr como maximo 15 digitos' . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');
                        } else {
                            /*  $qwert = Str::substr($row['celular'], 0, 1);
                            if ($qwert != 9) {
                            return redirect()->back()->with('alert', 'el numero de celular: ' . $row['celular'] . ' es invalido' . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');
                            } */

                            $seteo = Str::studly($row['celular']);
                            $numeroCelular = '+' . $row['prefijo'] . $seteo;
                        }
                    } else {
                        return redirect()->back()->with('alert', 'Numero de celular invalido.  El proceso se interrumpio en la fila:' . $filas);
                    }

                } else {
                    $numeroCelular = null;
                }

                //dd($arraysimple);

                /*    $busca=$this->Ndoc;
                $arraysimples=Arr::flatten($busca);
                 */

                /*      if($clave!=false){

                } else{return redirect()->back()->with('alert', 'numero de documento2 ya registrado: '.$clave.' El proceso se interrumpio en la fila:de excel');} */
                /* if($busca[1]==12345679){
                dd('1',$busca);
                } */

                /* f($busque==0){
                //dd($busca, $busque);
                return redirect()->back()->with('alert', 'numero de documento2 ya registrado: '.$busque.' El proceso se interrumpio en la fila:de excel');

                } */

                //dd($busque, $busca[0]);
                //departamento
                /*  $cadDep=$row['departamento'];
                if(strlen($cadDep)>3){
                $cadDep = substr ($cadDep, 0, -1);
                }
                ;
                if($row['departamento']!=null){
                $dep = ubigeo_peru_departments::where('name', 'like', "%".escape_like($cadDep)."%")->first();
                if($dep!=null){
                $row['iddep'] = $dep->id;  $row['name_depArray'] = $dep->name; }
                else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$row['departamento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['iddep']=null;}
                }
                else{ $row['name_depArray']=null; } */

                //provincia
                /*   $cadProv=$row['provincia'];
                if(strlen($cadProv)>3){
                $cadProv = substr ($cadProv, 0, -1);
                }
                if($row['provincia']!=null){
                $provi = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProv)."%")->first();
                if( $provi!=null){
                $row['idprov'] = $provi->id;  $row['provArray'] = $provi->name; }
                else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$row['provincia'].'.  El proceso se interrumpio en la fila:'.$filas); $row['idprov']=null;}
                } else{ $row['provArray']=null; } */

                //distrito
                $cadDist = $row['distrito'];
                if (strlen($cadDist) > 4) {
                    $cadDist = substr($cadDist, 0, -1);
                }
                if ($row['distrito'] != null) {

                            $idD = ubigeo_peru_districts::where("name", '=',$row['distrito'])->get();



                    if ($idD->isNotEmpty()) {

                        if ($idD->count() > 1) {

                            $row['id'] = $idD[0]->id;
                            $row['distArray'] = $idD[0]->name;
                            $row['provArray'] = null;
                            $row['name_depArray'] = null;
                        } else {

                            $row['id'] = $idD[0]->id;
                            $row['distArray'] = $idD[0]->name;
                            $provi = ubigeo_peru_provinces::where("id", "=", $idD[0]->province_id)->first();
                            $row['provArray'] = $provi->name;

                            $dep = ubigeo_peru_departments::where('id', '=', $provi->departamento_id)->first();
                            $row['name_depArray'] = $dep->name;

                        }
                    } else {return redirect()->back()->with('alert', 'No se encontro el distrito:' . $row['distrito'] . '.  El proceso se interrumpio en la fila:' . $filas);
                        $row['id'] = null;}
                } else { $row['distArray'] = null;
                    $row['provArray'] = null;
                    $row['name_depArray'] = null;}

                //cargo
                $cargo = cargo::where("cargo_descripcion", "like", "%" . $row['cargo'] . "%")->where('organi_id', '=', session('sesionidorg'))->first();
                if ($row['cargo'] != null) {
                    if ($cargo != null) {
                        $row['idcargo'] = $cargo->cargo_id;
                        $row['cargoArray'] = $cargo->cargo_descripcion;
                    } else { $row['cargoArray'] = $row['cargo'];}
                } else { $row['cargoArray'] = null;}

                //area
                $area = area::where("area_descripcion", "like", "%" . $row['area'] . "%")->where('organi_id', '=', session('sesionidorg'))->first();
                if ($row['area'] != null) {
                    if ($area != null) {
                        $row['idarea'] = $area->area_id;
                        $row['areaArray'] = $area->area_descripcion;
                    } else {
                        $row['areaArray'] = $row['area'];
                    }
                } else { $row['areaArray'] = null;}

                //*CODIGO DE CENTRO DE COSTO
                if ($row['codigo_centro_costo'] != null) {
                    $codcentro_costo = centro_costo::where('codigo', '=', $row['codigo_centro_costo'])
                    ->where('centroC_descripcion', '!=', $row['centro_costo'])
                    ->where('organi_id', '=', session('sesionidorg'))->get()->first();
                    if($codcentro_costo!=null){
                        return redirect()->back()->with('alert', 'Código de centro de costo ya registrado. El proceso se interrumpio en la fila:' . $filas);
                    } else{
                        if ($row['centro_costo'] == null) {
                            return redirect()->back()->with('alert', 'La descripcion del centro de costo debe completarse, ya que has ingresado el código de centro de costo. El proceso se interrumpio en la fila:' . $filas);

                        }
                    }


                } else{
                    if($row['centro_costo'] != null){
                        return redirect()->back()->with('alert', 'El código del centro de costo debe completarse, ya que has ingresado una descripción. El proceso se interrumpio en la fila:' . $filas);
                    }
                }

                //*************************** */
                //centro_costo
                $centro_costo = centro_costo::where('centroC_descripcion', '=', $row['centro_costo'] )
                ->where('organi_id', '=', session('sesionidorg'))->where('porEmpleado', '=', 1)->get()->first();
                if ($row['centro_costo'] != null) {
                    if ($centro_costo != null) {
                        $row['idcentro_costo'] = $centro_costo->centroC_id;
                        $row['centro_costoArray'] = $centro_costo->centroC_descripcion;
                    } else {
                        $row['centro_costoArray'] = $row['centro_costo'];
                    }
                } else { $row['centro_costoArray'] = null;}

                //departamentoNac
                /*  $cadDepN=$row['departamento_nacimiento'];
                if(strlen($cadDepN)>3){
                $cadDepN = substr ($cadDepN, 0, -1);
                }

                if($row['departamento_nacimiento']!=null){
                $depN = ubigeo_peru_departments::where("name", "like", "%".escape_like($cadDepN)."%")->first();
                if($depN!=null){
                $row['iddepartamento_nacimiento'] = $depN->id; $row['name_depNArray'] = $depN->name;
                }
                else{return redirect()->back()->with('alert', 'No se encontro el departamento:'.$row['departamento_nacimiento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['iddepartamento_nacimiento']=null;}
                } else{ $row['name_depNArray']=null; } */

                //provinciaNac
                /*  $cadProvN=$row['provincia_nacimiento'];
                if(strlen($cadProvN)>3){
                $cadProvN = substr ($cadProvN, 0, -1);
                }
                if($row['provincia_nacimiento']!=null){
                $proviN = ubigeo_peru_provinces::where("name", "like", "%".escape_like($cadProvN)."%")->first();
                if($proviN!=null){
                $row['idprovincia_nacimiento'] = $proviN->id; $row['provNArray'] = $proviN->name;
                }
                else{return redirect()->back()->with('alert', 'No se encontro la provincia:'.$row['provincia_nacimiento'].'.  El proceso se interrumpio en la fila:'.$filas); $row['idprovincia_nacimiento']=null;}
                } else{ $row['provNArray']=null; } */

                //distritoNac
                $cadDistN = $row['distrito_nacimiento'];
                if (strlen($cadDistN) >4) {
                    $cadDistN = substr($cadDistN, 0, -1);
                }
                if ($row['distrito_nacimiento'] != null) {

                        $idDN = ubigeo_peru_districts::where("name", '=',$row['distrito_nacimiento'])->get();

                    if ($idDN->isNotEmpty()) {
                        if ($idDN->count() > 1) {
                            $row['iddistrito_nacimiento'] = $idDN[0]->id;
                            $row['distNArray'] = $idDN[0]->name;
                            $row['provNArray'] = null;
                            $row['name_depNArray'] = null;
                        } else {

                            $row['iddistrito_nacimiento'] = $idDN[0]->id;
                            $row['distNArray'] = $idDN[0]->name;
                            $provi2 = ubigeo_peru_provinces::where("id", "=", $idDN[0]->province_id)->first();
                            $row['provNArray'] = $provi2->name;

                            $dep2 = ubigeo_peru_departments::where('id', '=', $provi2->departamento_id)->first();
                            $row['name_depNArray'] = $dep2->name;

                        }

                    } else {return redirect()->back()->with('alert', 'No se encontro el distrito:' . $row['distrito_nacimiento'] . '.  El proceso se interrumpio en la fila:' . $filas);
                        $row['iddistrito_nacimiento'] = null;}
                } else { $row['distNArray'] = null;
                    $row['provNArray'] = null;
                    $row['name_depNArray'] = null;}

                //tipo_contrato
                $tipo_contrato = tipo_contrato::where("contrato_descripcion", "like", "%" . $row['tipo_contrato'] . "%")->where('organi_id', '=', session('sesionidorg'))->first();
                if ($row['tipo_contrato'] != null) {
                    if ($tipo_contrato != null) {
                        $row['idtipo_contrato'] = $tipo_contrato->contrato_id;
                        $row['tipo_contratoArray'] = $tipo_contrato->contrato_descripcion;
                    } else {
                        $row['tipo_contratoArray'] = $row['tipo_contrato'];
                    }
                } else {return redirect()->back()->with('alert', 'Debe especificar tipo de contrato' . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');}

                //local
                $local = local::where("local_descripcion", "like", "%" . $row['local'] . "%")->where('organi_id', '=', session('sesionidorg'))->first();
                if ($row['local'] != null) {
                    if ($local != null) {
                        $row['idlocal'] = $local->local_id;
                        $row['localArray'] = $local->local_descripcion;
                    } else {

                        $row['localArray'] = $row['local'];
                    }
                } else { $row['localArray'] = null;}

                //nivel
                $nivel = nivel::where("nivel_descripcion", "like", "%" . $row['nivel'] . "%")->where('organi_id', '=', session('sesionidorg'))->first();
                if ($row['nivel'] != null) {
                    if ($nivel != null) {
                        $row['idnivel'] = $nivel->nivel_id;
                        $row['nivelArray'] = $nivel->nivel_descripcion;
                    } else {
                        $row['nivelArray'] = $row['nivel'];
                    }
                } else { $row['nivelArray'] = null;}

                //CONDICION_PAGO
                $condicPago = condicion_pago::where("condicion", "like", "%" . $row['condicion_pago'] . "%")
                    ->where('organi_id', '=', session('sesionidorg'))->first();
                if ($row['condicion_pago'] != null) {
                    if ($condicPago != null) {
                        $row['idcondicion'] = $condicPago->id;
                        $row['condicionArray'] = $condicPago->condicion;
                    } else {

                        $row['condicionArray'] = $row['condicion_pago'];
                    }
                } else { $row['condicionArray'] = null;}
                ///

                if ($row['fecha_nacimiento'] != null || $row['fecha_nacimiento'] != '') {
                    if (is_numeric($row['fecha_nacimiento']) == true) {
                        $fechaNacimieB = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_nacimiento']), 'Y-m-d');
                    } else {
                        $sincomilla = str_replace("'", "", $row['fecha_nacimiento']);
                        $crearformat = DateTime::createFromFormat('d/m/Y', $sincomilla)->format('Y/m/d');
                        $validacion = validateDate($crearformat, 'Y/m/d');
                        if ($validacion == true) {
                            $formatoFN = Carbon::create($crearformat);
                            $fechaNacimieB = date_format($formatoFN, 'Y-m-d');
                        } else {
                            return redirect()->back()->with('alert', 'Formato de fecha de contrato incorrecta.  El proceso se interrumpio en la fila:' . $filas);
                        }

                    }

                } else {
                    $fechaNacimieB = '';
                }

                if ($row['tipo_contratoArray'] != null || $row['tipo_contratoArray'] != '') {
                    if ($row['condicionArray'] != null || $row['condicionArray'] != '') {
                        if ($row['monto_pago'] != null || $row['monto_pago'] != '') {
                            if (is_numeric($row['monto_pago'])) {
                            } else {
                                return redirect()->back()->with('alert', 'el monto no es numerico.  El proceso se interrumpio en la fila:' . $filas);

                            }
                        }
                    } else {
                        if ($row['monto_pago'] != null || $row['monto_pago'] != '') {
                            return redirect()->back()->with('alert', 'Debe especificar condicion de pago.  El proceso se interrumpio en la fila:' . $filas);
                        }
                    }
                } else {

                    if ($row['monto_pago'] != null || $row['monto_pago'] != '' || $row['condicionArray'] != null || $row['condicionArray'] != '') {
                        return redirect()->back()->with('alert', 'Debe especificar tipo de contrato.  El proceso se interrumpio en la fila:' . $filas);
                    }

                }

                //fechaIContrato

                if ($row['inicio_contrato'] != null || $row['inicio_contrato'] != '') {
                    if (is_numeric($row['inicio_contrato']) == true) {
                        $fechaInicioC = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['inicio_contrato']), 'Y-m-d');
                    } else {
                        $sincomilla = str_replace("'", "", $row['inicio_contrato']);
                        $crearformat = DateTime::createFromFormat('d/m/Y', $sincomilla)->format('Y/m/d');

                        $validacion = validateDate($crearformat, 'Y/m/d');

                        if ($validacion == true) {
                            $formatoFc = Carbon::create($crearformat);
                            $fechaInicioC = date_format($formatoFc, 'Y-m-d');
                        } else {
                            return redirect()->back()->with('alert', 'Formato de fecha de contrato incorrecta.  El proceso se interrumpio en la fila:' . $filas);
                        }

                    }
                } else {
                    return redirect()->back()->with('alert', 'Debe especificar inicio de contrato' . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');

                }

                //*VALIDACION FIN DE CONTRATO////////////////////////
                if ($row['fin_contrato'] != null || $row['fin_contrato'] != '') {

                    //*OBTENEMOS FECHA INICIAL
                    $fechaICarbon=Carbon::create($fechaInicioC);

                    if (is_numeric($row['fin_contrato']) == true) {
                        $fechaFinC = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fin_contrato']), 'Y-m-d');
                        $fechaFCarbon1=Carbon::create($fechaFinC);

                        if($fechaFCarbon1->lt($fechaICarbon)){
                            return redirect()->back()->with('alert', 'Fecha de fin de contrato debe ser mayor a la fecha inicial de contrato.  El proceso se interrumpio en la fila:' . $filas);
                        }

                    } else {
                        $sincomilla = str_replace("'", "", $row['fin_contrato']);
                        $crearformat = DateTime::createFromFormat('d/m/Y', $sincomilla)->format('Y/m/d');

                        $validacion = validateDate($crearformat, 'Y/m/d');

                        if ($validacion == true) {
                            $formatoFc = Carbon::create($crearformat);
                            $fechaFinC = date_format($formatoFc, 'Y-m-d');
                            $fechaFCarbon2=Carbon::create($fechaFinC);

                            if($fechaFCarbon2->lt($fechaICarbon)){
                                return redirect()->back()->with('alert', 'Fecha de fin de contrato debe ser mayor a la fecha inicial de contrato.  El proceso se interrumpio en la fila:' . $filas);
                            }
                        } else {
                            return redirect()->back()->with('alert', 'Formato de fecha de fin de contrato incorrecta.  El proceso se interrumpio en la fila:' . $filas);
                        }

                    }
                }
                else{
                    $fechaFinC =null;
                }

                /* ********************************************** */
                //VALIDACION GENERO
                if ($row['genero'] != null || $row['genero'] != '') {

                } else {
                    return redirect()->back()->with('alert', 'Debe especificar genero de empleado' . ' El proceso se interrumpio en la fila: ' . $filas . ' de excel');

                }

                //*VALIDACION DIAS NOTIFICACION
                if ($row['dias_notificacion'] != null || $row['dias_notificacion'] != '') {
                    $diasNotificacion=$row['dias_notificacion'];
                } else {

                    $diasNotificacion=0;
                }
                /*   dd(date_format( $fechaNacimieB, 'Y-m-d')); */
                //////////MANDA DATOS A VISTA
                $din = [$row['tipo_docArray'], $row['numero_documento'], $row['nombres'], $row['apellido_paterno'], $row['apellido_materno'],
                        $row['correo'], $numeroCelular, $row['genero'], $fechaNacimieB, $row['name_depNArray'], $row['provNArray'],
                        $row['distNArray'], $row['direccion'], $row['name_depArray'], $row['provArray'], $row['distArray'],
                        $row['tipo_contratoArray'], $row['localArray'], $row['nivelArray'], $row['cargoArray'], $row['areaArray'],
                        $row['centro_costoArray'], $row['condicionArray'], $row['monto_pago'], $fechaInicioC, $row['codigo'], $fechaFinC,$diasNotificacion,
                        $row['codigo_centro_costo']];
                array_push($this->dnias, $din);

                ++$this->numRows;

            }
            $contando1 = count($this->dnias);
            if ($contando1 == 0) {

                return redirect()->back()->with('alert', 'Archivo de carga vacío');}
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
    public function chunkSize(): int
    {
        return 2000;
    }
    public function getRowCount(): int
    {
        return $this->numRows;
    }
    public function dniE()
    {
        return $this->dnias;
    }


    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }

}
