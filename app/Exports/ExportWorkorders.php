<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportWorkorders implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{


        $field = $this->request->get('field', 'id');
        $orderby = $this->request->get('orderBy', 'desc');
        $status = $this->request->get('filter_status');
        $color = $this->request->get('filter_color');
        $search = $this->request->get('search');

        // Base Query
        $qry = DB::table('workorders as w')
            ->leftJoin('itemcodes as i', 'w.itemcode_id', '=', 'i.item_code')
            ->where('w.is_deleted', 0);

        // Filter by status
        if ($this->request->has('filter_status') && in_array($status, ['0', '1'])) {
            $qry->where('w.status', (int) $status);
        }

        // Filter by color
        if (!empty($color)) {
            $qry->where('w.itemcode_color_id', 'like', '%' . $color . '%');
        }

        // Search filters
        if (!empty($search)) {
            $qry->where(function ($query) use ($search) {
                $query->where('i.item_code', 'like', '%' . $search . '%')
                      ->orWhere('i.description', 'like', '%' . $search . '%')
                      ->orWhere('w.workorder_no', 'like', '%' . $search . '%')
                      ->orWhere('w.itemcode_color_id', 'like', '%' . $search . '%');
            });
        }

        // Finalize query
        $qry = $qry->select('w.*', 'i.item_code', 'i.description')
                   ->orderBy('w.' . $field, $orderby)
                   ->groupBy('w.id')
                   ->get(); // Do NOT paginate for export

// Default ID for view logic
$GETID = request('id', optional($qry->first())->id);




    return view('exports.ExportWorkorders', [
        'qry' => $qry
    ]);
 }
}
