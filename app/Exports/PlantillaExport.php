<?php

namespace App\Exports;

use App\tipo_contrato;
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

class PlantillaExport implements WithHeadings,ShouldAutoSize,WithEvents
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
        return[
            'tipo_documento','numero_documento','nombres','apellido_paterno',
            'apellido_materno','direccion','departamento','provincia','distrito',
            'cargo','area','centro_costo','fecha_nacimiento','departamento_nacimiento',
            'provincia_nacimiento','distrito_nacimiento','sexo','tipo_contrato','local'
            ,'nivel'
        ];
    }

    public function registerEvents(): array
    {
        return[
            AfterSheet::class => function(AfterSheet $event){
                $styleArray = [
                    'font' => [
                        'bold' => true,
                    ],
                    'color'=>[
                        'rgb' => 'FF0000',
                    ]
                ];

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
                $event->sheet->getDelegate()->setTitle("Empleado");
                $departamentos=ubigeo_peru_departments::all();
                $provincias = ubigeo_peru_provinces::all();
                $tipoDocumento = tipo_documento::all();

                $drop_column = 'G';
                $drop_columnP = 'H';
                $drop_columnD = 'A';
                //getHighestRow();

                $row = 1;
                $rowP = 1;
                $rowD = 1;

                //TIPODOCUMENTO
                foreach($tipoDocumento as $tipoDocumentos){
                    $event->sheet->getDelegate()->setCellValue('CA'.$rowD++,$tipoDocumentos->tipoDoc_id ." ".$tipoDocumentos->tipoDoc_descripcion);
                }

                $validationD = $event->sheet->getDelegate()->getCell("{$drop_columnD}2")->getDataValidation();
                $validationD->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationD->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationD->setAllowBlank(false);
                $validationD->setShowInputMessage(true);
                $validationD->setShowErrorMessage(true);
                $validationD->setShowDropDown(true);
                $validationD->setErrorTitle('Input Error');
                $validationD->setError('Value is not in list.');
                $validationD->setPromptTitle('Pick from list');
                $validationD->setPrompt('Please value from');
                $validationD->setFormula1('Empleado!$CA$1:$CA$3');

                //DEPARTAMENTO
                foreach($departamentos as $departamento){
                    $event->sheet->getDelegate()->setCellValue('BA'.$row++,$departamento->id." ".$departamento->name);
                }

                $validation = $event->sheet->getDelegate()->getCell("{$drop_column}2")->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please value from');
                $validation->setFormula1('Empleado!$BA$1:$BA$25');

                //PROVINCIA
                foreach($provincias as $provincia){
                    $event->sheet->getDelegate()->setCellValue('GA'.$rowP++,$provincia->id." ".$provincia->name);
                }

                $validationP = $event->sheet->getDelegate()->getCell("{$drop_columnP}2")->getDataValidation();
                $validationP->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validationP->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validationP->setAllowBlank(false);
                $validationP->setShowInputMessage(true);
                $validationP->setShowErrorMessage(true);
                $validationP->setShowDropDown(true);
                $validationP->setErrorTitle('Input Error');
                $validationP->setError('Value is not in list.');
                $validationP->setPromptTitle('Pick from list');
                $validationP->setPrompt('Please value from');
                $validationP->setFormula1('Empleado!$GA$1:$GA$25');

                for ($i = 2; $i <= $this->total; $i++) {
                    $event->sheet->getCell("{$drop_columnD}{$i}")->setDataValidation(clone $validationD);
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    $event->sheet->getCell("{$drop_columnP}{$i}")->setDataValidation(clone $validationP);
                }
            }
        ];
    }
}
