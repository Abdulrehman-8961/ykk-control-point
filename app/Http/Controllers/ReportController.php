<?php

namespace App\Http\Controllers;

use App\Exports\TestingSummaryExport;
use App\Exports\WorkOrderReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function testingSummary(Request $request)
    {
        $wo_no = $request->wo_no;
        $daterange = $request->daterange; // 2026-01-05 to 2026-01-30
        $startDate = '';
        $endDate = '';
        if ($daterange) {
            $dateParts = explode(' to ', $daterange);

            if (count($dateParts) == 2) {
                $startDate = trim($dateParts[0]);
                $endDate   = trim($dateParts[1]);
            }
        }



        $rowsQuery = DB::table('workorders as wo')
            ->join('sample_tests as st', function ($join) {
                $join->on('wo.id', '=', 'st.workorder_id')
                    ->where('st.is_deleted', 0);
            })
            ->join('sample_tests_samples as sts', 'st.id', '=', 'sts.sample_test_id')
            ->join('assets as a', 'st.asset_id', '=', 'a.id')
            ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
            ->leftJoin('test_thresholds as tt', 'tt.id', '=', 'st.test_name_id')
            ->where('wo.workorder_no', $wo_no)
            ->where('wo.is_deleted', 0);

        // ✅ only apply if daterange provided and both dates found
        if ($startDate && $endDate) {
            $rowsQuery->whereBetween('st.production_date', [$startDate, $endDate]);
        }

        $rows = $rowsQuery
            ->orderBy('sts.sample_number')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No record found for the selected Work Order.'
            ], 404);
        }

        /** ----------------------------------
         *  DYNAMIC P HEADERS
         * ---------------------------------- */
        $maxSamples = $rows->max('sample_number');

        $pHeaders = [];
        for ($i = 1; $i <= $maxSamples; $i++) {
            $pHeaders[] = "P{$i}";
        }

        /** ----------------------------------
         *  FINAL HEADERS
         * ---------------------------------- */
        $headings = array_merge([
            'ORDER',
            'ITEM',
            'TAPE',
            'COLOR',
            'LOT',
            'MACHINE',
            'DATE LOT',
            'DATE TEST',
            'TEST',
            'BOSUBI',
            'STANDARD YFS'
        ], $pHeaders, [
            'MEAN',
            'MIN',
            'MAX',
            'STD DEV',
            'DIFFERENCE',
            'ACCEPT'
        ]);

        /** ----------------------------------
         *  GROUP ROWS BY TEST
         * ---------------------------------- */
        $grouped = $rows->groupBy('sample_test_id');

        $excelData = [];

        foreach ($grouped as $testId => $samples) {

            $first = $samples->first();

            // Initialize empty P values
            $pValues = array_fill(1, $maxSamples, '');

            // Fill P values from samples table
            foreach ($samples as $s) {
                $pValues[(int)$s->sample_number] = $s->sample_value;
            }

            // Ensure correct order (P1 → Pn)
            $pValues = array_values($pValues);

            $excelData[] = array_merge([
                $first->workorder_no,      // ORDER
                $first->item_category,     // ITEM
                'HR',     // TAPE
                $first->color,             // COLOR
                $first->lot,               // LOT
                $first->asset_no,        // MACHINE
                $first->production_date,   // DATE LOT
                $first->sample_date,       // DATE TEST
                $first->test_name,         // TEST
                $first->bosubi,            // BOSUBI
                $first->test_standard      // STANDARD YFS
            ], $pValues, [
                $first->avg,               // MEAN
                $first->min,               // MIN
                $first->max,               // MAX
                $first->stdva_value,       // STD DEV
                $first->avg_plus,          // DIFFERENCE
                strtoupper($first->avg_result) // ACCEPT
            ]);
        }

        $fileName = 'Testing_Summary_' . $wo_no . '.xlsx';

        return Excel::download(
            new TestingSummaryExport($excelData, $headings),
            $fileName
        );
    }
    public function testingSummaryItemCategory(Request $request)
    {
        $item_category = $request->item_category;
        $daterange = $request->daterange; // 2026-01-05 to 2026-01-30
        $startDate = '';
        $endDate = '';
        if ($daterange) {
            $dateParts = explode(' to ', $daterange);

            if (count($dateParts) == 2) {
                $startDate = trim($dateParts[0]);
                $endDate   = trim($dateParts[1]);
            }
        }



        $rowsQuery = DB::table('workorders as wo')
            ->join('sample_tests as st', function ($join) {
                $join->on('wo.id', '=', 'st.workorder_id')
                    ->where('st.is_deleted', 0);
            })
            ->join('sample_tests_samples as sts', 'st.id', '=', 'sts.sample_test_id')
            ->join('assets as a', 'st.asset_id', '=', 'a.id')
            ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
            ->leftJoin('test_thresholds as tt', 'tt.id', '=', 'st.test_name_id')
            ->where('st.item_category', $item_category)
            ->where('wo.is_deleted', 0);

        // ✅ only apply if daterange provided and both dates found
        if ($startDate && $endDate) {
            $rowsQuery->whereBetween('st.production_date', [$startDate, $endDate]);
        }

        $rows = $rowsQuery
            ->orderBy('sts.sample_number')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No record found for the selected Work Order.'
            ], 404);
        }

        /** ----------------------------------
         *  DYNAMIC P HEADERS
         * ---------------------------------- */
        $maxSamples = $rows->max('sample_number');

        $pHeaders = [];
        for ($i = 1; $i <= $maxSamples; $i++) {
            $pHeaders[] = "P{$i}";
        }

        /** ----------------------------------
         *  FINAL HEADERS
         * ---------------------------------- */
        $headings = array_merge([
            'ORDER',
            'ITEM',
            'TAPE',
            'COLOR',
            'LOT',
            'MACHINE',
            'DATE LOT',
            'DATE TEST',
            'TEST',
            'BOSUBI',
            'STANDARD YFS'
        ], $pHeaders, [
            'MEAN',
            'MIN',
            'MAX',
            'STD DEV',
            'DIFFERENCE',
            'ACCEPT'
        ]);

        /** ----------------------------------
         *  GROUP ROWS BY TEST
         * ---------------------------------- */
        $grouped = $rows->groupBy('sample_test_id');

        $excelData = [];

        foreach ($grouped as $testId => $samples) {

            $first = $samples->first();

            // Initialize empty P values
            $pValues = array_fill(1, $maxSamples, '');

            // Fill P values from samples table
            foreach ($samples as $s) {
                $pValues[(int)$s->sample_number] = $s->sample_value;
            }

            // Ensure correct order (P1 → Pn)
            $pValues = array_values($pValues);

            $excelData[] = array_merge([
                $first->workorder_no,      // ORDER
                $first->item_category,     // ITEM
                'HR',     // TAPE
                $first->color,             // COLOR
                $first->lot,               // LOT
                $first->asset_no,        // MACHINE
                $first->production_date,   // DATE LOT
                $first->sample_date,       // DATE TEST
                $first->test_name,         // TEST
                $first->bosubi,            // BOSUBI
                $first->test_standard      // STANDARD YFS
            ], $pValues, [
                $first->avg,               // MEAN
                $first->min,               // MIN
                $first->max,               // MAX
                $first->stdva_value,       // STD DEV
                $first->avg_plus,          // DIFFERENCE
                strtoupper($first->avg_result) // ACCEPT
            ]);
        }

        $fileName = 'Testing_Summary_' . $item_category . '.xlsx';

        return Excel::download(
            new TestingSummaryExport($excelData, $headings),
            $fileName
        );
    }

    public function workOrder_old(Request $request)
    {
        $request->validate([
            'wo_no'        => 'required',
            'total_years'  => 'required',
            'sample_no'    => 'required',
        ]);

        // Check if any records exist before exporting
        $recordExists = DB::table('workorders as wo')
            ->join('sample_tests as st', 'wo.id', '=', 'st.workorder_id')
            ->where('wo.id', $request->wo_no)
            ->where('st.is_deleted', 0)
            ->exists();

        if (!$recordExists) {
            // Return JSON error if the request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No record found for the selected Work Order.'
                ], 404);
            }
        }

        return Excel::download(
            new WorkOrderReportExport($request->all()),
            'YKK_Workorder_Report_' . $request->wo_no . '.xlsx'
        );
    }



    public function checkExistance(Request $request)
    {
        // Get report type
        $report_name = $request->input('report_name');

        // Common validations for all report types
        $request->validate([
            'total_years'  => 'required',
            'sample_no'    => 'required',
            'report_name'  => 'required|in:by-wo,by-itemcat,by-test',
        ]);

        // Report-specific validations
        if ($report_name == 'by-wo') {
            $request->validate([
                'wo_no' => 'required',
            ]);

            // Check if any records exist for by-wo report
            $recordExists = DB::table('workorders as wo')
                ->join('sample_tests as st', 'wo.id', '=', 'st.workorder_id')
                ->where('wo.id', $request->wo_no)
                ->where('st.is_deleted', 0)
                ->exists();

            if (!$recordExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No record found for the selected Work Order.'
                    ], 404);
                }
            }
        } elseif ($report_name == 'by-itemcat') {
            $request->validate([
                'item_category' => 'required',
                'prduction_start_date' => 'required|date',
                'prduction_end_date' => 'required|date|after_or_equal:prduction_start_date',
            ]);

            // Validate date range
            $startDate = Carbon::parse($request->prduction_start_date)->startOfDay();
            $endDate = Carbon::parse($request->prduction_end_date)->endOfDay();

            if ($startDate->gt($endDate)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Production Start Date cannot be greater than Production End Date.'
                    ], 422);
                }
            }
            $dbStartDate = $startDate->format('Y-m-d');
            $dbEndDate = $endDate->format('Y-m-d');

            // Check if any records exist for by-itemcat report
            $recordExists = DB::table('sample_tests')
                ->where('item_category', $request->item_category)
                ->whereBetween('production_date', [$dbStartDate, $dbEndDate])
                ->where('is_deleted', 0)
                ->exists();

            if (!$recordExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No record found for the selected Item Category.'
                    ], 404);
                }
            }
        } elseif ($report_name == 'by-test') {
            $request->validate([
                'test_name' => 'required',
                'prduction_start_date' => 'required|date',
                'prduction_end_date' => 'required|date|after_or_equal:prduction_start_date',
            ]);

            // Validate date range
            $startDate = Carbon::parse($request->prduction_start_date)->startOfDay();
            $endDate = Carbon::parse($request->prduction_end_date)->endOfDay();

            if ($startDate->gt($endDate)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Production Start Date cannot be greater than Production End Date.'
                    ], 422);
                }
            }
            $dbStartDate = $startDate->format('Y-m-d');
            $dbEndDate = $endDate->format('Y-m-d');

            // Check if any records exist for by-test report
            $recordExists = DB::table('sample_tests as st')
                ->join('test_definitions as td', 'st.test_name_id', '=', 'td.id')
                ->where('td.id', $request->test_name)
                ->where('st.is_deleted', 0)
                ->whereBetween('st.production_date', [$dbStartDate, $dbEndDate])
                ->exists();

            if (!$recordExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No record found for the selected Test Name within the specified date range.'
                    ], 404);
                }
            }
        }

        return response()->json([
            'status' => true
        ]);
    }
    public function workOrder(Request $request)
    {
        // Get report type
        $report_name = $request->input('report_name');

        // Common validations for all report types
        $request->validate([
            'total_years'  => 'required',
            'sample_no'    => 'required',
            'report_name'  => 'required|in:by-wo,by-itemcat,by-test',
        ]);

        // Report-specific validations
        if ($report_name == 'by-wo') {
            $request->validate([
                'wo_no' => 'required',
            ]);

            // Check if any records exist for by-wo report
            $recordExists = DB::table('workorders as wo')
                ->join('sample_tests as st', 'wo.id', '=', 'st.workorder_id')
                ->where('wo.id', $request->wo_no)
                ->where('st.is_deleted', 0)
                ->exists();

            if (!$recordExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No record found for the selected Work Order.'
                    ], 404);
                }
            }
        } elseif ($report_name == 'by-itemcat') {
            $request->validate([
                'item_category' => 'required',
                'prduction_start_date' => 'required|date',
                'prduction_end_date' => 'required|date|after_or_equal:prduction_start_date',
            ]);

            // Validate date range
            $startDate = Carbon::parse($request->prduction_start_date)->startOfDay();
            $endDate = Carbon::parse($request->prduction_end_date)->endOfDay();

            if ($startDate->gt($endDate)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Production Start Date cannot be greater than Production End Date.'
                    ], 422);
                }
            }
            $dbStartDate = $startDate->format('Y-m-d');
            $dbEndDate = $endDate->format('Y-m-d');

            // Check if any records exist for by-itemcat report
            $recordExists = DB::table('sample_tests')
                ->where('item_category', $request->item_category)
                ->whereBetween('production_date', [$dbStartDate, $dbEndDate])
                ->where('is_deleted', 0)
                ->exists();

            if (!$recordExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No record found for the selected Item Category.'
                    ], 404);
                }
            }
        } elseif ($report_name == 'by-test') {
            $request->validate([
                'test_name' => 'required',
                'prduction_start_date' => 'required|date',
                'prduction_end_date' => 'required|date|after_or_equal:prduction_start_date',
            ]);

            // Validate date range
            $startDate = Carbon::parse($request->prduction_start_date)->startOfDay();
            $endDate = Carbon::parse($request->prduction_end_date)->endOfDay();

            if ($startDate->gt($endDate)) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Production Start Date cannot be greater than Production End Date.'
                    ], 422);
                }
            }
            $dbStartDate = $startDate->format('Y-m-d');
            $dbEndDate = $endDate->format('Y-m-d');

            // Check if any records exist for by-test report
            $recordExists = DB::table('sample_tests as st')
                ->join('test_definitions as td', 'st.test_name_id', '=', 'td.id')
                ->where('td.id', $request->test_name)
                ->where('st.is_deleted', 0)
                ->whereBetween('st.production_date', [$dbStartDate, $dbEndDate])
                ->exists();

            if (!$recordExists) {
                if ($request->ajax()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No record found for the selected Test Name within the specified date range.'
                    ], 404);
                }
            }
        }

        // Generate filename based on report type
        $filename = 'YKK_';
        if ($report_name == 'by-wo') {
            $filename .= 'Workorder_Report_' . $request->wo_no . '.xlsx';
        } elseif ($report_name == 'by-itemcat') {
            $filename .= 'ItemCat_' . $request->item_category_name . '.xlsx';
        } elseif ($report_name == 'by-test') {
            $filename .= 'Test_Name_' . $request->test_name_text . '_' .
                Carbon::parse($request->prduction_start_date)->format('Ymd') . '_to_' .
                Carbon::parse($request->prduction_end_date)->format('Ymd') . '.xlsx';
        } else {
            $filename .= 'Report_' . date('Ymd_His') . '.xlsx';
        }

        return Excel::download(
            new WorkOrderReportExport($request->all()),
            $filename
        );
    }
}
