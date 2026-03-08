<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportTestDefinitions implements FromView
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
$filter_test_type = request('filter_test_type');
$status = request('filter_status');
$search = request('search');

// Build base query with JOIN
$qry = DB::table('test_definitions')
    ->where('is_deleted', 0);

// Apply status filter - exact match for 1 or 0
if (request()->has('filter_status') && in_array($status, ['0', '1'])) {
    $qry->where('status', (int)$status);
}

// Apply model filter
if (!empty($filter_test_type)) {
    $qry->where('test_type', 'like', '%' . $filter_test_type . '%');
}

// Apply search filter
if (!empty($search)) {
    $qry->where(function($query) use ($search) {
        $query->where('test_name', 'like', '%' . $search . '%')
              ->orWhere('test_type', 'like', '%' . $search . '%')
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

    return view('exports.ExportTestDefinitions', [
        'qry' => $qry
    ]);
 }
}
