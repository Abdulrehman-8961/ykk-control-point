<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class TestingSummaryExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $data;
    protected $headings;

    public function __construct(array $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * --------------------------------
     * BASIC STYLES
     * --------------------------------
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ],
        ];
    }

    /**
     * --------------------------------
     * ADVANCED STYLING (COLORS / BOLD)
     * --------------------------------
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Auto-size columns
                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Bold TEST column (adjust index if needed)
                $sheet->getStyle('I2:I' . $highestRow)->getFont()->setBold(true);

                // PASS / FAIL coloring (last column = ACCEPT)
                $acceptCol = $highestCol;

                for ($row = 2; $row <= $highestRow; $row++) {

                    $cell = $acceptCol . $row;
                    $value = strtoupper($sheet->getCell($cell)->getValue());

                    if ($value === 'PASS') {
                        $sheet->getStyle($cell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => Color::COLOR_DARKGREEN],
                                'bold' => true
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => 'C6EFCE'],
                            ],
                        ]);
                    }

                    if ($value === 'FAIL') {
                        $sheet->getStyle($cell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => Color::COLOR_DARKRED],
                                'bold' => true
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => 'FFC7CE'],
                            ],
                        ]);
                    }
                }

                // Highlight MEAN / MIN / MAX columns (optional)
                // Adjust letters if your column order changes
                $sheet->getStyle('O2:Q' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true],
                ]);
            },
        ];
    }
}
