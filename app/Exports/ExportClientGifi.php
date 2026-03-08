<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportClientGifi implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{
$request = $this->request;
$id = $request->id;
        $searchVal = @$request->searchVal;
        $account_type = @$request->account_type;
        $sub_account_type = @$request->sub_account_type ?? [];
        $account_no = @$request->account ?? [];
        $description = @$request->description;

        $qry = DB::table('clients_gifi')
        ->where('clients_gifi.client_id', $id)
        ->where(function ($query) use ($searchVal, $account_type, $sub_account_type, $account_no, $description) {
            $query->where('clients_gifi.is_deleted', 0);
            if (!empty($account_type)) {
                $query->where('clients_gifi.account_type', $account_type);
            }
            if (count($sub_account_type) > 0) {
                $query->whereIn('clients_gifi.sub_type', $sub_account_type);
            }
            if (count($account_no) > 0) {
                $query->whereIn('clients_gifi.account_no', $account_no);
            }
            if (!empty($description)) {
                $query->where('clients_gifi.description', $description);
            }
            if (!empty($searchVal)) {
                $query->where('clients_gifi.account_no', 'like', '%' . $searchVal . '%')
                    ->orWhere('clients_gifi.sub_type', 'like', '%' . $searchVal . '%')
                    ->orWhere('clients_gifi.account_type', 'like', '%' . $searchVal . '%')
                    ->orWhere('clients_gifi.description', 'like', '%' . $searchVal . '%')
                    ->orWhere('clients_gifi.note', 'like', '%' . $searchVal . '%');
            }
        })
        ->Join("clients", function ($join) {
            $join->on("clients_gifi.client_id", "=", "clients.id")
            ->where("clients.is_deleted", 0);
        })
        ->select("clients_gifi.*", "clients.logo", "clients.company")
                    ->orderBy('clients_gifi.account_no', 'asc')
                    ->get();


    return view('exports.ExportClients2', [
        'qry' => $qry
    ]);
 }
}
