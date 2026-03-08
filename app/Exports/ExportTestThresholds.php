<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportTestThresholds implements FromView
{
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function view(): View
    {
        // Allowed sortable fields
        $sortableFields = [
            'tt.id',
            'tt.item_category_id',
            'i.item_category'
        ];

        $field = $this->request->get('field', 'tt.id');
        $orderby = $this->request->get('orderBy', 'desc');

        // Validate sorting field
        if (!in_array($field, $sortableFields)) {
            $field = 'tt.id';
        }

        // Filters
        $status = $this->request->get('filter_status');
        $search = $this->request->get('search');

        // Base query
        $qry = DB::table('test_thresholds as tt')
            ->leftJoin('test_definitions as td', 'td.id', '=', 'tt.test_name_id')
            ->leftJoin('test_threshold_item_categories as ttic', 'ttic.test_threshold_id', '=', 'tt.id')
            ->leftJoin('item_categories as i', 'i.id', '=', 'ttic.item_category_id')
            ->where('tt.is_deleted', 0);

        // Status filter
        if ($this->request->has('filter_status') && in_array($status, ['0', '1'])) {
            $qry->where('tt.status', (int)$status);
        }

        // Search filter
        if (!empty($search)) {
            $qry->where(function ($query) use ($search) {
                $query->where('td.test_name', 'like', '%' . $search . '%')
                      ->orWhere('i.item_category', 'like', '%' . $search . '%');
            });
        }

        // Select fields
        $qry = $qry->select(
                'tt.*',
                'td.test_name',
                'ttic.min',
                'ttic.max',
                'ttic.avg',
                'ttic.YFS',
                'ttic.YFGS',
                'ttic.safety_threshold',
                'ttic.absorption',
                'i.item_category'
            )
            ->orderBy($field, $orderby)
            ->get();

        // Optional: Get selected ID if needed
        $GETID = $this->request->get('id', $qry[0]->id ?? null);

        // Pass data to view
        return view('exports.ExportTestThresholds', [
            'qry' => $qry
        ]);
    }
}
