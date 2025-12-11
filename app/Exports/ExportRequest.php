<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ExportRequest implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $data;
    protected $statusFilter;
    protected $startDate;
    protected $endDate;
    protected $count;

    public function __construct(array $data, $statusFilter = 'all', $startDate = null, $endDate = null, $count = 0)
    {
        $this->data = $data;
        $this->statusFilter = $statusFilter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->count = $count;
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
            'REQ #',
            'STUDENT',
            'DOC',
            'SCHOOL',
            'VIA',
            'REL MODE',
            'REMARKS',
            'STATUS',
            'REQ DATE',
            'APP DATE',
            'REL DATE',
            'CLM DATE',
            'CLAIMER'
        ];
    }

    /**
     * Register events for adding export details
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 5 rows at the top for export details
                $sheet->insertNewRowBefore(1, 5);

                // Add title
                $sheet->setCellValue('A1', 'DOCUMENT REQUESTS REPORT');
                $sheet->mergeCells('A1:M1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'name' => 'Bookman Old Style'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Filter Type
                $filterText = $this->statusFilter === 'all' ? 'ALL REQUESTS' : strtoupper($this->statusFilter) . ' REQUESTS';
                $sheet->setCellValue('A2', 'FILTER TYPE:');
                $sheet->setCellValue('B2', $filterText);

                // Date Range
                if ($this->startDate && $this->endDate) {
                    $sheet->setCellValue('A3', 'DATE RANGE:');
                    $sheet->setCellValue('B3',
                        strtoupper(Carbon::parse($this->startDate)->format('M d, Y') . ' - ' . Carbon::parse($this->endDate)->format('M d, Y')));
                }

                // Total Requests
                $sheet->setCellValue('A4', 'TOTAL REQUESTS:');
                $sheet->setCellValue('B4', $this->count);

                // Generated Date
                $sheet->setCellValue('A5', 'GENERATED ON:');
                $sheet->setCellValue('B5', strtoupper(Carbon::now()->format('F d, Y h:i A')));

                // Style the export details section
                $sheet->getStyle('A2:A5')->applyFromArray([
                    'font' => ['bold' => true, 'name' => 'Bookman Old Style'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);

                $sheet->getStyle('B2:B5')->applyFromArray([
                    'font' => ['name' => 'Bookman Old Style'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);

                // Add some spacing
                $sheet->getRowDimension(1)->setRowHeight(25);
            },
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
                    'name' => 'Bookman Old Style',
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
                'font' => [
                    'name' => 'Bookman Old Style',
                ],
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
                    'name' => 'Bookman Old Style',
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
            'A' => 16,  // Req #
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
        return 'DOCUMENT REQUESTS REPORT';
    }
}
