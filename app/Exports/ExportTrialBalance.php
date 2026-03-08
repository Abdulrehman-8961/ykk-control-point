<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class ExportTrialBalance implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{


    // $client_id = $this->request->input('tb_client');
    // $fyear = $this->request->input('tb_fiscal_year');
    // $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
    // $reports = DB::table('journals as j')
    // ->where('j.is_deleted', 0)
    // ->where('j.client', $client_id)
    // ->where('j.fyear', $fyear)
    // ->Join("clients_gifi as g", function ($join) {
    //     $join->on("j.account_no", "=", "g.account_no")
    //     ->where('g.is_deleted', 0);
    // })
    // ->groupBy("j.account_no")
    // ->select(
    //     "j.account_no",
    //     "g.description",
    //     DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits'),
    //     DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits'),
    // )
    // ->orderBy("j.account_no", 'asc')
    // ->get();


 $client_id = $this->request->input('tb_client');
    $fyear = $this->request->input('tb_fiscal_year');
        // dd($fyear);
        $system_settings = DB::table('system_settings')->where('id', 1)->first();
        $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
        //DB::enableQueryLog();
        $reports = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->where('j.fyear', $fyear)
        ->Join("clients_gifi as g", function ($join) use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.client_id', $client_id);
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "j.client",
            "j.fyear"
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits'),
          //  DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits'),
        )
        ->orderBy("j.account_no", 'asc')
        ->get();



    return view('exports.ExportTrialBalance', [
        'reports' => $reports,
        "client" => $client,
        'fyear' => $fyear,
    ]);
 }
}
