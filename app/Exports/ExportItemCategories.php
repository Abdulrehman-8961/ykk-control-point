<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportItemCategories implements FromView
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

    $item_category = @$_GET['filter_item_category'];
    $item_code = @$_GET['filter_item_code'];
    $description = @$_GET['description'];

    $qry = DB::table('item_categories as i')
        ->leftJoin('item_categories_itemcodes as ic', 'ic.item_category_id', '=', 'i.id')
        ->leftJoin('itemcodes', 'itemcodes.id', '=', 'ic.itemcode_id')
        ->select(
            'i.*',
            'itemcodes.item_code',
            'itemcodes.description',
        )
        ->where('i.is_deleted', 0)
        ->where('ic.is_deleted', 0)
        ->where(function ($query) use ($item_category, $item_code, $description) {
            if (!empty($item_category)) {
                $query->where('i.item_category', $item_category);
            }
            if (!empty($item_code)) {
                $query->where('itemcodes.item_code', $item_code);
            }
            if (!empty($description)) {
                $query->where('itemcodes.description', $description);
            }
            if (@$_GET['search']) {
                $query->orWhere('i.item_category', 'like', '%' . @$_GET['search'] . '%');
                $query->orWhere('itemcodes.item_code', 'like', '%' . @$_GET['search'] . '%');
                $query->orWhere('itemcodes.description', 'like', '%' . @$_GET['search'] . '%');
            }
        })
        ->orderBy('i.' . $field, $orderby)
        ->get();
} else {
    $qry = DB::table('item_categories as i')
        ->leftJoin('item_categories_itemcodes as ic', 'ic.item_category_id', '=', 'i.id')
        ->leftJoin('itemcodes', 'itemcodes.id', '=', 'ic.itemcode_id')
        ->select(
            'i.*',
            'itemcodes.item_code',
            'itemcodes.description',
        )
        ->where('i.is_deleted', 0)
        ->where('ic.is_deleted', 0)
        ->orderBy('i.id', 'desc')
        ->get();
}




    return view('exports.ExportItemCategories', [
        'qry' => $qry
    ]);
 }
}
