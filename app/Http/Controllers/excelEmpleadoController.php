<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmpleadoImport;
use Maatwebsite\Excel\Facades\Excel;
class excelEmpleadoController extends Controller
{
    //
    public function import()
    {
        $import =new  EmpleadoImport();//del userimpor
        Excel::import($import,request()->file('file'));

        return view('empleado.cargarEmpleado', ['numRows'=>$import->getRowCount(),'alert'=>$import->errors()]);
    }
}
