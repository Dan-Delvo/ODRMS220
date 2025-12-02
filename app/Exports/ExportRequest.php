<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportRequest implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return the data array
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Req #',
            'Student',
            'Doc',
            'School',
            'Via',
            'Rel Mode',
            'Remarks',
            'Status',
            'Req Date',
            'App Date',
            'Rel Date',
            'Clm Date',
            'Claimer'
        ];
    }

    /**
     * Apply styles to the Excel file
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],

            // Style all data rows
            "A2:{$highestColumn}{$highestRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Style the Status column (column H)
            "H2:H{$highestRow}" => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Req #
            'B' => 25,  // Student
            'C' => 20,  // Doc
            'D' => 20,  // School
            'E' => 15,  // Via
            'F' => 15,  // Rel Mode
            'G' => 30,  // Remarks
            'H' => 15,  // Status
            'I' => 15,  // Req Date
            'J' => 15,  // App Date
            'K' => 15,  // Rel Date
            'L' => 15,  // Clm Date
            'M' => 25,  // Claimer
        ];
    }

    /**
     * Set the title of the worksheet
     */
    public function title(): string
    {
        return 'Document Requests Report';
    }
}
