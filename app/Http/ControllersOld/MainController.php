<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mail;
use Hash;
use PDF;
use DateTime;
use Excel;
use App\Exports\ExportClients;
use App\Exports\ExportClients2;
use App\Exports\ExportGifi;
use App\Exports\ExportClientGifi;
use App\Imports\JournalImport;
use App\Imports\JournalStandardImport;
use App\Imports\GIFIImport;
use App\Exports\ExportJournals;
use App\Exports\ExportTrialBalance;
use App\Exports\ExportUsers;
use App\Exports\ExportVendors;
use App\Exports\ExportSites;
use App\Exports\ExportDistributors;
use App\Exports\ExportAssetType;
use App\Exports\ExportExcelNetwork;
use App\Exports\ExportFinancialStatement;
use App\Exports\ExportJournalReport;
use App\Mail\UserMail;
use App\Exports\ExportOperatingSystems;
use App\Exports\ExportDomains;
use Symfony\Component\HttpFoundation\Cookie;

use Cache;
use Validator;

class MainController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('expire_session_after_one_minute');
    }

    public function Clients()
    {
        $gifi = DB::table("gifi")->where("is_deleted", 0)->orderBy('account_no')->get();
        return view('clients', compact("gifi"));
    }
    public function Journals()
    {
        $system_settings = DB::table('system_settings')->first();
        $cities = DB::table('cities as c')
            ->where('c.country_name', 'Canada')
            ->Join("tax_rate as tr", function ($join) {
                $join->on("c.state_name", "=", "tr.province")
                ->where("tr.is_deleted", 0)->take(1);
            })
            ->groupBy('c.state_name')
            ->get();
        $tax_rate = DB::table('tax_rate')->where('province', @$cities[0]->state_name)->where('is_deleted', 0)->first();
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('company', 'ASC')->get();
        $editNo = 1; //(@DB::table('journals')->orderByDesc('edit_no')->first()->edit_no ?? 0) + 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status',1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)

        ->orderBy('account_no', 'asc')->get();

        return view("journals", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis"));
    }
    public function JournalFinancialStatementLoadContent(Request $request)
    {
        $client_id = $request->input('client_id');
        $fyear = $request->input('fyear');
        $rounding = $request->input('rounding');
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

        return view("JournalFinancialStatementContent", compact(
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
    public function JournalFinancialStatement(Request $request)
    {
        $client_id = $request->input('fs_client');
        $fyear = $request->input('fs_fyear');
        $rounding = str_replace(" ", "", trim($request->input('fs_rounding')));

        return view("JournalFinancialStatement", compact(
            "client_id",
            "fyear",
            "rounding",
        ));
    }
    public function JournalTrialBalanceLoadContent(Request $request)
    {
        $client_id = $request->input('client_id');
        $fyear = $request->input('fyear');
        // dd($fyear);
        $system_settings = DB::table('system_settings')->where('id', 1)->first();
        $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
        //DB::enableQueryLog();
        $reports = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->where('j.client', $client_id)
        ->where('j.fyear', $fyear)
        ->Join("clients_gifi as g", function ($join)use ($client_id) {
            $join->on("j.account_no", "=", "g.account_no")
            ->where('g.is_deleted', 0);
              $join->where('g.client_id',$client_id);
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
        //return response()->json(DB::getQueryLog());
        return view("JournalTrialBalanceContent", compact("fyear", "client", "system_settings", "reports"));
    }
    public function JournalReportRemittanceStatus(Request $request)
    {
        $clients = $request->input('report_client');
        $type = $request->input('report_type');
        $month = $request->input('report_month');
        $year = $request->input('report_year');

       if($clients == null || $clients == "") {
            $clients = DB::table('clients')->where('is_deleted', 0)->pluck('id')->toArray();
       }

        return view("JournalRemittanceStatus", compact("clients", "type", "month", "year"));
    }
    public function JournalReportRemittanceStatusLoadContent(Request $request)
    {
        $filters = (object) [
            "clients" => json_decode($request->get('clients')),
            "type" => trim($request->get('type')),
            "month" => $request->get('month'),
            "year" => $request->get('year'),
        ];
        $clients = DB::table('clients')->where('is_deleted', 0)->whereIn("id", $filters->clients)->get();
        $system_settings = DB::table('system_settings')->where('id', 1)->first();
        return view("JournalRemittanceStatusContent", compact("filters", "clients", "system_settings"))->render();
    }
    public function JournalReportProgress(Request $request)
    {
        $clients = $request->input('report_client');
        $type = $request->input('report_type');
        $fiscal_year = $request->input('report_fiscal_year');
        $fiscal_years = $request->input('report_fiscal_years');

        if($clients == null || $clients == "") {
            $clients = DB::table('clients')->where('is_deleted', 0)->pluck('id')->toArray();
        }

        return view("JournalProgress", compact("clients", "type", "fiscal_year", "fiscal_years"));
    }
    public function JournalReportProgressLoadContent(Request $request)
    {
        $filters = (object) [
            "clients" => json_decode($request->get('clients')),
            "type" => trim($request->get('type')),
            "fiscal_year" => $request->get('fiscal_year'),
            "fiscal_years" => json_decode($request->get('fiscal_years'))
        ];
        $clients = DB::table('clients')->where('is_deleted', 0)->whereIn("id", $filters->clients)->get();
        $system_settings = DB::table('system_settings')->where('id', 1)->first();
        return view("JournalProgressContent", compact("filters", "clients", "system_settings"));
    }
    public function JournalTrialBalance(Request $request)
    {
        $client_id = $request->input('tb_client');
        $fyear = $request->input('tb_fiscal_year');
       return view("JournalTrialBalance", compact("client_id", "fyear"));
    }
    public function JournalReportsLoadContent(Request $request)
    {
        $filters = (object)[
            "client_id" => $request->get('client_id'),
            "fiscal_year" => $request->get('fiscal_year'),
            "period" => json_decode($request->get('period')),
            "source" => json_decode($request->get('source')),
            "account" => json_decode($request->get('account')),
        ];
        $rollups = $request->input('rollups');
        $client = DB::table('clients')->where('is_deleted', 0)->where('id', $filters->client_id)->first();



        $accounts = DB::table('clients_gifi')
        ->where('is_deleted', 0)
        ->where('client_id', $filters->client_id)

        ->orderBy('account_no', 'asc')
        ->get();

        $sources = DB::table('source_code')->where('is_deleted', 0)

        ->orderBy('source_code', 'asc')
        ->get();

            $periods = [1,2,3,4,5,6,7,8,9,10,11,12];
            return view("JournalReportContent", compact( "rollups", "client", "accounts", "sources", "periods", "filters"))->render();
    }
    public function JournalReports(Request $request)
    {
        $client_id = $request->input('report_client');
        $fiscal_year = $request->input('report_fiscal_year');
        $period = $request->input('report_period') ?? [];
        $source = $request->input('report_source') ?? [];
        $account = $request->input('report_account') ?? [];
        $rollups = $request->input('report_rollups');
    
        // Define individual variables for each filter to pass to the view
        $filter_edit_no = $request->input('filter_edit_no');
        $filter_client = $client_id;
        $filter_fiscal_year = $fiscal_year;
        $filter_period = $period;
        $filter_source = $source;
        $filter_ref = $request->input('filter_ref') ?? [];
        $filter_account = $account;
        $orderBy = $request->input('orderBy', 'desc');
        $field = $request->input('field', 'edit_no');
    
        $filters = (object) [
            "client_id" => $client_id,
            "fiscal_year" => $fiscal_year,
            "period" => $period,
            "source" => $source,
            "account" => $account,
        ];
    
        return view("JournalReports", compact(
            "rollups", 
            "filters", 
            "filter_edit_no", 
            "filter_client", 
            "filter_fiscal_year", 
            "filter_period", 
            "filter_source", 
            "filter_ref", 
            "filter_account", 
            "orderBy", 
            "field"
        ));
    }
    public function journals_client_data(Request $request)
    {
        $client_id = $request->post('client_id');
        $month = $request->post('month');
        $year = $request->post('year');
        $fyear = $request->post('fyear');
        $period = $request->post('period');
        $data['dateCreated'] = $request->dateCreated;
        $data['periodsArr'] = $request->periodsArr ?? [];
        $data['accountsArr'] = $request->accountsArr ?? [];
        $data['sourcesArr'] = $request->sourcesArr ?? [];
        $data['refsArr'] = $request->refsArr ?? [];
        $data['client'] = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
        $data['client_gifi'] = DB::table('clients_gifi')->where('is_deleted', 0)->where('client_id', $client_id)->get();
        $data['client_refs'] = DB::table('journals')
            ->where('is_deleted', 0)
            ->where('client', $client_id)
            ->distinct("ref_no")
            ->orderBy('ref_no', 'asc')
            ->pluck('ref_no')
            ->toArray();

        $data['journals'] = $this->clientJournals($request);

        $data['period_balance_debit'] = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', $period)
            ->sum('debit');
        $data['period_balance_credit'] = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', $period)
            ->sum('credit');
        $data['fyear_balance_debit'] = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->sum('debit');
        $data['fyear_balance_credit'] = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->sum('credit');


        $data['p1_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 1)
        ->count();
        $data['p2_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 2)
        ->count();
        $data['p3_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 3)
        ->count();
        $data['p4_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 4)
        ->count();
        $data['p5_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 5)
        ->count();
        $data['p6_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 6)
        ->count();
        $data['p7_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 7)
        ->count();
        $data['p8_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 8)
        ->count();
        $data['p9_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 9)
        ->count();
        $data['p10_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 10)
        ->count();
        $data['p11_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 11)
        ->count();
        $data['p12_journals'] = DB::table('journals')->where('is_deleted', 0)
        ->where('client', $client_id)
        ->where('fyear', $fyear)
        ->where('period', 12)
        ->count();

        if($data['p1_journals'] > 0) {
            $p1_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 1)
            ->sum('debit');
            $p1_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 1)
            ->sum('credit');
            if($p1_debits == $p1_credits) {
                $data['p1_indicator'] = 'indicator-period-balance';
            } else {
                $data['p1_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p1_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p2_journals'] > 0) {
            $p2_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 2)
            ->sum('debit');
            $p2_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 2)
            ->sum('credit');
            if($p2_debits == $p2_credits) {
                $data['p2_indicator'] = 'indicator-period-balance';
            } else {
                $data['p2_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p2_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p3_journals'] > 0) {
            $p3_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 3)
            ->sum('debit');
            $p3_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 3)
            ->sum('credit');
            if($p3_debits == $p3_credits) {
                $data['p3_indicator'] = 'indicator-period-balance';
            } else {
                $data['p3_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p3_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p4_journals'] > 0) {
            $p4_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 4)
            ->sum('debit');
            $p4_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 4)
            ->sum('credit');
            if($p4_debits == $p4_credits) {
                $data['p4_indicator'] = 'indicator-period-balance';
            } else {
                $data['p4_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p4_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p5_journals'] > 0) {
            $p5_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 5)
            ->sum('debit');
            $p5_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 5)
            ->sum('credit');
            if($p5_debits == $p5_credits) {
                $data['p5_indicator'] = 'indicator-period-balance';
            } else {
                $data['p5_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p5_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p6_journals'] > 0) {
            $p6_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 6)
            ->sum('debit');
            $p6_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 6)
            ->sum('credit');
            if($p6_debits == $p6_credits) {
                $data['p6_indicator'] = 'indicator-period-balance';
            } else {
                $data['p6_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p6_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p7_journals'] > 0) {
            $p7_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 7)
            ->sum('debit');
            $p7_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 7)
            ->sum('credit');
            if($p7_debits == $p7_credits) {
                $data['p7_indicator'] = 'indicator-period-balance';
            } else {
                $data['p7_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p7_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p8_journals'] > 0) {
            $p8_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 8)
            ->sum('debit');
            $p8_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 8)
            ->sum('credit');
            if($p8_debits == $p8_credits) {
                $data['p8_indicator'] = 'indicator-period-balance';
            } else {
                $data['p8_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p8_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p9_journals'] > 0) {
            $p9_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 9)
            ->sum('debit');
            $p9_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 9)
            ->sum('credit');
            if($p9_debits == $p9_credits) {
                $data['p9_indicator'] = 'indicator-period-balance';
            } else {
                $data['p9_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p9_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p10_journals'] > 0) {
            $p10_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 10)
            ->sum('debit');
            $p10_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 10)
            ->sum('credit');
            if($p10_debits == $p10_credits) {
                $data['p10_indicator'] = 'indicator-period-balance';
            } else {
                $data['p10_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p10_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p11_journals'] > 0) {
            $p11_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 11)
            ->sum('debit');
            $p11_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 11)
            ->sum('credit');
            if($p11_debits == $p11_credits) {
                $data['p11_indicator'] = 'indicator-period-balance';
            } else {
                $data['p11_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p11_indicator'] = 'indicator-period-no-journals';
        }
        if($data['p12_journals'] > 0) {
            $p12_debits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 12)
            ->sum('debit');
            $p12_credits = DB::table('journals')->where('is_deleted', 0)
            ->where('client', $client_id)
            ->where('fyear', $fyear)
            ->where('period', 12)
            ->sum('credit');
            if($p12_debits == $p12_credits) {
                $data['p12_indicator'] = 'indicator-period-balance';
            } else {
                $data['p12_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p12_indicator'] = 'indicator-period-no-journals';
        }


        $lastInsertedEditNo = 1;
        $LatestJournal = DB::table('journals')
            ->where('fyear', $fyear)
            ->where('client', $client_id)
            ->where('is_deleted', 0)
            ->orderBy('editNo', 'desc')
            ->first();
        if (@$LatestJournal) {
            $lastInsertedEditNo = $LatestJournal->editNo + 1;
        }
        $data['fyearLatestEditNo'] = $lastInsertedEditNo;
        return response()->json(compact('data'));
    }
    public function get_tax_rate_by_province(Request $request)
    {
        return response()->json(DB::table('tax_rate')->where('is_deleted', 0)->where('province', $request->get('province'))->first());
    }
    public function get_gifi_client_accounts(Request $request)
    {
        $account = DB::table('clients_gifi')->where('client_id', $request->get('client_id'))->where('account_no', $request->get('account_no'))->where('is_deleted', 0)->first();
        $fyear_balance_debit = 0;
        $fyear_balance_credit = 0;
        if (@$account) {
            $fyear_balance_debit = DB::table('journals')->where('is_deleted', 0)
                ->where('fyear', $request->get('fyear'))
               // ->where('period', $request->get('period'))
                ->where('account_no', $account->account_no)
                ->sum('debit');
            $fyear_balance_credit = DB::table('journals')->where('is_deleted', 0)
                ->where('fyear', $request->get('fyear'))
              // ->where('period', $request->get('period'))
                ->where('account_no', $account->account_no)
                ->sum('credit');
        }

        return response()->json(compact('account', "fyear_balance_debit", "fyear_balance_credit"));
    }
    public function get_client_gifi(Request $request)
    {
        return response()->json(DB::table('clients_gifi')->where('is_deleted', 0)

            ->where('client_id', $request->get('client_id'))
            ->get());
    }
    public function InsertJournal(Request $request)
    {
        /***
         * ROUND OFF ALL CURRENCY VALUES UPTO 2 DECIMALS FOR PRECISION
         */
        $data = [
            "editNo" => null,
            "client" => $request->post('client'),
            "month" => $request->post('month'),
            "year" => $request->post('year'),
            "fyear" => $request->post('fyear'),
            "period" => $request->post('period'),
            "account_no" => $request->post('account_no'),
            "original_account" => $request->post('account_no'),
            "source" => $request->post('source'),
            "ref_no" => $request->post('ref_no'),
            "description" => $request->post('description'),
            "gl_date" => $request->post('gl_date'),
            "date" => $request->post('date'),
            "debit" => @$request->post('debit') ? round($request->post('debit'), 2) : 0.00,
            "credit" => @$request->post('credit') ? round($request->post('credit'), 2) : 0.00,
            "taxable" => $request->post('taxable'),
            "original_debit" => @$request->post('original_debit') ? round($request->post('original_debit'), 2) : 0.00,
            "original_credit" => @$request->post('original_credit') ? round($request->post('original_credit'), 2) : 0.00,
            "net" => @$request->post('net') ? round($request->post('net'), 2) : 0.00,
            "tax1" => @$request->post('tax1') ? round($request->post('tax1'), 2) : 0.00,
            "tax2" => @$request->post('tax2') ? round($request->post('tax2'), 2) : 0.00,
            "province" => $request->post('province'),
            "pr_tax1" => @$request->post('pr_tax1') ? round(floatval(str_replace('%', '', $request->post('pr_tax1'))), 2) : 0.00,
            "pr_tax2" => @$request->post('pr_tax2') ? round(floatval(str_replace('%', '', $request->post('pr_tax2'))), 2) : 0.00,
            "portion" => @$request->post('portion') > 0 ? round(floatval(str_replace('%', '', $request->post('portion'))), 2) : 100.00,
            "wo_portion_net" => @$request->post('net') ? round($request->post('net'), 2) : 0.00,
            "wo_portion_tax1" => @$request->post('tax1') ? round($request->post('tax1'), 2) : 0.00,
            "wo_portion_tax2" => @$request->post('tax2') ? round($request->post('tax2'), 2) : 0.00,
            "created_by" => Auth::user()->id,
            "edit_by" => Auth::user()->id,
            "updated_at" => date("Y-m-d H:i:s")
        ];
        $editNo_ = [];
        $count_journals = 0;
        $lastInsertedEditNo = 0;
        $fyearLatestJournal = DB::table('journals')
        ->where('fyear', $request->post('fyear'))
        ->where('client', $request->post('client'))
        ->where('is_deleted', 0)
        ->orderBy('editNo', 'desc')
        ->first();
        if (@$fyearLatestJournal) {
            $lastInsertedEditNo = $fyearLatestJournal->editNo;
        }
        //apply portion && create a journal for portion
        if($data['portion'] < 100) {
            $client = DB::table('clients')->where('id', $data['client'])->where('is_deleted', 0)->first();
           if($data['taxable'] == 1) {

            $data['net'] = round(($data['portion'] / 100) * $data['net'], 2);
            $data['tax1'] = round(($data['portion'] / 100) * $data['tax1'], 2);
            $data['tax2'] = round(($data['portion'] / 100) * $data['tax2'], 2);

            $portion_journal = $data;
            $portion_journal['account_no'] = @$client->dividends_account;
            $portion_journal['description'] = "Personal Portion " . $portion_journal['description'];
            if($data['original_debit'] > $data['original_credit']) {
$portion_journal['debit'] = round($portion_journal['original_debit'] - ($portion_journal['net'] + $portion_journal['tax1'] + $portion_journal['tax2']), 2);
                $portion_journal['credit'] = 0.00;
                $lastInsertedEditNo++;
                $portion_journal['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($portion_journal);
                array_push($editNo_, $Eno);
            } else {
$portion_journal['credit'] = round($portion_journal['original_credit'] - ($portion_journal['net'] + $portion_journal['tax1'] + $portion_journal['tax2']), 2);
                $portion_journal['debit'] = 0.00;
                $lastInsertedEditNo++;
                $portion_journal['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($portion_journal);
                array_push($editNo_, $Eno);
            }
           } else {
            $portion_journal = $data;
            $portion_journal['account_no'] = @$client->dividends_account;
            $portion_journal['description'] = "Personal Portion " . $portion_journal['description'];
            if($data['original_debit'] > $data['original_credit']) {
                $data['debit'] = round(($data['portion'] / 100) * $data['original_debit'], 2);
                $portion_journal['debit'] = round($portion_journal['original_debit'] - $data['debit'], 2);
                       $portion_journal['credit'] = 0.00;
                       $lastInsertedEditNo++;
                       $portion_journal['editNo'] = $lastInsertedEditNo;
                       $Eno = DB::table('journals')->insertGetId($portion_journal);
                       array_push($array, $Eno);
                   } else {
                    $data['credit'] = round(($data['portion'] / 100) * $data['original_credit'], 2);
                $portion_journal['credit'] = round($portion_journal['original_credit'] - $data['credit'], 2);
                       $portion_journal['debit'] = 0.00;
                       $lastInsertedEditNo++;
                       $portion_journal['editNo'] = $lastInsertedEditNo;
                       $Eno = DB::table('journals')->insertGetId($portion_journal);
                       array_push($array, $Eno);
                   }
           }
        }
        if ($data['taxable'] == 1) {
            $client = DB::table('clients')->where('id', $data['client'])->where('is_deleted', 0)->first();
            if ($data['tax1'] > 0 && $data['tax1'] != '' && $data['tax2'] > 0 && $data['tax2'] != '' && $data['original_debit'] > $data['original_credit']) {
                /**
                 * If taxable = on and tax1 and tax2 are not equal to 0 and Debit > Credit)
                 *- Create 3 journals
                 */
                //journal 1 net
                $data['debit'] = $data['net'];
                $data['credit'] = 0.00;
                $lastInsertedEditNo++;
                $data['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($data);
                array_push($editNo_, $Eno);
                //journal 2 (tax1)
                $data['debit'] = $data['tax1'];
                $data['credit'] = 0.00;
                $data['account_no'] = $client->federal_tax;
                $lastInsertedEditNo++;
                $data['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($data);
                array_push($editNo_, $Eno);
                //journal 3 (tax2)
                $data['debit'] = $data['tax2'];
                $data['credit'] = 0.00;
                $data['account_no'] = $client->provincial_tax;
                $lastInsertedEditNo++;
                $data['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($data);
                array_push($editNo_, $Eno);
            } else {
                if ($data['tax1'] > 0 && $data['tax1'] != '' && $data['tax2'] > 0 && $data['tax2'] != '' && $data['original_credit'] > $data['original_debit']) {
                    /**
                     * If taxable = on and tax1 and tax2 are not equal to 0 and Credit > Debit)
                     *- Create 3 journals
                     */
                    //journal 1 net
                    $data['debit'] = 0.00;
                    $data['credit'] = $data['net'];
                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table('journals')->insertGetId($data);
                    array_push($editNo_, $Eno);
                    //journal 2 (tax1)
                    $data['debit'] = 0.00;
                    $data['credit'] = $data['tax1'];
                    $data['account_no'] = $client->federal_tax;
                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table('journals')->insertGetId($data);
                    array_push($editNo_, $Eno);
                    //journal 3 (tax2)
                    $data['debit'] = 0.00;
                    $data['credit'] = $data['tax2'];
                    $data['account_no'] = $client->provincial_tax;
                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table('journals')->insertGetId($data);
                    array_push($editNo_, $Eno);
                } else {
                    if ((($data['tax1'] > 0 && $data['tax1'] != '') || ($data['tax2'] > 0 && $data['tax2'] != '')) && $data['original_debit'] > $data['original_credit']) {
                        /**
                         * If taxable = on and tax1 or tax2 are not equal to 0 and Debit > Credit)
                         *- Create 2 journals
                         */
                        //journal 1 net
                        $data['debit'] = $data['net'];
                        $data['credit'] = 0.00;
                        $lastInsertedEditNo++;
                        $data['editNo'] = $lastInsertedEditNo;
                        $Eno = DB::table('journals')->insertGetId($data);
                        array_push($editNo_, $Eno);
                        if ($data['tax1'] > 0 && $data['tax1'] != '') {
                            //journal 2 (tax1)
                            $data['debit'] = $data['tax1'];
                            $data['credit'] = 0.00;
                            $data['account_no'] = $client->federal_tax;
                            $lastInsertedEditNo++;
                            $data['editNo'] = $lastInsertedEditNo;
                            $Eno = DB::table('journals')->insertGetId($data);
                            array_push($editNo_, $Eno);
                        }
                        if ($data['tax2'] > 0 && $data['tax2'] != '') {
                            //journal 2 (tax2)
                            $data['debit'] = $data['tax2'];
                            $data['credit'] = 0.00;
                            $data['account_no'] = $client->provincial_tax;
                            $lastInsertedEditNo++;
                            $data['editNo'] = $lastInsertedEditNo;
                            $Eno = DB::table('journals')->insertGetId($data);
                            array_push($editNo_, $Eno);
                        }
                    } else {
                        if ((($data['tax1'] > 0 && $data['tax1'] != '') || ($data['tax2'] > 0 && $data['tax2'] != '')) && $data['original_credit'] > $data['original_debit']) {
                            /**
                             * If taxable = on and tax1 or tax2 are not equal to 0 and Credit > Debit)
                             *- Create 2 journals
                             */
                            //journal 1 net
                            $data['debit'] = 0.00;
                            $data['credit'] = $data['net'];
                            $lastInsertedEditNo++;
                            $data['editNo'] = $lastInsertedEditNo;
                            $Eno = DB::table('journals')->insertGetId($data);
                            array_push($editNo_, $Eno);
                            if ($data['tax1'] > 0 && $data['tax1'] != '') {
                                //journal 2 (tax1)
                                $data['debit'] = 0.00;
                                $data['credit'] = $data['tax1'];
                                $data['account_no'] = $client->federal_tax;
                                $lastInsertedEditNo++;
                                $data['editNo'] = $lastInsertedEditNo;
                                $Eno = DB::table('journals')->insertGetId($data);
                                array_push($editNo_, $Eno);
                            }
                            if ($data['tax2'] > 0 && $data['tax2'] != '') {
                                //journal 2 (tax2)
                                $data['debit'] = 0.00;
                                $data['credit'] = $data['tax2'];
                                $data['account_no'] = $client->provincial_tax;
                                $lastInsertedEditNo++;
                                $data['editNo'] = $lastInsertedEditNo;
                                $Eno = DB::table('journals')->insertGetId($data);
                                array_push($editNo_, $Eno);
                            }
                        } else {
                            if (($data['tax1'] <= 0 || $data['tax1'] == '') && ($data['tax2'] <= 0 || $data['tax2'] == '')) {
                                $data['debit'] = $data['original_debit'];
                                $data['credit'] = $data['original_credit'];
                                $data['account_no'] = $data['original_account'];
                                $lastInsertedEditNo++;
                                $data['editNo'] = $lastInsertedEditNo;
                                $Eno = DB::table("journals")->insertGetId($data);
                                array_push($editNo_, $Eno);
                            }
                        }
                    }
                }
            }
        } else {
            $lastInsertedEditNo++;
            $data['editNo'] = $lastInsertedEditNo;
            $Eno = DB::table("journals")->insertGetId($data);
            array_push($editNo_, $Eno);
        }
        foreach ($editNo_ as $e) {
            DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Added ', 'journal_id' => $e]);
        }

        return response()->json([
            "count" => count($editNo_),
            "edits" => sort($editNo_),
            "lastInserted" => $lastInsertedEditNo,
        ]);
    }
    public function UndoAddedJournals(Request $request)
    {
        if ($request->post('edits') != '') {
            $edits = json_decode($request->post('edits'));
            DB::table('journals')->whereIn("edit_no", $edits)->update([
                "is_deleted" => 1,
                "deleted" => date("Y-m-d H:i:s"),
            ]);
            foreach ($edits as $e) {
                DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored Deleted Journal.', 'journal_id' => $e]);
            }
        }
        return response()->json();
    }
    public function UpdateJournal(Request $request)
    {
        $id = $request->edit_no;



        $data = [
            "client" => $request->post('pp_client_edit'),
            "month" => $request->post('pp_month_edit'),
            "year" => $request->post('pp_year_edit'),
            "fyear" => $request->post('pp_fyear_edit'),
            "period" => $request->post('pp_period_edit'),
            "account_no" => $request->post('dt_account_edit'),
            "source" => $request->post('dt_source_code_edit'),
            "ref_no" => $request->post('dt_ref_edit'),
            "description" => $request->post('dt_description_edit'),
            "gl_date" => $request->post('translation_edit'),
            "date" => $request->post('dt_date_edit'),
            "debit" => $request->post('amnt_debit_edit') ?? 0,
            "credit" => $request->post('amnt_credit_edit') ?? 0,

            'updated_at' => date('Y-m-d H:i:s'),
            'edit_by' => Auth::user()->id,
        ];
        DB::Table('journals')->where('edit_no', $id)->update($data);
        if ($request->post('_submit_type') != '1') {

            DB::table('journal_comments')->where('journal_id', $id)->delete();
            DB::table('journal_attachments')->where('journal_id', $id)->delete();



            $attachment_array = $request->attachmentArray;
            if (isset($request->attachmentArray)) {
                foreach ($attachment_array as $a) {
                    $a = json_decode($a);


                    DB::table('journal_attachments')->insert([
                        'journal_id' => $id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'attachment' => $a->attachment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }

            $commentArray = $request->commentArray;
            if (isset($request->commentArray)) {
                foreach ($commentArray as $a) {
                    $a = json_decode($a);


                    DB::table('journal_comments')->insert([
                        'journal_id' => $id,
                        'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                        'comment' => $a->comment,
                        'name' => $a->name,
                        'added_by' => Auth::id(),
                    ]);
                }
            }
        }
        DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Updated ', 'journal_id' => $id]);
        Session::flash("success", "Journal saved successfully");
        return response()->json('Journal saved successfully');
    }
    private function getMonthName($monthNumber)
    {
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        if (array_key_exists($monthNumber, $monthNames)) {
            return $monthNames[$monthNumber];
        } else {
            return 'Invalid month number';
        }
    }
    public function getClientJournalEditContent(Request $request, $edit_no)
    {
        $html = '';

        $q = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.edit_no', $edit_no)
            ->leftJoin("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->leftJoin("clients_gifi as g", function ($join) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where("g.is_deleted", 0);
            })
            ->select("j.*", "c.firstname", "c.lastname", "sc.source_code", "sc.source_description", "g.description as account_description")
            ->first();


        $clients = DB::table('clients')->where('is_deleted', 0)->orderByDesc('id')->get();
        $html .= '<input type="hidden" name="edit_no" value=' . $q->edit_no . ' >';

        $html .= '<div class="row justify-content- form-group  push d-none">


                                    <div class="col-lg-2">
                                        <label class="col-form-label">Clients</label>
                                    </div>

                                    <div class="col-lg-8">

                                        <select type="" name="pp_client_edit" class="form-control" placeholder="" > ';
        foreach ($clients as $c) {
            $html .= '<option remittance="' . $c->tax_remittance . '" fiscal-start="' . $c->fiscal_start . '" value="' . $c->id . '" ' . ($c->id == $q->client ? 'selected' : '') . '>' . $c->company . '</option>';
        }
        $html .= '</select>
                                    </div>

                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-2">
                                        <label class="col-form-label">Edit#</label>
                                    </div>
                                    <div class="col-lg-3">
                                    <div class="bubble-white-new2 w-100 bubble-text-first">
                                    #' . $q->editNo . '
                                    </div>
                                    </div>
                                </div>
                                <div class="row form-group  ">
                                    <div class="col-lg-2">

                                        <label class="col-form-label">Month</label>

                                    </div>

                                    <div class="col-lg-3 ">

                                        <select type="" name="pp_month_edit" class="form-control" placeholder="" >
                                                ';
        for ($m = 1; $m <= 12; $m++) {
            $html .= '<option value="' . $m . '" ' . ($m == $q->month ? 'selected' : '') . '>' . $this->getMonthName($m) . '</option>';
        }
        $html .= '</select>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="bubble-white-new2 w-100 bubble-text-first pp_period_edit">
                                        Period ' . $q->period . '
                                        </div>
                                        <input type="hidden" name="pp_period_edit" value="' . $q->period . '">
                                    </div>


                                </div>
                                <div class="row form-group  ">
                                    <div class="col-lg-2">

                                        <label class="col-form-label">Year</label>

                                    </div>

                                    <div class="col-lg-3 ">

                                        <select type="" name="pp_year_edit" class="form-control" placeholder="" > ';
        for ($y = (intval(date("Y")) + 1); $y >= 1930; $y--) {
            $html .= '<option value="' . $y . '" ' . ($y == $q->year ? 'selected' : '') . '>' . $y . '</option>';
        }
        $html .= '</select>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="bubble-white-new2 w-100 bubble-text-first pp_fyear_edit">
                                        Fiscal Year ' . $q->fyear . '
                                        </div>
                                        <input type="hidden" name="pp_fyear_edit" value="' . $q->fyear . '">
                                    </div>

                                </div>';

        $clientsGifi = DB::table('clients_gifi')->where('is_deleted', 0)->where('client_id', $q->client)->pluck('account_no')->toArray();
        $html .= '

                                <div class="row ">
                                    <label class="col-sm-2 col-form-label"
                                        for="example-hf-email">Account#</label>
                                    <div class="col-sm-3  form-group">
                                        <input type="" class="form-control" id="dt_account_edit" name="dt_account_edit"
                                            placeholder="4500" list="dt_account_description_list3" value="' . $q->account_no . '">

                                            <datalist id="dt_account_description_list3">';
        foreach ($clientsGifi as $cg) {
            $html .= '<option value="' . $cg . '"/>';
        }
        $html .= '</datalist>
                                    </div>
                                    <div class="col-sm-7 form-group">
                                        <div class="bubble-white-new2 w-100 bubble-text-first dt-account-description-edit">' . $q->account_description . '
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">
                                    <label class="col-sm-2 col-form-label" for="example-hf-email">Source</label>
                                    <div class="col-sm-3  form-group">
                                        <select type="text" class="form-control" id="dt_source_code_edit"
                                            name="dt_source_code_edit" placeholder="Salutation">';
        $sources = DB::table('source_code')->where('is_deleted', 0)->get();
        foreach ($sources as $s) {
            $html .= '<option description="' . $s->source_description . '" value="' . $s->id . '" ' . ($s->id == $q->source ? 'selected' : '') . '>' . $s->source_code . '</option>';
        }
        $html .= '</select>
                                    </div>
                                    <div class="col-sm-7 form-group">
                                        <div class="bubble-white-new2 w-100 bubble-text-first dt-source-description-edit">' . $q->source_description . '
                                        </div>
                                    </div>
                                </div>
                                <div class=" row ">
                                    <label class="col-sm-2 col-form-label" for="example-hf-email">Ref#</label>
                                    <div class="col-sm-3  form-group">
                                        <input type="" class="form-control" id="dt_ref_edit" name="dt_ref_edit"
                                            placeholder="00000000" value="' . $q->ref_no . '" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title="00000000" data-original-title="00000000">
                                    </div>
                                    <div class="col-sm-7 form-group">
                                        <div class=" row ">
                                            <label class="col-sm-2 col-form-label"
                                                for="example-hf-email">Date</label>
                                            <div class="col-sm-5">
                                                <input type="" class="form-control" id="dt_date_edit" name="dt_date_edit"
                                                    placeholder="DDMMYYYY" value="' . $q->date . '" data-toggle="tooltip"
                                                    data-trigger="hover" data-placement="top" title="DDMMYYYY" data-original-title="DDMMYYYY">
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="bubble-white-new2 w-100 bubble-text-first translation-edit">
                                                    ' . $q->gl_date . '
                                                </div>
                                                <input type="hidden" name="translation_edit"
                                                    value="' . $q->gl_date . '">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" row ">
                                    <label class="col-sm-2 col-form-label"
                                        for="example-hf-email">Description</label>
                                    <div class="col-sm-10  form-group">
                                        <input type="" class="form-control" list="dt_description_list" id="dt_description_edit"
                                            name="dt_description_edit" placeholder="Journal Description" data-toggle="tooltip"
                                            data-trigger="hover focus" data-placement="top" title="Journal Description"
                                            data-original-title="Journal Description" value="' . $q->description . '">
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label class="col-sm-2 col-form-label"
                                    for="example-hf-email">Debit</label>
                                <div class="col-sm-3  form-group">
                                    <input type="" class="form-control form-debit text-left"
                                        id="amnt_debit_edit" name="amnt_debit_edit" placeholder="0.00"
                                        value="' . $q->debit . '">
                                </div>
                                <label class="col-sm-2 col-form-label"
                                    for="example-hf-email">Credit</label>
                                <div class="col-sm-3  form-group">
                                    <input type="" class="form-control form-credit text-left"
                                        id="amnt_credit_edit" name="amnt_credit_edit" placeholder="0.00"
                                        value="' . $q->credit . '">
                                </div>
                                <div class="col-md-2">
                                                    <button type="submit" class="btn mr-3 btn-new ">Save</button>
                                                </div>
                            </div>

                                ';
        return response()->json($html);
    }
    public function getJournalEditContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.edit_no', $id)
            ->leftJoin("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->leftJoin("clients_gifi as g", function ($join) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where("g.is_deleted", 0);
            })
            ->select("j.*", "c.firstname", "c.lastname", "sc.source_code", "sc.source_description", "g.description as account_description")
            ->first();


        $clients = DB::table('clients')->where('is_deleted', 0)->orderByDesc('id')->get();

        $html .= '
                      <div style="margin-bottom: 0.875rem !important;" class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header py-new-header2" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/icons_2024_02_24/icon-journal-white.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:21px">Edit Journal #' . $q->editNo . '</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" >
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="public/img/paper-clip-white.png" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" >
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="public/img/comment-white.png" width="20px"></a>
                                         </span>


                                              <a href="javascript:;" class="text-white saveContract"  data="' . $id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save"><img src="' . asset('public') . '/icons2/icon-save-white.png" width="24px"> </a>

                                            <a  data-toggle="tooltip" data-trigger="hover"   data="' . $id . '" data-placement="top" title=""   id="btnClose" data-original-title="Close" href="javascript:;" class="text-white btnClose"><img src="' . asset('public') . '/icons2/icon-close-white.png" width="20px"> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full px-0 pt-0 pb-0 -boxed form-journals" style=" padding-left: 18px !important;
                padding-right: 7px !important;"  >
                    <!-- New Post -->
                    <form  id="edit-journal" action="" class="js-validation form-1  " method="POST"   >

                         <input type="hidden" name="attachment_array" id="attachment_array" >
                           <input type="hidden" name="edit_no" value=' . $q->edit_no . ' >';

        $html .= '<div class="block new-block" >
                            <div class="block-header py-0" style="padding-left:7mm;">

                                <a class="  section-header"  >Journal Header
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">
                             <div class="row justify-content-   push">
                             <div class="col-sm-12 pl-4">

                                <div class="row form-group fg-evenly">


                                    <div class="col-lg-2">
                                        <label class="col-form-label">Client</label>
                                    </div>

                                    <div class="col-lg-10">

                                        <select type="" name="pp_client_edit" class="form-control" placeholder="" > ';
        foreach ($clients as $c) {
            $html .= '<option remittance="' . $c->tax_remittance . '" fiscal-start="' . $c->fiscal_start . '" value="' . $c->id . '" ' . ($c->id == $q->client ? 'selected' : '') . '>' . $c->company . '</option>';
        }
        $html .= '</select>
                                    </div>

                                </div>
                                <div class="row form-group fg-evenly ">
                                    <div class="col-lg-2">

                                        <label class="col-form-label">Month</label>

                                    </div>

                                    <div class="col-lg-3 ">

                                        <select type="" name="pp_month_edit" class="form-control" placeholder="" >
                                                ';
        for ($m = 1; $m <= 12; $m++) {
            $html .= '<option value="' . $m . '" ' . ($m == $q->month ? 'selected' : '') . '>' . $this->getMonthName($m) . '</option>';
        }
        $html .= '</select>
                                    </div>

                                    <div class="col-lg-7">
                                        <div class="col-sm-5 col-form-label text-nowrap translation pp_period_edit" style="color: #252525">
                                        Period ' . $q->period . '
                                        </div>
                                        <input type="hidden" name="pp_period_edit" value="' . $q->period . '">
                                    </div>


                                </div>
                                <div class="row form-group fg-evenly  ">
                                    <div class="col-lg-2">

                                        <label class="col-form-label ">Year</label>

                                    </div>

                                    <div class="col-lg-3 ">

                                        <select type="" name="pp_year_edit" class="form-control" placeholder="" > ';
        for ($y = (intval(date("Y")) + 1); $y >= 1930; $y--) {
            $html .= '<option value="' . $y . '" ' . ($y == $q->year ? 'selected' : '') . '>' . $y . '</option>';
        }
        $html .= '</select>
                                    </div>

                                    <div class="col-lg-7">
                                        <div class="col-sm-5 col-form-label text-nowrap translation pp_fyear_edit" style="color: #252525">
                                        Fiscal Year ' . $q->fyear . '
                                        </div>
                                        <input type="hidden" name="pp_fyear_edit" value="' . $q->fyear . '">
                                    </div>

                                </div>

</div>
</div>

                 </div>
             </div><!--EndBlock-->';

        $clientsGifi = DB::table('clients_gifi')->where('is_deleted', 0)->where('client_id', $q->client)->pluck('account_no')->toArray();
        $html .= '<div class="block new-block" >
                            <div class="block-header py-0" style="padding-left:7mm;">

                                <a class="  section-header"  >Journal Entry
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">
                            <div class="row justify-content-   push">
                            <div class="col-lg-12 pl-4">
                                <div class="row ">
                                    <label class="col-sm-2 col-form-label"
                                        for="example-hf-email">Account# &nbsp;  <a href="javascript:void(0);" client-id="' . $q->client . '" class="view-accounts-chart-edit"
                                        id="view-accounts-chart"><img src="' . asset('public') . '/icons2/icon-info.png"
                                            width="16"></a></label>
                                    <div class="col-sm-3  form-group">
                                        <input type="" class="form-control" id="dt_account_edit" name="dt_account_edit"
                                            placeholder="4500" list="dt_account_description_list2" value="' . $q->account_no . '">
                                            <datalist id="dt_account_description_list2">';
        foreach ($clientsGifi as $cg) {
            $html .= '<option value="' . $cg . '"/>';
        }
        $html .= '</datalist>
                                            </div>
                                    <div class="col-sm-7 form-group fg-evenly">
                                        <div class="col-sm-5 col-form-label text-nowrap translation dt-account-description-edit" style="color: #252525">' . $q->account_description . '
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">
                                    <label class="col-sm-2 col-form-label" for="example-hf-email">Source</label>
                                    <div class="col-sm-3  form-group fg-evenly">
                                        <select type="text" class="form-control" id="dt_source_code_edit"
                                            name="dt_source_code_edit" placeholder="Salutation">';
        $sources = DB::table('source_code')->where('is_deleted', 0)->orderBy('source_code', 'ASC')->get();
        foreach ($sources as $s) {
            $html .= '<option description="' . $s->source_description . '" value="' . $s->id . '" ' . ($s->id == $q->source ? 'selected' : '') . '>' . $s->source_code . '</option>';
        }
        $html .= '</select>
                                    </div>
                                    <div class="col-sm-7 form-group fg-evenly">
                                        <div class="col-sm-5 col-form-label text-nowrap translation dt-source-description-edit" style="color: #252525">' . $q->source_description . '
                                        </div>
                                    </div>
                                </div>
                                <div class=" row ">
                                    <label class="col-sm-2 col-form-label" for="example-hf-email">Ref#</label>
                                    <div class="col-sm-3  form-group fg-evenly">
                                        <input type="" class="form-control" id="dt_ref_edit" name="dt_ref_edit"
                                            placeholder="00000000" value="' . $q->ref_no . '" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title="00000000" data-original-title="00000000">
                                    </div>
                                    <div class="col-sm-7 form-group fg-evenly">
                                        <div class="row ">
                                            <label class="col-sm-2 col-form-label"
                                                for="example-hf-email">Date</label>
                                            <div class="col-sm-5  ">
                                                <input type="" class="form-control" id="dt_date_edit" name="dt_date_edit"
                                                    placeholder="DDMMYYYY" value="' . $q->date . '" data-toggle="tooltip"
                                                    data-trigger="hover" data-placement="top" title="DDMMYYYY" data-original-title="DDMMYYYY">
                                            </div>
                                            <div class="col-sm-5  ">
                                                <div class="col-sm-5 col-form-label text-nowrap translation translation-edit" style="color: #252525">
                                                    ' . $q->gl_date . '
                                                </div>
                                                <input type="hidden" name="translation_edit"
                                                    value="' . $q->gl_date . '">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" row ">
                                    <label class="col-sm-2 col-form-label"
                                        for="example-hf-email">Description</label>
                                    <div class="col-sm-10  form-group fg-evenly">
                                        <input type="" class="form-control" list="dt_description_list" id="dt_description_edit"
                                            name="dt_description_edit" placeholder="Journal Description" data-toggle="tooltip"
                                            data-trigger="hover focus" data-placement="top" title="Journal Description"
                                            data-original-title="Journal Description" value="' . $q->description . '">
                                    </div>
                                </div>
                            </div>
                        </div>
                            </div>
                    </div><!--EndBlock-->';


        $html .= '<div class="block new-block" >
                            <div class="block-header py-0" style="padding-left:7mm;">

                                <a class="  section-header"  >Amount
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">
                            <div class="row justify-content-   push">
                            <div class="col-md-12 row">
                                <div class="col-md-10 pl-4">
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label"
                                            for="example-hf-email">Debit</label>
                                        <div class="col-sm-4  form-group fg-evenly">
                                            <input type="" class="form-control form-debit text-left"
                                                id="amnt_debit_edit" name="amnt_debit_edit" placeholder="0.00"
                                                value="' . $q->debit . '">
                                        </div>
                                        <label class="col-sm-2 col-form-label"
                                            for="example-hf-email">Credit</label>
                                        <div class="col-sm-4  form-group fg-evenly">
                                            <input type="" class="form-control form-credit text-left"
                                                id="amnt_credit_edit" name="amnt_credit_edit" placeholder="0.00"
                                                value="' . $q->credit . '">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>

                            </div>
                    </div><!--EndBlock-->';



        $html .= '</div>
     </div>

     <div class="block new-block  commentDiv d-none ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Comments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content" id="commentBlock">



 </div>
 </div>


     <div class="block new-block attachmentDiv d-none   ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Attachments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content row" id="attachmentBlock">



 </div>

                        </div>
                    </form>
                    </div>
      ';

        echo $html;
    }
    public function getJournalContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.edit_no', $id)
            ->leftJoin("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->leftJoin("clients_gifi as g", function ($join) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where('g.is_deleted', 0);
            })
            ->select("j.*", "c.firstname", "c.lastname", "c.company", "sc.source_code", "sc.source_description", "g.description as account_description")
            ->first();
        $user = DB::table('users')->where('id', $q->edit_by)->first();

        $html .= '<div style="margin-bottom: 0.875rem !important;" class="block card-round   ' . ($q->journal_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . '  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/icons_2024_02_24/icon-journal-white.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:20px">Edit No. #' . $q->editNo . '</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . (@$q->updated_at ? date('Y-M-d', strtotime($q->updated_at)) : '') . ' by ' . @$user->firstname . ' ' . @$user->lastname . '</p>
                                    </div>
                                </div>';



        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="javascript:;" d class="text-white   btnEdit MajorEdit" Fyear="' . $q->fyear . '" client="' . $q->client . '" data="' . $q->edit_no . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>

                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->edit_no . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
        }



        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

        $html .= '</div></div>
                            </div>
                        </div>';

        $html .= '  <div class="block new-block position-relative  5">
                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Journal Header
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

                             <div class="row">

                                <div class="col-sm-12">
                                    <div class="form-group fg-evenly row">
                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label">Client</div>
                                       </div>

                                        <div class="col-sm-10">

                                           <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->edit_no . '">' . $q->company . '</div>

                                        </div>

                                    </div>



                                    <div class="form-group fg-evenly row">
                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label" data="' . $q->edit_no . '">Month</div>
                                        </div>

                                        <div class="col-sm-4">

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $this->getMonthName($q->month) . '</div>

                                        </div>

                                        <div class="col-sm-2">
                                        <div class=" -new col-form-label" data="' . $q->edit_no . '">Period</div>
                                     </div>

                                     <div class="col-sm-4">

                                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->period . '</div>

                                     </div>

                                    </div>

                                    <div class="form-group fg-evenly row">
                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label" data="' . $q->edit_no . '">Year</div>
                                       </div>

                                        <div class="col-sm-4">

                                            <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->year . '</div>

                                        </div>

                                        <div class="col-sm-2">
                                            <div class=" -new col-form-label" data="' . $q->edit_no . '">Fiscal Year</div>
                                        </div>

                                        <div class="col-sm-4">

                                            <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->fyear . '</div>

                                        </div>

                                    </div>



                                  </div>

                            </div>

                    </div>

             </div><!--End-->';



        $html .= '  <div class="block new-block position-relative  5">
             <div class="block-header py-0" style="padding-left:7mm;">

                  <a class="  section-header"  >Journal Entry
                 </a>

                 <div class="block-options">

                 </div>
             </div>
             <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

              <div class="row">

                 <div class="col-sm-12">
                     <div class="form-group fg-evenly row">
                         <div class="col-sm-2">
                            <div class=" -new col-form-label">Account</div>
                        </div>

                         <div class="col-sm-4">

                            <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->edit_no . '">' . $q->account_no . '</div>

                         </div>

                         <div class="col-sm-6">
                         <div class=" -new col-form-label" data="' . $q->edit_no . '">' . $q->account_description . '</div>
                         <!--
                            <div class="bubble-white-new2 w-100 bubble-text-first provinceText" data="' . $q->edit_no . '">' . $q->account_description . '</div>
                            -->
                         </div>

                     </div>



                     <div class="form-group fg-evenly row">
                         <div class="col-sm-2">
                            <div class=" -new col-form-label" data="' . $q->edit_no . '">Source</div>
                         </div>

                         <div class="col-sm-4">

                            <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->source_code . '</div>

                         </div>

                         <div class="col-sm-6">
                         <div class=" -new col-form-label" data="' . $q->edit_no . '">' . $q->source_description . '</div>
                         <!--
                            <div class="bubble-white-new2 w-100 bubble-text-first" data="' . $q->edit_no . '">' . $q->source_description . '</div>
                            -->
                         </div>

                     </div>

                     <div class="form-group fg-evenly row">
                         <div class="col-sm-2">
                            <div class=" -new col-form-label" data="' . $q->edit_no . '">Reference#</div>
                        </div>

                         <div class="col-sm-4">

                             <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->ref_no . '</div>

                         </div>

                         <div class="col-sm-2">
                            <div class=" -new col-form-label" data="' . $q->edit_no . '">Date</div>
                        </div>

                         <div class="col-sm-4">

                             <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->gl_date . '</div>

                         </div>

                     </div>

                     <div class="form-group fg-evenly row">
                         <div class="col-sm-2">
                            <div class=" -new col-form-label" data="' . $q->edit_no . '">Description</div>
                        </div>

                         <div class="col-sm-10">

                            <div class="bubble-white-new1 bubble-text-first" data="' . $q->edit_no . '">' . $q->description . '</div>

                         </div>

                     </div>

                   </div>

             </div>

     </div>

</div><!--End-->';


        $html .= '  <div class="block new-block position-relative  5">
<div class="block-header py-0" style="padding-left:7mm;">

     <a class="  section-header"  >Amount
    </a>

    <div class="block-options">

    </div>
</div>
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

 <div class="row">

    <div class="col-sm-12">
        <div class="form-group row fg-evenly">
            <div class="col-sm-2">
               <div class=" -new col-form-label">Debit</div>
           </div>

            <div class="col-sm-4">

               <div class="bubble-debit bubble-debit-wval provinceText" style="font-family: Jura !important;text-align: left !important;" data="' . $q->edit_no . '">' . number_format($q->debit, 2) . '</div>

            </div>

            <div class="col-sm-2">
                <div class=" -new col-form-label">Credit</div>
            </div>

            <div class="col-sm-4">

                <div class="bubble-credit bubble-credit-wval provinceText" style="font-family: Jura !important;text-align: left !important;" data="' . $q->edit_no . '">' . number_format($q->credit, 2) . '</div>

            </div>

        </div>



      </div>

</div>

</div>

</div><!--End-->';




        $html .=  '</div>
                  </div>
                    </div>
                      </div>

';




        $contract = DB::table('journal_comments as c')
        ->where('c.journal_id', $q->edit_no)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Comments</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class="form-group">
                                                            <a class="section-header">Comments</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                        if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                        } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                            <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                        }


                                                          $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                                                        </span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
                                                        </p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('journal_attachments as a')
        ->where('a.journal_id', $q->edit_no)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative">
                                                <!--<div class="top-div text-capitalize">Attachments</div>-->

                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock">
                                                          <div class="col-sm-12 px-0">
                                                             <div class="form-group">
                                                                <a class="section-header">Attachments</a>
                                                             </div>
                                                          </div>
                                                          <div class="col-sm-12 row">
                                                          ';
            foreach ($contract as $key => $c) {

                $f = explode('.', $c->attachment);
                $fileExtension = end($f);
                $icon = 'attachment.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }



                $html .= '<div class="col-sm-6 px-0 attach-other-col  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';
                                                    if($c->user_image) {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                    } else {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                        <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                    }

                                                          $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">


                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div></div>

                            </div>';
        }

        $contract = DB::table('journal_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.journal_id', $q->edit_no)->get();


        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Audit Trail</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class="form-group">
                                                            <a class="section-header">Audit Trail</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                    if($c->user_image) {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                    }    else {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                        <img width="30px" src="public/img/profile-white.png?cache=1"> </b></h1>';
                                                    }


                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }



        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }
    public function getClientJournals(Request $request)
    {
        echo $this->clientJournals($request);
    }
    public function JournalBatchDelete(Request $request)
    {
        $journals = json_decode($request->post('journals')) ?? [];
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::table('journals')->whereIn('edit_no', $journals)->update([
            "deleted_at" => date("Y-m-d H:i:s"),
            "is_deleted" => 1,
        ]);
        return response()->json(count($journals));
    }
public function JournalBatchUpdate(Request $request)
{
    $client = $request->post('client');
    $month = $request->post('month');
    $year = $request->post('year');
    $fyear = $request->post('fyear');
    $period = $request->post('period');
    $source = $request->post('source');
    $journals = json_decode($request->post('journals')) ?? [];
    $update = [];
    if($client != "") {
        $update["client"] = $client;
    }
    if($month != "") {
        $update["month"] = $month;
    }
    if($year != "") {
        $update["year"] = $year;
    }
    if($fyear != "") {
        $update["fyear"] = $fyear;
    }
    if($period != "") {
        $update["period"] = $period;
    }
    if($source != "") {
        $update["source"] = $source;
    }
    $count = 0;
    $oldstates = [];
    foreach($journals as $journal) {
        DB::table('journals')->where('edit_no', $journal)->update($update);
        // Store the original old data before updating
        $oldstates[$journal] = DB::table('journals')->where('edit_no', $journal)->first();
        DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Updated ', 'journal_id' => $journal]);
       
        $count++;
    }
    session(['journal_updates' => $oldstates]);
   return response()->json($count);
}
//  Undo Logic i m create a function 
public function UndoJournalBatchUpdate(Request $request)
{
    $oldstates = session('journal_updates', []);
    if (empty($oldstates)) {
        return response()->json(['status' => 'error','message' => 'Nothing to undo']);
    }

    foreach ($oldstates as $journalId => $originalState) {
        DB::table('journals')->where('edit_no', $journalId)->update((array)$originalState);
        DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Update Undone', 'journal_id' => $journalId]);
    }
    session()->forget('journal_updates');
    return response()->json(['status' => 'success', 'message' => 'Batch update undone']);
}
public function clientGetJournalFyears(Request $request)
{
    return response()->json(array_values(array_unique(DB::table('journals')->where('is_deleted', 0)->where('client', $request->post('client'))->pluck('fyear')->toArray())));
}
public function clientCountFYJournals(Request $request)
{
    return response()->json(DB::table('journals')->where('is_deleted', 0)->where('client', $request->post('client'))->where('fyear', $request->post('fyear'))->count());
}
public function JournalFYReIndex(Request $request)
{
    $validated = $request->validate([
        "reindex_client" => 'required',
        "reindex_fiscal_year" => 'required',
    ]);
    if($validated) {
        $journals = DB::table('journals')->where('is_deleted', 0)->where('client', $request->input('reindex_client'))->where('fyear', $request->input('reindex_fiscal_year'))->orderBy('created_at', 'asc')->pluck('edit_no')->toArray();
        $edit_no = 1;
        foreach($journals as $j) {
            DB::table('journals')->where('edit_no', $j)->update([
                "editNo" => $edit_no,
            ]);
            DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Reindexed ', 'journal_id' => $j]);
            $edit_no++;
        }
        return redirect()->back()->with('success', $edit_no-1 . ' journals reindexed');
    }
}

    private function clientJournals(Request $request)
    {
        $limit = @$request->limitVal != "" ? $request->limitVal : 10;
        $client_id = $request->client_id;
        $month = $request->month;
        $year = $request->year;
        $fyear = $request->fyear;
        $searchVal = @$request->searchVal;
        $dateCreated = $request->dateCreated;
        $periodsArr = $request->periodsArr ?? [];
        $accountsArr = $request->accountsArr ?? [];
        $sourcesArr = $request->sourcesArr ?? [];
        $refsArr = $request->refsArr ?? [];
        $journals = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.client', $client_id)
            ->where('j.fyear', $fyear)
            ->where(function ($query) use ($searchVal, $month, $dateCreated, $periodsArr, $accountsArr, $sourcesArr, $refsArr) {
                if (!empty($searchVal)) {
                    $query->where('j.ref_no', 'like', '%' . $searchVal . '%');
                    $query->orWhere('j.description', 'like', '%' . $searchVal . '%');
                }
                if(!empty($dateCreated)) {

                    $query->where('j.created_at', '>=', date("Y-m-d", strtotime($dateCreated)) . " 00:00:00")
                    ->where('j.created_at', '<=', date("Y-m-d", strtotime($dateCreated)) . " 23:59:59");
                }
                if (count($periodsArr) > 0) {
                    //    $query->where('j.month', $month);

                    $query->whereIn('j.period', $periodsArr);
                }
                if (count($accountsArr) > 0) {
                    $query->whereIn("account_no", $accountsArr);
                }
                if (count($sourcesArr) > 0) {
                    $query->whereIn("source", $sourcesArr);
                }
                if (count($refsArr) > 0) {
                    $query->whereIn("ref_no", $refsArr);
                }
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                ->where("c.is_deleted", 0);
            })
            ->select(
                "j.*",
                "sc.source_code as source_code",
                "c.logo"
            )
            ->orderBy("j.edit_no", "desc")
            ->paginate($limit, ['*'], 'journalPage', $request->journalPage);




//         $html = '
//             <div class="con   no-print page-header py-1  mb-3" style="border-radius:10px;height:54.2px !important;" id="">
//                 <!-- Full Table -->
//                 <div class="b   mb-0  ">

//                     <div class="block-content pt-0 mt-0">

//                         <div class="TopArea" style="position: sticky;
//                             padding-top: 5px;
//                             z-index: 1000;
//     padding-bottom: 5px;">
//     <div class="row" >
//    <div class="col-sm-6">

//  <!--<form class="push mb-0"   method="get" id="form-search"  >-->

//                                 <div class="input-group">
//                                     <input type="text"  class="form-control searchNew w-75" style="height:36px !important;" name="client-journal-search" value="' . $searchVal . '" data="' . $client_id . '" placeholder="Search Journals">
//                                     <div class="input-group-append" style="width: 31px !important;">
//                                         <span class="input-group-text">
//                                               <img src="' . asset('public/img/ui-icon-search.png') . '" width="15px">
//                                         </span>
//                                     </div>
//                                 </div>
//                                  <div class="    float-left " role="tab" id="accordion2_h1">


//                                    <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->

//                                             </div>
//                             <!--</form>-->

// </div>
// <div class="col-sm-6 pl-0 d-flex align-items-center"  style="">

//      <span data-toggle="modal" id="btnFilterClientJournals" data-client-id="' . $client_id . '" data-bs-target="#filterClientJournalModal" data-target="#filterClientJournalModal">
//       <button type="button" class="btn btn-dual d1   "   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Filter Journals"   >
//                            <img src="' . asset('public/img/ui-icon-filters.png') . '" width="19px">
//                         </button>



//                     </span>


//                     <span>
//                     <button type="button" class="btn btn-dual d1   batch-selection"  data-selected="0"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Select Journals"   >
//                     <img src="' . asset('public/batch_icons/icon-journals-select-all.png') . '" width="19px">
//                  </button></span>

//                  <span>
//                  <button type="button" class="btn btn-dual d1   batch-update"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Batch Update"   >
//                  <img src="' . asset('public/batch_icons/icon-journals-batchupdate.png') . '" width="19px">
//               </button></span>
//               <select name="client-journal-limit" class="float-right form-control ml-auto   px-0" style="width:auto">
//               <option value="10" '.(@$limit==10?'selected':'').'>10</option>
//               <option value="25" '.(@$limit==25?'selected':'').'>25</option>
//               <option value="50" '.(@$limit==50?'selected':'').'>50</option>
//               <option value="100" '.(@$limit==100?'selected':'').'>100</option>
//               <option value="200" '.(@$limit==200?'selected':'').'>200</option>
//               <option value="300" '.(@$limit==300?'selected':'').'>300</option>
//               <option value="400" '.(@$limit==400?'selected':'').'>400</option>
//               <option value="500" '.(@$limit==500?'selected':'').'>500</option>
//           </select>

//                     ';

//         $html .= '</div></div></div></div></div></div></div></div>';



$html = '';



$html .=' <div class="client-journals-view"><!--startblock-->';
        foreach ($journals as $q) {
            $amount_clr = '';
            $amount = 0;
            $symbol = '';
            if ($q->debit > $q->credit) {
                $amount_clr = '#4194F6';
                $amount = $q->debit;
                $symbol = 'DR';
            } else {
                $amount_clr = '#E54643';
                $amount = $q->credit;
                $symbol = 'CR';
            }



            $html .= '
                <div class="block block-rounded   table-block-new mb-2 pb-0 view-------Content  -  journal-view" data="'.$q->edit_no.'"
                    style="cursor:pointer;padding-left: 0px !important;padding-right: 0px !important;">

                    <div class="block-content pt-1 pb-1 pl-1 d-flex  position-relative" style="">
                        <div class=" justify-content-center align-items-center  d-flex mr-1" style="width: 20%;float:: left;padding: 7px;">';
                            if($q->logo!='')
                            {$html.='<img src="public/client_logos/'.$q->logo.'" class="rounded-circle  "
                                 style="object-fit: cover;width: 75px;height: 75px;">';
                            }else{
                                $html.='<img src="public/img/image-default.png" class="rounded- circle  " style="width: 100%;object-fit: cover;">';
                            }
                        $html.='</div>
                        <div class="d-flex justify-content-between" style="width: 70%;">
                            <div class="d-flex flex-column " style="width:calc(100% - 15px);">
                                <div>

                                    <span class="font-12pt mb-0 text-truncate font-w600 c1" style="font-family: Calibri;color:#4194F6 !important;">'.substr($q->ref_no, 0, 8).' / '.date("d-M-Y", strtotime($q->gl_date)).'</span>
                                </div>
                            <div class="d-flex flex-row">
                                <span style="
                                border-style: dashed !important;
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                                color: #fff;
                                border:1px solid #fff;
                                background-color: #4194F6;
                                border-radius: 2px;
                                line-height: 1.6;
                                padding-top: 2px;
                                padding-bottom: 2px;
                                padding-left: 5px;
                                padding-right: 5px;
                                margin-right: 0.375rem;" >#'.$q->editNo.'</span>
                                <span style="overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                                color: #262626;
                                border-style: dashed !important;
                                min-width: calc(100% - 160px);
                                border:1px solid #262626;
                                background-color: #BFBFBF;
                                border-radius: 2px;
                                line-height: 1.6;
                                padding-top: 2px;
                                padding-bottom: 2px;
                                padding-left: 5px;
                                padding-right: 5px;">'.$q->fyear.' - Period '.$q->period.'</span>
                                </div>
                                <div class="d-flex flex-row" style="padding-top: 3px;">
                                    <div>
                                        <span style="line-height: 1.6;
                                        font-family: Calibri;
                                        width: fit-content;
                                        font-size: 11pt;
                                        color:#3F3F3F;
                                        border:1px solid #3F3F3F;
                                        border-radius: 2px;

                                        margin-right: 0.675rem;" class="px-2">'.$q->account_no.'</span>
                                    </div>
                                    <div style="overflow: hidden;
                                    text-overflow: ellipsis;
                                    width: fit-content;
                                    line-height: 1.6;
                                    white-space: nowrap;
                                    font-size: 11pt;
                                    font-family: Calibri;">
                                        <span>'.$q->description.'</span>
                                    </div>
                                </div>
                            </div>

                                <div style="position: absolute;right: 10px;top: 10px;">
                                    <span style="float:right;
                                    font-family: Calibri;
                                    line-height: 1.5 !important;
                                    color: #FFF;
                                    background-color: '.$amount_clr.';
                                    min-width: 100%;
                                font-weight: 600!important;
                                border: 1px solid #D9D9D9;
                                text-align:center;
                                align-items: center;
                                border-radius: 5px;
                                justify-content: center;
                                display: flex;
                                padding-left: 15px;
                                padding-right: 15px;
                                padding-top: 2px;
                                padding-bottom: 2px;
                                display: block;
                                line-height: 1;
                                text-align: center;
                                border-radius: 3px;
                                font-size: 11pt;">$'.number_format($amount, 2).'</span>
                                </div>
                                <div class="d-flex flex-row justify-content-end" style="margin-top: 20px;position: absolute;right: 10px;bottom: 9px;">';

                        if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {
                        $html .= '<div class="ActionIcon  ml-2    " style="border-radius: 1rem;"  >
                                    <a    href="javascript:;" data="' . $q->edit_no . '" data-client-id="' . $q->client . '"   class="btnEditClientJournal"   >
                                        <img src="' . asset('public') . '/icons2/icon-edit-grey.png?cache=1" width="25px"  >
                                    </a>
                                </div>

                                <div class="ActionIcon  ml-2     " style="border-radius: 1rem;"  >
                                    <a    href="javascript:;"  class="btnDeleteClientJournal" data="' . $q->edit_no . '" data-client-id="' . $q->client . '"  >
                                        <img src="' . asset('public') . '/icons2/icon-delete-grey.png?cache=1"    width="25px"   >
                                    </a>
                                </div>';
                        }
            }
                                $html.='</div>

                        </div>
                    </div>
                </div>';
        }

        if($journals->lastPage() > 1) {

$html .= '<div class="container-fluid journal-view-pagination-footer px-0" style="">
<div class="con   no-print page-header py-1  mb-3" style="border-radius:10px;" id="">
<!-- Full Table -->
<div class="b   mb-0  ">

    <div class="block-content pt-0 mt-0">

<div class="TopArea" style="position: sticky;
padding-top: 5px;
z-index: 1000;

padding-bottom: 5px;">
<div class="row">

<div class="col-sm-12 d-flex align-items-center justify-content-center" style="">';

$html .= '
        <nav aria-label="Photos Search Navigation">
        <ul class="pagination client-journals-pagination">

            <!-- Previous Page Link -->';
            if ($journals->currentPage() > 1)
             {
                $html .= '<li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                    <a class="page-link" href="javascript:void(0)" style="border-left:1px solid">&lt;</a>
                </li>';
             }else{
                $html .= '<li class="page-item">
                    <a class="page-link journal-page-link" href="javascript:void(0)" page-no="'.($journals->currentPage() - 1).'" rel="prev" aria-label="« Previous" style="border-left:1px solid">&lt;</a>
                </li>';
            }

            $html .= '<!-- Pagination Elements -->';

            $html .= '<!--<li class="page-item disabled" aria-disabled="true"><a class="page-link">...</a></li>-->';

            for($i = max($journals->currentPage()-2, 1); $i <= min(max($journals->currentPage()-2, 1)+4,$journals->lastPage()); $i++)
            {
                if($journals->currentPage() == $i)
                {
                    $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="javascript:void(0);">' . $i . '</a></li>';
                } else {
                    $html .= '<li class="page-item "><a class="page-link journal-page-link" page-no="' . $i . '" href="javascript:void(0)">' . $i . '</a></li>';
                }
            }

            $html .= '<!-- Next Page Link -->';
            if ($journals->currentPage() < $journals->lastPage())
             {
                $html .= '<li class="page-item">
                <a class="page-link journal-page-link" href="javascript:;" page-no="'.($journals->currentPage()+1).'" rel="next" aria-label="Next »" style="border-right:1px solid">&gt;</a>
            </li>';
             }else {
                $html .= '<li class="page-item disabled" aria-disabled="true">
                    <a class="page-link" href="javascript:void(0)" style="border-right:1px solid">&gt;</a>
                </li>';
            }

        $html .= '</ul>
    </nav>';

$html .= '</div></div></div></div></div></div> </div>';

    }

        return $html;
    }
    public function AddClients()
    {

        return view('AddClients');
    }

    public function EditClients()
    {


        return view('EditClients');
    }
    public function Notifications()
    {


        return view('Notifications');
    }

    public function Settings()
    {


        return view('Settings');
    }

    public function Taxes()
    {


        return view('Taxes');
    }


    public function InsertTax(Request $request)
    {

        $check = DB::Table('tax_rate')->where('province', $request->province)->update(['tax_rate_status' => 0]);
        $data = [
            'province' => $request->province,
            'tax_label_1' => $request->tax_label_1,
            'tax_label_2' => $request->tax_label_2,
            'tax_rate_1' => $request->tax_rate_1,
            'tax_rate_2' => $request->tax_rate_2,
            'slider' => $request->slider,
            'applied_to_tax1' => $request->applied_to_tax1,
            'tax_rate_status' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,

        ];

        DB::Table('tax_rate')->insert($data);
        $id = DB::getPdo()->lastInsertId();
        DB::table('tax_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Tax Rate Added', 'tax_id' => $id]);

        return redirect()->back()->with('success', 'Tax rate created successfully.');
    }
    public function UpdateTax(Request $request)
    {
        $id = $request->id;



        $data = [
            'province' => $request->province_edit,
            'tax_label_1' => $request->tax_label_1_edit,
            'tax_label_2' => $request->tax_label_2_edit,
            'tax_rate_1' => $request->tax_rate_1_edit,
            'tax_rate_2' => $request->tax_rate_2_edit,
            'slider' => $request->slider_edit,
            'applied_to_tax1' => $request->applied_to_tax1_edit,

            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,

        ];

        DB::Table('tax_rate')->where('id', $id)->update($data);
        DB::table('tax_comments')->where('tax_id', $request->id)->delete();
        DB::table('tax_attachments')->where('tax_id', $request->id)->delete();



        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);


                DB::table('tax_attachments')->insert([
                    'tax_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('tax_comments')->insert([
                    'tax_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        DB::table('tax_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Tax Rate Updated ', 'tax_id' => $id]);
        Session::flash("success", "Tax saved successfully");
        return response()->json('Tax saved successfully');
    }







    public function Source()
    {


        return view('Source');
    }

    public function checkGifi(Request $request)
    {
        $qry = DB::Table('gifi')->where('account_no', $request->account_no)->where('is_deleted', 0)->first();
        if ($qry != '') {
            echo 1;
        } else {
            echo "";
        }
    }

    public function checkClientGifi(Request $request)
    {
        $qry = DB::Table('clients_gifi')
            ->where('account_no', $request->account_no)
            ->where('client_id', $request->client_id)
            ->where('is_deleted', 0)->first();
        if ($qry != '') {
            echo 1;
        } else {
            echo "";
        }
    }



    public function InsertSource(Request $request)
    {


        $data = [
            'source_code' => $request->source_code,
            'source_description' => $request->source_description,
            'source_code_status' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,

        ];

        DB::Table('source_code')->insert($data);
        $id = DB::getPdo()->lastInsertId();
        DB::table('source_code_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Source Code Added', 'source_code_id' => $id]);

        return redirect()->back()->with('success', 'Source code created successfully');
    }
    public function UpdateSource(Request $request)
    {
        $id = $request->id;



        $data = [
            'source_code' => $request->source_code_edit,
            'source_description' => $request->source_description_edit,


            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,

        ];
        if (DB::table('source_code')->where('is_deleted', 0)->where('source_code', $request->source_code_edit)->where('id', '!=', $id)->where('source_code_status', 1)->exists()) {
            return response()->json(1);
        }
        DB::Table('source_code')->where('id', $id)->update($data);
        DB::table('source_code_comments')->where('source_code_id', $request->id)->delete();
        DB::table('source_code_attachments')->where('source_code_id', $request->id)->delete();



        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);


                DB::table('source_code_attachments')->insert([
                    'source_code_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('source_code_comments')->insert([
                    'source_code_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        DB::table('source_code_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Source Code Updated ', 'source_code_id' => $id]);
        Session::flash("success", "Source saved successfully");
        return response()->json('Source Code saved successfully');
    }





    public function Gifi()
    {
        return view('Gifi');
    }

    public function InsertClientAccount(Request $request)
    {


        $data = [
            'account_type' => $request->post('account_type'),
            'sub_type' => $request->post('sub_account_type'),
            'account_no' => $request->post('account_no'),
            'description' => $request->post('description'),
            'note' => $request->post('note'),
            'created_at' => date('Y-m-d H:i:s'),
            'client_id' => $request->post('client_id'),
            //'gifi_id' => $request->gifi_id,
        ];

        DB::Table('clients_gifi')->insert($data);
        $id = DB::getPdo()->lastInsertId();
        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client Account Added', 'client_id' => $request->post('client_id')]);
        return response()->json($request->post('client_id'));
        //return redirect()->back()->with('success', 'Client Account saved successfully');
    }


    public function EditClientAccount(Request $request)
    {
        $data = [
            'account_type' => $request->post('account_type'),
            'sub_type' => $request->post('sub_account_type'),
            //'account_no' => $request->post('account_no'),
            'description' => $request->post('description'),
            'note' => $request->post('note'),
            'updated_at' => date('Y-m-d H:i:s'),
            //'gifi_id' => $request->gifi_id,
        ];

        DB::Table('clients_gifi')->where('id', $request->post('client_account_id'))->update($data);
        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client Account Saved', 'client_id' => $request->post('client_id')]);
        return response()->json($request->post('client_id'));
    }




    public function InsertGifi(Request $request)
    {


        $data = [
            'account_type' => $request->account_type,
            'sub_type' => $request->sub_account_type,
            'account_no' => $request->account_no,
            'description' => $request->description,
            'note' => $request->note,
            'gifi_status' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,

        ];

        DB::Table('gifi')->insert($data);
        $id = DB::getPdo()->lastInsertId();
        DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Gifi Account Added', 'gifi_id' => $id]);

        return redirect()->back()->with('success', 'Gifi account created successfully');
    }
    public function UpdateGifi(Request $request)
    {
        $id = $request->id;



        $data = [
            'account_type' => $request->account_type_edit,
            'sub_type' => $request->sub_account_type_edit,
            'account_no' => $request->account_no_edit,
            'description' => $request->description_edit,
            'note' => $request->note_edit,


            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,

        ];

        DB::Table('gifi')->where('id', $id)->update($data);
        DB::table('gifi_comments')->where('gifi_id', $request->id)->delete();
        DB::table('gifi_attachments')->where('gifi_id', $request->id)->delete();



        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);


                DB::table('gifi_attachments')->insert([
                    'gifi_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('gifi_comments')->insert([
                    'gifi_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }
        DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Gifi Updated ', 'gifi_id' => $id]);
        Session::flash("success", "Gifi saved successfully");
        return response()->json('Gifi saved successfully');
    }



    public function UpdateSystemSettings(Request $request)
    {

        $check = DB::Table('system_settings')->first();
        $imageName = @$check->logo ? @@$check->logo : "";
        if($request->hasFile("logo")) {
            $imageName = mt_rand(1,1000).''.time().'.'.$request->file("logo")->getClientOriginalExtension();
            $request->file("logo")->move(public_path(). '/company_logos', $imageName);
        }
        $data = [
            'salutation' => $request->system_salutation,
            'firstname' => $request->system_firstname,
            'lastname' => $request->system_lastname,
            'designation' => $request->system_designation,
            'company' => $request->system_company_name,
            'email' => $request->system_email,
            'telephone' => $request->system_telephone,
            'fax' => $request->system_fax,
            'website' => $request->system_website,
            'country' => $request->system_country,
            'address' => $request->system_address,
            'city' => $request->system_city,
            'province' => $request->system_province,
            'postal_code' => $request->system_postal_code,
            "logo" => $imageName,
            'tax_remittance' => $request->system_tax_remittance,
            "federal_corp_tax_perc" => $request->system_federal_corp_tax_perc,
            "federal_corp_tax" => $request->system_federal_corp_tax,
            "provincial_corp_tax_perc" => $request->system_provincial_corp_tax_perc,
            "provincial_corp_tax" => $request->system_provincial_corp_tax,
        ];
        if ($check == '') {
            DB::Table('system_settings')->insert($data);
        } else {
            DB::Table('system_settings')->update($data);
        }
        return redirect()->back()->with('success', 'Settings saved successfully');
    }
    public function GetProvince(Request $request)
    {
        $country = $request->country == 'United States' ? $request->country : 'Canada';
        $qry = DB::Table('cities')
            ->where('country_name', $country)
            ->groupBy('state_name')->get();
        return $qry;
    }
    public function UpdateSettings(Request $request)
    {


        DB::Table('notification_settings')->update(['interval_1' => $request->interval_1, 'interval_2' => $request->interval_2, 'interval_3' => $request->interval_3, 'interval_4' => $request->interval_4, 'interval_5' => $request->interval_5, 'interval_6' => $request->interval_6, 'interval_7' => $request->interval_7, 'from_name' => $request->from_name]);
        return redirect()->back()->with('success', 'Settings Updated Successfully');
    }



    public function DeleteClients(Request $request)
    {


        DB::Table('clients')->where('id', $request->id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect('/clients')->with('alert-delete', 'Client Deleted Successfully.|' . $request->id);
    }
    public function UndoDeleteClients(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('clients')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted client account.', 'client_id' => $request->id]);
        return redirect()->back()->with('success', 'Client undeleted successfully.');
    }
    public function ShowClients(Request $request)
    {


        $qry = DB::Table('clients')->where('id', $request->id)->first();
        return response()->json($qry);
    }
    public function getSubAccount(Request $request)
    {

        $qry = DB::Table('sub_account_type')->where('account_type', $request->account)->get();
        return response()->json($qry);
    }



    public function getAttachmentTax(Request $request)
    {
        $qry = DB::table('tax_attachments as a')
        ->where('a.tax_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getCommentsTax(Request $request)
    {
        $qry = DB::table('tax_comments as c')
        ->where('c.tax_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }


    public function getAttachmentSource(Request $request)
    {
        $qry = DB::table('source_code_attachments as a')
        ->where('a.source_code_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getCommentsSource(Request $request)
    {
        $qry = DB::table('source_code_comments as c')
        ->where('c.source_code_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getAttachmentGifi(Request $request)
    {
        $qry = DB::table('gifi_attachments as a')
        ->where('a.gifi_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getCommentsGifi(Request $request)
    {
        $qry = DB::table('gifi_comments as c')
        ->where('c.gifi_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function ImportExcelJournals(Request $request)
    {
        $validated = $request->validate([
            "client" => 'required',
        ]);
        if($validated) {
            try {
                $import = new JournalImport(DB::table('clients')->where('is_deleted', 0)->where('id', $request->input('client'))->first());
                Excel::import($import, $request->file('file')->store('temp'));
                if (count($import->data) > 0) {
                    return redirect()->back()->with('success', count($import->data) . ' journals are added successfully');
                }
            } catch(\Exception $e) {

            }
        }
        return redirect()->back()->with('error', "No journals added");
    }
    public function ImportExcelStandardJournals(Request $request)
    {
        $validated = $request->validate([
            "client" => 'required',
        ]);
        if($validated) {
            try {
                $import = new JournalStandardImport(DB::table('clients')->where('is_deleted', 0)->where('id', $request->input('client'))->first());
                Excel::import($import, $request->file('file')->store('temp'));
                if (count($import->data) > 0) {
                    return redirect()->back()->with('success', count($import->data) . ' journals are added successfully');
                }
            } catch (\Exception $e) {

            }
        }
        return redirect()->back()->with('error', "No journals added");
    }
    public function ImportExcelGIFI(Request $request)
    {
        $import = new GIFIImport;
        Excel::import($import, $request->file('file')->store('temp'));
        if (count($import->data) > 0) {
            return redirect()->back()->with('success', count($import->data) . ' accounts created successfully.');
        }
        return redirect()->back()->with('error', "No accounts added.");
    }

    public function ExportExcelJournals(Request $request)
    {
        return Excel::download(new ExportJournals($request), 'journals.csv');
    }
    public function ExportExcelTrialBalance(Request $request)
    {
          $response = Excel::download(new ExportTrialBalance($request), 'Trail Balance.xlsx');

    // Create a cookie that expires in 1 minute.
    $cookie = new Cookie(
        'downloadStarted', // Name of the cookie
        '1', // Value of the cookie
        time() + 60, // Cookie expiration time (1 minute)
        '/', // Path
        null, // Domain, null means that it applies to all subdomains
        false, // Secure, set to true if you're using HTTPS
        false // HttpOnly, set to true if you want to make the cookie accessible only through the HTTP protocol
    );

    // Add the cookie to the response
    $response->headers->setCookie($cookie);

    return $response;
    }
    public function ExportExcelFinancialStatement(Request $request)
    {

            $response = Excel::download(new ExportFinancialStatement($request), 'Financial Statement.xlsx');

    // Create a cookie that expires in 1 minute.
    $cookie = new Cookie(
        'downloadStarted', // Name of the cookie
        '1', // Value of the cookie
        time() + 60, // Cookie expiration time (1 minute)
        '/', // Path
        null, // Domain, null means that it applies to all subdomains
        false, // Secure, set to true if you're using HTTPS
        false // HttpOnly, set to true if you want to make the cookie accessible only through the HTTP protocol
    );

    // Add the cookie to the response
    $response->headers->setCookie($cookie);


    return $response;


    }
    public function ExportExcelClients(Request $request)
    {

        return Excel::download(new ExportClients($request), 'Clients.xlsx');
    }


    public function InsertClients(Request $request)
    {
        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        }

        $data = array(
            'salutation' => $request->salutation,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'company' => $request->company,
            'type' => $request->type,
            'business' => $request->business,
            'federal_no' => $request->federal_no,
            'provincial_no' => $request->provincial_no,
            'website' => $request->website,
            'logo' => $image,
            'country' => $request->country,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'city' => $request->city,
            'fax' => $request->fax,
            'address' => $request->address,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'fiscal_start' => $request->fiscal_start,
            'fiscal_year_end' => $request->fiscal_year_end,
            'tax_remittance' => $request->tax_remittance,
            'client_status' => 1,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => Auth::id(),
            'default_prov' => $request->default_province,
            'federal_tax' => $request->federal_tax,
            'provincial_tax' => $request->provincial_tax,
            'dividends_account' => $request->dividends_account,
        );
        if($data['tax_remittance'] == 'Quarterly'){
            $timestamp = strtotime($data['fiscal_start']);
            $currentMonth = date('n', $timestamp);
            $quarters = [];

            for ($i = 0; $i < 4; $i++) {
                $quarters[] = $currentMonth;
                $currentMonth = ($currentMonth + 3) % 12; // Increment by 3 and wrap around to 1-12
            }

            $data['tax_remittance_months'] = implode(', ', $quarters);
        }
        if ($data['tax_remittance'] == 'Yearly') {
            $timestamp = strtotime($data['fiscal_start']);
            $currentMonth = date('n', $timestamp);

            $data['tax_remittance_months'] = $currentMonth;
        }

        if ($data['tax_remittance'] == 'Monthly') {
            $timestamp = strtotime($data['fiscal_start']);
            $currentMonth = date('n', $timestamp);
            $yearlyMonths = [];

            for ($i = 0; $i < 12; $i++) {
                $yearlyMonths[] = $currentMonth;
                $currentMonth = ($currentMonth % 12) + 1; // Increment by 1 and wrap around to 1-12
            }

            $data['tax_remittance_months'] = implode(', ', $yearlyMonths);
        }



        DB::Table('clients')->insert($data);
        $last_id = DB::getPdo()->lastInsertId();



        $qry = DB::Table('gifi')->where('gifi_status', 1)->where('is_deleted', 0)->get();
        foreach ($qry as $q) {
            DB::table('clients_gifi')->insert([
                'account_type' => $q->account_type,
                'sub_type' => $q->sub_type,
                'account_no' => $q->account_no,
                'description' => $q->description,
                'note' => $q->note,
                'client_id' => $last_id,
                'gifi_id' => $q->id,

            ]);
        }

        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client added', 'client_id' => $last_id]);




        return redirect()->back()->with('success', 'Client Added Successfully');
    }

    public function UpdateClients(Request $request)
    {
        $image = '';
        if ($request->logo_edit != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo_edit')->getClientOriginalExtension();
            $request->file('logo_edit')->move(public_path('client_logos'), $image);
        } else {
            $image = $request->hidden_img;
        }


        $data = array(
            'salutation' => $request->salutation_edit,
            'firstname' => $request->firstname_edit,
            'lastname' => $request->lastname_edit,
            'firstname' => $request->firstname_edit,
            'company' => $request->company_edit,
            'type' => $request->type_edit,
            'business' => $request->business_edit,
            'federal_no' => $request->federal_no_edit,

            'provincial_no' => $request->provincial_no_edit,
            'website' => $request->website_edit,
            'logo' => $image,
            'country' => $request->country_edit,
            'email' => $request->email_edit,
            'telephone' => $request->telephone_edit,
            'city' => $request->city_edit,
            'fax' => $request->fax_edit,
            'address' => $request->address_edit,
            'province' => $request->province_edit,
            'postal_code' => $request->postal_code_edit,
            'fiscal_start' => $request->fiscal_start_edit,
            'fiscal_year_end' => $request->fiscal_year_end_edit,
            'tax_remittance' => $request->tax_remittance_edit,

            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id(),
            'default_prov' => $request->default_province_edit,
            'federal_tax' => $request->federal_tax_edit,
            'provincial_tax' => $request->provincial_tax_edit,
            'dividends_account' => $request->dividends_account_edit,
        );
        DB::Table('clients')->where('id', $request->id)->update($data);



        DB::table('client_attachments')->where('client_id', $request->id)->delete();
        DB::table('client_comments')->where('client_id', $request->id)->delete();


        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('client_attachment/' . $a->attachment));
                DB::table('client_attachments')->insert([
                    'client_id' => $request->id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('client_comments')->insert([
                    'client_id' => $request->id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client updated', 'client_id' => $request->id]);



        Session::flash("success", "Client saved successfully");
        return response()->json('success');
    }






    public function getAttachmentJournals(Request $request)
    {
        $qry = DB::table('journal_attachments as a')
        ->where('journal_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getCommentsJournals(Request $request)
    {
        $qry = DB::table('journal_comments as c')
        ->where('c.journal_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getAttachmentClients(Request $request)
    {
        $qry = DB::table('client_attachments as a')
        ->where('a.client_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getCommentsClients(Request $request)
    {
        $qry = DB::table('client_comments as c')
        ->where('c.client_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }


    public function getAttachmentUsers(Request $request)
    {
        $qry = DB::table('user_attachments as a')
        ->where('a.user_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }

    public function getCommentsUsers(Request $request)
    {
        $qry = DB::table('user_comments as c')
        ->where('c.user_id', $request->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        return response()->json($qry);
    }


    public function getEmailClients(Request $request)
    {
        $qry = DB::table('client_ssl_emails')->where('client_id', $request->id)->get();

        return response()->json($qry);
    }

    public function getEmailContractClients(Request $request)
    {
        $qry = DB::table('client_emails')->where('client_id', $request->id)->get();

        return response()->json($qry);
    }




    public function UpdateUserProfile(Request $request)
    {

        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        }
        DB::table('users')->where('id', Auth::id())->update(['user_image' => $image]);
        return redirect()->back()->with('success', 'Profile update Successfully');
    }












    public function Users()
    {

        return view('users');
    }
    public function AddUsers()
    {

        return view('AddUsers');
    }

    public function EditUsers()
    {


        return view('EditUsers');
    }

    public function ExportPrintUsers()
    {


        return view('exports/ExportPrintUsers');
    }

    public function ExportPdfUsers()
    {


        $pdf = PDF::loadView('exports/ExportPdfUsers');

        return $pdf->stream('Users.pdf');
    }



    public function EndTax(Request $request)
    {

        $check = DB::Table('tax_rate')->where('id', $request->id)->first();


        if ($check->tax_rate_status == 0) {
            DB::Table('tax_rate')->where('id', $request->id)->update(['tax_rate_status' => 1]);

            DB::table('tax_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'tax_id' => $request->id, 'comment' => 'Tax rate successfully reinstated.<br>' . $request->reason]);

            DB::table('tax_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Tax rate successfully reinstated.', 'tax_id' => $request->id]);
            return redirect()->back()->with('success', 'Tax rate reactivated.');
        } else {
            DB::Table('tax_rate')->where('id', $request->id)->update(['tax_rate_status' => 0]);

            DB::table('tax_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'tax_id' => $request->id, 'comment' => 'Tax rate successfully revoked.<br>' . $request->reason]);

            DB::table('tax_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Tax rate successfully revoked.', 'tax_id' => $request->id]);
            return redirect()->back()->with('success', 'Tax rate deactivated.');
        }
    }



    public function EndSource(Request $request)
    {

        $check = DB::Table('source_code')->where('id', $request->id)->first();


        if ($check->source_code_status == 0) {
            DB::Table('source_code')->where('id', $request->id)->update(['source_code_status' => 1]);

            DB::table('source_code_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'source_code_id' => $request->id, 'comment' => 'Source Code successfully reinstated.<br>' . $request->reason]);

            DB::table('source_code_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Source Code successfully reactivated.', 'source_code_id' => $request->id]);
            return redirect()->back()->with('success', 'Source Code Reinstated Successfully');
        } else {
            DB::Table('source_code')->where('id', $request->id)->update(['source_code_status' => 0]);

            DB::table('source_code_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'source_code_id' => $request->id, 'comment' => 'Source Code successfully revoked.<br>' . $request->reason]);

            DB::table('source_code_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Source Code successfully deactivated.', 'source_code_id' => $request->id]);
            return redirect()->back()->with('success', 'Source Code Deactivated Successfully');
        }
    }

    public function Endgifi(Request $request)
    {

        $check = DB::Table('gifi')->where('id', $request->id)->first();


        if ($check->gifi_status == 0) {
            DB::Table('gifi')->where('id', $request->id)->update(['gifi_status' => 1]);

            DB::table('gifi_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'gifi_id' => $request->id, 'comment' => 'Gifi successfully reinstated.<br>' . $request->reason]);

            DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Gifi successfully reinstated.', 'gifi_id' => $request->id]);
            return redirect()->back()->with('success', 'Gifi Reinstated Successfully');
        } else {
            DB::Table('gifi')->where('id', $request->id)->update(['gifi_status' => 0]);

            DB::table('gifi_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'gifi_id' => $request->id, 'comment' => 'Gifi successfully revoked.<br>' . $request->reason]);

            DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Gifi successfully revoked.', 'gifi_id' => $request->id]);
            return redirect()->back()->with('success', 'Gifi Deactivated Successfully');
        }
    }


    public function DeleteTax(Request $request)
    {


        $id = $request->id;




        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::table('tax_rate')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);

        return redirect('/taxes')->with('alert-delete', 'Tax rate deleted.|' . $id);
    }
    public function UndoDeleteTax(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('tax_rate')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('tax_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted tax rate.', 'tax_id' => $request->id]);
        return redirect()->back()->with('success', 'Tax rate undeleted successfully.');
    }


    public function UndoDeleteSource(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('source_code')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('source_code_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted source code.', 'source_code_id' => $request->id]);
        return redirect()->back()->with('success', 'Source code undeleted successfully.');
    }


    public function DeleteSource(Request $request)
    {


        $id = $request->id;




        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::table('source_code')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect('/source')->with('alert-delete', 'Source code deleted successfully.|' . $id);
    }

    public function DeletRemittance(Request $request)
    {
        $id = $request->id;




        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::table('remittances')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect('/remittances')->with('success', 'Remittance Deleted Successfully');
    }

    public function DeleteGifi(Request $request)
    {


        $id = $request->id;




        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('gifi')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect('/gifi')->with('alert-delete', 'Gifi account deleted.|' . $id);
    }

    public function UndoDeleteGifi(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('gifi')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted gifi account.', 'gifi_id' => $request->id]);
        return redirect()->back()->with('success', 'Gifi account undeleted successfully.');
    }

    public function DeleteClientGifi(Request $request)
    {
        $client_id = $request->post('client_id');
        $client_account_id = $request->post('client_account_id');
        if (Auth::user()->role == 'read') {
            return response()->json(0);
        }
        DB::table('clients_gifi')->where('id', $client_account_id)->update([
            "deleted_at" => date("Y-m-d H:i:s"),
            "is_deleted" => 1,
        ]);
        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client Account Deleted', 'client_id' => $client_id]);
        return response()->json(1);
    }

    public function UndoDeleteClientGifi(Request $request)
    {
        $client_account_id = $request->post('client_account_id');
        $client_id = $request->post('client_id');
        if (Auth::user()->role == 'read') {
            return response()->json(0);
        }
        DB::table('clients_gifi')->where('id', $client_account_id)->update([
            "is_deleted" => 0,
            "deleted_at" => null,
        ]);
        DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored Deleted Client Account', 'client_id' => $client_id]);
        return response()->json(1);
    }


    public function DeleteUsers(Request $request)
    {
        DB::Table('users')->where('id', $request->id)->update([
            "is_deleted" => 1,
            "deleted_at" => date("Y-m-d H:i:s"),
        ]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Delete User Account', 'client_id' => $request->id]);
        Session::flash('delete', 'User deleted successfully. | '. $request->id);
        return redirect()->back();//->with('alert-delete', 'User deleted successfully.|' . $request->id);
    }
    public function UndoDeleteUsers($id)
    {
        DB::Table('users')->where('id', $id)->update([
            "is_deleted" => 0,
        ]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored Deleted User Account', 'client_id' => $id]);
        return redirect()->back()->with('success', 'User account undeleted successfully');
    }
    public function ShowUsers(Request $request)
    {


        $qry = DB::Table('users')->where('id', $request->id)->first();
        return response()->json($qry);
    }
    public function ShowUsersClients(Request $request)
    {


        $qry = DB::Table('users')->where('id', $request->id)->first();
        $arr = explode(',', $qry->access_to_client);
        $data = DB::Table('clients')->whereIn('id', $arr)->get();
        return response()->json($data);
    }

    public function UpdateUserPassword(Request $request)
    {


        DB::Table('users')->where('id', $request->id)->update(
            ['password' => Hash::make($request->password), 'password_verified' => date('Y-m-d H:i:s')]
        );

        return redirect()->back()->with('success', 'Password Changed Successfully');
    }



    public function ExportExcelUsers(Request $request)
    {

        return Excel::download(new ExportUsers($request), 'Users.xlsx');
    }



    public function InsertUsers(Request $request)
    {

        $access_to_client =  $request->access_to_client != '' ? implode(',', $request->access_to_client) : '';

        $check = DB::table('users')->where('email', $request->email)->first();


        if ($check != '') {
            return response()->json('Email Already Exist');
        }
        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        }
        $password = uniqid();
        $data = array(
            'salutation' => $request->salutation,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($password),
            'mobile' => $request->mobile,
            'work_phone' => $request->work_phone,
            'role' => $request->access_type,
            'mobile' => $request->mobile,
            'user_image' => $image,
            'portal_access' => $request->portal_access,

            'access_to_client' => $request->access_to_client != '' ? implode(',', $request->access_to_client) : '',

            'created_by' => Auth::id(),
        );




        // $settings = DB::Table('notification_settings')->first();

        DB::Table('users')->insert($data);
        $last_id = DB::getPdo()->lastInsertId();
        // $data = array('email' => $request->email,
        //  'password' => $password,
        //  'name' => $request->firstname . ' ' . $request->lastname,
        //  'subject' => 'Access your Contracts and Assets',
        //  'from_name' => $settings->from_name);






        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('client_attachment/' . $a->attachment));
                DB::table('user_attachments')->insert([
                    'user_id' => $last_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('user_comments')->insert([
                    'user_id' => $last_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User added', 'client_id' => $last_id]);






        // Mail::to($request->email)->send(new UserMail($data));


        return response()->json('success');
    }

    public function UpdateUsers(Request $request)
    {



        $access_to_client =  $request->access_to_client != '' ? implode(',', $request->access_to_client) : '';
        $check = DB::table('users')->where('id', '!=', $request->id)->where('email', $request->email)->first();
        if ($check != '') {
            return redirect()->back()->with('success', 'Email Already Exist');
        }

        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        } else {
            $image = $request->hidden_img;
        }



        $data = array(
            'salutation' => $request->salutation,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,

            'mobile' => $request->mobile,
            'work_phone' => $request->work_phone,
            'role' => $request->access_type,
            'mobile' => $request->mobile,
            'user_image' => $image,
            'portal_access' => $request->portal_access,

            'access_to_client' => $request->access_to_client != '' ? implode(',', $request->access_to_client) : '',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::id(),

        );


        DB::Table('users')->where('id', $request->id)->update($data);
        $last_id = $request->id;

        DB::table('user_attachments')->where('user_id', $request->id)->delete();
        DB::table('user_comments')->where('user_id', $request->id)->delete();

        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('client_attachment/' . $a->attachment));
                DB::table('user_attachments')->insert([
                    'user_id' => $last_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('user_comments')->insert([
                    'user_id' => $last_id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User updated', 'client_id' => $last_id]);


        return response()->json('Users Updated Successfully');
    }





    public function getTaxEditContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('tax_rate as a')->where('a.id', $id)->first();
        $html .= '
                      <div style="margin-bottom: 0.875rem !important;" class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header py-new-header2" style="padding-top: 5px  !important;padding-bottom: 5px !important;" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/img/gl-menu-icons/gl-menu-taxes-2.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:22px">Edit Tax Rate</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" >
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="public/img/paper-clip-white.png" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" >
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="public/img/comment-white.png" width="20px"></a>
                                         </span>


                                              <a href="javascript:;" class="text-white saveContract"  data="' . $id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save"><img src="' . asset('public') . '/icons2/icon-save-white.png" width="24px"> </a>

                                            <a  data-toggle="tooltip" data-trigger="hover"   data="' . $id . '" data-placement="top" title=""   id="btnClose" data-original-title="Close" href="javascript:;" class="text-white btnClose"><img src="' . asset('public') . '/icons2/icon-close-white.png" width="20px"> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full px-0 pt-0 pb-0 -boxed" style="padding-left: 24px !important;
                padding-right: 7px !important; "  >
                    <!-- New Post -->
                    <form  id="form-1" action="' . url('update-tax') . '" class="js-validation form-1  " method="POST" enctype="multipart/form-data"  >

                         <input type="hidden" name="attachment_array" id="attachment_array" >
                           <input type="hidden" name="id" value=' . $q->id . ' >

                        <div class="block new-block" >
  <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >General Information
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">

                                <div class="row justify-content- form-group fg-evenly push">


    <div class="col-lg-3">
              <label class="col-form-label mandatory">Province</label>
            </div>

   <div class="col-lg-4">

    <select type="" name="province_edit" class="form-control select2"  >

<option value="">Select Province</option>';
        $system_settings = DB::Table('system_settings')->first();

        if (@$system_settings->country != '') {

            // $city_qry = DB::Table('cities')->where('country_name', $system_settings->country)->groupBy('state_name')->get();
            $city_qry = DB::Table('cities')->where('country_name', "Canada")->groupBy('state_name')->get();
            foreach ($city_qry as $c) {
                $html .= '<option value="' . $c->state_name . '"  ' . ($c->state_name == $q->province ? 'selected' : '') . '>' . $c->state_name . '</option>';
            }
        }
        $html .= ' </select>
   </div>
   <div class="col-lg-2">
      <input type="text" class="js-rangeslider slider2" id="slider2"  name="slider_edit"   data-from="' . $q->slider . '" data-values="0,1,2">

   </div>
        </div>';

        $html .= '
        <div class="row form-group fg-evenly TaxDiv1_edit ' . ($q->slider == 1 || $q->slider == 2 ? '' : 'd-none') . '">
            <div class="col-lg-3">

                <input type="" name="tax_label_1_edit" class="form-control label-control" placeholder="Label"  value="' . $q->tax_label_1 . '">

            </div>

            <div class="col-lg-4 ">
                <input type="" name="tax_rate_1_edit" class="form-control" placeholder="Tax Rate%"   value="' . $q->tax_rate_1 . '" >
            </div>
            <div class="col-auto pl-0">
                <span class="badge-tag ' . ($q->slider == 2 ? '' : 'd-none') . '" id="federal_tag_edit">Federal</span>
                <span class="badge-tag ' . ($q->slider == 2 ? 'd-none' : '') . '" id="harmonized_tag_edit"
                    style="margin-left: 9px;">Harmonized</span>
            </div>
        </div>';


        $html .= '
         <div class="row form-group fg-evenly TaxDiv2_edit ' . ($q->slider == 2 ? '' : 'd-none') . '">
            <div class="col-lg-3">
                <input type="" name="tax_label_2_edit" class="form-control label-control" placeholder="Label"   value="' . $q->tax_label_2 . '"  >

            </div>

            <div class="col-lg-4">
                <input type="" name="tax_rate_2_edit" class="form-control" placeholder="Tax Rate%"    value="' . $q->tax_rate_2 . '">
            </div>
            <div class="col-auto pl-0">
                <span class="badge-tag" id="provincial_tag_edit">Provincial</span>
            </div>
            <div class="col-lg-2 pl-0">
                <div class="custom-control custom-  custom-control-  custom-control-lg mt-2 ">
                                  <input type="checkbox" class="custom-control-input" id="applied_to_tax1_edit" name="applied_to_tax1_edit" value="1" ' . ($q->applied_to_tax1 == 1 ? 'checked' : '') . ' >
                                                <label class="custom-control-label  applied_to_tax1_tooltip" data="tax_label_1_edit"  for="applied_to_tax1_edit"  > </label>
                                            </div>

            </div>';

        $html .= '


                 </div>
             </div>
         </div>
     </div>






     <div class="block new-block  commentDiv d-none ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Comments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content" id="commentBlock">



 </div>
 </div>


     <div class="block new-block attachmentDiv d-none   ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Attachments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content row" id="attachmentBlock">



 </div>

                        </div>
                    </form>
                    </div>
      ';

        echo $html;
    }

    public function DeleteJournalOnReload(Request $request)
    {
        $id = $request->id;

        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::table('journals')->where('edit_no', $id)->update([
            "deleted_at" => date("Y-m-d H:i:s"),
            "is_deleted" => 1,
        ]);
        return redirect()->back()->with('alert-delete', 'Journal Deleted Successfully|'.$id);
    }
    public function DeleteJournal(Request $request)
    {
        $journal_id = $request->post('journal_id');
        DB::table('journals')->where('edit_no', $journal_id)->update([
            "deleted_at" => date("Y-m-d H:i:s"),
            "is_deleted" => 1,
        ]);
        return response()->json();
    }
    public function UndoDeleteJournal(Request $request)
    {
        $journal_id = $request->post('journal_id');
        DB::table('journals')->where('edit_no', $journal_id)->update([
            "is_deleted" => 0,
        ]);
        DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored Deleted Journal.', 'journal_id' => $journal_id]);
        return response()->json();
    }
    public function UndoDeleteJournalOnReload(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('journals')->where('edit_no', $id)->update([
            "is_deleted" => 0,
        ]);
        DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored Deleted Journal.', 'journal_id' => $id]);
        return redirect()->back()->with('success', 'Journal Undeleted Successfully');
    }

    public function getTaxContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('tax_rate as a')->where('a.id', $id)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();

        $html .= '<div style="margin-bottom: 0.875rem !important;" class="block card-round   ' . ($q->tax_rate_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . '  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/img/gl-menu-icons/gl-menu-taxes-2.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem">
                                <h4  class="mb-0 header-new-text " style="line-height:20px">Tax Rate</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d', strtotime($q->updated_at)) . ' by ' . @$user->firstname . ' ' . $user->lastname . '</p>
                                    </div>
                                </div>';



        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';






        if (Auth::user()->role != 'read') {



            if ($q->tax_rate_status == 1) {
                $html .= '<span  >
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->tax_rate_status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Deactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px"></a>
                                         </span>';
            } else {
                $html .= '    <span  >
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->tax_rate_status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px" style="-webkit-transform:rotate(180deg);
                                             -moz-transform: rotate(180deg);
                                             -ms-transform: rotate(180deg);
                                             -o-transform: rotate(180deg);
                                             transform: rotate(180deg);"></a>
                                         </span>';
            }
        }



        if (Auth::user()->role != 'read') {

            $html .= '<a   href="javascript:;" d class="text-white   btnEdit " data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>

                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
        }



        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

        $html .= '</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5">
                                           <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Tax Rates
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">



                             <div class="row">

                                    <div class="col-sm-12">
                                        <div class="form-group row fg-evenly">
                                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label">Province</div>
                                       </div>

                                            <div class="col-sm-4" style="padding-left: 32px !important;">

                                           <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' . $q->province . '</div>

                                            </div>

                                         </div>
          ';
        if ($q->slider == 1 || $q->slider == 2) {
            $html .= '


                                          <div class="form-group row fg-evenly">
                                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label" data="' . $q->id . '">' . $q->tax_label_1 . '</div>
                                       </div>

                                            <div class="col-sm-4" style="padding-left:32px !important;">

                                                <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->tax_rate_1 . '</div>

                                            </div>
                                            <div class="col-auto pl-0">
                                            <span class="badge-tag ' . ($q->slider == 2 ? '' : 'd-none') . '" >Federal</span>
                                            <span class="badge-tag ' . ($q->slider == 2 ? 'd-none' : '') . '"
                                                style="margin-left: 9px;">Harmonized</span>
                                        </div>
                                         </div>
                                      ';
        }
        if ($q->slider == 2) {
            $html .= '
                                                  <div class="form-group row fg-evenly">
                                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label" data="' . $q->id . '">' . $q->tax_label_2 . '</div>
                                       </div>

                                            <div class="col-sm-4" style="padding-left: 32px !important;">

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->tax_rate_2 . '</div>

                                            </div>
                                            <div class="col-auto pl-0">
                                            <span class="badge-tag" id="provincial_tag">Provincial</span>
                                        </div>
                                            <div class="col-sm-4 pl-0">

                                           <div class="bubble-white-new2  " data="' . $q->id . '">Applied to ' . $q->tax_label_1 . '</div>

                                            </div>



                                         ';
        }



        $html .= '





                                           </div>



                                               </div>

                         </div>

             </div>
               </div>
                  </div>
                    </div>
                      </div>

';




        $contract = DB::table('tax_comments as c')
        ->where('c.tax_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Comments</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class="form-group">
                                                          <a class="section-header">Comments</a>
                                                            </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                        if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                        } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                            <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                        }
                                                    $html.='</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('tax_attachments as a')
        ->where('a.tax_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Attachments</div>-->

                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock">
                                                          <div class="col-sm-12 px-0">
                                                            <div class="form-group ">
                                                                <a class="section-header">Attachments</a>
                                                            </div>
                                                          </div>
                                                          <div class="col-sm-12 row">
                                                          ';
            foreach ($contract as $key => $c) {

                $f = explode('.', $c->attachment);
                $fileExtension = end($f);
                $icon = 'attachment.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }



                $html .= '<div class="col-sm-6 px-0 attach-other-col  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                         }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">


                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div></div>

                            </div>';
        }

        $contract = DB::table('tax_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.tax_id', $q->id)->get();


        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative">
                                               <!-- <div class="top-div text-capitalize">Audit Trail</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class="form-group">
                                                            <a class="section-header">Audit Trail</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png?cache=1"> </b></h1>';
                                                         }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }



        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }





    public function getSourceEditContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('source_code as a')->where('a.id', $id)->first();
        $html .= '
                      <div style="margin-bottom: 0.875rem !important;" class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header py-new-header2" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/new-gl-icons-dec/icon-source2.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:21px">Edit Source Code</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" >
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="public/img/paper-clip-white.png" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" >
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="public/img/comment-white.png" width="20px"></a>
                                         </span>


                                              <a href="javascript:;" class="text-white saveContract"  data="' . $id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save"><img src="' . asset('public') . '/icons2/icon-save-white.png" width="24px"> </a>

                                            <a  data-toggle="tooltip" data-trigger="hover"   data="' . $id . '" data-placement="top" title=""   id="btnClose" data-original-title="Close" href="javascript:;" class="text-white btnClose"><img src="' . asset('public') . '/icons2/icon-close-white.png" width="20px"> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full px-0 pt-0 pb-0 -boxed" style=" padding-left: 20px !important;
                padding-right: 20px !important;"  >
                    <!-- New Post -->
                    <form  id="form-1" action="' . url('update-tax') . '" class="js-validation form-1  " method="POST" enctype="multipart/form-data"  >

                         <input type="hidden" name="attachment_array" id="attachment_array" >
                           <input type="hidden" name="id" value=' . $q->id . ' >

                        <div class="block new-block" >
  <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Source Code
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 " style="padding-left: 50px;padding-right: 50px;">
                             <div class="row">
                             <div class="col-sm-10">
                                <div class="row justify-content- form-group fg-evenly push">


                                    <div class="col-lg-2">
                                        <label class="col-form-label mandatory">Code</label>
                                    </div>

                                    <div class="col-lg-6" style="padding-left:32px !important;">

                                        <input type="" name="source_code_edit" class="form-control" placeholder=""   value="' . $q->source_code . '" >

                                    </div>

                                </div>';

        $html .= '
                                <div class="row form-group fg-evenly ">
                                    <div class="col-lg-2">

                                        <label class="col-form-label mandatory">Description</label>

                                    </div>

                                    <div class="col-lg-9 " style="padding-left:32px !important;">
                                        <input type="" name="source_description_edit" class="form-control" placeholder=""   value="' . $q->source_description . '" >
                                    </div>

                                </div>
                            </div>
                        </div>
                                ';



        $html .= '


                 </div>
             </div>
         </div>
     </div>






     <div class="block new-block  commentDiv d-none ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Comments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content" id="commentBlock">



 </div>
 </div>


     <div class="block new-block attachmentDiv d-none   ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Attachments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content row" id="attachmentBlock">



 </div>

                        </div>
                    </form>
                    </div>
      ';

        echo $html;
    }




    public function getSourceContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('source_code as a')->where('a.id', $id)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();

        $html .= '<div class="block card-round   ' . ($q->source_code_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . '  new-nav" style="margin-bottom: 0.875rem !important">
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/new-gl-icons-dec/icon-source2.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:20px">Source Code</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d', strtotime($q->updated_at)) . ' by ' . @$user->firstname . ' ' . $user->lastname . '</p>
                                    </div>
                                </div>';



        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';







        if ($q->source_code_status == 1) {
            $html .= '<span  >
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->source_code_status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Deactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px"></a>
                                         </span>';
        } else {
            $html .= '    <span  >
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->source_code_status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="22px" style="-webkit-transform:rotate(180deg);
                                             -moz-transform: rotate(180deg);
                                             -ms-transform: rotate(180deg);
                                             -o-transform: rotate(180deg);
                                             transform: rotate(180deg);"></a>
                                         </span>';
        }




        if (Auth::user()->role != 'read') {

            $html .= '<a   href="javascript:;" d class="text-white   btnEdit " data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>

                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
        }



        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

        $html .= '</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5">
                                           <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Source Code
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">



                             <div class="row">

                                    <div class="col-sm-10">
                                        <div class="form-group row fg-evenly">
                                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label">Code</div>
                                       </div>

                                            <div class="col-sm-6" style="padding-left: 32px !important;">

                                           <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' . $q->source_code . '</div>

                                            </div>

                                         </div>
          ';

        $html .= '


                                          <div class="form-group row fg-evenly">
                                                        <div class="col-sm-2">
                                           <div class=" -new col-form-label" data="' . $q->id . '">Description</div>
                                       </div>

                                            <div class="col-sm-9" style="padding-left:32px !important;">

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->source_description . '</div>

                                            </div>

                                         </div>
                                      ';




        $html .= '





                                           </div>



                                               </div>

                         </div>

             </div>
               </div>
                  </div>
                    </div>
                      </div>

';




        $contract = DB::table('source_code_comments as c')
        ->where('c.source_code_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", 'u.user_image')
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Comments</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">
                                                            <div class="form-group">
                                                                <a class="section-header">Comments</a>
                                                            </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                         }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('source_code_attachments as a')

        ->where('a.source_code_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Attachments</div>-->

                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock">

                                                          <div class="col-sm-12 px-0">
                                                            <div class="form-group">
                                                                <a class="section-header">Attachments</a>
                                                            </div>
                                                          </div>
                                                          <div class="col-sm-12 row">
                                                          ';
            foreach ($contract as $key => $c) {

                $f = explode('.', $c->attachment);
                $fileExtension = end($f);
                $icon = 'attachment.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }



                $html .= '<div class="col-sm-6 px-0 attach-other-col  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                         }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">


                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div></div>

                            </div>';
        }

        $contract = DB::table('source_code_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.source_code_id', $q->id)->get();


        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Audit Trail</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class="form-group">
                                                            <a class="section-header">Audit Trail</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png?cache=1"> </b></h1>';
                                                         }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }



        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }











    public function getGifiEditContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('gifi')->where('id', $id)->first();
        $html .= '
                      <div style="margin-bottom: 0.875rem !important;" class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header py-new-header2" style="padding-top: 5px !important;padding-bottom: 5px !important;">
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/icons2/icon-gifi-white.png" style="width: 36px; height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:22px">Edit Gifi Account</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" >
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="public/img/paper-clip-white.png" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" >
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="public/img/comment-white.png" width="20px"></a>
                                         </span>


                                              <a href="javascript:;" class="text-white saveContract"  data="' . $id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save"><img src="' . asset('public') . '/icons2/icon-save-white.png" width="24px"> </a>

                                            <a  data-toggle="tooltip" data-trigger="hover"   data="' . $id . '" data-placement="top" title=""   id="btnClose" data-original-title="Close" href="javascript:;" class="text-white btnClose"><img src="' . asset('public') . '/icons2/icon-close-white.png" width="20px"> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full px-0 pt-0 pb-0 -boxed" style=" padding-left: 18px !important;
                padding-right: 10px !important;"  >
                    <!-- New Post -->
                    <form  id="form-1" action="' . url('update-tax') . '" class="js-validation form-1  " method="POST" enctype="multipart/form-data"  >

                         <input type="hidden" name="attachment_array" id="attachment_array" >
                           <input type="hidden" name="id" value=' . $q->id . ' >

                        <div class="block new-block" >
  <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >GIFI Account
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content" style="padding-left:45px !important;">

                                <div class="row justify-content- form-group fg-evenly  push">


    <div class="col-sm-2">
              <label class="col-form-label mandatory">Account Type</label>
            </div>

   <div class="col-sm-6" style="padding-left: 32px !important;">

    <select type="" name="account_type_edit" class="form-control" placeholder="" >
 <option value="Asset" ' . ($q->account_type == 'Asset' ? 'selected' : '') . '>Asset</option>
 <option value="Liability" ' . ($q->account_type == 'Liability' ? 'selected' : '') . '>Liability</option>
 <option value="Retained Earning" ' . ($q->account_type == 'Retained Earning' ? 'selected' : '') . '>Retained Earning</option>
 <option value="Income" ' . ($q->account_type == 'Income' ? 'selected' : '') . '>Income</option>
</select>
   </div>

        </div>';

        $html .= '
        <div class="row form-group fg-evenly ">
    <div class="col-sm-2">

             <label class="col-form-label mandatory">Sub Type</label>

            </div>

 <div class="col-sm-6 " style="padding-left: 32px !important;">

    <select type="" name="sub_account_type_edit" class="form-control" placeholder="" >
    ';
        $sub_account_type = DB::Table('sub_account_type')->where('account_type', $q->account_type)->get();
        foreach ($sub_account_type as $s) {
            $html .= '<option value="' . $s->sub_type . '" data-min="' . $s->min . '"  data-max="' . $s->max . '" ' . ($q->sub_type == $s->sub_type ? 'selected' : '') . '>' . $s->sub_type . '</option>';
        }
        $html .= '</select>
   </div>

      </div>
        <div class="row form-group fg-evenly ">
    <div class="col-sm-2">

             <label class="col-form-label mandatory">Account No.</label>

            </div>

 <div class="col-sm-4 " style="padding-left: 32px !important;">

    <input type="" name="account_no_edit" class="form-control" placeholder="4-digit numeric code" value="' . $q->account_no . '" >

   </div>

      </div>

     <div class="row form-group fg-evenly ">
    <div class="col-sm-2">

             <label class="col-form-label mandatory">Description</label>

            </div>

 <div class="col-sm-6 " style="padding-left: 32px !important;">

    <input type="" name="description_edit" class="form-control" placeholder="Account description"  value="' . $q->description . '">

   </div>

      </div>

     <div class="row form-group fg-evenly ">
    <div class="col-sm-2">

             <label class="col-form-label  ">Note</label>

            </div>

 <div class="col-sm-6 " style="padding-left: 32px !important;">

    <textarea type="" name="note_edit" class="form-control" rows="5" placeholder="Gifi note" >' . $q->note . '</textarea>

   </div>

      </div>



      ';



        $html .= '


                 </div>
             </div>
         </div>
     </div>






     <div class="block new-block  commentDiv d-none ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Comments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content" id="commentBlock">



 </div>
 </div>


     <div class="block new-block attachmentDiv d-none   ">

                            <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Attachments
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content new-block-content row" id="attachmentBlock">



 </div>

                        </div>
                    </form>
                    </div>
      ';

        echo $html;
    }




    public function getGifiContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('gifi as a')->where('a.id', $id)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();

        $html .= '<div style="margin-bottom: 0.875rem !important;" class="block card-round   ' . ($q->gifi_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . '  new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="' . asset('public') . '/icons2/icon-gifi-white.png" style="width: 36px; height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:22px">Gifi Account</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d', strtotime($q->updated_at)) . ' by ' . @$user->firstname . ' ' . $user->lastname . '</p>
                                    </div>
                                </div>';



        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';


        if ($q->gifi_status == 1) {
            $html .= '<span  >
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->gifi_status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Deactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px"></a>
                                         </span>';
        } else {
            $html .= '    <span  >
                                             <a href="javascript:;" class="btnEnd"  data="' . $q->gifi_status . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px" style="-webkit-transform:rotate(180deg);
                                             -moz-transform: rotate(180deg);
                                             -ms-transform: rotate(180deg);
                                             -o-transform: rotate(180deg);
                                             transform: rotate(180deg);"></a>
                                         </span>';
        }









        if (Auth::user()->role != 'read') {

            $html .= '<a   href="javascript:;" d class="text-white   btnEdit " data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>

                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
        }



        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

        $html .= '</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5">
                                           <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >GIFI account
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">



                             <div class="row">

                                    <div class="col-sm-10">
                                        <div class="form-group row fg-evenly">
                                                        <div class="col-sm-3">
                                           <div class=" -new col-form-label">Account Type</div>
                                       </div>

                                            <div class="col-sm-6 pl-0">

                                           <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' . $q->account_type . '</div>

                                            </div>

                                         </div>



                                          <div class="form-group row fg-evenly">
                                                        <div class="col-sm-3">
                                           <div class=" -new col-form-label" data="' . $q->id . '">Sub-Type</div>
                                       </div>

                                            <div class="col-sm-6 pl-0" >

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->sub_type . '</div>

                                            </div>

                                         </div>

                                           <div class="form-group row fg-evenly">
                                                        <div class="col-sm-3">
                                           <div class=" -new col-form-label" data="' . $q->id . '">Account No</div>
                                       </div>

                                            <div class="col-sm-6 pl-0">

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->account_no . '</div>

                                            </div>

                                         </div>

                                           <div class="form-group row fg-evenly">
                                                        <div class="col-sm-3">
                                           <div class=" -new col-form-label" data="' . $q->id . '">Description</div>
                                       </div>

                                            <div class="col-sm-6 pl-0">

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->description . '</div>

                                            </div>

                                         </div>

                                          <div class="form-group row fg-evenly">
                                                        <div class="col-sm-3">
                                           <div class=" -new col-form-label" data="' . $q->id . '">Note</div>
                                       </div>

                                            <div class="col-sm-6 pl-0">

                                           <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->note . '</div>

                                            </div>

                                         </div>

                                      ';




        $html .= '





                                           </div>



                                               </div>

                         </div>

             </div>
               </div>
                  </div>
                    </div>
                      </div>

';




        $contract = DB::table('gifi_comments as c')
        ->where('c.gifi_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                                                <!--<div class="top-div text-capitalize">Comments</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">
                                                          <div class="form-group">
                                                            <a class="section-header">Comments</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                        if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                        } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                        }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('gifi_attachments as a')->where('a.gifi_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                            <!--                    <div class="top-div text-capitalize">Attachments</div>-->

                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock">
                                                          <div class="col-sm-12 px-0">
                                                          <div class="form-group">
                                                          <a class="section-header">Attachments</a>
                                                          </div>
                                                          </div>
                                                          ';
            foreach ($contract as $key => $c) {

                $f = explode('.', $c->attachment);
                $fileExtension = end($f);
                $icon = 'attachment.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }



                $html .= '<div class="col-sm-6 px-0 attach-other-col ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';
                                                       if($c->user_image) {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                       } else {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                        <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                       }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">


                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="public/temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }

        $contract = DB::table('gifi_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.gifi_id', $q->id)->get();


        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative ">
                           <!--                     <div class="top-div text-capitalize">Audit Trail</div>-->

                                                          <div class="block-content new-block-content" id="commentBlock">

                                                          <div class="form-group">
                                                            <a class="section-header">Audit Trail</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                       if($c->user_image) {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                       } else {
                                                        $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                        <img width="30px" src="public/img/profile-white.png?cache=1"> </b></h1>';
                                                       }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }



        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';






       $html2 = '
       <div class="block-content pt-1 pb-1  pl-1 d-flex position-relative" style="">
           <div class=" mr-1 d-flex justify-content-center align-items-center" style="width: 20%;float: left;padding: 7px;">
               <img src="'.asset('public').'/icons/icon-gifi-grey.png" class=""
                   style="width: 72px;height: 72px;object-fit: cover;">
           </div>
           <div  class="w- 100 d-flex justify-content-between" style="width: 70%;">
               <div class="d-flex flex-column" style="width: calc(100% - 50px)">
                   <span class="font-12pt mb-0 text-truncate font-w600 c1" style="font-family: Calibri;color:#4194F6 !important;">GIFI</span>
                   <span style="overflow: hidden;
                   text-overflow: ellipsis;
                   white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                   color: #262626;
                   border-style: dashed !important;
                   min-width: 100%;
                   border:1px solid #262626;
                   background-color: #BFBFBF;
                   border-radius: 2px;
                   line-height: 1.6;
                   padding-top: 2px;
                   padding-bottom: 2px;
                   padding-left: 5px;
                   padding-right: 5px;">'.$q->account_no.' - '.$q->sub_type.'</span>
                   <div class="d-flex flex-row" style="padding-top: 3px;">
                       <div>
                           <span style="line-height: 1.6;
                           font-family: Calibri;
                           width: fit-content;
                           font-size: 11pt;
                           color:#3F3F3F;
                           border:1px solid #3F3F3F;
                           border-radius: 2px;

                           margin-right: 0.675rem;" class="px-2">'.$q->account_type[0].'</span>
                       </div>
                       <div style="overflow: hidden;
                       text-overflow: ellipsis;
                       width: fit-content;
                       line-height: 1.6;
                       white-space: nowrap;
                       font-size: 11pt;
                       font-family: Calibri;">
                           <span>'.$q->description.'</span>
                       </div>
                   </div>
               </div>

               <div style="position: absolute;right: 10px;top: 10px;">';

                   if($q->gifi_status==1)
                  {
                    $html2 .= '
                    <span style="float:right;
                    font-family: Calibri;
                    line-height: 1.5 !important;
                    color: #FFF;
                    background-color: #4EA833;
                    width:fit-content;
                    font-weight: 600!important;
                    border: 1px solid #D9D9D9;
                    text-align:center;
                    align-items: center;
                    border-radius: 5px;
                    justify-content: center;
                    display: flex;
                    padding-left: 15px;
                    padding-right: 15px;
                    padding-top: 2px;
                    padding-bottom: 2px;
                    display: block;
                    line-height: 1;
                    text-align: center;
                    border-radius: 3px;
                    font-size: 11pt;">Active</span>
                    ';
                  }else{
                    $html2 .= '<span style="float:right;
                    font-family: Calibri;
                    line-height: 1.5 !important;
                    color: #FFF;
                    background-color: #E54643;
                    width:fit-content;
                    font-weight: 600!important;
                    border: 1px solid #D9D9D9;
                    text-align:center;
                    align-items: center;
                    border-radius: 5px;
                    justify-content: center;
                    display: flex;
                    padding-left: 15px;
                    padding-right: 15px;
                    padding-top: 2px;
                    padding-bottom: 2px;
                    display: block;
                    line-height: 1;
                    text-align: center;
                    border-radius: 3px;
                    font-size: 11pt;">Inactive</span>';
                  }
               $html2 .= '</div>

               <div class="d-flex flex-row justify-content-end" style="margin-top: 20px;position: absolute;right: 10px;bottom: 4px;">
                   <div class="ActionIcon px-0 ml-2  client-info" data-notes="'.$q->note.'"
                       data="{{$q->id}}" style="border-radius: 1rem;position: relative;">
                       <a href="javascript:;" class="" >
                           <img src="'.asset('public').'/icons2/icon-comments-grey-2.png?cache=1"
                               width="25px">
                       </a>
                   </div>';
                   if(Auth::check()){
                    if(@Auth::user()->role!='read'){
                $html2 .= '<div class="ActionIcon ml-2   px-0 " style="border-radius: 1rem">
                    <a href="javascript:;" data="'.$q->id.'" class="btnEdit ">
                        <img src="public/icons2/icon-edit-grey.png?cache=1"
                        width="25px">
                    </a>
                </div>
                <div class="ActionIcon ml-2 px-0 " style="border-radius: 1rem">
                    <a href="javascript:;" class="btnDelete" data="'.$q->id.'">
                        <img src="public/icons2/icon-delete-grey.png?cache=1"
                            width="25px">
                    </a>
                </div>';
                } }
              $html .=' </div>
           </div>
       </div>
       ';






        return response()->json([
            "editContent" => $html,
            "viewContent" => $html2,
        ]);
    }



















    public function getUsersContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('users as a')->where('a.id', $id)
        ->where('is_deleted',0)
        ->first();


        $html .= '<div style="margin-bottom: 0.875rem !important;" class="block card-round   ' . ($q->portal_access == 1 ? 'read-mode-active' : 'read-mode-inactive') . ' new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="public/img/gl-menu-icons/gl-menu-users-removebg-preview.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: .91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:20px">Users</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>';



        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';






        if ($q->portal_access == 1) {
            $html .= '<span  >
                                             <a href="javascript:;" class="btnEnd"   data="' . $q->id . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Deactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px"></a>
                                         </span>';
        } else {
            $html .= '    <span  >
                                             <a href="javascript:;" class="btnEnd" data-ended="1" data="' . $q->id . '" data-id="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="22px" style="-webkit-transform:rotate(180deg);
                                             -moz-transform: rotate(180deg);
                                             -ms-transform: rotate(180deg);
                                             -o-transform: rotate(180deg);
                                             transform: rotate(180deg);"></a>
                                         </span>';
        }


        if (Auth::user()->role != 'read') {

            $html .= '<a   href="edit-users?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>

                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
        }



        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

        $html .= '</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative  5" >

                            <div class="block-content pb-0   " style="padding-left: 1.45rem;padding-right: 1.45rem;padding-top: 0;padding-bottom: 0;">
                             <div class="form-group">
                             <a class="section-header">General Info</a>
                             </div>
                                <div class="row justify-content- position-relative inner-body-content push" style="box-shadow:none !important;border:0!important;padding-top:0!important;">
                                    <!--<div class="top-right-div top-right-div-yellow text-capitalize">General Info</div>-->

                                    <div class="col-sm-12 m-" style="padding-left: 27px !important; padding-right:23px !important;">

                                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                        <div class="row">

                                            <div class="col-sm-10">
                                                <div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label">Primary Contact</label>
                                                    </div>

                                                    <div class="col-sm-9 pl-0">

                                                        <div class="bubble-white-new1 bubble-text-first">' . $q->salutation . ' ' . $q->firstname . ' ' . $q->lastname . '</div>

                                                    </div>

                                                </div>


                                                <div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label">Email Address</label>
                                                    </div>

                                                    <div class="col-sm-9 pl-0">

                                                        <div class="bubble-white-new1 bubble-text-first">' . $q->email . '</div>

                                                    </div>

                                                </div>


                                                <div class="form-group row">
                                                    <div class="col-sm-3">
                                                        <label class="col-form-label">Telephone</label>
                                                    </div>
                                                    <div class="col-sm-9 row pr-0">
                                                    <div class="col-sm-6 pl-0">

                                                        <div class="bubble-white-new1 bubble-text-first">' . $q->work_phone . '</div>

                                                    </div>

                                                    <div class="col-sm-6 pr-0">

                                                        <div class="bubble-white-new1 bubble-text-first">' . $q->mobile . '</div>

                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="bubble-white-new1 bubble-text-sec" style="padding:10px">
                                         ';
        if ($q->user_image != '') {

            $html .= '<img src="public/client_logos/' . $q->user_image . '" style="width: 100%;">';
        } else {
            $html .= '<img src="public/img/image-default.png" style="width: 100%;">';
        }
        $html .= '

                                                </div>

                                            </div>


                                        </div>

                                    </div>

                                </div>

                            </div>  <!--end-->

                        </div>
                        <div class="block new-block position-relative  5" >

         <div class="block-content pb-0" style="padding-left: 1.45rem;padding-right: 1.45rem;padding-top: 0;padding-bottom: 0;">
     <div class="form-group d-flex justify-content-between">
        <a class="section-header">Portal Access</a>
        <div class="w-25">
                                                <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="">
                                                    <input type="checkbox" class="custom-control-input" id="monitored1" name="monitored" value="1" disabled=""' . ($q->portal_access == 1 ? 'checked' : '') . '>
                                                    <label class="btn btn-new w-100  py-1 font-11pt " for="monitored1">Enabled</label>
                                                </div>
                                            </div>
     </div>
                                <div class="row justify-content- position-relative inner-body-content push" style="box-shadow: none!important;border: 0!important;padding-top:0px;">
 <!--<div class="top-right-div top-right-div-yellow text-capitalize">Portal Access</div>-->

                            <div class="col-sm-12 m-
                            " style="padding-left: 27px !important;padding-right:23px !important;">

                             <div class="row">

                                    <div class="col-sm-10">
                                        <!--<div class="form-group row">
                                            <div class="col-lg-4">
                                                <div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="">
                                                    <input type="checkbox" class="custom-control-input" id="monitored1" name="monitored" value="1" disabled=""' . ($q->portal_access == 1 ? 'checked' : '') . '>
                                                    <label class="btn btn-new w-100  py-1 font-11pt " for="monitored1">Enabled</label>
                                                </div>
                                            </div>


                                        </div>-->


                                          <div class="form-group row">
                                                        <div class="col-sm-3">
                                           <label class="col-form-label">Access Type</label>
                                       </div>

                                            <div class="col-sm-9 pl-0">

                                           <div class="bubble-white-new1 bubble-text-first text-capitalize">' . $q->role . '</div>

                                            </div>
     </div>
                                      <div class="row pl-2 pt-2">
                                        ';
        $id_array = explode(',', $q->access_to_client);

        $client_array = DB::table('clients')
            ->select('firstname', 'lastname', 'id', DB::raw('(@row_number:=@row_number+1) AS rownumber'))
            ->from(DB::raw('(SELECT @row_number:=0) AS rn, clients'))
            ->whereIn('id', $id_array)
            ->orderByDesc('id')
            ->get();


        foreach ($client_array as $c) {
            $html .= '
                                        <div class=" col-lg-3 px-1 ">
                                      <div class="block block-rounded ml-2  table-block-new ">
<div class="d-flex block-content  align-items-center px-2 py-2">
 <p class="font-12pt mb-0  w-100 text-truncate   c4 - " style="  background-color: rgb(151, 192, 255);  ; color: rgb(89, 89, 89); border-color: rgb(89, 89, 89);" data="262">' . $c->firstname . ' ' . $c->lastname . '</p> <a class="dropdown-toggle ml-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:;" c="">
                                <img src="public/img/dots.png?cache=1">
                                                                        </a>
                                         <div class="dropdown-menu py-0 pt-1 " aria-labelledby="dropdown-dropright-primary">

                  <a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="clients?id=' . $c->id . '&page=' . (ceil($c->rownumber / 10)) . '">   <div style="width: 32;  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"> &nbsp;&nbsp;View Client</div></a>

                </div>
</div>

</div>
</div>
';
        }
        $html .= '



           </div></div>


                                           </div>



                                               </div>

                         </div>

             </div>
               </div>
                  </div>
                    </div>
                      </div>

';

        $contract = DB::table('user_comments as c')
        ->where('c.user_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative " >
                                                <!--<div class="top-div text-capitalize">Comments</div>-->

                                                          <div class="block-content new-block-content" style="padding-left: 1.45rem;padding-right: 1.45rem;padding-top: 0 !important;padding-bottom: 0 !important;" id="commentBlock">
                                                          <div class="form-group">
                                                            <a class="section-header">Comments</a>
                                                          </div>
                                                          ';

            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                         }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }




        $contract = DB::table('user_attachments as a')
        ->where('a.user_id', $q->id)
        ->leftJoin("users as u", function ($join) {
            $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative " >
                                                <!--<div class="top-div text-capitalize">Attachments</div>-->

                                                          <div class="block-content new-block-content  px-4 row" style="padding-left: 1.45rem;padding-right: 1.45rem;padding-top: 0 !important;padding-bottom: 0 !important;" id="attachmentBlock">
                                                          <div class="col-12 px-0">
                                                          <div class="form-group">
                                                            <a class="section-header">Attachments</a>
                                                          </div>
                                                          </div>
                                                          <div class="col-sm-12">
                                                            <div class="row">
                                                          ';
            foreach ($contract as $key => $c) {

                $f = explode('.', $c->attachment);
                $fileExtension = end($f);
                $icon = 'attachment.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }



                $html .= '<div class="col-sm-6 px-0 attach-other-col">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';
                                                         if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                         } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                         }
                                                   $html .= '</td>
                                                    <td class="js-task-content  px-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext text-truncate">' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">


                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
            }
            $html .= '
            </div>
            </div>
            </div>

                            </div>';
        }


        $contract = DB::table('user_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.client_id', $q->id)->get();

        if (sizeof($contract) > 0) {
            $html .= '<div class="block new-block position-relative " >
                                                <!--<div class="top-div text-capitalize">Audit Trail</div>-->

                                                          <div class="block-content new-block-content" style="padding-left: 1.45rem;padding-right: 1.45rem;padding-top: 0 !important;padding-bottom: 0 !important;" id="commentBlock">
                                                          <div class="form-group">
                                                            <a class="section-header">Audit Trail</a>
                                                          </div>
                                                          ';
            foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">';
                                                        if($c->user_image) {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                                        } else {
                                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                            <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                                        }
                                                    $html .= '</td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-2 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
            }
            $html .= '</div>

                            </div>';
        }





        $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';




        return response()->json($html);
    }














    public function EndClients(Request $request)
    {
        $check = DB::Table('clients')->where('id', $request->id)->first();
        if ($check->client_status == 1) {

            DB::Table('clients')->where('id', $request->id)->update(['client_status' => '1']);
            DB::table('client_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'client_id' => $request->id, 'comment' => 'Client Reactivated.<br>' . $request->reason]);

            DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client successfully Reactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'Client Reactivated');
        } else {
            DB::Table('clients')->where('id', $request->id)->update(['client_status' => '0']);
            DB::table('client_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'client_id' => $request->id, 'comment' => 'Client successfully Deactivated.<br>' . $request->reason]);

            DB::table('client_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Client successfully Deactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'Client Deactivated Successfully');
        }
    }






    public function EndUsers(Request $request)
    {
        if ($request->end == 1) {



            DB::Table('users')->where('id', $request->id)->update(['portal_access' => '1']);
            DB::table('user_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'user_id' => $request->id, 'comment' => 'User Reactivated.<br>' . $request->reason]);

            DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User successfully Reactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'User Reactivated');
        } else {
            DB::Table('users')->where('id', $request->id)->update(['portal_access' => '0']);
            DB::table('user_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'user_id' => $request->id, 'comment' => 'User successfully Deactivated.<br>' . $request->reason]);

            DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User successfully Deactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'User Deactivated Successfully');
        }
    }








    public function getClientsAccount2(Request $request)
    {
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

                return view("ClientGifiComponent", compact(
                    'qry',
                    'id',
                    'searchVal',
                    'account_type',
                    'sub_account_type',
                    'account_no',
                    'description'
                ))->render();
    }
    public function exportClients(Request $request)
    {
        return Excel::download(new ExportClients2($request), 'Clients.xlsx');
    }
    // public function exportGifi(Request $request)
    // {
    //     return Excel::download(new ExportGifi($request), 'Gifi.xlsx');
    // }


  public function ExportGifi(Request $request)
    {
        return Excel::download(new ExportGifi($request), 'Gifi.xlsx');
    }
    public function ExportClientGifi(Request $request)
    {
        return Excel::download(new ExportClientGifi($request), 'Client Gifi.xlsx');
    }

    public function getClientsEditContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('clients')->where('id', $id)->first();
        $remittance = '';
        $gifi = DB::table('gifi')->where('is_deleted', 0)->orderBy("account_no")->get();
        if ($q->tax_remittance == 'Monthly') {
            $remittance = 3;
        } else if ($q->tax_remittance == 'Yearly') {
            $remittance = 1;
        } else if ($q->tax_remittance == 'Quarterly') {
            $remittance = 2;
        } else {
            $remittance = 0;
        }
        $html .= '
                      <div style="margin-bottom: 0.875rem !important;" class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header py-new-header2" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                <img src="public/img/gl-menu-icons/gl-menu-clients-removebg-preview.png" style="width: 36px;height: 36px;">
                                <div class="" style="margin-left: 0.91rem;">
                                <h4  class="mb-0 header-new-text " style="line-height:21px">Edit Client</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d') . ' by ' . Auth::user()->firstname . ' ' . Auth::user()->lastname . '</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" >
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="public/img/paper-clip-white.png" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" >
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="public/img/comment-white.png" width="20px"></a>
                                         </span>


                                              <a href="javascript:;" class="text-white saveContract"  data="' . $id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save"><img src="' . asset('public') . '/icons2/icon-save-white.png" width="24px"> </a>

                                            <a  data-toggle="tooltip" data-trigger="hover"   data="' . $id . '" data-placement="top" title=""   id="btnClose" data-original-title="Close" href="javascript:;" class="text-white btnClose"><img src="' . asset('public') . '/icons2/icon-close-white.png" width="20px"> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full px-0 pt-0 pb-0 -boxed" style="    padding-left: 30px !important;
                padding-right: 24px !important;"  >
                    <!-- New Post -->
                    <form  id="form-1" action="' . url('update-tax') . '" class="js-validation form-1  " method="POST" enctype="multipart/form-data"  >

                         <input type="hidden" name="attachment_array" id="attachment_array" >
                           <input type="hidden" name="id" value=' . $q->id . ' >

                        <div class="block new-block" >
  <div class="block-header py-0" style="padding-left:7mm;">

                                 <a class="  section-header"  >Client Information
                                </a>

                                <!-- <div class="block-options">

                                </div> -->
                            </div>
                            <div class="block-content pb-0 new-block-content">

                                <div class="row justify-content- form-group  push" style="padding-left: 9px;">
                                    <div class="col-sm-11">
                                        <div class="form-group row fg-evenly">
                                            <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Contact</label>
                                            <?php

                                                                                  ?>
<div class="col-sm-2" style="padding-left: 19px !important;">
    <select type="text" class="form-control" id="salutation_edit" name="salutation_edit" placeholder="Salutation">
        <option value="Mr" ' . ($q->salutation == ' Mr' ? 'selected' : '' ) . '>Mr.</option>
                                                    <option value="Mrs" ' . ($q->salutation == 'Mrs' ? 'selected' : '')
            . '>Mrs.</option>
        <option value="Ms" ' . ($q->salutation == ' Ms' ? 'selected' : '' ) . '>Ms.</option>
                                                    <option value="Miss" ' . ($q->salutation == 'Miss' ? 'selected' :
            '') . '>Miss.</option>
        <option value="Dr" ' . ($q->salutation == ' Dr' ? 'selected' : '' )
            . '>Dr.</option>
                                                 </select>
                                            </div>
                                            <div class="col-sm-4">
                                                 <input type="text" class="form-control" id="firstname_edit" name="firstname_edit" placeholder="First Name"  value="' . $q->firstname .
            '">
</div>
<div class="col-sm-4  ">
    <input type="text" class="form-control" id="lastname_edit" name="lastname_edit" placeholder="Last Name"
        value="' . $q->lastname . '">
</div>
</div>
</div>
<div class="col-sm-11">
    <div class="form-group row fg-evenly">
        <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Company </label>
        <div class="col-sm-10 " style="padding-left: 19px !important;">
            <input type="text" class="form-control" id="company_edit" name="company_edit" placeholder="Company name"
                value="' . $q->company . '">
        </div>
    </div>
</div>
<div class="col-lg-11" style="position:relative;">



    <div class="form-group row fg-evenly">
        <label class="col-sm-2 col-form-label mandatory " for="example-hf-client_id">Type</label>

        <div class="col-sm-6  " style="padding-left: 19px;">
            <select type="text" class="form-control" id="type_edit" name="type_edit" placeholder="Salutation">
                <option value="">Select enterprise type</option>
                <option value="Sole proprietorship" ' . ($q->type == 'Sole proprietorship' ? 'selected' : '' ) . '>Sole proprietorship</option>
                <option value="Partnership"  ' . ($q->type == 'Partnership' ? 'selected' : '') . '>Partnership</option>
                <option value="Corporation" ' . ($q->type === 'Corporation' ? 'selected' : '' ) . '>Corporation</option>
                <option value="Limited liability partnership"  ' . ($q->type == 'Limited liability partnership' ? 'selected' : '') . '>Limited liability partnership</option>
                <option value="Cooperative" ' . ($q->type == 'Cooperative' ? 'selected' : '' ) . '>Cooperative</option>
                <option value="Franchise"  ' . ($q->type == 'Franchise' ? 'selected' : '') . '>Franchise</option>
                <option value="Non-profit organization" ' . ($q->type == 'Non-profit organization' ? 'selected' : '' ) . '>Non-profit organization</option>
                <option value="Professional corporation"  ' . ($q->type == 'Professional corporation' ? 'selected' : '') . '>Professional corporation</option>
                <option value="Joint venture" ' . ($q->type == 'Joint venture' ? 'selected' : '' ) . '>Joint venture</option>
                <option value="Limited partnership"  ' . ($q->type == 'Limited partnership' ? 'selected' : '') . '>Limited partnership</option>
            </select>
        </div>

    </div>

    <div class="form-group row fg-evenly">
        <label class="col-sm-2 col-form-label mandatory " for="example-hf-client_id">Business</label>

        <div class="col-sm-6" style="padding-left: 19px;">
            <select type="text" class="form-control" id="business_edit" name="business_edit" placeholder="Salutation">

                <option value="">Select business type</option>

                <option value="Agriculture and Farming" ' . ($q->business == ' Agriculture and Farming' ? 'selected'
                    : '' ) . '>Agriculture and Farming</option>
                                                    <option value="Automotive">  ' . ($q->business == 'Automotive' ?
                    'selected' : '') . 'Automotive</option>
                <option value="Construction" ' . ($q->business == ' Construction' ? 'selected' : '' ) . '>Construction</option>
                                                    <option value="Consulting"  ' . ($q->business == 'Consulting' ?
                    'selected' : '') . '>Consulting</option>
                <option value="Education and Training" ' . ($q->business == ' Education and Training' ? 'selected' : ''
                    ) . '>Education and Training</option>
                                                      <option value="Energy and Utilities"  ' . ($q->business ==
                    'Energy and Utilities' ? 'selected' : '') . '>Energy and Utilities</option>
                <option value="Entertainment" ' . ($q->business == ' Entertainment' ? 'selected' : '' ) . '>Entertainment</option>
                                                        <option value="Finance and Banking"  ' . ($q->business ==
                    'Finance and Banking' ? 'selected' : '') . '>Finance and Banking</option>
                <option value="Food and Beverage" ' . ($q->business == ' Food and Beverage' ? 'selected' : '' ) . '>Food and Beverage</option>
                                                          <option value="Government and Non-Profit"  ' . ($q->business
                    == 'Government and Non-ProfitHealthcare' ? 'selected' : '') . '>Government and Non-Profit</option>
                <option value="Healthcare" ' . ($q->business == ' Healthcare' ? 'selected' : '' ) . '>Healthcare</option>
                                                          <option value="Hospitality and Tourism"  ' . ($q->business ==
                    'Hospitality and Tourism' ? 'selected' : '') . '>Hospitality and Tourism</option>
                <option value="Information Technology" ' . ($q->business == ' Information Technology' ? 'selected' : ''
                    ) . '>Information Technology</option>
                                                          <option value="Marketing and Advertising"  ' . ($q->business
                    == 'Marketing and Advertising' ? 'selected' : '') . '>Marketing and Advertising</option>
                <option value="Media and Communications" ' . ($q->business == ' Media and Communications' ? 'selected'
                    : '' ) . '>Media and Communications</option>
                                                          <option value="Professional Services"  ' . ($q->business ==
                    'Professional Services' ? 'selected' : '') . '>Professional Services</option>
                <option value="Real Estate and Property Management" ' . ($q->business == ' Real Estate and Property
                    Management' ? 'selected' : '' ) . '>Real Estate and Property Management</option>
                                                          <option value="Retail and Consumer Goods"  ' . ($q->business
                    == 'Retail and Consumer Goods' ? 'selected' : '') . '>Retail and Consumer Goods</option>
                <option value="Transportation and Logistics" ' . ($q->business == ' Retail and Consumer Goods'
                    ? 'selected' : '' )
                    . '>Transportation and Logistics</option>
                                                 </select>  </div>

                                        </div>
                                              <div class="form-group row fg-evenly">
                                            <label class="col-sm-2  col-form-label mandatory" for="example-hf-client_id">Federal # </label>

                                            <div class="col-sm-6 " style="padding-left: 19px;">
                                                 <input type="text" class="form-control" id="federal_no_edit" name="federal_no_edit" placeholder="Federal enterprise number"  value="'
                    . $q->federal_no . '">
        </div>


    </div>
    <div class="form-group row fg-evenly">
        <label class="col-sm-2  col-form-label mandatory" for="example-hf-client_id">Provincial # </label>

        <div class="col-sm-6  " style="padding-left: 19px;">
            <input type="text" class="form-control" id="provincial_no_edit" name="provincial_no_edit"
                placeholder="Provincial enterprise number" value="' . $q->provincial_no . '">
        </div>


    </div>
    <div class="avatar-upload float-right" style="position:absolute;right: 17px;top:0px;">
        <div class="avatar-edit">
            <input type="file" id="imageUpload1" class="imageUpload1" name="logo_edit" accept=".png, .jpg, .jpeg" />
            <label for="imageUpload1"></label>
        </div>
        <div class="avatar-preview">
            <div id="imagePreview1" class="imagePreview1" style="background-image: url("' . asset('
                public/client_logos/' . $q->logo) . '");">
            </div>
        </div>
        <!--close-col-11-->
    </div>


</div>


<input type="hidden" value="' . $q->logo . '" name="hidden_img">


</div>



</div>
</div>
<div class="block new-block">
    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Contact Information
        </a>

        <div class="block-options">

        </div>
    </div>
    <div class="block-content pb-0 new-block-content">

        <div class="row">
            <div class="col-sm-11" style="padding-left: 22px;">
                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory col-form-label" for="email">Email</label>
                    <div class="col-sm-10" style="padding-left: 19px !important;">
                        <input type="" class="form-control" id="email_edit" name="email_edit"
                            placeholder="Company e-mail address" value="' . $q->email . '">
                    </div>
                </div>

                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory  col-form-label" for="example-hf-email">Telephone </label>
                    <div class="col-sm-4" style="padding-left: 19px !important;">
                        <input type="" class="form-control" id="telephone_edit" name="telephone_edit"
                            placeholder="555-555-5555" value="' . $q->telephone . '">
                    </div>
                    <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Fax </label>
                    <div class="col-sm-4 ">
                        <input type="" class="form-control" id="fax_edit" name="fax_edit" value="' . $q->fax . '"
                            placeholder="555-555-5555">
                    </div>

                </div>
                <div class="form-group f p  row fg-evenly">
                    <label class="col-sm-2   col-form-label" for="example-hf-email">Website </label>
                    <div class="col-sm-10" style="padding-left: 19px !important;">
                        <input type="url" class="form-control" id="website_edit" name="website_edit"
                            placeholder="https://www.web.url" value="' . $q->website . '">
                    </div>
                </div>

                <div class="row form-group fg-evenly">
                    <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Country </label>
                    <div class="col-sm-4" style="padding-left:19px !important;">
                        <select type="text" class="form-control select2" id="country_edit" name="country_edit"
                            placeholder="">


                            <option value="">Country</option>';

                            $use = DB::Table('countries')->get();
                            foreach ($use as $u) {

                            $html .= '<option value="' . $u->name . '" ' . ($u->name == $q->country ? ' selected' : '' )
                                . '>' . $u->name . '</option>';
                            }
                            $html .= '
                        </select>
                    </div>
                </div>


                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Address</label>
                    <div class="col-sm-10 " style="padding-left: 19px !important;">
                        <textarea style="min-height:100px;" class="form-control" rows="5" id="address_edit"
                            name="address_edit" placeholder="Address">' . $q->address . '</textarea>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">City</label>
                    <div class="col-sm-10 " style="padding-left: 19px !important;">
                        <input class="form-control" id="city_edit" name="city_edit" placeholder="City"
                            value="' . $q->city . '">
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Province</label>
                    <div class="col-sm-4 " style="padding-left: 19px !important;">
                        <select class="form-control select2" id="province_edit" name="province_edit"
                            value="' . $q->province . '">

                            <option value=""> Province</option>';
                            $city_qry = DB::Table('cities')->where('country_name', ($q->country == 'United States' ?
                            $q->country : 'Canada'))->groupBy('state_name')->get();

                            foreach ($city_qry as $c) {
                            $html .= '<option value="' . $c->state_name . '" ' . ($c->state_name == $q->province ? '
                                selected' : '' ) . '>' . $c->state_name . '</option>';
                            }
                            $html .= '
                        </select>
                        </select>
                    </div>

                    <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Postal Code</label>
                    <div class="col-sm-4">
                        <input class="form-control" id="postal_code_edit" name="postal_code_edit"
                            value="' . $q->postal_code . '" placeholder="A9A 980">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--endblock-->
</div>
<div class="block new-block">
    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Remittance Information
        </a>

        <div class="block-options">

        </div>
    </div>
    <div class="block-content pb-0 new-block-content">

        <div class="row">
            <div class="col-sm-11" style="padding-left: 22px;">

                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory col-form-label" for=" ">Fiscal Start</label>
                    <div class="col-sm-4" style="padding-left: 19px !important;">
                        <input class="form-control fiscal_start_edit js-flatpickr bg-white" id="fiscal_start_edit"
                            name="fiscal_start_edit" placeholder="Fiscal start date" data-alt-input="true"
                            data-date-format="Y-m-d" data-alt-format="Y-M-d" value="' . $q->fiscal_start . '">
                    </div>
                    <label class="col-sm-2   col-form-label" for=" ">Fiscal Year End</label>
                    <div class="col-sm-4 ">
                        <div class="bubble-white-new2  fiscalEnd w-100">' . $q->fiscal_year_end . '</div><input
                            type="hidden" name="fiscal_year_end_edit" id="fiscal_year_end_edit"
                            value="' . $q->fiscal_year_end . '">
                    </div>
                </div>

                <div class="form-group row fg-evenly">

                    <label class="col-sm-2 mandatory col-form-label " for="example-hf-email">Default Prov</label>
                    <div class="col-sm-4" style="padding-left: 19px !important;">

                        <select class="form-control select2" id="default_province_edit" name="default_province_edit"
                            value="' . $q->province . '">

                            <option value=""> Province</option>';
                            $city_qry = DB::Table('cities')->where('country_name', ($q->country == 'United States' ?
                            $q->country : 'Canada'))->groupBy('state_name')->get();

                            foreach ($city_qry as $c) {
                            $html .= '<option value="' . $c->state_name . '" ' . ($c->state_name == $q->default_prov ? '
                                selected' : '' ) . '>' . $c->state_name . '</option>';
                            }
                            $html .= '
                        </select>
                        </select>
                    </div>

                    <label class="col-sm-2   col-form-label" for=" ">Tax Remittance</label>
                    <div class="col-sm-4 ">
                        <input class="js-rangeslider" id="tax_remittance_edit" name="tax_remittance_edit"
                            value="' . $q->tax_remittance . '" data-values="No,Yearly,Quarterly,Monthly"
                            data-from="' . $remittance . '">

                    </div>
                </div>

                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory  col-form-label" for="example-hf-email">Federal Tax</label>
                    <div class="col-sm-4 " style="padding-left: 19px !important;">
                        <select class="form-control select2" id="federal_tax_edit" name="federal_tax_edit">';
                            foreach ($gifi as $g) {
                            if ($g->account_no == $q->federal_tax) {
                            $html .= '<option value="' . $g->account_no . '" selected>' . $g->account_no . '</option>';
                            } else {
                            $html .= '<option value="' . $g->account_no . '">' . $g->account_no . '</option>';
                            }
                            }
                            $html .= '</select>
                    </div>
                    <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Provincial Tax</label>
                    <div class="col-sm-4">
                        <select class="form-control select2" id="provincial_tax_edit" name="provincial_tax_edit">';
                            foreach ($gifi as $g) {
                            if ($g->account_no == $q->provincial_tax) {
                            $html .= '<option value="' . $g->account_no . '" selected>' . $g->account_no . '</option>';
                            } else {
                            $html .= '<option value="' . $g->account_no . '">' . $g->account_no . '</option>';
                            }
                            }

                            $html .= '</select>
                    </div>
                </div>


                <div class="form-group row fg-evenly">
                    <label class="col-sm-2 mandatory  col-form-label" for="example-hf-email">Dividends Account</label>
                    <div class="col-sm-4 " style="padding-left: 19px !important;">
                        <select class="form-control select2" id="dividends_account_edit"
                            name="dividends_account_edit">';
                            $html .= '<option value="" selected disabled>Select</option>';
                            foreach ($gifi as $g) {
                            if ($g->account_no == $q->dividends_account) {
                            $html .= '<option value="' . $g->account_no . '" selected>' . $g->account_no . '</option>';
                            } else {
                            $html .= '<option value="' . $g->account_no . '">' . $g->account_no . '</option>';
                            }
                            }

                            $html .= '</select>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>

';



$html .= '


</div>
</div>
</div>
</div>






<div class="block new-block  commentDiv d-none ">

    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Comments
        </a>

        <div class="block-options">

        </div>
    </div>
    <div class="block-content new-block-content" id="commentBlock">



    </div>
</div>


<div class="block new-block attachmentDiv d-none   ">

    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Attachments
        </a>

        <div class="block-options">

        </div>
    </div>
    <div class="block-content new-block-content row" id="attachmentBlock">



    </div>

</div>
</form>
</div>
';

echo $html;
}

public function SourceValidate(Request $request)
{
return response()->json(@DB::table('source_code')->where('is_deleted', 0)->where('source_code',
$request->get('source_code'))->where('source_code_status', 1)->first() ? 1 : 0);
}
public function getClientsContent(Request $request)
{
$id = $request->id;
$html = '';
$settings = DB::Table('system_settings')->first();
$q = DB::table('clients as a')->where('a.id', $id)->first();
$remittance = '';

if ($q->tax_remittance == 'Monthly') {
$remittance = 3;
} else if ($q->tax_remittance == 'Yearly') {
$remittance = 1;
} else if ($q->tax_remittance == 'Quarterly') {
$remittance = 2;
} else {
$remittance = 0;
}
$user = DB::table('users')->where('id', $q->updated_by)->first();





$html .= '<div style="margin-bottom: 0.875rem !important;"
    class="block card-round   ' . ($q->client_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . ' new-nav">
    <div class="block-header   py-new-header">
        <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
            <div class="d-flex justify-content-center align-items-center">
                <img src="public/img/gl-menu-icons/gl-menu-clients-removebg-preview.png"
                    style="width: 36px;height: 36px;">
                <div class="" style="margin-left: 0.91rem">
                    <h4 class="mb-0 header-new-text " style="line-height:20px">' . $q->company . '</h4>
                    <p class="mb-0  header-new-subtext" style="line-height:17px">' . date('Y-M-d',
                        strtotime($q->updated_at)) . ' by ' . @$user->firstname . ' ' . @$user->lastname . '</p>
                </div>
            </div>';



            $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';


                if ($q->client_status == 1) {
                $html .= '<span>
                    <a href="javascript:;" class="btnEnd" data="' . $q->client_status . '" data-id="' . $q->id . '"
                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                        data-original-title="Deactivate" class=" "><img
                            src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px"></a>
                </span>';
                } else {
                $html .= ' <span>
                    <a href="javascript:;" class="btnEnd" data="' . $q->client_status . '" data-id="' . $q->id . '"
                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                        data-original-title="Reactivate" class=" "><img
                            src="' . asset('public') . '/icons2/icon-deactivate-white.png?cache=1" width="24px" style="-webkit-transform:rotate(180deg);
                                             -moz-transform: rotate(180deg);
                                             -ms-transform: rotate(180deg);
                                             -o-transform: rotate(180deg);
                                             transform: rotate(180deg);"></a>
                </span>';
                }



                if (Auth::user()->role != 'read') {

                $html .= '<a href="javascript:;" class="text-white    btnEdit" data="' . $q->id . '"
                    data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit"
                    data-original-title="Edit"> <img src="' . asset('public') . '/icons2/icon-edit-white.png"
                        width="24px"> </a>

                <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title=""
                    data-original-title="Delete" href="javascript:;" class="text-white btnDelete"> <img
                        src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
                }

                $html .= '
            </div>
        </div>
    </div>
</div>



<div class="col-sm-12 m-
                            ">

    <input type="hidden" name="attachment_array" id="attachment_array">





    <div class="block new-block">

        <div class="block-header py-0" style="padding-left:7mm;">

            <a class="  section-header">Client Information
            </a>

            <!-- <div class="block-options">

            </div> -->
        </div>
        <div class="block-content pb-0 new-block-content">

            <div class="row justify-content-  push">

                <div class="col-sm-12 m-
                            " style="padding-left: 22px;">

                    <input type="hidden" name="attachment_array" id="attachment_array">
                    <div class="row">

                        <div class="col-sm-11">
                            <div class="form-group row fg-evenly">
                                <label class="col-sm-2 col-form-label  " for="example-hf-client_id"> Contact</label>
                                <?php

                                                                                  ?>
                                <div class="col-sm-2" style="padding-left: 19px !important;">

                                    <div class="bubble-white-new1 bubble-text-first">' . $q->salutation . '</div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->firstname . '</div>
                                </div>
                                <div class="col-sm-4  ">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->lastname . '</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-11">
                            <div class="form-group row fg-evenly">
                                <label class="col-sm-2  col-form-label  " for="example-hf-client_id">Company</label>

                                <div class="col-sm-10" style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->company . '</div>
                                </div>

                            </div>
                        </div>

                        <div class="col-sm-11" style="position:relative;">



                            <div class="form-group row fg-evenly">
                                <label class="col-sm-2  col-form-label   " for="example-hf-client_id">Type</label>

                                <div class="col-sm-6  " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->type . '</div>
                                </div>

                            </div>


                            <div class="form-group row fg-evenly">
                                <label class="col-sm-2  col-form-label   " for="example-hf-client_id">Business</label>

                                <div class="col-sm-6  " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->business . '</div>
                                </div>

                            </div>

                            <div class="form-group row fg-evenly">
                                <label class="col-sm-2  col-form-label   " for="example-hf-client_id">Federal #</label>

                                <div class="col-sm-6 " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->federal_no . '</div>
                                </div>

                            </div>


                            <div class="form-group row fg-evenly">
                                <label class="col-sm-2 col-form-label   " for="example-hf-client_id">Provincial
                                    #</label>

                                <div class="col-sm-6 " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->provincial_no . '</div>
                                </div>

                            </div>


                            <div class="avatar-upload float-right" style="position: absolute;right:17px;top:0px;">

                                <div class="avatar-preview">';
                                    if ($q->logo != '') {

                                    $html .= '<div id="imagePreview"
                                        style="background-image: url(' . asset('public/client_logos/' . $q->logo) . ');">
                                        ';
                                        } else {
                                        $html .= '<div id="imagePreview"
                                            style="background-image: url(' . asset('public/img/image-default.png') . ');">
                                            ';
                                            }

                                            $html .= ' </div>
                                    </div>
                                </div>
                                <!--close-col-11-->

                            </div>




                        </div>



                    </div>
                </div>
            </div>
        </div>


        <div class="block new-block   ">

            <div class="block-header py-0" style="padding-left:7mm;">

                <a class="  section-header">Contact Information

                </a>

                <div class="block-options">

                </div>
            </div>
            <div class="block-content new-block-content">

                <div class="row">
                    <div class="col-sm-11" style="padding-left: 22px;">


                        <div class="row form-group fg-evenly">
                            <label class="col-sm-2    col-form-label" for="example-hf-email">Email </label>
                            <div class="col-sm-10" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->email . '</div>
                            </div>
                        </div>


                        <div class="form-group row fg-evenly">
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Telephone</label>
                            <div class="col-sm-4" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->telephone . '</div>
                            </div>
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Fax</label>
                            <div class="col-sm-4">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->fax . '</div>
                            </div>
                        </div>

                        <div class="form-group row fg-evenly">
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Website</label>
                            <div class="col-sm-10" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->website . '</div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Country</label>
                            <div class="col-sm-4" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->country . '</div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Address</label>
                            <div class="col-sm-10" style="padding-left: 19px !important;">
                                <div style="min-height:100px;" class="bubble-white-new1 bubble-text-first">' .
                                    $q->address . '</div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-2   col-form-label" for="example-hf-email">City</label>
                            <div class="col-sm-10" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->city . '</div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Province</label>
                            <div class="col-sm-4" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->province . '</div>
                            </div>
                            <label class="col-sm-2   col-form-label" for="example-hf-email">Postal Code</label>
                            <div class="col-sm-4">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->postal_code . '</div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
            <!--endblockcontent-->
        </div>




        <div class="block new-block   ">

            <div class="block-header py-0" style="padding-left:7mm;">

                <a class="  section-header">Remittance Information


                </a>

                <div class="block-options">

                </div>
            </div>
            <div class="block-content new-block-content">

                <div class="row">
                    <div class="col-sm-11" style="padding-left: 22px;">


                        <div class="row form-group fg-evenly">
                            <label class="col-sm-2    col-form-label" for="example-hf-email">Fiscal Start </label>
                            <div class="col-sm-4 " style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->fiscal_start . '</div>
                            </div>
                            <label class="col-sm-2    col-form-label" for="example-hf-email">Fiscal Year End </label>
                            <div class="col-sm-4">
                                <div class=" w-100 bubble-white-new2  ">' . $q->fiscal_year_end . '</div>
                            </div>
                        </div>

                        <div class="row form-group fg-evenly">
                            <label class="col-sm-2 col-form-label" for="example-hf-email">Default Province </label>
                            <div class="col-sm-4" style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->default_prov . '</div>
                            </div>
                            <label class="col-sm-2    col-form-label" for="example-hf-email">Tax Remittance </label>
                            <div class="col-sm-4">
                                <input class="js-rangeslider" id="tax_remittance" name="tax_remittance"
                                    value="' . $q->tax_remittance . '" data-values="No,Yearly,Quarterly,Monthly"
                                    data-from="' . $remittance . '">

                            </div>
                        </div>

                        <div class="row form-group fg-evenly">
                            <label class="col-sm-2 col-form-label" for="example-hf-email">Federal Tax</label>
                            <div class="col-sm-4" style="padding-left:19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->federal_tax . '</div>
                            </div>
                            <label class="col-sm-2     pr-0 col-form-label" for="example-hf-email">Provincial Tax
                            </label>
                            <div class="col-sm-4">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->provincial_tax . '</div>
                            </div>
                        </div>

                        <div class="row form-group fg-evenly">
                            <label class="col-sm-2 col-form-label" for="example-hf-email">Dividends Account</label>
                            <div class="col-sm-4" style="padding-left:19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->dividends_account . '</div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>



        ';


        $contract = DB::table('client_comments as c')
        ->where('c.client_id', $q->id)
        ->leftJoin("users as u", function ($join) {
        $join->on("c.added_by", "=", "u.id");
        })
        ->select("c.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
        $html .= '<div class="block new-block position-relative ">
            <!--<div class="top-div text-capitalize">Comments</div>-->

            <div class="block-content new-block-content" style="padding-left: 28px !important;" id="commentBlock">
                <div class="form-group">
                    <a class="section-header">Comments</a>
                </div>
                ';
                foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false"
                    data-task-starred="false">
                    <table class="table table-borderless table-vcenter mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center pr-0" style="width: 38px;">';
                                    if($c->user_image) {
                                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded"
                                                src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                    } else {
                                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                            <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                    }

                                    $html .= '</td>
                                <td class="js-task-content  pl-0">
                                    <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On
                                            ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A',
                                            strtotime($c->date)) . ' GMT
                                        </span></h2>
                                </td>

                            </tr>
                            <tr>
                                <td colspan="2" class="pt-0">
                                    <p class="px-2 mb-0 comments-section-text"> ' . nl2br($c->comment) . '
                                    </p>
                                </td>

                            </tr>

                        </tbody>
                    </table>

                </div>';
                }
                $html .= '
            </div>

        </div>';
        }




        $contract = DB::table('client_attachments as a')
        ->where('a.client_id', $q->id)
        ->leftJoin("users as u", function ($join) {
        $join->on("a.added_by", "=", "u.id");
        })
        ->select("a.*", "u.user_image")
        ->get();
        if (sizeof($contract) > 0) {
        $html .= '<div class="block new-block position-relative ">
            <!--<div class="top-div text-capitalize">Attachments</div>-->

            <div class="block-content new-block-content  px-4 row" style="padding-left: 28px !important;"
                id="attachmentBlock">
                <div class="col-12 px-0">
                    <div class="form-group">
                        <a class="section-header">Attachments</a>
                    </div>
                </div>
                <div class="row col-sm-12">
                    ';
                    foreach ($contract as $key => $c) {

                    $f = explode('.', $c->attachment);
                    $fileExtension = end($f);
                    $icon = 'attachment.png';
                    if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                    } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                    } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                    } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' ||
                    $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                    } else if ($fileExtension == 'png' || $fileExtension == 'gif' || $fileExtension == 'webp' ||
                    $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                    } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                    } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' ||
                    $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                    }



                    $html .= '<div class="col-sm-6 px-0  attach-other-col">
                        <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9"
                            data-task-completed="false" data-task-starred="false">
                            <table class="table table-borderless table-vcenter mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';

                                            if($c->user_image) {
                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded" style=""><b>
                                                    <img width="34px" height="34px" style="object-fit:cover;"
                                                        class="rounded" src="public/client_logos/'.$c->user_image.'">
                                                </b></h1>';
                                            } else {
                                            $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                    <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                            }
                                            $html .= '</td>
                                        <td class="js-task-content  pl-0">
                                            <h2 class="mb-0 comments-text ">' . $c->name . '<br><span
                                                    class="comments-subtext text-truncate">' . date('Y-M-d',
                                                    strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date))
                                                    . ' GMT
                                                </span></h2>
                                        </td>
                                        <td class="text-right position-relative" style="width: auto;">


                                            <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="pt-2">
                                            <p class=" pb-0 mb-0">
                                                <a href="temp_uploads/' . $c->attachment . '" download target="_blank"
                                                    class="text-truncate   attachmentDivNew comments-section-text"><img
                                                        src="public/img/' . $icon . '" width="25px"> &nbsp;<span
                                                        class="text-truncate  ">' . substr($c->attachment, 0, 25) .
                                                        '</span>
                                                </a>
                                            </p>
                                        </td>

                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>';
                    }
                    $html .= '</div>
            </div>

        </div>';
        }


        $contract = DB::table('client_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname',
        'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.client_id', $q->id)->get();

        if (sizeof($contract) > 0) {
        $html .= '<div class="block new-block position-relative ">
            <!--<div class="top-div text-capitalize">Audit Trail</div>-->

            <div class="block-content new-block-content" style="padding-left: 28px !important;" id="commentBlock">
                <div class="form-group">
                    <a class="section-header">Audit Trail</a>
                </div>
                ';
                foreach ($contract as $c) {
                $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false"
                    data-task-starred="false">
                    <table class="table table-borderless table-vcenter mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center pr-0" style="width: 38px;">';

                                    if($c->user_image) {
                                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded"
                                                src="public/client_logos/'.$c->user_image.'"> </b></h1>';
                                    } else {
                                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                            <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                                    }

                                    $html .= '</td>
                                <td class="js-task-content  pl-0">
                                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span
                                            class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . '
                                            at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                                        </span></h2>
                                </td>

                            </tr>
                            <tr>
                                <td colspan="2" class="pt-0">
                                    <p class="px-2 mb-0 comments-section-text"> ' . $c->description . '
                                    </p>
                                </td>

                            </tr>

                        </tbody>
                    </table>

                </div>';
                }
                $html .= '
            </div>

        </div>';
        }




        $html .= '
    </div>


</div>



</div>
</div>
</div>';




return response()->json($html);
}


public function getGifiAccounts(Request $request)
{
$accounts = DB::table('gifi')->where('is_deleted', 0)
->where('account_type', $request->get('account_type'))
->orderBy("account_type")
->pluck('account_no')
->toArray();
$sub_accounts = DB::Table('sub_account_type')->where('account_type', $request->account)->get();
return response()->json(compact("accounts", "sub_accounts"));
}



public function getRemittanceContent(Request $request)
{
    $id = $request->id;
    $html = '';

    $q = DB::table('remittances as r')
        ->where('r.is_deleted', 0)
        ->where('r.id', $id)
        ->join("clients as c", function ($join) {
            $join->on("c.id", "=", "r.client");
            $join->where("c.is_deleted", 0);
        })
        ->leftJoin("cities as p", function ($join) {
            $join->on("c.province", "=", "p.state_name");
            $join->where("p.state_name", "=", "c.province");
            $join->limit(1);
        })
        ->select(
            "r.*",
            "c.firstname",
            "c.lastname",
            "c.company as company_name",
            "c.federal_tax",
            "c.provincial_tax",
            "c.federal_no",
            "c.provincial_no",
            "c.tax_remittance",
            "c.fiscal_start",
            "p.state_code as province_code",
            "c.default_prov"
        )
        ->first();

    $calender = $this->remittanceCalender($q->tax_remittance, $q->month, $q->year);
    $calender_month = [];
    $calender_year = [];

    foreach ($calender as $c) {
        $arr = explode("-", $c);
        array_push($calender_month, intval($arr[0]));
        array_push($calender_year, intval($arr[1]));
    }

    $calender_month = array_values(array_unique($calender_month));
    $calender_year = array_values(array_unique($calender_year));

    $month = $q->month;
    $year = $q->year;

    $fs = $q->fiscal_start;
    $fiscal_start = explode("-", $fs);
    $fiscal_start = $fiscal_start[0];
    $period = $this->findPeriod($fs, $fiscal_start . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01");
    $fiscal_year_end = $this->getFiscalYearEnd(intval($period), intval($month), intval($year));

    $tax_remittance = $q->tax_remittance;
    $taxes = $q->taxes;
    $federal_tax = $q->federal_tax;
    $provincial_tax = $q->provincial_tax;
    $federal_credit = 0;
    $federal_rev_credit = 0;
    $federal_exp_credit = 0;
    $federal_debit = 0;
    $federal_rev_debit = 0;
    $federal_exp_debit = 0;
    $federal_remit = 0;
    $federal_rev_remit = 0;
    $federal_exp_remit = 0;
    $provincial_credit = 0;
    $provincial_rev_credit = 0;
    $provincial_exp_credit = 0;
    $provincial_debit = 0;
    $provincial_rev_debit = 0;
    $provincial_exp_debit = 0;
    $provincial_remit = 0;
    $provincial_rev_remit = 0;
    $provincial_exp_remit = 0;
    $total_remittance = 0;
    $total_rev_remittance = 0;
    $total_exp_remittance = 0;

    $whereClauses = [
        ['j.client', $q->client],
        ['j.is_deleted', 0]
    ];

    $_federal_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $federal_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo', 'asc')
        ->orderBy('j.debit', 'asc')
        ->get();

        $_federal_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $federal_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo', 'asc')
        ->orderBy('j.credit', 'asc')
        ->get();

    $_Rev_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.client_id', $q->client)
            ->where('cg.is_deleted', 0)->where('cg.sub_type', "Revenue");
        })
        ->select("j.*", "sc.source_code")
        ->get();
        
      

        $_Rev_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.client_id', $q->client)
            ->where('cg.is_deleted', 0)->where('cg.sub_type', "Revenue");
        })
        ->select("j.*", "sc.source_code")
        ->get();


        $_Exp_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.is_deleted', 0)
            ->where('cg.client_id', $q->client)
            ->where(function ($qry) {
                $qry->where('cg.sub_type', "Cost of sale")
                ->orWhere('cg.sub_type', "Operating expense");
            });
        })
        ->select("j.*", "sc.source_code")
        ->get();

        $_Exp_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.is_deleted', 0)
            ->where('cg.client_id', $q->client)
            ->where(function ($qry) {
                $qry->where('cg.sub_type', "Cost of sale")
                ->orWhere('cg.sub_type', "Operating expense");
            });
        })
        ->select("j.*", "sc.source_code")
        ->get();
    $_provincial_debit = array();
    $_provincial_credit = array();
    if ($taxes == 'Both') {
        $_provincial_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $provincial_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo')
        ->orderBy('j.debit')
        ->get();

        $_provincial_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $provincial_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo')
        ->orderBy('j.credit')
        ->get();
    }
    foreach ($_federal_debit as $f) {
        $federal_debit += $f->debit;
    }
    foreach ($_federal_credit as $f) {
        $federal_credit += $f->credit;
    }
    $federal_remit = $federal_credit - $federal_debit;

    foreach ($_provincial_debit as $p) {
        $provincial_debit += $p->debit;
    }
    foreach ($_provincial_credit as $p) {
        $provincial_credit += $p->credit;
    }
    $provincial_remit = $provincial_credit - $provincial_debit;

    $total_rev_debit = 0;
    foreach($_Rev_debit as $p) {
        $total_rev_debit += $p->debit;
    }

    $total_rev_credit = 0;
    foreach($_Rev_credit as $p) {
        $total_rev_credit += $p->credit;
    }

    $total_exp_debit = 0;
    foreach($_Exp_debit as $p) {
        $total_exp_debit += $p->debit;
    }

    $total_exp_credit = 0;
    foreach($_Exp_credit as $p) {
        $total_exp_credit += $p->credit;
    }

    $provincial_remit = $provincial_credit - $provincial_debit;

    $total_remittance = $federal_remit + $provincial_remit;
    $total_debit = $federal_debit + $provincial_debit;
    $total_credit = $federal_credit + $provincial_credit;

    $total_rev = $total_rev_credit - $total_rev_debit;

    $total_exp = $total_exp_debit - $total_exp_credit;

$net_revenue_before_exp = $total_rev;

    $net_revenue = $total_rev - $total_exp;

    $tax_rate = DB::table('tax_rate')->where('province', $q->default_prov)->where('is_deleted', 0)->first();
    $user = DB::table('users')->where('id', $q->created_by)->first();

    $html .= '<div style="margin-bottom: 0.875rem !important;"
        class="block card-round   ' . ($q->remittance_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . '  new-nav">
    <div class="block-header   py-new-header">
        <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
            <div class="d-flex justify-content-center align-items-center">
                <img src="public/icons2/icon-remit-white.png" style="width: 36px;height: 36px;">
                <div class="" style="margin-left: 0.91rem;">
                    <h4 class="mb-0 header-new-text " style="line-height:24px">Tax Remittance</h4>
                    <p class="mb-0  header-new-subtext" style="line-height:17px">' . (@$q->updated_at ? date('Y-M-d',
                        strtotime($q->updated_at)) : ($q->created_at ? date("Y-M-d", strtotime($q->created_at)) : '')) . ' by ' . @$user->firstname . ' ' . @$user->lastname . '</p>
                </div>
            </div>';



            $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';


                if (Auth::user()->role != 'read') {

                $html .= '

                <a href="javascript:;" onclick="window.print()" data-toggle="tooltip" data-trigger="hover"
                    data-placement="top" title="" data-original-title="Print" class=" ">
                    <img src="public/img/action-white-print.png" width="20px">
                </a>

                <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title=""
                    data-original-title="Delete" href="javascript:;" class="text-white btnDelete"> <img
                        src="' . asset('public') . '/icons2/icon-delete-white.png" width="24px"></a>';
                }



                // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));

                $html .= '
            </div>
        </div>
    </div>
</div>';

$html .= ' <div class="block new-block position-relative  5">
    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Client Information
        </a>

        <!-- <div class="block-options">

        </div> -->
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

        <div class="row">

            <div class="col-sm-12">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label">Client</div>
                    </div>

                    <div class="col-sm-10" style="padding-left:32px !important;">

                        <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' .
                            $q->company_name . '</div>

                    </div>

                </div>



                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Federal#</div>
                    </div>

                    <div class="col-sm-4" style="padding-left:32px !important;">

                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->federal_no . '
                        </div>

                    </div>

                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Provincial#</div>
                    </div>

                    <div class="col-sm-4">

                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->provincial_no .
                            '</div>

                    </div>

                </div>


                <div class="form-group row fg-evenly">
                <div class="col-sm-2">
                    <div class=" -new col-form-label" data="' . $q->id . '">Account#</div>
                </div>

                <div class="col-sm-4" style="padding-left:32px !important;">

                    <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->federal_tax . '
                    </div>

                </div>

                <div class="col-sm-2">
                    <div class=" -new col-form-label" data="' . $q->id . '">Account#</div>
                </div>

                <div class="col-sm-4">

                    <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->provincial_tax .
                        '</div>

                </div>

            </div>


            </div>

        </div>

    </div>

</div>
<!--End-->';



$html .= ' <div class="block new-block position-relative  5">
    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Remittance Summary
        </a>

        <div class="block-options">

        </div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

        <div class="row">

            <div class="col-sm-12">



                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Period</div>
                    </div>

                    <div class="col-sm-4" style="padding-left: 32px !important;">

                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->tax_remittance .
                            '</div>

                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex w-100 text-nowrap" style="margin-top:6px;">';
                            $calender = $this->remittanceCalender($q->tax_remittance, $q->month, $q->year);
                             if($q->tax_remittance == "Quarterly" || $q->tax_remittance == "Yearly") {
                            $calender = array_reverse($calender);
                            }




                                if($q->tax_remittance == 'Yearly') {
            $_e1 = explode("-", $calender[0]);
            $_e2 = explode("-", $calender[count($calender) - 1]);
            $date1 = date("M-Y", strtotime($_e1[1] . "-" . $_e1[0]));
            $date2 = date("M-Y", strtotime($_e2[1] . "-" . $_e2[0]));
            $html .= '<span class="range-capsule px-3 rounded mr-2 text-nowrap">' . $date1 . ' to ' . $date2 . '</span>';
        } else {
            foreach ($calender as $range) {
                $_e = explode("-", $range);
                $date = date("M-Y", strtotime($_e[1] . "-" . $_e[0]));

                $html .= '<span class="range-capsule px-3 rounded mr-2 text-nowrap">' . $date . '</span>';
            }
        }



                            $html .= '</div>
                    </div>
                </div>


                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_1 . ' Received
                        </div>
                    </div>

                    <div class="col-sm-4" style="padding-left: 32px !important;">

                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important;background: #fff;color: #E54643 !important; border-color: #E54643 !important" data="' . $q->id . '">' .
                            number_format($federal_credit, 2) . '</div>

                    </div>

                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_2 . ' Received
                        </div>
                    </div>

                    <div class="col-sm-4">

                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important;background:#fff;color: #E54643 !important;border-color: #E54643 !important;" data="' . $q->id . '">' .
                            number_format($provincial_credit, 2) . '</div>

                    </div>

                </div>

                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_1 . ' Paid
                        </div>
                    </div>

                    <div class="col-sm-4" style="padding-left: 32px!important;">

                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;" data="' . $q->id . '">' .
                            number_format($federal_debit, 2) . '</div>

                    </div>

                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_2 . ' Paid
                        </div>
                    </div>

                    <div class="col-sm-4">

                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;" data="' . $q->id . '">' .
                            number_format($provincial_debit, 2) . '</div>

                    </div>

                </div>

                <div class="form-group row fg-evenly ">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Total ' . @$tax_rate->tax_label_1 . '
                            Remit</div>
                    </div>

                    <div class="col-sm-4" style="padding-left: 32px!important;">

                        <div class="bubble-white-new1 bubble-text-first"
                        
                        ';
                        
                        if($federal_remit > 0)
                        {
                            $html .= 'style="text-align: right !important;background: #fff;color: #E54643 !important;border-color: #E54643  !important;"' ;
                        } 
                        else {
                            $html .= 'style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;"'  ;   
                        }
                       
                        
                    $html .= '    data="' . $q->id . '">' . ($federal_remit <
                            0 ? '(' . number_format(abs($federal_remit), 2) . ')' : number_format($federal_remit, 2)) . '</div>

                    </div>

                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Total ' . @$tax_rate->tax_label_2 . '
                            Remit</div>
                    </div>

                    <div class="col-sm-4">

                        <div class="bubble-white-new1 bubble-text-first"'; 
                        
                         if($provincial_remit > 0)
                        {$html .= 'style="text-align: right !important;background: #fff;color: #E54643 !important;border-color: #E54643  !important;"';
                        }
                        else { 
                            $html .= 'style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;"';
                        }
                        
                        $html .= 'data="' . $q->id . '">' . ($provincial_remit <
                            0 ? '(' . number_format(abs($provincial_remit), 2) . ')' : number_format($provincial_remit, 2)) . '</div>

                    </div>

                </div>

                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Total Remittance</div>
                    </div>

                    <div class="col-sm-4" style="padding-left: 32px!important;">

                        <div class="bubble-white-new1 bubble-text-first" 
                        style="text-align: right !important;background: #fff;color: #4EA833 !important;border-color: #4EA833  !important;"
                        data="' . $q->id . '">' . ($total_remittance >= 0 ? number_format($total_remittance, 2) : '(' . number_format(abs($total_remittance), 2) . ')') . '</div>

                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Net Revenue</div>
                    </div>

                    <div class="col-sm-4">

                        <div class="bubble-white-new1 bubble-text-first"
                        style="text-align: right !important;background: #fff;color: #4EA833 !important;border-color: #4EA833  !important;"
                        data="' . $q->id . '">' . ($net_revenue_before_exp >= 0 ? number_format($net_revenue_before_exp, 2) : '(' . number_format(abs($net_revenue_before_exp), 2) .')') . '</div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
    <!--End-->';


$system_settings = DB::table('system_settings')->where('id', 1)->first();
$federal_corp_tax = 0;
$provincial_corp_tax = 0;
if($net_revenue >= 0) {
    $federal_corp_tax = ($system_settings->federal_corp_tax_perc / 100) * $net_revenue;
    $provincial_corp_tax = ($system_settings->provincial_corp_tax_perc / 100) * $net_revenue;
}

$html .= ' <div class="block new-block position-relative  5">
    <div class="block-header py-0" style="padding-left:7mm;">

        <a class="  section-header">Payment Summary
        </a>

        <div class="block-options">

        </div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

        <div class="row">

            <div class="col-sm-12">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label">Net Revenue (after expenses)</div>
                    </div>

                    <div class="col-sm-4" style="padding-left:32px !important;">

                        <div class="bubble-white-new1 bubble-text-first provinceText" style="text-align: right !important" data="' . $q->id . '">' . ($net_revenue >= 0 ? '$' . number_format($net_revenue, 2) : '($' . number_format(abs($net_revenue), 2) . ')') . '</div>

                    </div>

                </div>



                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_1 . '/' . @$tax_rate->tax_label_2 . '</div>
                    </div>

                    <div class="col-sm-4" style="padding-left:32px !important;">

                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important" data="' . $q->id . '">
                        ' . ($total_remittance <= 0 ? '$0.00' : '$' . number_format($total_remittance, 2)) . '
                        </div>

                    </div>

                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="' . $q->id . '">'. $system_settings->tax_remittance .'</div>
                    </div>

                </div>


                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Federal Corp Tax</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important" data="' . $q->id . '">' . ($federal_corp_tax >= 0 ? '$'.number_format($federal_corp_tax, 2) : '($' . number_format(abs($federal_corp_tax), 2) . ')') . '</div>
                    </div>
                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="' . $q->id . '">'. $system_settings->federal_corp_tax .'</div>
                    </div>
                </div>
                
                 <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Provincial Corp Tax</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important" data="' . $q->id . '">' . ($federal_corp_tax >= 0 ? '$'.number_format($provincial_corp_tax, 2) : '($' . number_format(abs($provincial_corp_tax), 2) . ')') . '</div>
                    </div>
                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . $system_settings->provincial_corp_tax . '</div>
                    </div>
                </div>


            </div>

        </div>

    </div>

</div>
<!--End-->';



if (count($_federal_debit) > 0 || count($_federal_credit) > 0) {
    $html .= ' <div class="block new-block position-relative  5    break-before" style="margin-left: 0px !important;
            margin-right: 0px !important;">
        <div class="block-header py-0" style="padding-left:7mm;">

            <a class="  section-header">Federal ' . @$tax_rate->tax_label_1 . ' Tax Summary ' . $q->federal_tax . '
            </a>

            <div class="block-options">

            </div>
        </div>
        <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

            <div class="row">

                <div class="table- responsive col-sm-12">
                    <table class="table border-0 table-tax-summary">
                        <thead>
                            <tr>
                                <td style="font-weight:bold;padding:0;border:0 !important;">EN</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">Src</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">Date</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">RefNo</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">Description</td>
                                <td class="text-right" style="font-weight:bold;padding:0;border:0 !important;">DR</td>
                                <td class="text-right" style="font-weight:bold;padding:0;border:0 !important;">CR</td>
                            </tr>
                        </thead>
                        <tbody>
                            ';
                            foreach ($_federal_credit as $f) {
                            $html .= '
                            <tr>
                                <td style="padding:0;border:0 !important;">' . $f->editNo . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->source_code . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->date . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->ref_no . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->description . '</td>
                                <td class="text-right" style="padding:0;border:0 !important;">' . $f->debit . '</td>
                                <td class="text-right" style="padding:0;border:0 !important;">' . $f->credit . '</td>
                            </tr>
                            ';
                            }
                            foreach ($_federal_debit as $f) {
                                $html .= '
                                <tr>
                                    <td style="padding:0;border:0 !important;">' . $f->editNo . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->source_code . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->date . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->ref_no . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->description . '</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">' . $f->debit . '</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">' . $f->credit . '</td>
                                </tr>
                                ';
                                }
                            $html .= '
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="font-weight:bold;padding:0;border:0 !important;"></td>
                                <td class="text-right"
                                    style="font-weight:bold;padding:0;border:0 !important;padding-top:15px !important;">
                                    <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                                        <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                            ' . number_format($federal_debit, 2) . '
                                        </span>
                                    </span>
                                </td>
                                <td class="text-right"
                                    style="font-weight:bold;padding:0;border:0 !important;padding-top:15px !important;">
                                    <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                                        <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                            ' . number_format($federal_credit, 2) . '
                                        </span>
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>

    </div>
    <!--End-->';
    }


    if (count($_provincial_credit) > 0 || count($_provincial_debit) > 0) {
    $html .= ' <div class="block new-block position-relative  5" style="margin-left: 0px !important;
            margin-right: 0px !important;">
        <div class="block-header py-0" style="padding-left:7mm;">

            <a class="  section-header">Provincial ' . @$tax_rate->tax_label_2 . ' Tax Summary ' . $q->provincial_tax . '
            </a>

            <div class="block-options">

            </div>
        </div>
        <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">

            <div class="row">

                <div class="table- responsive col-sm-12">
                    <table class="table border-0 table-tax-summary">
                        <thead>
                            <tr>
                                <td style="font-weight:bold;padding:0;border:0 !important;">EN</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">Src</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">Date</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">RefNo</td>
                                <td style="font-weight:bold;padding:0;border:0 !important;">Description</td>
                                <td class="text-right" style="font-weight:bold;padding:0;border:0 !important;">DR</td>
                                <td class="text-right" style="font-weight:bold;padding:0;border:0 !important;">CR</td>
                            </tr>
                        </thead>
                        <tbody>
                            ';
                            foreach ($_provincial_credit as $f) {
                            $html .= '
                            <tr>
                                <td style="padding:0;border:0 !important;">' . $f->editNo . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->source_code . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->date . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->ref_no . '</td>
                                <td style="padding:0;border:0 !important;">' . $f->description . '</td>
                                <td class="text-right" style="padding:0;border:0 !important;">' . $f->debit . '</td>
                                <td class="text-right" style="padding:0;border:0 !important;">' . $f->credit . '</td>
                            </tr>
                            ';
                            }
                            foreach ($_provincial_debit as $f) {
                                $html .= '
                                <tr>
                                    <td style="padding:0;border:0 !important;">' . $f->editNo . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->source_code . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->date . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->ref_no . '</td>
                                    <td style="padding:0;border:0 !important;">' . $f->description . '</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">' . $f->debit . '</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">' . $f->credit . '</td>
                                </tr>
                                ';
                                }
                            $html .= '
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="font-weight:bold;padding:0;border:0 !important;"></td>
                                <td class="text-right"
                                    style="font-weight:bold;padding:0;border:0 !important;padding-top:15px !important;">
                                    <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                                        <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                            ' . number_format($provincial_debit, 2) . '
                                        </span>
                                    </span>
                                </td>
                                <td class="text-right"
                                    style="font-weight:bold;padding:0;border:0 !important;padding-top:15px !important;">
                                    <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                                        <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                            ' . number_format($provincial_credit, 2) . '
                                        </span>
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>

    </div>
    <!--End-->';
    }


$html .= '</div>
</div>
</div>

';





$html .= '
</div>


</div>



</div>
</div>
</div>';




return response()->json($html);
}


private function getFiscalYearEnd($period, $month, $year) {
    $monthCalendar = array($month);
    while ($period <= 12) {
        $month = $month + 1;
        if ($month == 13) {
            $month = 1;
        }
        array_push($monthCalendar, $month);
        if (count($monthCalendar) == 12) {
            break;
        }
        $period++;
    }
    foreach ($monthCalendar as $key => $m) {
        if ($m == 1 && $key != 0) {
            $year++;
        }
    }
    return $year;
}

private function findPeriod($fiscalStart, $dateString) {
    $startDate = strtotime($fiscalStart);
    $endDate = strtotime($dateString);

    $diffMonths = (date('Y', $endDate) - date('Y', $startDate)) * 12 + date('n', $endDate) - date('n', $startDate) + 1;
    $period = ($diffMonths > 0) ? $diffMonths : 12 - abs($diffMonths % 12);

    if ($period > 12) {
        return "";
    }

    $periodString = str_pad($period, 2, "0", STR_PAD_LEFT);
    return $periodString;
}

public function Remittance()
{
$clients = DB::table('clients')->where('is_deleted', 0)
->where('tax_remittance', '!=', "No")

->orderBy("company", "asc")
->get();

return view("Remittance", compact("clients"));
}
private function remittanceCalender($remittance, $month_no, $year)
{
$result = [];
if ($remittance == "Quarterly") {
for ($i = 0; $i < 3; $i++) { $result[]=$month_no . '-' . $year; if ($month_no==1) { $month_no=12; $year--; } else {
    $month_no--; } } } elseif ($remittance=="Yearly" ) { for ($i=0; $i < 12; $i++) { $result[]=$month_no . '-' . $year;
    if ($month_no==1) { $month_no=12; $year--; } else { $month_no--; } } } elseif ($remittance=="Monthly" ) {
    $result[]=$month_no . '-' . $year; } return $result; }

public function PreInsertRemittance(Request $request)
{
    $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $client_id = $request->post('client');
    $year = $request->post('year');
    $month = $request->post('month');
    $taxes = $request->post('taxes');
    $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
    if($client) {
        $month = intval($month);
        $year = intval($year);
        if(DB::table('remittances')->where('client', $client->id)->where('year', $year)->where('month', $month)->where('is_deleted', 0)->exists()) {
            return response()->json([
                "status" => "error",
                "message" => "A remittance for " . $client->company . " and " . $year . " and " . $monthNames[$month-1] .  " already exists."
            ]);
        }
        return response()->json([
            "status" => "success"
        ]);
    }
    return response()->json([
        "status" => 'error',
        "message" => ""
    ]);
}
    public function InsertRemittance(Request $request) {
        $client_id = $request->input('client');
        $year = $request->input('year');
        $month = $request->input('month');
        $taxes = $request->input('taxes');

        $client = DB::table('clients')->where('is_deleted', 0)->where("id", $client_id)->first();

        if (@$client) {
            $month = intval($month);
            $year = intval($year);
            // $tax_remittance = $client->tax_remittance;
            // $calender = $this->remittanceCalender($tax_remittance, $month, $year);
            // $calender_month = [];
            // $calender_year = [];
            // foreach ($calender as $c) {
            //     $arr = explode("-", $c);
            //     array_push($calender_month, intval($arr[0]));
            //     array_push($calender_year, intval($arr[1]));
            // }
            // $calender_month = array_values(array_unique($calender_month));
            // $calender_year = array_values(array_unique($calender_year));

            DB::table('remittances')->insert([
                "client" => $client->id,
                "year" => $year,
                "month" => $month,
                "taxes" => $taxes,
                // "calender_month" => serialize($calender_month),
                // "calender_year" => serialize($calender_year),
                // "calender" => serialize($calender),
                "created_by" => Auth::user()->id
            ]);
    return redirect()->back()->with('success', 'Remittance added successfully');
    }
    return redirect()->back()->with('error', 'Enable to create remittance');
    }
    private function getPreviousMonth($date)
    {
    $currentDate = new DateTime($date);
    $currentDate->modify('first day of this month');
    $currentDate->modify('last day of previous month');
    $lastDayOfPreviousMonth = $currentDate->format('Y-m-d');
    return $lastDayOfPreviousMonth;
    }
    public function getClientsForRemittance(Request $request)
    {
        $remits_this_month = $request->get('remits_this_month');
        $show_remitted = $request->get('show_remitted');
        if ($remits_this_month == 1) {
            $clients = DB::table('clients')->where('is_deleted', 0)
            ->whereIn('tax_remittance', ['Monthly', 'Yearly', 'Quarterly'])
            // ->where(function ($query) use ($show_remitted) {
            //     if ($show_remitted == 0) {
            //         $query->whereNotExists(function ($subquery) {
            //             $subquery->select(DB::raw(1))
            //                 ->from('remittances')
            //                 ->whereColumn('clients.id', 'remittances.client');
            //         });
            //     }
            // })->where('fiscal_year_end', date("F d", strtotime($this->getPreviousMonth(date("Y-m-d")))))
            ->orderBy("firstname", 'asc')->get();
            $filtered_clients = [];
            foreach($clients as $client) {
                if($client->tax_remittance == 'Monthly') {
                    array_push($filtered_clients, $client);
                }   else {
                    if($show_remitted == 0) {
                        if($client->tax_remittance == 'Quarterly') {
                            $this_month = date("m", strtotime("-5 days"));
                            $this_year = date("Y", strtotime("-5 days"));
                            $fiscal_start = date("Y-m-d", strtotime($client->fiscal_start));
                            $q1 = date("m", strtotime($fiscal_start . " +3 months"));
                            $q2 = date("m", strtotime($fiscal_start . " +6 months"));
                            $q3 = date("m", strtotime($fiscal_start . " +9 months"));
                            $q4 = date("m", strtotime($fiscal_start . " +12 months"));
                            if($this_month == $q1 || $this_month == $q2 || $this_month == $q3 || $this_month == $q4) {
                                if(!DB::table('remittances')->where('is_deleted', 0)->where('client', $client->id)->where('remittanceMonth', $this_month)->where('remittanceYear', $this_year)->exists()) {
                                    array_push($filtered_clients, $client);
                                }
                            }
                        }
                        if($client->tax_remittance == 'Yearly') {
                            $this_month = date("m", strtotime("-5 days"));
                            $this_year = date("Y", strtotime("-5 days"));
                            $fiscal_start = date("Y-m-d", strtotime($client->fiscal_start));
                            $remitMonth = date("m", strtotime($fiscal_start . " +12 months"));
                            if($this_month == $remitMonth) {
                                if(!DB::table('remittances')->where('is_deleted', 0)->where('client', $client->id)->where('remittanceMonth', $this_month)->where('remittanceYear', $this_year)->exists()) {
                                    array_push($filtered_clients, $client);
                                }
                            }
                        }
                    } else {
                        array_push($filtered_clients, $client);
                    }
                }
            }
            return response()->json($filtered_clients);
        } else {
            return response()->json(DB::table('clients')->where('is_deleted', 0)
                ->where('tax_remittance', '!=', "No")
                ->orderByDesc("id")->get());
        }
    }
    
    public function exportJournalReport(Request $request)
    {
        
        return Excel::download(new ExportJournalReport($request), 'JournalReports.xlsx');
    }
}
