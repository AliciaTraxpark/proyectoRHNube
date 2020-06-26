<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\PlantillaExport;

class MyController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function importExportView()
    {
        return view('import');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new PlantillaExport(501), 'Empleados.xlsx');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import()
    {
        $import = new  UsersImport(); //del userimpor
        Excel::import($import, request()->file('file'));

        return view('import', ['numRows' => $import->getRowCount()]);
    }
}
