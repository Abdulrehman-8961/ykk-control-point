<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ExportSampleTests implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $field = request('field', 'st.id');
        $orderby = request('orderBy', 'desc');
        $search = request('search');

        // Filters
        $filter_test_name = request('filter_test_name');
        $filter_item_cat = request('filter_item_cat');
        $filter_asset_no = request('filter_asset_no');
        $filter_workorder_no = request('filter_workorder_no');
        $filter_bosubi = request('filter_bosubi');
        $filter_sample_date = request('filter_sample_date');
        $filter_production_date = request('filter_production_date');
        $filter_user = request('filter_user');

        // Base query
        $qry = DB::table('sample_tests as st')
            ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
            ->leftJoin('workorders as w', 'w.id', '=', 'st.workorder_id')
            ->leftJoin('assets as a', 'a.id', '=', 'st.asset_id')
            ->leftJoin('users as u', 'u.id', '=', 'st.created_by')
            ->where('st.is_deleted', 0);

        // Apply filters
        if ($filter_test_name) {
            $qry->where('td.test_name', 'like', "%$filter_test_name%");
        }
        if ($filter_item_cat) {
            $qry->where('st.item_category', 'like', "%$filter_item_cat%");
        }
        if ($filter_asset_no) {
            $qry->where('a.asset_no', 'like', "%$filter_asset_no%");
        }
        if ($filter_workorder_no) {
            $qry->where('w.workorder_no', 'like', "%$filter_workorder_no%");
        }
        if ($filter_bosubi) {
            $qry->where('st.bosubi', 'like', "%$filter_bosubi%");
        }
        if ($filter_sample_date) {
            $qry->whereDate('st.sample_date', Carbon::createFromFormat('d-M-Y', $filter_sample_date)->format('Y-m-d'));
        }
        if ($filter_production_date) {
            $qry->whereDate('st.production_date', Carbon::createFromFormat('d-M-Y', $filter_production_date)->format('Y-m-d'));
        }
        if ($filter_user) {
            $qry->where(DB::raw("CONCAT(u.firstname, ' ', u.lastname)"), 'like', "%$filter_user%");
        }

        if ($search) {
            $qry->where(function ($query) use ($search) {
                $query->where('td.test_name', 'like', "%$search%")
                    ->orWhere('st.item_category', 'like', "%$search%")
                    ->orWhere('st.itemcode', 'like', "%$search%")
                    ->orWhere('st.lot', 'like', "%$search%")
                    ->orWhere('w.workorder_no', 'like', "%$search%")
                    ->orWhere('a.asset_no', 'like', "%$search%");
            });
        }

        // Select required columns
        $qry = $qry->select(
            'st.*',
            'td.test_name',
            'td.test_type',
            'td.criteria',
            'td.uom',
            'td.standard',
            'w.workorder_no',
            'a.asset_no',
            DB::raw("CONCAT(u.firstname, ' ', u.lastname) as user_name")
        )->orderBy($field, $orderby)->get();

        // Attach item description, threshold, and samples
        foreach ($qry as $q) {
            $q->item_description = DB::table('itemcodes')
                ->where('item_code', $q->itemcode)
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->value('description');

            $itemcodeId = DB::table('itemcodes')->where('item_code', $q->itemcode)->value('id');
            $itemCategoryId = DB::table('item_categories_itemcodes')->where('itemcode_id', $itemcodeId)->value('item_category_id');

            $threshold = DB::table('test_thresholds')->where('test_name_id', $q->test_name_id)->first();
            $q->threshold_item = null;

            if ($threshold && $itemCategoryId) {
                $q->threshold_item = DB::table('test_threshold_item_categories')
                    ->where('test_threshold_id', $threshold->id)
                    ->where('item_category_id', $itemCategoryId)
                    ->first();
            }

            $q->samples = DB::table('sample_tests_samples')
                ->where('sample_test_id', $q->id)
                ->where('is_deleted', 0)
                ->orderBy('sample_number')
                ->pluck('sample_value', 'sample_number')
                ->toArray();
        }

        return view('exports.ExportSampleTests', [
            'qry' => $qry
        ]);
    }
}
