<?php

namespace App\Exports;

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

class PlantillaExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
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
                    ]
                ];

                $departamentos=ubigeo_peru_departments::all();

                $drop_column = 'G';
                //getHighestRow();

                $row = 1;
                foreach($departamentos as $departamento){
                    $event->sheet->getDelegate()->setCellValue('Z'.$row++,$departamento->id."".$departamento->name);
                }
                $event->sheet->getDelegate()->setTitle("Departamento");

                $validation = $event->sheet->getDelegate()->getCell("{$drop_column}11")->getDataValidation();
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
                $validation->setFormula1('Departamento!$Z$1:$Z$25');

                for ($i = 2; $i <= $this->total; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }
            }
        ];
    }
}
