<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportGifi implements FromView
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

        $account_type = @$_GET['filter_account_type'];
        $sub_account_type = @$_GET['filter_sub_account_type'] ?? [];
        $account = @$_GET['filter_account'] ?? [];

        $qry = DB::table('gifi')
            ->where('is_deleted', 0)
            ->where(function ($query) use ($account_type, $sub_account_type, $account) {
                if (!empty($account_type)) {
                    $query->where('account_type', $account_type);
                }
                if (count($sub_account_type) > 0) {
                    $query->whereIn('sub_type', $sub_account_type);
                }
                if (count($account) > 0) {
                    $query->whereIn('account_no', $account);
                }
                if (@$_GET['search']) {
                    $query->Orwhere('sub_type', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('account_type', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('account_no', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('description', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('note', 'like', '%' . @$_GET['search'] . '%');
                }
            })
            ->orderBy($field, $orderby)
            ->get();
    } else {
        $qry = DB::table('gifi')
            ->where('is_deleted', 0)
            ->orderBy('id', 'desc')
            ->get();
    }


    return view('exports.ExportGifi', [
        'qry' => $qry
    ]);
 }
}
