<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmpleadoImport;
use Maatwebsite\Excel\Facades\Excel;
class excelEmpleadoController extends Controller
{
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
        //$parameters =  ['numRows'=>$import->getRowCount(),'alert'=>$import->errors()];
        //return back()->with(['numRows'=>$import->getRowCount(),'alert'=>$import->errors()]);
        return back()->with('filas',$filas);
    }
}
