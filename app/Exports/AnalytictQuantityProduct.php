<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
class AnalytictQuantityProduct implements FromArray,WithHeadings,WithStyles,WithColumnWidths,WithStrictNullComparison
{
   
    protected $data;
    // thêm tiêu đề
    public function headings(): array
    {
        return [
            'Sản phẩm',
            'Số lượng(kg)',
        ];
    }
    // chỉnh style
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            'A' => ['alignment' => ['horizontal' => 'center']],
            'B' => ['alignment' => ['horizontal' => 'center']],

        ];
    }
    // tùy chỉnh chiều rộng cột
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,            
        ];
    }
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }
}
