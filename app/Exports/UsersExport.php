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

class UsersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
   
    

    public function headings(): array
    {
        return[
            'Date','Task','Time'
        ];
    }

    public function collection()
    {
        return User::all();

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

                $opcionesDepartamento = "";

                $drop_column = 'B';
                //getHighestRow();

                $row = 1;
                foreach($departamentos as $departamento){
                    $event->sheet->getDelegate()->setCellValue('F'.$row,$departamento->name);
                }
                $event->sheet->getDelegate()->getStyle('A1');
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
                $validation->setFormula1('Departamento!$F$1:$F$15');

                for ($i = 11; $i <= 15; $i++) {
                    $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                }
            }
        ];
    }
}
