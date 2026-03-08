<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
use VerumConsilium\Browsershot\Facades\PDF as NEWPDF;


use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

use Validator;

class AdminController extends Controller
{
    //
    public function __construct() {}

    public function index()
    {

        return view('dashboard');
    }


    public function byWorkorder(Request $request)
    {
        $from = Carbon::now()->subMonths(12)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $perTest = DB::table('sample_tests as st')
            ->join('workorders as wo', function ($j) {
                $j->on('wo.id', '=', 'st.workorder_id')
                    ->where('wo.is_deleted', 0);
            })
            ->leftJoin('sample_tests_samples as sts', function ($j) {
                $j->on('sts.sample_test_id', '=', 'st.id')
                    ->where('sts.is_deleted', 0);
            })
            ->whereBetween('st.sample_date', [$from, $to])
            ->where('st.is_deleted', 0)
            ->groupBy('st.id', 'st.workorder_id', 'wo.workorder_no')
            ->selectRaw('
            st.workorder_id,
            wo.workorder_no,
            st.id as sample_test_id,
            COUNT(sts.id) as total_samples,
            SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) as fail_count,
            CASE 
                WHEN COUNT(sts.id) = 0 THEN 0
                ELSE SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) / COUNT(sts.id)
            END as failure_rate
        ');

        $query = DB::query()
            ->fromSub($perTest, 't')
            ->groupBy('t.workorder_id', 't.workorder_no')
            ->selectRaw('
            t.workorder_id,
            t.workorder_no,
            ROUND(AVG(t.failure_rate), 4) as avg_failure_rate
        ');

        if ($search !== '') {
            $query->where('t.workorder_no', 'like', "%{$search}%");
        }



        if ($request->get('export') === 'csv') {
            $filename = 'top-failures-workorders-' . now()->format('Ymd_His') . '.csv';
            $rows = $query->orderByDesc('avg_failure_rate')->get();

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Workorder No', 'Avg Failure Rate']);

                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->workorder_no ?? '',
                        number_format((float) $row->avg_failure_rate, 4, '.', '')  * 100 . '%',
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $data = $query->orderByDesc('avg_failure_rate')
            ->paginate($perPage);

        return response()->json($data);
    }

    public function byitemcategory(Request $request)
    {
        $from = Carbon::now()->subMonths(12)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $perTest = DB::table('sample_tests as st')
            ->leftJoin('sample_tests_samples as sts', function ($j) {
                $j->on('sts.sample_test_id', '=', 'st.id')
                    ->where('sts.is_deleted', 0);
            })
            ->whereBetween('st.sample_date', [$from, $to])
            ->where('st.is_deleted', 0)
            ->groupBy('st.id', 'st.item_category')
            ->selectRaw('
            st.item_category,
            st.id as sample_test_id,
            COUNT(sts.id) as total_samples,
            SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) as fail_count,
            CASE 
                WHEN COUNT(sts.id) = 0 THEN 0
                ELSE SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) / COUNT(sts.id)
            END as failure_rate
        ');

        $query = DB::query()
            ->fromSub($perTest, 't')
            ->groupBy('t.item_category')
            ->selectRaw('
            t.item_category,
            ROUND(AVG(t.failure_rate), 4) as avg_failure_rate
        ');

        if ($search !== '') {
            $query->where('t.item_category', 'like', "%{$search}%");
        }

        if ($request->get('export') === 'csv') {
            $filename = 'top-failures-item-categories-' . now()->format('Ymd_His') . '.csv';
            $rows = $query->orderByDesc('avg_failure_rate')->get();

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Item Category', 'Avg Failure Rate']);

                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->item_category ?? '',
                        number_format((float) $row->avg_failure_rate, 4, '.', '')  * 100 . '%',
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $data = $query->orderByDesc('avg_failure_rate')
            ->paginate($perPage);

        return response()->json($data);
    }


    public function byassets(Request $request)
    {
        $from = Carbon::now()->subMonths(12)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $perTest = DB::table('sample_tests as st')
            ->join('assets as wo', function ($j) {
                $j->on('wo.id', '=', 'st.asset_id')
                    ->where('wo.is_deleted', 0);
            })
            ->leftJoin('sample_tests_samples as sts', function ($j) {
                $j->on('sts.sample_test_id', '=', 'st.id')
                    ->where('sts.is_deleted', 0);
            })
            ->whereBetween('st.sample_date', [$from, $to])
            ->where('st.is_deleted', 0)
            ->groupBy('st.id', 'st.asset_id', 'wo.asset_no')
            ->selectRaw('
            st.asset_id,
            wo.asset_no,
            st.id as sample_test_id,
            COUNT(sts.id) as total_samples,
            SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) as fail_count,
            CASE 
                WHEN COUNT(sts.id) = 0 THEN 0
                ELSE SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) / COUNT(sts.id)
            END as failure_rate
        ');

        $query = DB::query()
            ->fromSub($perTest, 't')
            ->groupBy('t.asset_id', 't.asset_no')
            ->selectRaw('
            t.asset_id,
            t.asset_no,
            ROUND(AVG(t.failure_rate), 4) as avg_failure_rate
        ');

        if ($search !== '') {
            $query->where('t.asset_no', 'like', "%{$search}%");
        }

        if ($request->get('export') === 'csv') {
            $filename = 'top-failures-assets-' . now()->format('Ymd_His') . '.csv';
            $rows = $query->orderByDesc('avg_failure_rate')->get();

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Asset No', 'Avg Failure Rate']);

                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->asset_no ?? '',
                        number_format((float) $row->avg_failure_rate, 4, '.', '')  * 100 . '%',
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $data = $query->orderByDesc('avg_failure_rate')
            ->paginate($perPage);

        return response()->json($data);
    }

    public function bytests(Request $request)
    {
        $from = Carbon::now()->subMonths(12)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $search  = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $perTest = DB::table('sample_tests as st')
            ->join('test_definitions as wo', function ($j) {
                $j->on('wo.id', '=', 'st.test_name_id')
                    ->where('wo.is_deleted', 0);
            })
            ->leftJoin('sample_tests_samples as sts', function ($j) {
                $j->on('sts.sample_test_id', '=', 'st.id')
                    ->where('sts.is_deleted', 0);
            })
            ->whereBetween('st.sample_date', [$from, $to])
            ->where('st.is_deleted', 0)
            ->groupBy('st.id', 'st.test_name_id', 'wo.test_name')
            ->selectRaw('
            st.test_name_id,
            wo.test_name,
            st.id as sample_test_id,
            COUNT(sts.id) as total_samples,
            SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) as fail_count,
            CASE 
                WHEN COUNT(sts.id) = 0 THEN 0
                ELSE SUM(CASE WHEN LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%" THEN 1 ELSE 0 END) / COUNT(sts.id)
            END as failure_rate
        ');

        $query = DB::query()
            ->fromSub($perTest, 't')
            ->groupBy('t.test_name_id', 't.test_name')
            ->selectRaw('
            t.test_name_id,
            t.test_name,
            ROUND(AVG(t.failure_rate), 4) as avg_failure_rate
        ');

        if ($search !== '') {
            $query->where('t.test_name', 'like', "%{$search}%");
        }

        if ($request->get('export') === 'csv') {
            $filename = 'top-failures-test-' . now()->format('Ymd_His') . '.csv';
            $rows = $query->orderByDesc('avg_failure_rate')->get();

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Test Name', 'Avg Failure Rate']);

                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->test_name ?? '',
                        number_format((float) $row->avg_failure_rate, 4, '.', '') * 100 . '%',
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $data = $query->orderByDesc('avg_failure_rate')
            ->paginate($perPage);

        return response()->json($data);
    }

    public function historical_sample_tests(Request $request)
    {
        $defaultFrom = Carbon::now()->subMonths(12)->startOfDay();
        $defaultTo   = Carbon::now()->endOfDay();
        $prodFrom = $defaultFrom->copy();
        $prodTo = $defaultTo->copy();
        $testFrom = $defaultFrom->copy();
        $testTo = $defaultTo->copy();

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $testName = trim((string) $request->get('test_name', ''));
        $assetNo = trim((string) $request->get('asset_no', ''));
        $itemCategory = trim((string) $request->get('item_category', ''));
        $itemCode = trim((string) $request->get('item_code', ''));
        $workorderNo = trim((string) $request->get('workorder_no', ''));
        $prodDateRange = trim((string) $request->get('prod_date_range', ''));
        $testDateRange = trim((string) $request->get('test_date_range', ''));
        $dateRange = trim((string) $request->get('date_range', '')); // backward compatibility

        // Keep support for old "date_range" and prioritize explicit "prod_date_range"
        if ($prodDateRange === '' && $dateRange !== '') {
            $prodDateRange = $dateRange;
        }

        if ($prodDateRange !== '' && str_contains($prodDateRange, ' to ')) {
            [$fromInput, $toInput] = explode(' to ', $prodDateRange, 2);
            try {
                $prodFrom = Carbon::parse(trim($fromInput))->startOfDay();
                $prodTo = Carbon::parse(trim($toInput))->endOfDay();
            } catch (\Exception $e) {
                // Keep default range if parsing fails
            }
        }

        if ($testDateRange !== '' && str_contains($testDateRange, ' to ')) {
            [$fromInput, $toInput] = explode(' to ', $testDateRange, 2);
            try {
                $testFrom = Carbon::parse(trim($fromInput))->startOfDay();
                $testTo = Carbon::parse(trim($toInput))->endOfDay();
            } catch (\Exception $e) {
                // Keep default range if parsing fails
            }
        }


        $query = DB::table('sample_tests as st')
            ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
            ->leftJoin('workorders as w', 'w.id', '=', 'st.workorder_id')
            ->leftJoin('itemcodes as i', 'i.item_code', '=', 'w.itemcode_id')
            ->leftJoin('assets as a', 'a.id', '=', 'st.asset_id')
            ->leftJoin('users as u', 'u.id', '=', 'st.created_by')
            ->where('st.is_deleted', 0)
            ->whereBetween('st.production_date', [$prodFrom, $prodTo])
            ->whereBetween('st.sample_date', [$testFrom, $testTo])
            ->selectRaw(
                '   st.id,
                    st.sample_date as test_date,
                    st.production_date,
                    w.workorder_no,
                    td.test_name,
                    st.item_category,
                    COALESCE(i.item_code, w.itemcode_id) as item_code,
                    a.asset_no,
                    st.stdva_value,
                    st.min,
                    st.max,
                    st.sample_number,
                    st.avg
                '
            )->orderByDesc('st.id');

        if ($testName !== '') {
            $query->where('td.test_name', 'like', "%{$testName}%");
        }
        if ($assetNo !== '') {
            $query->where('a.asset_no', 'like', "%{$assetNo}%");
        }
        if ($itemCategory !== '') {
            $query->where('st.item_category', 'like', "%{$itemCategory}%");
        }
        if ($itemCode !== '') {
            $query->where(function ($q) use ($itemCode) {
                $q->where('i.item_code', 'like', "%{$itemCode}%")
                    ->orWhere('w.itemcode_id', 'like', "%{$itemCode}%");
            });
        }
        if ($workorderNo !== '') {
            $query->where('w.workorder_no', 'like', "%{$workorderNo}%");
        }


        if ($request->get('export') === 'csv') {
            $filename = 'historical-sample-tests-' . now()->format('Ymd_His') . '.csv';
            $rows = $query->orderByDesc('st.sample_date')->get();

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Test Date', 'WO#', 'Test Name', 'Asset#', 'Prod Date', 'Item Cate', 'Item Code', 'MIN', 'MAX', 'AVG', 'STD', '# Samples']);

                foreach ($rows as $row) {
                    $min = $row->min !== null ? (float) $row->min : 0.0;
                    $max = $row->max !== null ? (float) $row->max : 0.0;
                    $avg = $row->avg !== null ? (float) $row->avg : 0.0;
                    fputcsv($out, [
                        $row->test_date,
                        $row->workorder_no,
                        $row->test_name,
                        $row->asset_no,
                        $row->production_date,
                        $row->item_category,
                        $row->item_code,
                        number_format($min, 4, '.', ''),
                        number_format($max, 4, '.', ''),
                        number_format($avg, 4, '.', ''),
                        $row->stdva_value ?? '',
                        $row->sample_number ?? 0,
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }


        $data = $query->paginate($perPage);


        return response()->json($data);
    }

    // public function historical_sample_tests(Request $request)
    // {
    //     $from = Carbon::now()->subMonths(12)->startOfDay();
    //     $to   = Carbon::now()->endOfDay();

    //     $perPage = (int) $request->get('per_page', 10);
    //     $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
    //     $testName = trim((string) $request->get('test_name', ''));
    //     $assetNo = trim((string) $request->get('asset_no', ''));
    //     $itemCategory = trim((string) $request->get('item_category', ''));
    //     $dateRange = trim((string) $request->get('date_range', ''));

    //     if ($dateRange !== '' && str_contains($dateRange, ' to ')) {
    //         [$fromInput, $toInput] = explode(' to ', $dateRange, 2);
    //         try {
    //             $from = Carbon::parse(trim($fromInput))->startOfDay();
    //             $to = Carbon::parse(trim($toInput))->endOfDay();
    //         } catch (\Exception $e) {
    //             // Keep default range if parsing fails
    //         }
    //     }

    //     $query = DB::table('sample_tests as st')
    //         ->join('sample_tests_samples as sts', function ($j) {
    //             $j->on('sts.sample_test_id', '=', 'st.id')
    //                 ->where('sts.is_deleted', 0);
    //         })
    //         ->leftJoin('test_definitions as td', function ($j) {
    //             $j->on('td.id', '=', 'st.test_name_id')
    //                 ->where('td.is_deleted', 0);
    //         })
    //         ->leftJoin('assets as a', function ($j) {
    //             $j->on('a.id', '=', 'st.asset_id')
    //                 ->where('a.is_deleted', 0);
    //         })
    //         ->whereBetween('st.sample_date', [$from, $to])
    //         ->where('st.is_deleted', 0)
    //         ->whereRaw('LOWER(COALESCE(sts.sample_result, "")) LIKE "%fail%"')
    //         ->groupBy('st.id', 'st.sample_date')
    //         ->selectRaw('
    //             st.id as sample_test_id,
    //             td.test_name,
    //             st.item_category,
    //             a.asset_no,
    //             st.sample_date,
    //             st.stdva_value,
    //             COUNT(sts.id) as total_samples,
    //             MIN(CAST(sts.sample_value AS DECIMAL(18,4))) as min_value,
    //             MAX(CAST(sts.sample_value AS DECIMAL(18,4))) as max_value
    //         ');

    //     if ($testName !== '') {
    //         $query->where('td.test_name', 'like', "%{$testName}%");
    //     }
    //     if ($assetNo !== '') {
    //         $query->where('a.asset_no', 'like', "%{$assetNo}%");
    //     }
    //     if ($itemCategory !== '') {
    //         $query->where('st.item_category', 'like', "%{$itemCategory}%");
    //     }

    //     if ($request->get('export') === 'csv') {
    //         $filename = 'historical-sample-tests-' . now()->format('Ymd_His') . '.csv';
    //         $rows = $query->orderByDesc('st.sample_date')->get();

    //         $callback = function () use ($rows) {
    //             $out = fopen('php://output', 'w');
    //             fputcsv($out, ['Date', 'Min', 'Max', 'Avg', 'Std', '# Samples']);

    //             foreach ($rows as $row) {
    //                 $min = $row->min_value !== null ? (float) $row->min_value : 0.0;
    //                 $max = $row->max_value !== null ? (float) $row->max_value : 0.0;
    //                 $avg = ($min + $max) / 2;
    //                 fputcsv($out, [
    //                     $row->sample_date,
    //                     number_format($min, 4, '.', ''),
    //                     number_format($max, 4, '.', ''),
    //                     number_format($avg, 4, '.', ''),
    //                     $row->stdva_value ?? '',
    //                     $row->total_samples ?? 0,
    //                 ]);
    //             }
    //             fclose($out);
    //         };

    //         return response()->streamDownload($callback, $filename, [
    //             'Content-Type' => 'text/csv; charset=UTF-8',
    //         ]);
    //     }

    //     $data = $query->orderByDesc('st.sample_date')->paginate($perPage);

    //     return response()->json($data);
    // }



    // Show the form
    public function showChangePasswordForm()
    {
        return view('auth.force_change_password');
    }

    // public function saveEmails(Request $request)
    // {
    //     $request->validate([
    //         'emails' => 'required|array|min:1',
    //         'emails.*.email' => 'required|email|distinct',
    //     ]);

    //     foreach ($request->emails as $emailData) {            
    //         DB::table('sample_failure_notification')->insert([
    //             'email' => $emailData['email']
    //         ]);
    //     }

    //     return response()->json(['message' => 'Emails saved successfully!']);
    // }

    public function saveEmails(Request $request)
    {
        $request->validate([
            'emails' => 'required|array|min:1',
            'emails.*.email' => 'required|email|distinct',
        ]);


        DB::table('sample_failure_notification')->truncate();


        $emailsToInsert = collect($request->emails)->map(function ($emailData) {
            return ['email' => $emailData['email']];
        })->toArray();

        DB::table('sample_failure_notification')->insert($emailsToInsert);

        return response()->json(['message' => 'Emails saved successfully!']);
    }
    public function saveNotificationEmails(Request $request)
    {
        $request->validate([
            'emails' => 'required|array|min:1',
            'emails.*.email' => 'required|email|distinct',
        ]);


        DB::table('email_notification')->truncate();


        $emailsToInsert = collect($request->emails)->map(function ($emailData) {
            return ['email' => $emailData['email']];
        })->toArray();

        DB::table('email_notification')->insert($emailsToInsert);

        return response()->json(['message' => 'Emails saved successfully!']);
    }

    public function getEmails()
    {
        $emails = DB::table('sample_failure_notification')
            ->select('id', 'email')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['emails' => $emails]);
    }
    public function getNotificationEmails()
    {
        $emails = DB::table('email_notification')
            ->select('id', 'email')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json(['emails' => $emails]);
    }


    // Handle the update
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'new_password' => [
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&#^(){}\[\]<>~+=|\/.,:;\'"-]/',
                ],
                'confirm_password' => 'required|same:new_password',
            ]);

            DB::table('users')
                ->where('id', auth()->id())
                ->update([
                    'name' => $request->new_password,
                    'password' => Hash::make($request->new_password),
                    'password_verified' => now(),
                    'must_change' => 0,
                ]);

            auth()->user()->refresh();

            return redirect('/home')->with('success', 'Password changed successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // You can keep field-level errors
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('general_error', 'Change password failed validation.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Something went wrong. Please try again.']);
        }
    }
}
