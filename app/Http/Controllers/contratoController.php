<?php

namespace App\Http\Controllers;

use App\contrato;
use App\doc_empleado;
use App\empleado;
use App\historial_empleado;
use Illuminate\Http\Request;
use App\tipo_contrato;
use Carbon\Carbon;
use App\persona;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NuevaNotification;
use Illuminate\Support\Facades\DB;

class contratoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(Request $request)
    {
        $tipoC = new tipo_contrato();
        $tipoC->contrato_descripcion = $request->get('contrato_descripcion');
        $tipoC->organi_id = session('sesionidorg');
        $tipoC->save();
        return  $tipoC;
    }
    //* HISTORIAL CON CONTRATO
    public function historialEmpleado(Request $request)
    {
        $idempleado = $request->idempleado;

        $historial_empleado = DB::table('historial_empleado as he')
            ->leftJoin('doc_empleado as de', 'he.idhistorial_empleado', '=', 'de.idhistorial_empleado')
            ->leftJoin('contrato as c', 'he.idContrato', '=', 'c.id')
            ->leftJoin('tipo_contrato as tc', 'tc.contrato_id', '=', 'c.id_tipoContrato')
            ->select('he.fecha_alta', 'he.fecha_baja', 'tc.contrato_descripcion as contrato', 'he.idhistorial_empleado as id', 'c.id as idContrato')
            ->selectRaw('GROUP_CONCAT(de.rutaDocumento) as rutaDocumento')
            ->groupBy('he.idhistorial_empleado')
            ->where('he.emple_id', $idempleado)
            ->get();

        return $historial_empleado;
    }

    //* FUNCION DE BAJA
    public function bajaHistorialEmpleado(Request $request)
    {
        $id = $request->get('id');
        $fechaBaja = $request->get('fechaBaja');

        //* HISTORIAL EMPLEADO
        $historial_empleado = historial_empleado::where('idhistorial_empleado', '=', $id)->get()->first();
        if (Carbon::parse($fechaBaja)->gt(Carbon::parse($historial_empleado->fecha_alta))) {
            $historial_empleado->fecha_baja = $fechaBaja;
            $historial_empleado->save();

            $contrato = contrato::where('id', '=', $historial_empleado->idContrato)->get()->first();
            $contrato->fechaFinal = $request->get('fechaBaja');
            $contrato->estado = 0;
            $contrato->save();

            $empleado = empleado::where('emple_id', '=', $historial_empleado->emple_id)->get()->first();
            $empleado->emple_estado = 0;
            $empleado->save();

            return response()->json($historial_empleado->idContrato, 200);
        } else {
            return response()->json(array("respuesta" => false, "fecha" => $historial_empleado->fecha_alta), 200);
        }
    }

    //* FUNCION DETALLES DE CONTRATO
    public function detallesContrato(Request $request)
    {
        $id = $request->get('id');
        // DB::enableQueryLog();
        $contrato = DB::table('contrato as c')
            ->join('tipo_contrato as tc', 'tc.contrato_id', '=', 'c.id_tipoContrato')
            ->leftJoin('condicion_pago as cp', 'cp.id', 'c.id_condicionPago')
            ->join('historial_empleado as he', 'he.idContrato', 'c.id')
            ->leftJoin('doc_empleado as de', 'de.idhistorial_empleado', '=', 'he.idhistorial_empleado')
            ->select('tc.contrato_id as tipoContrato', 'c.id_condicionPago as condPago', 'c.fechaInicio', 'c.fechaFinal', 'c.monto', 'c.id as idC', 'he.fecha_alta as fechaAlta', 'he.fecha_baja as fechaBaja', 'c.estado', 'c.notiTiempo')
            ->selectRaw('GROUP_CONCAT(de.rutaDocumento) as rutaDocumento')
            ->where('c.id', '=', $id)
            ->get()
            ->first();
        // dd(DB::getQueryLog());
        return response()->json($contrato, 200);
    }

    //* EDITAR DATOS DE DETALLE DE CONTRATO
    public function editarContrato(Request $request)
    {
        $idContrato = $request->get('idContrato');
        $contrato = contrato::where('id', '=', $idContrato)->get()->first();
        $contrato->fechaInicio = $request->get('fechaInicial');
        $contrato->fechaFinal = $request->get('fechaFinal');
        $contrato->monto = $request->get('monto');
        $contrato->id_condicionPago = $request->get('condicionPago');
        $contrato->notiTiempo = $request->get('notiTiempo');
        $contrato->save();

        //* HISTORIAL DE EMPLEADO

        $historial_empleado = historial_empleado::where('idContrato', '=', $idContrato)->get()->first();
        $historial_empleado->fecha_alta = $request->get('fechaAlta');
        $historial_empleado->fecha_baja = $request->get('fechaBaja');
        $historial_empleado->save();

        return response()->json(array('status' => true), 200);
    }

    //* EDITAR ARCHIVOS
    public function agregarArchivosEdit(Request $request, $id)
    {
        //* VALIDAR SI ES VACIO O O ACTUALIZAR
        if ($request->hasFile('file')) {
            $historial_empleado = historial_empleado::where('idContrato', '=', $id)->get()->first();
            foreach ($request->file('file') as $filesC) {
                $file = $filesC;
                $path = public_path() . '/documEmpleado';
                $fileName = uniqid() . $file->getClientOriginalName();
                $file->move($path, $fileName);

                $doc_empleado = new doc_empleado();
                $doc_empleado->idhistorial_empleado = $historial_empleado->idhistorial_empleado;
                $doc_empleado->rutaDocumento = $fileName;
                $doc_empleado->save();
            }
        }

        return response()->json(array('status' => true), 200);
    }

    //* REGISTRAR UNA NUEVA ALTA
    public function nuevaAlta(Request $request)
    {
        $contrato = new contrato();
        $contrato->id_tipoContrato = $request->get('contrato');
        $contrato->id_condicionPago = $request->get('condicionPago');
        $contrato->fechaInicio = $request->get('fechaInicial');
        $contrato->fechaFinal = $request->get('fechaFinal');
        $contrato->monto = $request->get('monto');
        $contrato->idEmpleado = $request->get('idEmpleado');
        $contrato->estado = 1;
        $contrato->notiTiempo = $request->get('notiTiempo');
        $contrato->save();

        $idContrato = $contrato->id;

        $historial_empleado = new historial_empleado();
        $historial_empleado->emple_id = $request->get('idEmpleado');
        $historial_empleado->fecha_alta = $request->get('fechaAlta');
        $historial_empleado->idContrato = $idContrato;
        $historial_empleado->save();

        $empleado = empleado::where('emple_id', '=', $request->get('idEmpleado'))->get()->first();
        $empleado->emple_estado = 1;
        $empleado->save();
        // NOTIFICACI??N FIN DE CONTRATO
        //$persona = Persona::find($empleado->emple_persona);
        //$mensaje =  [
                        //"idOrgani" => session('sesionidorg'),
                        //"idEmpleado" => $persona->perso_id,
                        //"empleado" => [
                                //$persona->perso_nombre,
                                //$persona->perso_apPaterno,
                                //$persona->perso_apMaterno
                            //],
                        //"mensaje" => "Contratado",
                        //"asunto" => "contract"
                    //];

        //$recipient = Auth::user();
        //$recipient->notify(new NuevaNotification($mensaje));
        // FIN DE NOTIFICACI??N
        return response()->json($idContrato, 200);
    }

    //* ELIMINAR CONTRATO
    public function eliminarContrato(Request $request)
    {
        $id = $request->get('id');
        $historial_empleado = historial_empleado::where('idhistorial_empleado', '=', $id)->get()->first();
        $contrato = contrato::where('id', '=', $historial_empleado->idContrato)->get()->first();
        $documentos = doc_empleado::where('idhistorial_empleado', '=', $historial_empleado->idhistorial_empleado)->get();
        for ($j = 0; $j < sizeof($documentos); $j++) {
            $rutaDoc = public_path() . '/documEmpleado/' . $documentos[$j]->rutaDocumento;
            unlink($rutaDoc);
            $doc = doc_empleado::where('iddoc_empleado', '=', $documentos[$j]->iddoc_empleado)->delete();
        }
        $historial_empleado->delete();
        $contrato->delete();

        return response()->json(array('status' => true), 200);
    }

    //* COMPROBAR SI CONTIENE HISTORIAL
    public function dataHistorialEmpleado(Request $request)
    {
        $id = $request->get('id');
        $historial_empleado = historial_empleado::where('emple_id', '=', $id)->get()->first();
        if ($historial_empleado) {
            if (is_null($historial_empleado->idContrato)) {
                $arrayR = array("respuesta" => false, "he" => $historial_empleado->idhistorial_empleado);
                return $arrayR;
            } else {
                $contrato = contrato::where('id', '=', $historial_empleado->idContrato)->get()->first();
                if (is_null($contrato->id_condicionPago) || is_null($contrato->fechaInicio)) {
                    $arrayR = array("respuesta" => false, "he" => $historial_empleado->idhistorial_empleado);
                    return $arrayR;
                }
                return 1;
            }
        } else {
            return 0;
        }
    }

    //* NUEVO DETALLE DE CONTRATO
    public function nuevoDetalleC(Request $request)
    {
        $idHistorial = $request->get('idHistorial');
        $historial_empleado = historial_empleado::where('idhistorial_empleado', '=', $idHistorial)->get()->first();

        $contrato = new contrato();
        $contrato->id_tipoContrato = $request->get('contrato');
        $contrato->id_condicionPago = $request->get('condicionPago');
        $contrato->fechaInicio = $request->get('fechaInicial');
        $contrato->fechaFinal = $request->get('fechaFinal');
        $contrato->monto = $request->get('monto');
        $contrato->idEmpleado = $request->get('idEmpleado');
        $contrato->estado = 1;
        $contrato->save();

        $idContrato = $contrato->id;

        $historial_empleado->idContrato = $idContrato;
        $historial_empleado->save();

        return response()->json($idContrato, 200);
    }

    //* VALIDACION DE FECHA DE INICIO DE CONTRATO EN DETALLES DE CONTRATO
    public function validacionFechaInicioDetalle(Request $request)
    {
        $idContrato = $request->get('contrato');
        $respuesta = [];

        $contrato = contrato::where('id', '=', $idContrato)->get()->first();
        //* BUSCAR CONTRATO MENOR
        $contratoMenor = contrato::select('fechaFinal')->where('id', '<', $idContrato)->where('idEmpleado', '=', $contrato->idEmpleado)->orderBy('id', 'desc')->get()->first();
        if ($contratoMenor) {
            array_push($respuesta, $contratoMenor);
        }

        return response()->json($respuesta, 200);
    }

    //* VALIDACION DE FECHA DE INICIO EN NUEVA ALTA
    public function validacionFechaInicio(Request $request)
    {
        $idEmpleado = $request->get('empleado');
        $respuesta = [];
        $ultimoContrato = contrato::select('fechaFinal')->where('idEmpleado', '=', $idEmpleado)->orderBy('id', 'desc')->get()->first();
        if ($ultimoContrato) {
            array_push($respuesta, $ultimoContrato);
        }
        return response()->json($respuesta, 200);
    }
}
