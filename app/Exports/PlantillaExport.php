<?php

namespace App\Exports;

use App\area;
use App\cargo;
use App\centro_costo;
use App\local;
use App\nivel;
use App\condicion_pago;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormating;
use App\ubigeo_peru_departments;
use App\ubigeo_peru_provinces;
use App\ubigeo_peru_districts;
use App\tipo_documento;
use App\tipo_contrato;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlantillaExport implements WithHeadings, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($total)
    {
        $this->total = $total;
    }

    public function headings(): array
    {
        return [
            'tipo_documento', 'numero_documento', 'nombres', 'apellido_paterno',
            'apellido_materno','correo','celular','sexo', 'fecha_nacimiento', /* 'departamento_nacimiento',
            'provincia_nacimiento', */ 'distrito_nacimiento', 'direccion', /* 'departamento', 'provincia', */ 'distrito',
            'tipo_contrato', 'local', 'nivel','cargo', 'area', 'centro_costo','condicion_pago','monto_pago'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $styleArray = [
                    'font' => [
                        'bold' => true,
                        'color' => [
                            'rgb' => 'FFFFFF',
                        ]
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '0A0A2A',
                        ]
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $event->sheet->getStyle('A1:T1')->applyFromArray($styleArray);
                $event->sheet->getDelegate()->setTitle("Empleado");
                foreach (range('A', 'T') as $columnID) {
                    $event->sheet->getColumnDimension($columnID)->setAutoSize(false);
                    $event->sheet->getColumnDimension($columnID)
                        ->setWidth(25);
                }
              /*   $departamentos = ubigeo_peru_departments::all();
                $provincias = ubigeo_peru_provinces::all(); */
                $distrito = DB::table('ubigeo_peru_districts')->groupBy('name')
                ->orderBy('name','ASC')->get();
                $tipoDocumento = tipo_documento::all();
                $tipoContrato = tipo_contrato::all();
                $cargo = cargo::where('organi_id','=',session('sesionidorg'))->get();
                $area = area::where('organi_id','=',session('sesionidorg'))->get();
                $centroC = centro_costo::all();
                $local = local::all();
                $nivel = nivel::all();
                $condicion_pago=condicion_pago::where('organi_id','=',session('sesionidorg'))->get();

               /*  $drop_column = 'N';
                $drop_columnP = 'o'; */
                $drop_columnD = 'A';
                $drop_columnC = 'M';
              /*   $drop_columnN = 'J';
                $drop_columnPN = 'K'; */
                $drop_columnCargo = 'P';
                $drop_columnArea = 'Q';
                $drop_columnCentro = 'R';
                $drop_columnLocal = 'N';
                $drop_columnNivel = 'O';
                $drop_columnGenero = 'H';

                $drop_columnFecha = 'I';
                $drop_columnCondicionP = 'S';

                $drop_columnDisritoN = 'J';
                $drop_columnDisritoVive = 'L';

               /*  $row = 1;
                $rowP = 1; */
                $rowD = 1;
                $rowC = 1;
                $rowCargo = 1;
                $rowArea = 1;
                $rowCentro = 1;
                $rowLocal = 1;
                $rowNivel = 1;
                $rowCondP=1;
                $rowConDist=1;
                //TIPODOCUMENTO
                foreach ($tipoDocumento as $tipoDocumentos) {
                    $event->sheet->getDelegate()->setCellValue('BC' . $rowD++, $tipoDocumentos->tipoDoc_descripcion);
                }

                $validationD = $event->sheet->getDelegate()->getCell("{$drop_columnD}2")->getDataValidation();
                $validationD->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationD->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationD->setAllowBlank(false);
                $validationD->setShowInputMessage(true);
                $validationD->setShowErrorMessage(true);
                $validationD->setShowDropDown(true);
                $validationD->setErrorTitle('Error');
                $validationD->setError('Tipo de Documento no se encuentra en la lista.');
                $validationD->setPromptTitle('Tipo documento');
                $validationD->setPrompt('Elegir una opción');
                $validationD->setFormula1('Empleado!$BC$1:$BC$3');

                //DEPARTAMENTO
              /*   foreach ($departamentos as $departamento) {
                    $event->sheet->getDelegate()->setCellValue('BA' . $row++, $departamento->name);
                }

                $validation = $event->sheet->getDelegate()->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Error');
                $validation->setError('Departamento no se encuentra en la lista.');
                $validation->setPromptTitle('Departamentos');
                $validation->setPrompt('Elegir una opción');
                $validation->setFormula1('Empleado!$BA$1:$BA$25'); */

                //PROVINCIA
               /*  foreach ($provincias as $provincia) {
                    $event->sheet->getDelegate()->setCellValue('BF' . $rowP++, $provincia->name);
                }

                $validationP = $event->sheet->getDelegate()->getCell("{$drop_columnP}2")->getDataValidation();
                $validationP->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationP->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationP->setAllowBlank(false);
                $validationP->setShowInputMessage(true);
                $validationP->setShowErrorMessage(true);
                $validationP->setShowDropDown(true);
                $validationP->setErrorTitle('Error');
                $validationP->setError('Provincia no se encuentra en la lista.');
                $validationP->setPromptTitle('Provincias');
                $validationP->setPrompt('Elegir una opción');
                $validationP->setFormula1('Empleado!$BF$1:$BF$193'); */

                 //DISTRITO
                foreach ($distrito as $distritos) {
                    $event->sheet->getDelegate()->setCellValue('CJ' . $rowConDist++, $distritos->name);
                }

                $validationDist = $event->sheet->getDelegate()->getCell("{$drop_columnDisritoN}2")->getDataValidation();
                $validationDist->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationDist->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                 $validationDist->setAllowBlank(false);
                $validationDist->setShowInputMessage(true);
                $validationDist->setShowErrorMessage(true);
                $validationDist->setShowDropDown(true);
                $validationDist->setErrorTitle('Error');
                $validationDist->setError('Distrito no se encuentra en la lista.');
                $validationDist->setPromptTitle('Distritos');
                $validationDist->setPrompt('Elegir una opción');
                $validationDist->setFormula1('Empleado!$CJ$1:$CJ$1679');

                //TIPO CONTRATO
                foreach ($tipoContrato as $tipoContratos) {
                    $event->sheet->getDelegate()->setCellValue('BJ' . $rowC++, $tipoContratos->contrato_descripcion);
                }

                $validationC = $event->sheet->getDelegate()->getCell("{$drop_columnC}2")->getDataValidation();
                $validationC->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationC->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationC->setAllowBlank(false);
                $validationC->setShowInputMessage(true);
                $validationC->setShowErrorMessage(true);
                $validationC->setShowDropDown(true);
                $validationC->setErrorTitle('Error');
                $validationC->setError('tipo de Contrato no se encuentra en la lista.');
                $validationC->setPromptTitle('Tipo de Documento');
                $validationC->setPrompt('Elegir una opción o agregar nuevo');
                $validationC->setFormula1('Empleado!$BJ$1:$BJ$5');

                //CARGO
                foreach ($cargo as $cargos) {
                    $event->sheet->getDelegate()->setCellValue('BN' . $rowCargo++, $cargos->cargo_descripcion);
                }

                $validationCargo = $event->sheet->getDelegate()->getCell("{$drop_columnCargo}2")->getDataValidation();
                $validationCargo->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationCargo->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationCargo->setAllowBlank(false);
                $validationCargo->setShowInputMessage(true);
                $validationCargo->setShowErrorMessage(true);
                $validationCargo->setShowDropDown(true);
                $validationCargo->setErrorTitle('Error');
                $validationCargo->setError('Cargo no se encuentra en la lista.');
                $validationCargo->setPromptTitle('Cargo');
                $validationCargo->setPrompt('Elegir una opción o agregar nuevo');
                $validationCargo->setFormula1('Empleado!$BN$1:$BN$10');

                //AREA
                foreach ($area as $areas) {
                    $event->sheet->getDelegate()->setCellValue('BQ' . $rowArea++, $areas->area_descripcion);
                }

                $validationArea = $event->sheet->getDelegate()->getCell("{$drop_columnArea}2")->getDataValidation();
                $validationArea->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationArea->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationArea->setAllowBlank(false);
                $validationArea->setShowInputMessage(true);
                $validationArea->setShowErrorMessage(true);
                $validationArea->setShowDropDown(true);
                $validationArea->setErrorTitle('Error');
                $validationArea->setError('Área no se encuentra en la lista.');
                $validationArea->setPromptTitle('Área');
                $validationArea->setPrompt('Elegir una opción o agregar nuevo');
                $validationArea->setFormula1('Empleado!$BQ$1:$BQ$10');

                //CENTRO
                foreach ($centroC as $centroCs) {
                    $event->sheet->getDelegate()->setCellValue('BT' . $rowCentro++, $centroCs->centroC_descripcion);
                }

                $validationCentro = $event->sheet->getDelegate()->getCell("{$drop_columnCentro}2")->getDataValidation();
                $validationCentro->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationCentro->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationCentro->setAllowBlank(false);
                $validationCentro->setShowInputMessage(true);
                $validationCentro->setShowErrorMessage(true);
                $validationCentro->setShowDropDown(true);
                $validationCentro->setErrorTitle('Error');
                $validationCentro->setError('Centro no se encuentra en la lista.');
                $validationCentro->setPromptTitle('Centro');
                $validationCentro->setPrompt('Elegir una opción o agregar nuevo');
                $validationCentro->setFormula1('Empleado!$BT$1:$BT$10');

                //LOCAL
                foreach ($local as $locals) {
                    $event->sheet->getDelegate()->setCellValue('CA' . $rowLocal++, $locals->local_descripcion);
                }

                $validationLocal = $event->sheet->getDelegate()->getCell("{$drop_columnLocal}2")->getDataValidation();
                $validationLocal->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationLocal->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationLocal->setAllowBlank(false);
                $validationLocal->setShowInputMessage(true);
                $validationLocal->setShowErrorMessage(true);
                $validationLocal->setShowDropDown(true);
                $validationLocal->setErrorTitle('Error');
                $validationLocal->setError('Local no se encuentra en la lista.');
                $validationLocal->setPromptTitle('Local');
                $validationLocal->setPrompt('Elegir una opción o agregar nuevo');
                $validationLocal->setFormula1('Empleado!$CA$1:$CA$10');

                //NIVEL
                foreach ($nivel as $nivels) {
                    $event->sheet->getDelegate()->setCellValue('CC' . $rowNivel++, $nivels->nivel_descripcion);
                }

                $validationNivel = $event->sheet->getDelegate()->getCell("{$drop_columnNivel}2")->getDataValidation();
                $validationNivel->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationNivel->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationNivel->setAllowBlank(false);
                $validationNivel->setShowInputMessage(true);
                $validationNivel->setShowErrorMessage(true);
                $validationNivel->setShowDropDown(true);
                $validationNivel->setErrorTitle('Error');
                $validationNivel->setError('Nivel no se encuentra en la lista.');
                $validationNivel->setPromptTitle('Nivel');
                $validationNivel->setPrompt('Elegir una opción o agregar nuevo');
                $validationNivel->setFormula1('Empleado!$CC$1:$CC$10');

                //Genero

                $validationGenero = $event->sheet->getDelegate()->getCell("{$drop_columnGenero}2")->getDataValidation();
                $validationGenero->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationGenero->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationGenero->setAllowBlank(false);
                $validationGenero->setShowInputMessage(true);
                $validationGenero->setShowErrorMessage(true);
                $validationGenero->setShowDropDown(true);
                $validationGenero->setErrorTitle('Error');
                $validationGenero->setError('Género no se encuentra en la lista.');
                $validationGenero->setPromptTitle('Género');
                $validationGenero->setPrompt('Elegir una opción');
                $validationGenero->setFormula1('"Femenino,Masculino,Personalizado"');

                //condicion_pago
                foreach ($condicion_pago as $condicion_pagos) {
                    $event->sheet->getDelegate()->setCellValue('CD' . $rowCondP++, $condicion_pagos->condicion);
                }

                $validationCondiP = $event->sheet->getDelegate()->getCell("{$drop_columnCondicionP}2")->getDataValidation();
                $validationCondiP->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationCondiP->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationCondiP->setAllowBlank(false);
                $validationCondiP->setShowInputMessage(true);
                $validationCondiP->setShowErrorMessage(true);
                $validationCondiP->setShowDropDown(true);
                $validationCondiP->setErrorTitle('Error');
                $validationCondiP->setError('Condicion no se encuentra en la lista.');
                $validationCondiP->setPromptTitle('CondicionPago');
                $validationCondiP->setPrompt('Elegir una opción o agregar nuevo');
                $validationCondiP->setFormula1('Empleado!$CD$1:$CD$10');

                for ($i = 2; $i <= $this->total; $i++) {
                    $event->sheet->getCell("{$drop_columnD}{$i}")->setDataValidation(clone $validationD);
                   /*  $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    $event->sheet->getCell("{$drop_columnP}{$i}")->setDataValidation(clone $validationP);
                    $event->sheet->getCell("{$drop_columnN}{$i}")->setDataValidation(clone $validation);
                    $event->sheet->getCell("{$drop_columnPN}{$i}")->setDataValidation(clone $validationP); */
                    $event->sheet->getCell("{$drop_columnDisritoN}{$i}")->setDataValidation(clone $validationDist);
                    $event->sheet->getCell("{$drop_columnDisritoVive}{$i}")->setDataValidation(clone $validationDist);
                    $event->sheet->getCell("{$drop_columnC}{$i}")->setDataValidation(clone $validationC);
                    $event->sheet->getCell("{$drop_columnCargo}{$i}")->setDataValidation(clone $validationCargo);
                    $event->sheet->getCell("{$drop_columnArea}{$i}")->setDataValidation(clone $validationArea);
                    $event->sheet->getCell("{$drop_columnCentro}{$i}")->setDataValidation(clone $validationCentro);
                    $event->sheet->getCell("{$drop_columnLocal}{$i}")->setDataValidation(clone $validationLocal);
                    $event->sheet->getCell("{$drop_columnNivel}{$i}")->setDataValidation(clone $validationNivel);
                    $event->sheet->getCell("{$drop_columnGenero}{$i}")->setDataValidation(clone $validationGenero);
                    $event->sheet->getStyle("{$drop_columnFecha}{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD);
                    $event->sheet->getCell("{$drop_columnCondicionP}{$i}")->setDataValidation(clone $validationCondiP);
                }
                $event->sheet->getColumnDimension('BC')->setVisible(false);
                $event->sheet->getColumnDimension('BA')->setVisible(false);
                $event->sheet->getColumnDimension('BF')->setVisible(false);
                $event->sheet->getColumnDimension('BJ')->setVisible(false);
                $event->sheet->getColumnDimension('BN')->setVisible(false);
                $event->sheet->getColumnDimension('BQ')->setVisible(false);
                $event->sheet->getColumnDimension('BT')->setVisible(false);
                $event->sheet->getColumnDimension('CA')->setVisible(false);
                $event->sheet->getColumnDimension('CC')->setVisible(false);
                $event->sheet->getColumnDimension('CD')->setVisible(false);
                $event->sheet->getColumnDimension('CJ')->setVisible(false);
                //

            }
        ];
    }
}
