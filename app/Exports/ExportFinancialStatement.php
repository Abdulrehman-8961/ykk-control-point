<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class ExportFinancialStatement implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{


    $client_id = $this->request->input('fs_client');
    $fyear = $this->request->input('fs_fyear');
    $rounding = str_replace(" ", "", trim($this->request->input('fs_rounding')));
    $system_settings = DB::table('system_settings')->where('id', 1)->first();
    $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
 
        /**
         * Balance Sheet Assets
         * DIVIDED INTO 3 PARTS
         * CURRENT ASSETS
         * CAPITAL ASSETS
         * LONG TERM ASSETS
         */
        $current_assets = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join) use ($client_id){
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Asset');
            $join->where('g.sub_type', 'Current asset');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
        //     DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
        //     DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
        //    DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
        //     DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
         )
        ->orderBy('j.account_no', 'asc')
        ->get();
        $capital_assets = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join)use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Asset');
            $join->where('g.sub_type', 'Capital asset');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
        
        $long_term_assets = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join) use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Asset');
            $join->where('g.sub_type', 'Long-term asset');
                 $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            'j.client',
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();

 
          /**
         * Balance Sheet Liabilities
         * DIVIDED INTO 3 PARTS
         * CURRENT LIABILITIES
         * LONG TERM LIABILITIES
         * EQUITY
         */
        $current_liabilities = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join) use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Liability');
            $join->where('g.sub_type', 'Current liability');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
        $long_term_liabilities = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join)use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Liability');
            $join->where('g.sub_type', 'Long-term liability');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
        $equity = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join) use ($client_id){
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Liability');
            $join->where('g.sub_type', 'Equity');
              $join->where('g.client_id',$client_id); 
        })

        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
          /**
         * Balance Sheet Retained Earnings
         * ONE SECTION
         */
        $retained_earnings = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join)use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Retained Earning');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
        /**
         * Statement of Income
         * DIVIDE INTO 3 PARTS
         * REVENUE
         * COST OF SALES
         * EXPENSES
         */
        $revenue = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join)use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Income');
            $join->where('g.sub_type', 'Revenue');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
        $cost_of_sales = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join) use ($client_id){
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Income');
            $join->where('g.sub_type', 'Cost of sale');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
        $operating_expenses = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->Join("clients_gifi as g", function ($join)use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
            $join->where('g.account_type', 'Income');
            $join->where('g.sub_type', 'Operating expense');
              $join->where('g.client_id',$client_id); 
        })
        ->groupBy("j.account_no")
        ->select(
            "j.account_no",
            "g.description",
            "g.account_type",
            "g.sub_type",
            "j.client",
            "j.fyear",
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_debits_current_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear=j.fyear) as total_credits_current_fyear'),
           // DB::raw('(SELECT SUM(debit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_debits_previous_fyear'),
           // DB::raw('(SELECT SUM(credit) FROM journals where account_no=j.account_no and client=j.client and fyear='.($fyear-1).') as total_credits_previous_fyear'),
        )
        ->orderBy('j.account_no', 'asc')
        ->get();
    return view("exports.ExportFinancialStatement", compact(
        "system_settings",
        "client",
        "fyear",
        "rounding",
        "current_assets",
        "capital_assets",
        "long_term_assets",
        "current_liabilities",
        "long_term_liabilities",
        "equity",
        "retained_earnings",
        "revenue",
        "cost_of_sales",
        "operating_expenses",
    ));
 }
}
