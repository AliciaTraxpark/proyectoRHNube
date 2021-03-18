<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelServicioImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    public function collection(Collection $rows)
    {
    }
    public function batchSize(): int
    {
        return 2000;
    }
    public function chunkSize(): int
    {
        return 2000;
    }
}
