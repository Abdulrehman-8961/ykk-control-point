<?php

namespace App\Exports;

use DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class WorkOrderReportExport implements WithEvents
{
    protected $params;
    protected $tests;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->loadData();
    }

    protected function loadData()
    {
        if ($this->params['report_name'] == "by-wo") {
            $this->tests = DB::table('workorders as wo')
                ->join('sample_tests as st', function ($j) {
                    $j->on('wo.id', '=', 'st.workorder_id')
                        ->where('st.is_deleted', 0);
                })
                ->join('sample_tests_samples as sts', 'st.id', '=', 'sts.sample_test_id')
                ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
                ->where('wo.id', $this->params['wo_no'])
                ->orderBy('st.id')
                ->orderBy('sts.sample_number')
                ->get()
                ->groupBy('sample_test_id');
        } else if ($this->params['report_name'] == "by-itemcat") {
            // Validate date range
            $startDate = Carbon::parse($this->params['prduction_start_date'])->startOfDay();
            $endDate = Carbon::parse($this->params['prduction_end_date'])->endOfDay();
            $dbStartDate = $startDate->format('Y-m-d');
            $dbEndDate = $endDate->format('Y-m-d');

            // Check if any records exist for by-itemcat report
            $this->tests = DB::table('sample_tests as st')
                ->where('st.item_category', $this->params['item_category'])
                ->whereBetween('st.production_date', [$dbStartDate, $dbEndDate])
                ->join('sample_tests_samples as sts', 'st.id', '=', 'sts.sample_test_id')
                ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
                ->where('st.is_deleted', 0)
                ->orderBy('st.id')
                ->orderBy('sts.sample_number')
                ->get()
                ->groupBy('sample_test_id');
        } else if ($this->params['report_name'] == "by-test") {
            // Validate date range
            $startDate = Carbon::parse($this->params['prduction_start_date'])->startOfDay();
            $endDate = Carbon::parse($this->params['prduction_end_date'])->endOfDay();
            $dbStartDate = $startDate->format('Y-m-d');
            $dbEndDate = $endDate->format('Y-m-d');

            // Check if any records exist for by-itemcat report
            $this->tests = DB::table('sample_tests as st')
                ->join('sample_tests_samples as sts', 'st.id', '=', 'sts.sample_test_id')
                ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
                ->where('st.is_deleted', 0)
                ->where('td.id', $this->params['test_name'])
                ->whereBetween('st.production_date', [$dbStartDate, $dbEndDate])
                ->orderBy('st.id')
                ->orderBy('sts.sample_number')
                ->get()
                ->groupBy('sample_test_id');
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /** -----------------------------------
                 * PAGE SETUP
                 * ----------------------------------- */
                $sheet->getDefaultColumnDimension()->setWidth(12);
                $sheet->getDefaultRowDimension()->setRowHeight(18);

                /** -----------------------------------
                 * HEADER BLOCK
                 * ----------------------------------- */
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'Measurement data input sheet for upload');

                // LINE 4
                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('C4:D4');
                $sheet->setCellValue('A4', 'MFG Dept.');
                $sheet->setCellValue('C4', 'YKK CANADA INC.');

                $sheet->mergeCells('G4:H4');
                $sheet->mergeCells('I4:J4');
                $sheet->setCellValue('G4', 'Total years');
                $sheet->setCellValue('I4', $this->params['total_years']);

                // LINE 5
                $sheet->mergeCells('A5:B5');
                $sheet->mergeCells('C5:D5');
                $sheet->setCellValue('A5', 'Item information');

                $sheet->mergeCells('G5:H5');
                $sheet->mergeCells('I5:J5');
                $sheet->setCellValue('G5', 'Closed-end zippers specification');
                $sheet->setCellValue('I5', $this->params['closed_end_zippers']);

                // LINE 6
                $sheet->mergeCells('A6:B6');
                $sheet->mergeCells('C6:D6');
                $sheet->setCellValue('A6', 'Open-end separators specification');
                $sheet->setCellValue('C6', $this->params['open_end_separators']);

                $sheet->mergeCells('G6:H6');
                $sheet->mergeCells('I6:J6');
                $sheet->setCellValue('G6', 'Closed-end/Open-end : Special specification');
                $sheet->setCellValue('I6', $this->params['closed_end_open_end']);

                // LINE 7
                $sheet->mergeCells('A7:B7');
                $sheet->mergeCells('C7:D7');
                $sheet->setCellValue('A7', 'Chain : special specification');
                $sheet->setCellValue('C7', $this->params['chain_special']);

                $sheet->mergeCells('G7:H7');
                $sheet->mergeCells('I7:J7');
                $sheet->setCellValue('G7', 'Comment on Plan');
                $sheet->setCellValue('I7', $this->params['comment_on_plan']);

                // LINE 8
                $sheet->mergeCells('A8:B8');
                $sheet->mergeCells('C8:D8');
                $sheet->setCellValue('A8', 'Monthly Report No.');
                $sheet->setCellValue('C8', $this->params['monthly_report_no']);

                $sheet->mergeCells('G8:H8');
                $sheet->mergeCells('I8:J8');
                $sheet->setCellValue('G8', 'Sample No.');
                $sheet->setCellValue(
                    'I8',
                    str_pad($this->params['sample_no'], 3, '0', STR_PAD_LEFT)
                );

                if ($this->params['report_name'] == "by-itemcat") {
                    // LINE 9
                    $sheet->mergeCells('A9:B9');
                    $sheet->mergeCells('C9:D9');
                    $sheet->setCellValue('A9', 'Item Category');
                    $sheet->setCellValue('C9', $this->params['item_category']);
                }
                if ($this->params['report_name'] == "by-test") {
                    // LINE 9
                    $sheet->mergeCells('A9:B9');
                    $sheet->mergeCells('C9:D9');
                    $sheet->setCellValue('A9', 'Test Name');
                    $sheet->setCellValue('C9', $this->params['test_name_text']);
                }
                if ($this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test") {
                    $startDate = Carbon::parse($this->params['prduction_start_date'])->startOfDay();
                    $endDate = Carbon::parse($this->params['prduction_end_date'])->endOfDay();
                    $formatedStartDate = $startDate->format('d-M-Y');
                    $formatedEndDate = $endDate->format('d-M-Y');
                    // LINE 10
                    $sheet->mergeCells('A10:B10');
                    $sheet->mergeCells('C10:D10');
                    $sheet->setCellValue('A10', 'Production Date Range');
                    $sheet->setCellValue('C10', $formatedStartDate . ' to ' . $formatedEndDate);
                }

                /** -----------------------------------
                 * TEST TABLE SETUP
                 * ----------------------------------- */
                $baseCol = 2; // Column B
                $headerRow =  $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 13 : 11;
                $testRow = $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 14 : 12;
                $yfsRow = $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 15 : 13;
                $upperLimitRow = $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 16 : 14;
                $lowerLimitRow = $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 17 : 15;
                $unitRow = $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 18 : 16;
                $valueStartRow = $this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test" ? 19 : 17;

                /** LEFT LABEL COLUMN */
                $sheet->setCellValue("A{$headerRow}", 'No.');
                $sheet->setCellValue("A{$testRow}", $this->params['report_name'] == "by-test" ? 'Item Category' : 'Test Item');
                $sheet->setCellValue("A{$yfsRow}", 'YFS value');
                $sheet->setCellValue("A{$upperLimitRow}", 'Upper limit');
                $sheet->setCellValue("A{$lowerLimitRow}", 'Lower limit');
                $sheet->setCellValue("A{$unitRow}", 'Unit');

                /** -----------------------------------
                 * TEST NUMBER HEADERS (MERGED)
                 * ----------------------------------- */
                $col = $baseCol;
                $testNo = 1;

                foreach ($this->tests as $test) {
                    $sheet->mergeCellsByColumnAndRow($col, $headerRow, $col + 1, $headerRow);
                    $sheet->setCellValueByColumnAndRow($col, $headerRow, $testNo++);
                    $sheet->getStyleByColumnAndRow($col, $headerRow, $col + 1, $headerRow)
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $col += 2;
                }

                /** -----------------------------------
                 * TEST METADATA ROWS
                 * ----------------------------------- */
                $col = $baseCol;

                foreach ($this->tests as $test) {
                    $first = $test->first();

                    $sheet->mergeCellsByColumnAndRow($col, $testRow, $col + 1, $testRow);
                    $sheet->setCellValueByColumnAndRow($col, $testRow, $this->params['report_name'] == "by-test" ? $first->item_category : $first->test_name);

                    $sheet->mergeCellsByColumnAndRow($col, $yfsRow, $col + 1, $yfsRow);
                    $sheet->setCellValueByColumnAndRow($col, $yfsRow, $first->YFS ?? '');

                    $sheet->mergeCellsByColumnAndRow($col, $upperLimitRow, $col + 1, $upperLimitRow);
                    $sheet->setCellValueByColumnAndRow($col, $upperLimitRow, $first->max ?? '');

                    $sheet->mergeCellsByColumnAndRow($col, $lowerLimitRow, $col + 1, $lowerLimitRow);
                    $sheet->setCellValueByColumnAndRow($col, $lowerLimitRow, $first->min ?? '');

                    $sheet->setCellValueByColumnAndRow($col, $unitRow, $first->uom);

                    $sheet->getStyleByColumnAndRow($col, $testRow, $col + 1, $unitRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    $col += 2;
                }

                /** -----------------------------------
                 * SAMPLE VALUES
                 * ----------------------------------- */
                $maxSamples = $this->tests->flatten()->max('sample_number');

                for ($i = 1; $i <= $maxSamples; $i++) {
                    $sheet->setCellValue("A" . ($valueStartRow + $i - 1), $i);
                }

                $col = $baseCol;

                foreach ($this->tests as $test) {
                    foreach ($test as $s) {
                        $sheet->setCellValueByColumnAndRow(
                            $col,
                            $valueStartRow + $s->sample_number - 1,
                            $s->sample_value
                        );
                    }
                    $col += 2;
                }

                /** -----------------------------------
                 * SUMMARY ROWS
                 * ----------------------------------- */
                $summaryRow = $valueStartRow + $maxSamples + 1;

                $sheet->setCellValue("A{$summaryRow}", 'AVE');
                $sheet->setCellValue("A" . ($summaryRow + 1), 'MAX');
                $sheet->setCellValue("A" . ($summaryRow + 2), 'MIN');
                $sheet->setCellValue("A" . ($summaryRow + 3), 'Sigmas');
                $sheet->setCellValue("A" . ($summaryRow + 4), 'Remarks');

                $col = $baseCol;

                foreach ($this->tests as $test) {
                    $first = $test->first();

                    $sheet->setCellValueByColumnAndRow($col, $summaryRow,     $first->avg);
                    $sheet->setCellValueByColumnAndRow($col, $summaryRow + 1, $first->max);
                    $sheet->setCellValueByColumnAndRow($col, $summaryRow + 2, $first->min);
                    $sheet->setCellValueByColumnAndRow($col, $summaryRow + 3, $first->stdva_value);

                    $col += 2;
                }

                /** -----------------------------------
                 * COLUMN WIDTHS
                 * ----------------------------------- */
                $col = $baseCol;
                foreach ($this->tests as $test) {
                    $sheet->getColumnDimensionByColumn($col)->setWidth(10);
                    $sheet->getColumnDimensionByColumn($col + 1)->setWidth(7);
                    $col += 2;
                }

                /** -----------------------------------
                 * BORDERS
                 * ----------------------------------- */
                
                $testCount = count($this->tests);
                $endColumnIndex = 1 + ($testCount * 2);
                function numberToExcelColumn($num) {
                    $column = '';
                    while ($num > 0) {
                        $modulo = ($num - 1) % 26;
                        $column = chr(65 + $modulo) . $column;
                        $num = (int)(($num - $modulo) / 26);
                    }
                    return $column;
                }

                $secondHeaderEndColumn = numberToExcelColumn($endColumnIndex);
                $n = $yfsRow + $maxSamples + 9;
                $sheet->getStyle(
                    "A{$headerRow}:{$secondHeaderEndColumn}{$n}"
                )->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);
                $labelRanges = [
                    'A2:F2',

                    'A4:B4',
                    'G4:H4',
                    'A5:B5',
                    'G5:H5',
                    'A6:B6',
                    'G6:H6',
                    'A7:B7',
                    'G7:H7',
                    'A8:B8',
                    'G8:H8',
                    "A{$headerRow}:{$secondHeaderEndColumn}{$headerRow}",
                    "A{$testRow}:{$secondHeaderEndColumn}{$testRow}",
                    "A{$yfsRow}:A{$n}"
                ];
                if ($this->params['report_name'] == "by-itemcat" || $this->params['report_name'] == "by-test") {
                        array_push($labelRanges, 'A9:B9', 'A10:B10');
                    }

                foreach ($labelRanges as $range) {
                    $sheet->getStyle($range)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '007AF2'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ]
                        ]
                    ]);
                }
                $sheet->getStyle('A11:A32')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        ];
    }
}
