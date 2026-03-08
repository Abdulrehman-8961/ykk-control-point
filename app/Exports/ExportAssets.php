<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportAssets implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{

// Sorting setup
$field = request('field', 'id');
$orderby = request('orderBy', 'desc');

// Filters
$filter_machine_no = request('filter_machine_no');
$status = request('filter_status');
$search = request('search');

// Build base query with JOIN
$qry = DB::table('assets')
    ->where('is_deleted', 0);

// Apply status filter - exact match for 1 or 0
if (request()->has('filter_status') && in_array($status, ['0', '1'])) {
    $qry->where('status', (int)$status);
}

// Apply model filter
if (!empty($filter_machine_no)) {
    $qry->where('machine_no', 'like', '%' . $filter_machine_no . '%');
}

// Apply search filter
if (!empty($search)) {
    $qry->where(function($query) use ($search) {
        $query->where('asset_no', 'like', '%' . $search . '%')
              ->orWhere('machine_no', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
    });
}


// Get final results with grouping
$qry = $qry->select('*')
           ->groupBy('id')  // Group by asset ID to remove duplicates
           ->orderBy($field, $orderby)
           ->get();

// GETID assignment
$GETID = request('id', optional($qry->first())->id);

    return view('exports.ExportAssets', [
        'qry' => $qry
    ]);
 }
}
