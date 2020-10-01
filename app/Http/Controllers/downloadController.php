<?php

namespace App\Http\Controllers;

use App\licencia_empleado;
use App\vinculacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class downloadController extends Controller
{
    protected function downloadArchivo($src)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $content_type = finfo_file($finfo, $src);
        finfo_close($finfo);
        $file_name = basename($src) . PHP_EOL;
        $size = filesize($src);
        header("Content-Type:$content_type");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length:$size");
        readfile($src);
        return true;
    }

    public function download($code)
    {

        $vinculacion = vinculacion::where('descarga', '=', $code)->first();
        if ($vinculacion) {
            $vinculacion->fecha_entrega = Carbon::now();
            $vinculacion->save();
            return response()->download(app_path() . "/file/RH box.exe");
        } else {
            return view('Verificacion.link');
        }
    }

    public function vistaPrueba()
    {
        return view('Verificacion.download');
    }

    public function buscarLicencia(Request $request)
    {
        $licencia = $request->get('licencia');
        $licencia_empleado = licencia_empleado::where('licencia', '=', $licencia)->get()->first();
        if ($licencia_empleado) {
            if ($licencia_empleado->disponible == 'e') {
                $licencia_empleado->disponible = 'a';
                $licencia_empleado->save();
                return response()->json("Licencia Correcta", 200);
            }
            return response()->json("Licencia no disponible", 400);
        }
        return response()->json("Licencia incorrecta", 400);
    }
}
