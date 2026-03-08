<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportItemcodes implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{


if (sizeof($_GET) > 0) {
    $orderby = 'desc';
    $field = 'id';
    if (isset($_GET['orderBy'])) {
        $orderby = $_GET['orderBy'];
        $field = $_GET['field'];
    }

    $item_code = @$_GET['filter_item_code'];

    $qry = DB::table('itemcodes as i')
         ->select(
            'i.*',
            DB::raw('GROUP_CONCAT(ic.color SEPARATOR ", ") as colors')
        )
        ->where('i.is_deleted', 0)
        ->where(function ($query) use ($item_code) {
            if (!empty($item_code)) {
                $query->where('i.item_code', $item_code);
            }
            if (@$_GET['search']) {
                $query->orWhere('i.item_code', 'like', '%' . @$_GET['search'] . '%');
                $query->orWhere('i.description', 'like', '%' . @$_GET['search'] . '%');
            }
        })
        ->groupBy('i.id') // Required for GROUP_CONCAT
        ->orderBy('i.' . $field, $orderby)
        ->get();
} else {
    $qry = DB::table('itemcodes as i')
         ->select(
            'i.*',
            DB::raw('GROUP_CONCAT(ic.color SEPARATOR ", ") as colors')
        )
        ->where('i.is_deleted', 0)
        ->groupBy('i.id')
        ->orderBy('i.id', 'desc')
        ->get();
}




    return view('exports.ExportItemcodes', [
        'qry' => $qry
    ]);
 }
}
