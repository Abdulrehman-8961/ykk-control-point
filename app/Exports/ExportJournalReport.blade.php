<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportJournalReport implements FromView
{
    protected $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {


        if (sizeof($_GET) > 0) {
            $edit_no = @$_GET['filter_edit_no'];
            $client = @$_GET['filter_client'] ?? [];
            $fiscal_year = @$_GET['filter_fiscal_year'] ?? [];
            $period = @$_GET['filter_period'] ?? [];
            $source = @$_GET['filter_source'] ?? [];
            $ref = @$_GET['filter_ref'] ?? [];
            $account = @$_GET['filter_account'] ?? [];
            $orderby = 'desc';
            $field = 'edit_no';
            if (isset($_GET['orderBy'])) {
                $orderby = $_GET['orderBy'];
                $field = $_GET['field'];
            }



            $qry = DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->leftJoin("clients as c", function ($join) {
                    $join->on("j.client", "=", "c.id")
                        ->where("c.is_deleted", 0);
                })
                ->leftJoin("source_code as sc", function ($join) {
                    $join->on("j.source", "=", "sc.id")
                        ->where('sc.is_deleted', 0);
                })
                ->where(function ($query) use ($edit_no, $client, $fiscal_year, $period, $source, $ref, $account) {

                    if (!empty($edit_no)) {
                        $query->where('j.editNo', $edit_no);
                    }
                    if (!empty($client)) {
                        $query->whereIn('j.client', $client);
                    }
                    if (!empty($fiscal_year)) {
                        $query->whereIn('j.fyear', $fiscal_year);
                    }
                    if (count($period) > 0) {
                        $query->whereIn("j.period", $period);
                    }
                    if (count($source) > 0) {
                        $query->whereIn("j.source", $source);
                    }
                    if (count($ref) > 0) {
                        $query->whereIn("j.ref_no", $ref);
                    }
                    if (count($account) > 0) {
                        $query->whereIn("j.account_no", $account);
                    }
                    $query->where('j.description', 'like', '%' . @$_GET['search'] . '%');
                })
                ->select("j.*", "c.firstname", "c.lastname", "sc.source_code", "c.company")
                ->orderBy($field, $orderby)->get();
        } else {
            $qry = DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->leftJoin("clients as c", function ($join) {
                    $join->on("j.client", "=", "c.id")
                        ->where("c.is_deleted", 0);
                })
                ->leftJoin("source_code as sc", function ($join) {
                    $join->on("j.source", "=", "sc.id")
                        ->where('sc.is_deleted', 0);
                })
                ->select("j.*", "c.firstname", "c.lastname", "sc.source_code", "c.company")
                ->orderBy('j.edit_no', 'desc')
                ->get();
        }


        return view('exports.ExportJournalReport', [
            'qry' => $qry
        ]);
    }
}
