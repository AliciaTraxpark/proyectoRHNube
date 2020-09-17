<?php

namespace App\Http\Controllers;

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
            //$this->downloadArchivo(app_path() . "/file/Debug.rar");
            $vinculacion->descarga = null;
            $vinculacion->fecha_entrega = Carbon::now();
            $vinculacion->save();
            return response()->download(app_path() . "/file/RH Nube.exe");
        } else {
            return view('Verificacion.link');
        }
    }
}
