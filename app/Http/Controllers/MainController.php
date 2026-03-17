<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Mail;
use Hash;
use PDF;
use DateTime;
use Excel;
use App\Exports\ExportClients;
use App\Exports\ExportClients2;
use App\Exports\ExportGifi;
use App\Exports\ExportAssets;
use App\Exports\ExportTestDefinitions;
use App\Exports\ExportTestThresholds;
use App\Exports\ExportSampleTests;
use App\Exports\ExportItemcodes;
use App\Exports\ExportWorkorders;
use App\Exports\ExportItemCategories;
use App\Exports\ExportUsers;
use App\Exports\ExportClientGifi;
use App\Imports\JournalImport;
use App\Imports\JournalStandardImport;
use App\Imports\GIFIImport;
use App\Imports\ItemcodesImport;
use App\Imports\WorkordersImport;
use App\Imports\ItemCategoriesImport;
use App\Imports\UsersImport;
use App\Imports\AssetsImport;
use App\Imports\ImportTestDefinitions;
use App\Imports\ImportTestThresholds;
use App\Imports\ImportSampleTests;
use App\Exports\ExportJournals;
use App\Exports\ExportJournalsBySource;
use App\Exports\ExportJournalsByPeriod;
use App\Exports\ExportJournalsTrialBalance;
use App\Exports\ExportJournalsByAccount;
use App\Exports\ExportTrialBalance;
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
use Carbon\Carbon;
use Cache;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Milon\Barcode\DNS1D;

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
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
        }
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'ASC')->get();
        $editNo = 1; //(@DB::table('journals')->orderByDesc('edit_no')->first()->edit_no ?? 0) + 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status', 1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)
            ->orderBy('account_no', 'asc')->get();
        return view("journals", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis", "defaultClient", "defaultFyear"));
    }
    public function JournalsBySource()
    {
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        $defaultClientId = 0;
        $clientFstart = 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
            $defaultClientId = $data->id;
            $clientFstart = $data->fiscal_start;
        }
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'ASC')->get();
        $editNo = 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status', 1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)->orderBy('account_no', 'asc')->get();
        return view("journalsBySource", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis", "defaultClient", "defaultFyear", "defaultClientId", "clientFstart"));
    }
    public function JournalsByPeriod()
    {
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        $defaultClientId = 0;
        $clientFstart = 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
            $defaultClientId = $data->id;
            $clientFstart = $data->fiscal_start;
        }
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'ASC')->get();
        $editNo = 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status', 1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)->orderBy('account_no', 'asc')->get();
        return view("journalsByPeriod", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis", "defaultClient", "defaultFyear", "defaultClientId", "clientFstart"));
    }
    public function JournalsProgressReport()
    {
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        $defaultClientId = 0;
        $clientFstart = 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
            $defaultClientId = $data->id;
            $clientFstart = $data->fiscal_start;
        }
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'ASC')->get();
        $editNo = 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status', 1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)->orderBy('account_no', 'asc')->get();
        return view("journalsProgressReport", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis", "defaultClient", "defaultFyear", "defaultClientId", "clientFstart"));
    }
    public function trialBalanceReport()
    {
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        $defaultClientId = 0;
        $clientFstart = 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
            $defaultClientId = $data->id;
            $clientFstart = $data->fiscal_start;
        }
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'ASC')->get();
        $editNo = 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status', 1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)->orderBy('account_no', 'asc')->get();
        return view("journalsNewTrialBalance", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis", "defaultClient", "defaultFyear", "defaultClientId", "clientFstart"));
    }
    public function JournalsByAccount()
    {
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        $defaultClientId = 0;
        $clientFstart = 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
            $defaultClientId = $data->id;
            $clientFstart = $data->fiscal_start;
        }
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'ASC')->get();
        $editNo = 1;
        $sources = DB::table('source_code')->where('is_deleted', 0)->where('source_code_status', 1)->orderBy('source_code', 'ASC')->get();
        $gifis = DB::table('gifi')->where('is_deleted', 0)->orderBy('account_no', 'asc')->get();
        return view("journalsByAccount", compact("clients", "cities", "system_settings", "tax_rate", "editNo", "sources", "gifis", "defaultClient", "defaultFyear", "defaultClientId", "clientFstart"));
    }
    public function JournalsBySourceContent(Request $request)
    {
        $client_id = $request->client_id;
        $client_fyear = $request->client_fyear;
        $period = $request->period;
        $source_id = $request->source_id;
        $source_text = $request->source_text;
        $sort_column = $request->sort_column;
        $sort_order = $request->sort_order;
        return view("journalBySourceContent", compact('period', 'source_id', 'source_text', 'client_id', 'client_fyear', 'sort_column', 'sort_order'))->render();
    }
    public function JournalsBySourceContentByPeriod(Request $request)
    {
        $client_id = $request->client_id;
        $client_fyear = $request->client_fyear;
        $period = $request->period;
        $source_text = $request->source_text;
        $sort_column = $request->sort_column;
        $sort_order = $request->sort_order;
        return view("journalByPeriodContent", compact('period', 'source_text', 'client_id', 'client_fyear', 'sort_column', 'sort_order'))->render();
    }
    public function JournalsProgressReportContent(Request $request)
    {
        $client_id = $request->client_id;
        $client_fyear = $request->client_fyear;
        $fiscal_year = $request->fiscal_year;
        $sort_column = $request->sort_column;
        $sort_order = $request->sort_order;
        return view("journalProgressReportContent", compact('fiscal_year', 'client_id', 'client_fyear', 'sort_column', 'sort_order'))->render();
    }
    public function JournalsTrialBalance(Request $request)
    {
        $client_id = $request->client_id;
        $client_fyear = $request->client_fyear;
        $fiscal_year = $request->fiscal_year;
        $sort_column = $request->sort_column;
        $sort_order = $request->sort_order;
        return view("journalNewTrialBalanceContent", compact('fiscal_year', 'client_id', 'client_fyear', 'sort_column', 'sort_order'))->render();
    }
    public function JournalsExtraSources(Request $request)
    {
        $client_id = $request->client_id;
        $client_fyear = $request->client_fyear;
        $fiscal_year = $request->fiscal_year;
        $excludedIds = [3, 21, 5, 4, 23, 20];
        $column_no = 9;
        $html = '';

        $extra = DB::table('journals as j')
            ->where('j.client', $client_id)
            ->where('j.fyear', $fiscal_year)
            ->where('j.is_deleted', 0)
            ->join('source_code as sc', function ($join) use ($excludedIds) {
                $join->on('sc.id', 'j.source')->whereNotIn('sc.id', $excludedIds);
            })
            ->select('sc.source_code')
            ->distinct()
            ->orderBy('sc.source_code', 'asc')
            ->get();
        foreach ($extra as $item) {
            $html .= '<div class="text-center mr-2 px-2 sortable" data-column="column_' . $column_no . '" data-order="asc"
                            style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 80px; border: 1px solid #ECEFF4;">
                            <a href="javascript:void();"
                                style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">' . $item->source_code . '</a>
                    </div>';
            $column_no++;
        }
        return $html;
    }
    public function JournalsBySourceContentByAccount(Request $request)
    {
        $client_id = $request->client_id;
        $client_fyear = $request->client_fyear;
        $period = $request->period;
        $account = $request->account;
        $source_text = $request->source_text;
        $sort_column = $request->sort_column;
        $sort_order = $request->sort_order;
        return view("journalByAccountContent", compact('period', 'source_text', 'account', 'client_id', 'client_fyear', 'sort_column', 'sort_order'))->render();
    }
    public function getSources(Request $request)
    {
        $client = $request->input('client');
        $year = $request->input('year');
        // Get distinct source IDs from `journals`
        $sourceIds = DB::table('journals')
            ->where('client', $client)
            ->where('fyear', $year)
            ->distinct()
            ->where('is_deleted', 0)
            ->pluck('source');
        // Get source codes from `source_code` table
        $sourceData = DB::table('source_code')
            ->where('is_deleted', 0)
            ->whereIn('id', $sourceIds)
            ->orderBy('source_code', 'asc')
            ->get();
        return response()->json(['sources' => $sourceData]);
    }
    public function getAccounts(Request $request)
    {
        $client = $request->input('client');
        $year = $request->input('year');
        $accounts = DB::table('journals')
            ->where('client', $client)
            ->where('fyear', $year)
            ->select('account_no')
            ->distinct()
            ->where('is_deleted', 0)
            ->orderBy('account_no', 'asc')
            ->get();
        return response()->json(['accounts' => $accounts]);
    }
    public function getAllYears(Request $request)
    {
        $client = $request->input('client_id');
        $years = DB::table('journals')
            ->select('fyear')
            ->distinct()
            ->where('client', $client)
            ->where('is_deleted', 0)
            ->orderBy('fyear', 'asc')
            ->get();
        return response()->json(['years' => $years]);
    }
    public function delete_journal(Request $request)
    {
        $journal_id = $request->journalId;
        DB::table('journals')->where('edit_no', $journal_id)->update([
            "deleted_at" => date("Y-m-d H:i:s"),
            "is_deleted" => 1,
        ]);
        return response()->json('success');
    }
    public function undo_delete_journal(Request $request)
    {
        $journal_id = $request->journalId;
        DB::table('journals')->where('edit_no', $journal_id)->update([
            "deleted_at" => null,
            "is_deleted" => 0,
        ]);
        return response()->json('success');
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
            ->Join("clients_gifi as g", function ($join) use ($client_id) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where('g.is_deleted', 0);
                $join->where('g.account_type', 'Asset');
                $join->where('g.sub_type', 'Current asset');
                $join->where('g.client_id', $client_id);
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
            ->Join("clients_gifi as g", function ($join) use ($client_id) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where('g.is_deleted', 0);
                $join->where('g.account_type', 'Asset');
                $join->where('g.sub_type', 'Capital asset');
                $join->where('g.client_id', $client_id);
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
                $join->where('g.client_id', $client_id);
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
                $join->where('g.client_id', $client_id);
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
            ->Join("clients_gifi as g", function ($join) use ($client_id) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where('g.is_deleted', 0);
                $join->where('g.account_type', 'Liability');
                $join->where('g.sub_type', 'Long-term liability');
                $join->where('g.client_id', $client_id);
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
            ->Join("clients_gifi as g", function ($join) use ($client_id) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where('g.is_deleted', 0);
                $join->where('g.account_type', 'Liability');
                $join->where('g.sub_type', 'Equity');
                $join->where('g.client_id', $client_id);
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
        // $retained_earnings = DB::table('journals as j')
        //     ->where('j.is_deleted', 0)
        //     ->where('j.client', $client_id)
        //     ->Join("clients_gifi as g", function ($join) use ($client_id) {
        //         $join->on("j.account_no", "=", "g.account_no")
        //             ->where('g.is_deleted', 0);
        //         $join->where('g.sub_type', 'Equity');
        //         $join->orWhere('g.sub_type', 'Retained earning/deficit');
        //         $join->orWhere('g.sub_type', 'Contra-Equity Account');
        //         $join->where('g.client_id', $client_id);
        //     })
        //     ->groupBy("j.account_no")
        //     ->select(
        //         "j.account_no",
        //         "g.description",
        //         "g.account_type",
        //         "g.sub_type",
        //         "j.client",
        //         "j.fyear",
        //     )
        //     ->orderBy('j.account_no', 'asc')
        //     ->get();
        $retained_earnings = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.client', $client_id)
            ->join('clients_gifi as g', function ($join) use ($client_id) {
                $join->on('j.account_no', '=', 'g.account_no')
                    ->where('g.is_deleted', 0)
                    ->where('g.client_id', $client_id)
                    ->whereIn('g.sub_type', [
                        'Equity',
                        'Retained earning/deficit',
                        'Contra-equity account'
                    ]);
            })
            ->groupBy(
                'j.account_no',
                'g.description',
                'g.account_type',
                'g.sub_type',
                'j.client',
                'j.fyear'
            )
            ->select(
                'j.account_no',
                'g.description',
                'g.account_type',
                'g.sub_type',
                'j.client',
                'j.fyear'
            )
            ->orderByRaw("FIELD(g.sub_type, 'Equity', 'Retained earning/deficit', 'Contra-equity account')")
            ->orderBy('j.account_no', 'asc')
            ->get();
        /**
         * Statement of Income
         * DIVIDE INTO 3 PARTS
         * REVENUE
         * COST OF SALES
         * EXPENSES
         */
        // $revenue = DB::table('journals as j')
        //     ->where('j.is_deleted', 0)
        //     ->where('j.client', $client_id)
        //     ->Join("clients_gifi as g", function ($join) use ($client_id) {
        //         $join->on("j.account_no", "=", "g.account_no")
        //             ->where('g.is_deleted', 0);
        //         $join->whereIn('g.sub_type', [
        //             'Operating revenue',
        //             'Non-operating revenue',
        //             'Miscellaneous income'
        //         ]);
        //         $join->where('g.client_id', $client_id);
        //     })
        //     ->groupBy("j.account_no")
        //     ->select(
        //         "j.account_no",
        //         "g.description",
        //         "g.account_type",
        //         "g.sub_type",
        //         "j.client",
        //         "j.fyear"
        //     )
        //     ->orderBy('j.account_no', 'asc')
        //     ->get();
        $revenue = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.client', $client_id)
            ->join('clients_gifi as g', function ($join) use ($client_id) {
                $join->on('j.account_no', '=', 'g.account_no')
                    ->where('g.is_deleted', 0)
                    ->where('g.account_type', 'Revenue')
                    ->whereIn('g.sub_type', [
                        'Operating revenue',
                        'Non-operating revenue',
                        'Miscellaneous income'
                    ])
                    ->where('g.client_id', $client_id);
            })
            ->groupBy(
                'j.account_no',
                'g.description',
                'g.account_type',
                'g.sub_type',
                'j.client',
                'j.fyear'
            )
            ->select(
                'j.account_no',
                'g.description',
                'g.account_type',
                'g.sub_type',
                'j.client',
                'j.fyear'
            )
            ->orderByRaw("FIELD(g.sub_type, 'Operating revenue', 'Non-operating revenue', 'Miscellaneous income')")
            ->orderBy('j.account_no', 'asc')
            ->get();
        $cost_of_sales = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where('j.client', $client_id)
            ->Join("clients_gifi as g", function ($join) use ($client_id) {
                $join->on("j.account_no", "=", "g.account_no")->where('g.is_deleted', 0);
                $join->where('g.account_type', 'Expense');
                $join->where('g.sub_type', 'Cost of sale');
                $join->where('g.client_id', $client_id);
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
            ->Join("clients_gifi as g", function ($join) use ($client_id) {
                $join->on("j.account_no", "=", "g.account_no")
                    ->where('g.is_deleted', 0);
                $join->where('g.account_type', 'Expense');
                $join->where('g.sub_type', 'Operating expense');
                $join->where('g.client_id', $client_id);
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
        //return response()->json(DB::getQueryLog());
        return view("JournalTrialBalanceContent", compact("fyear", "client", "system_settings", "reports"));
    }
    public function JournalReportRemittanceStatus(Request $request)
    {
        $clients = $request->input('report_client');
        $type = $request->input('report_type');
        $month = $request->input('report_month');
        $year = $request->input('report_year');
        if ($clients == null || $clients == "") {
            $clients = DB::table('clients')->where('is_deleted', 0)->pluck('id')->toArray();
        }
        return view("JournalRemittanceStatus", compact("clients", "type", "month", "year"));
    }
    public function RemitanceReportRemittanceStatus(Request $request)
    {
        $clients = $request->input('report_client');
        $type = $request->input('report_type');
        $month = $request->input('report_month');
        $year = $request->input('report_year');
        if ($clients == null || $clients == "") {
            $clients = DB::table('clients')->where('is_deleted', 0)->pluck('id')->toArray();
        }
        return view("remittanceRemittanceStatus", compact("clients", "type", "month", "year"));
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
    public function RemittanceReportRemittanceStatusLoadContent(Request $request)
    {
        $filters = (object) [
            "clients" => json_decode($request->get('clients')),
            "type" => trim($request->get('type')),
            "month" => $request->get('month'),
            "year" => $request->get('year'),
        ];
        $clients = DB::table('clients')->where('is_deleted', 0)->whereIn("id", $filters->clients)->get();
        $system_settings = DB::table('system_settings')->where('id', 1)->first();
        return view("remittanceRemittanceStatusContent", compact("filters", "clients", "system_settings"))->render();
    }
    public function JournalReportProgress(Request $request)
    {
        $clients = $request->input('report_client');
        $type = $request->input('report_type');
        $fiscal_year = $request->input('report_fiscal_year');
        $fiscal_years = $request->input('report_fiscal_years');
        if ($type == "By Fiscal Year" && ($fiscal_years == null || $fiscal_years == "")) {
            $fiscal_years = DB::table('journals')->where('is_deleted', 0)->distinct()->pluck('fyear')->toArray();
        }
        if ($clients == null || $clients == "") {
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
        $periods = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        if ($filters->period) {
            if (count($filters->period) > 0) {
                $periods = $filters->period;
            }
        }
        return view("JournalReportContent", compact("rollups", "client", "accounts", "sources", "periods", "filters"))->render();
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
        $data['client_gifi'] = DB::table('clients_gifi')->where('is_deleted', 0)->where('client_id', $client_id)->orderBy("account_no", "asc")->get();
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
        if ($data['p1_journals'] > 0) {
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
            if ($p1_debits == $p1_credits) {
                $data['p1_indicator'] = 'indicator-period-balance';
            } else {
                $data['p1_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p1_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p2_journals'] > 0) {
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
            if ($p2_debits == $p2_credits) {
                $data['p2_indicator'] = 'indicator-period-balance';
            } else {
                $data['p2_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p2_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p3_journals'] > 0) {
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
            if ($p3_debits == $p3_credits) {
                $data['p3_indicator'] = 'indicator-period-balance';
            } else {
                $data['p3_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p3_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p4_journals'] > 0) {
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
            if ($p4_debits == $p4_credits) {
                $data['p4_indicator'] = 'indicator-period-balance';
            } else {
                $data['p4_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p4_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p5_journals'] > 0) {
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
            if ($p5_debits == $p5_credits) {
                $data['p5_indicator'] = 'indicator-period-balance';
            } else {
                $data['p5_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p5_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p6_journals'] > 0) {
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
            if ($p6_debits == $p6_credits) {
                $data['p6_indicator'] = 'indicator-period-balance';
            } else {
                $data['p6_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p6_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p7_journals'] > 0) {
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
            if ($p7_debits == $p7_credits) {
                $data['p7_indicator'] = 'indicator-period-balance';
            } else {
                $data['p7_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p7_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p8_journals'] > 0) {
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
            if ($p8_debits == $p8_credits) {
                $data['p8_indicator'] = 'indicator-period-balance';
            } else {
                $data['p8_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p8_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p9_journals'] > 0) {
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
            if ($p9_debits == $p9_credits) {
                $data['p9_indicator'] = 'indicator-period-balance';
            } else {
                $data['p9_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p9_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p10_journals'] > 0) {
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
            if ($p10_debits == $p10_credits) {
                $data['p10_indicator'] = 'indicator-period-balance';
            } else {
                $data['p10_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p10_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p11_journals'] > 0) {
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
            if ($p11_debits == $p11_credits) {
                $data['p11_indicator'] = 'indicator-period-balance';
            } else {
                $data['p11_indicator'] = 'indicator-period-not-balance';
            }
        } else {
            $data['p11_indicator'] = 'indicator-period-no-journals';
        }
        if ($data['p12_journals'] > 0) {
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
            if ($p12_debits == $p12_credits) {
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
        $status = '';
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
            $status = 'found';
        } else {
            $status = 'not found';
        }
        return response()->json(compact('account', "fyear_balance_debit", "fyear_balance_credit", "status"));
    }
    public function get_sources(Request $request)
    {
        $data = DB::table('source_code')->where('source_code', $request->get('dt_source_code'))->where('is_deleted', 0)->first();
        if ($data) {
            $status = 'found';
        } else {
            $status = 'not found';
        }
        return response()->json(compact('data', 'status'));
    }
    public function get_client_gifi(Request $request)
    {
        return response()->json(DB::table('clients_gifi')->where('is_deleted', 0)
            ->where('client_id', $request->get('client_id'))
            ->orderBy('description', 'asc')
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
        if ($data['portion'] < 100) {
            $client = DB::table('clients')->where('id', $data['client'])->where('is_deleted', 0)->first();
            if ($data['taxable'] == 1) {
                $data['net'] = round(($data['portion'] / 100) * $data['net'], 2);
                $data['tax1'] = round(($data['portion'] / 100) * $data['tax1'], 2);
                $data['tax2'] = round(($data['portion'] / 100) * $data['tax2'], 2);
                $portion_journal = $data;
                $portion_journal['account_no'] = @$client->dividends_account;
                $portion_journal['description'] = "Personal Portion " . $portion_journal['description'];
                if ($data['original_debit'] > $data['original_credit']) {
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
                if ($data['original_debit'] > $data['original_credit']) {
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
        // dd($request->all());
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
            // "debit" => $request->post('amnt_debit_edit') ?? 0,
            // "credit" => $request->post('amnt_credit_edit') ?? 0,
            "debit" => (float) str_replace(',', '', $request->post('amnt_debit_edit')) ?? 0,
            "credit" => (float) str_replace(',', '', $request->post('amnt_credit_edit')) ?? 0,
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
    private function getShortMonthName($monthNumber)
    {
        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        return $monthNames[$monthNumber] ?? 'Invalid month number';
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
                                        for="example-hf-email">Account# &nbsp;  <a href="javascript:void(0);" client-id="' . $q->client . '" class="view-accounts-chart-edit-1"
                                        id="view-accounts-chart"><img src="' . asset('public') . '/icons2/icon-info.png"
                                            width="16"></a></label>
                                    <div class="col-sm-3  form-group">
                                        <input type="" class="form-control" id="dt_account_edit" name="dt_account_edit"
                                            placeholder="4500" list="dt_account_description_list3" value="' . $q->account_no . '">
                                            <datalist id="dt_account_description_list3">';
        foreach ($clientsGifi as $cg) {
            $html .= '<option value="' . $cg . '"/>';
        }
        $source = DB::table('source_code')->where('id', $q->source)->where('is_deleted', 0)->first();
        $html .= '</datalist>
                                    </div>
                                    <div class="col-sm-7 form-group">
                                        <div class="bubble-white-new2 w-100 bubble-text-first dt-account-description-edit">' . $q->account_description . '
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">
                                    <label class="col-sm-2 col-form-label" for="example-hf-email">Source &nbsp;  <a href="javascript:void(0);" data-toggle="modal" data-target="#editSourceModel"
                                        id="view-accounts-chart"><img src="' . asset('public') . '/icons2/icon-info.png"
                                            width="16"></a></label>
                                    <div class="col-sm-3  form-group">
                                    <input type="text" name="dt_source_code_edit_" id="dt_source_code_edit_"
                                                class="form-control" maxlength="4" list="sorce_list_edit" value="' . $source->source_code . '">
                                    <input type="hidden" name="dt_source_code_edit" id="dt_source_code_edit"
                                                class="form-control" value="' . $source->id . '">
                                        <datalist id="sorce_list_edit">';
        $sources = DB::table('source_code')->where('is_deleted', 0)->get();
        foreach ($sources as $s) {
            $html .= '<option value="' . $s->source_code . '" >';
        }
        $date_new = trim($q->date);
        $formattedDate = Carbon::createFromFormat('dmY', $date_new)->format('Y-m-d');
        $formattedDateOutput = Carbon::parse($formattedDate)->format('d-M-Y');
        $html .= '</datalist>
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
                                                    ' . $formattedDateOutput . '
                                                </div>
                                                <input type="hidden" name="translation_edit"
                                                    value="' . $formattedDateOutput . '">
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
                                        value="' . number_format($q->debit, 2) . '">
                                </div>
                                <label class="col-sm-2 col-form-label"
                                    for="example-hf-email">Credit</label>
                                <div class="col-sm-3  form-group">
                                    <input type="" class="form-control form-credit text-left"
                                        id="amnt_credit_edit" name="amnt_credit_edit" placeholder="0.00"
                                        value="' . number_format($q->credit, 2) . '">
                                </div>
                                <div class="col-md-2">
                                                    <button type="submit" class="btn mr-3 btn-new ">Save</button>
                                                </div>
                            </div>
                                ';
        return response()->json($html);
    }
    public function getClientJournalEditContent_(Request $request, $edit_no)
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
                                    <div class="col-lg-3">
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
                                <div class="row form-group  ">
                                    <div class="col-lg-3">
                                        <label class="col-form-label">Fiscal Year</label>
                                    </div>
                                    <div class="col-lg-3 ">
                                        <select type="" name="pp_year_edit" class="form-control" placeholder="" > ';
        for ($y = (intval(date("Y")) + 1); $y >= 1930; $y--) {
            $html .= '<option value="' . $y . '" ' . ($y == $q->year ? 'selected' : '') . '>' . $y . '</option>';
        }
        $html .= '</select>
                                    </div>
                                    <div class="col-lg-3 pt-2">
                                        <div style="padding: 0px 10px; border: 1px solid black; color: black; width: fit-content; text-align: center; border-radius: 5px;" class="w-100 bubble-text-first pp_fyear_edit">
                                        ' . $q->fyear . '
                                        </div>
                                        <input type="hidden" name="pp_fyear_edit" value="' . $q->fyear . '">
                                        <input type="hidden" name="pp_month_edit" value="' . $q->month . '">
                                    </div>
                                </div>
                                <div class="row form-group  ">
                                    <div class="col-lg-3">
                                        <label class="col-form-label">Period</label>
                                    </div>
                                    <div class="col-lg-3">
                                        <select type="" name="pp_period_edit" class="form-control" placeholder="" >
                                                ';
        for ($m = 1; $m <= 12; $m++) {
            $html .= '<option value="' . $m . '" ' . ($m == $q->period ? 'selected' : '') . '>' . $m . '</option>';
        }
        $html .= '</select>
                                    </div>
                                    <div class="col-lg-3 pt-2">
                                        <div style="padding: 0px 10px; border: 1px solid black; color: black; width: fit-content; text-align: center; border-radius: 5px;" class="w-100 bubble-text-first pp_period_edit">' . $this->getMonthName($q->month) . '
                                        </div>
                                        <input type="hidden" name="pp_period_edit" value="' . $q->period . '">
                                    </div>
                                </div>
                                ';
        $clientsGifi = DB::table('clients_gifi')->where('is_deleted', 0)->where('client_id', $q->client)->pluck('account_no')->toArray();
        $source = DB::table('source_code')->where('id', $q->source)->where('is_deleted', 0)->first();
        $html .= '
<div class="row ">
                                    <label class="col-sm-3 col-form-label" for="example-hf-email">Source &nbsp;</label>
                                    <div class="col-sm-3  form-group">
                                    <input type="text" name="dt_source_code_edit_" id="dt_source_code_edit_"
                                                class="form-control" maxlength="4" list="sorce_list_edit" value="' . $source->source_code . '">
                                    <input type="hidden" name="dt_source_code_edit" id="dt_source_code_edit"
                                                class="form-control" value="' . $source->id . '">
                                        <datalist id="sorce_list_edit">';
        $sources = DB::table('source_code')->where('is_deleted', 0)->get();
        foreach ($sources as $s) {
            $html .= '<option value="' . $s->source_code . '" >';
        }
        $date_new = trim($q->date);
        $formattedDate = Carbon::createFromFormat('dmY', $date_new)->format('Y-m-d');
        $formattedDateOutput = Carbon::parse($formattedDate)->format('d-M-Y');
        $html .= '</datalist>
                                    </div>
                                    <div class="col-sm-6 form-group pt-2">
                                        <div style="padding: 0px 10px; border: 1px solid black; color: black; width: fit-content; text-align: center; border-radius: 5px;" class=" bubble-text-first dt-source-description-edit">' . $q->source_description . '
                                        </div>
                                    </div>
                                </div>
                                <div style="border: 1px dashed lightgrey; margin-bottom: 25px;"></div>
                                <div class="row ">
                                    <label class="col-sm-3 col-form-label"
                                        for="example-hf-email">Account# &nbsp;  <a href="javascript:void(0);" client-id="' . $q->client . '" class="view-accounts-chart-edit-1"
                                        id="view-accounts-chart"><img src="' . asset('public') . '/icons_2024_02_24/icon-change-account-grey-off.png"
                                            width="16"></a></label>
                                    <div class="col-sm-3  form-group">
                                        <input type="" class="form-control" id="dt_account_edit" name="dt_account_edit"
                                            placeholder="4500" list="dt_account_description_list3" value="' . $q->account_no . '">
                                            <datalist id="dt_account_description_list3">';
        foreach ($clientsGifi as $cg) {
            $html .= '<option value="' . $cg . '"/>';
        }
        $html .= '</datalist>
                                    </div>
                                    <div class="col-sm-6 form-group pt-2">
                                        <div style="padding: 0px 10px; border: 1px solid black; color: black; width: fit-content; text-align: center; border-radius: 5px;" class=" bubble-text-first dt-account-description-edit">' . $q->account_description . '
                                        </div>
                                    </div>
                                </div>
                                <div style="border: 1px dashed lightgrey; margin-bottom: 25px;"></div>
                                <div class=" row ">
                                    <label class="col-sm-3 col-form-label" for="example-hf-email">Ref#</label>
                                    <div class="col-sm-4  form-group">
                                        <input type="" class="form-control" id="dt_ref_edit" name="dt_ref_edit"
                                            placeholder="00000000" value="' . $q->ref_no . '" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title="00000000" data-original-title="00000000">
                                    </div>
                                </div>
                                <div class=" row ">
                                    <label class="col-sm-3 col-form-label"
                                        for="example-hf-email">Date</label>
                                    <div class="col-sm-4  form-group">
                                        <input type="" class="form-control" id="dt_date_edit" name="dt_date_edit"
                                                    placeholder="DDMMYYYY" value="' . $q->date . '" data-toggle="tooltip"
                                                    data-trigger="hover" data-placement="top" title="DDMMYYYY" data-original-title="DDMMYYYY">
                                    </div>
                                    <div class="col-sm-3 pt-2">
                                                <div style="padding: 0px 10px; border: 1px solid black; color: black; width: fit-content; text-align: center; border-radius: 5px;" class=" bubble-text-first translation-edit">
                                                    ' . $formattedDateOutput . '
                                                </div>
                                                <input type="hidden" name="translation_edit"
                                                    value="' . $formattedDateOutput . '">
                                            </div>
                                </div>
                                <div class=" row ">
                                <div class="col-sm-12  form-group">
                                <label class="col-form-label"
                                    for="example-hf-email">Description</label>
                                        <input type="" class="form-control" list="dt_description_list" id="dt_description_edit"
                                            name="dt_description_edit" placeholder="Journal Description" data-toggle="tooltip"
                                            data-trigger="hover focus" data-placement="top" title="Journal Description"
                                            data-original-title="Journal Description" value="' . $q->description . '">
                                    </div>
                                </div>
                                <div class="form-group row">
                                <div class="col-sm-5  form-group">
                                <label class="col-form-label"
                                    for="example-hf-email">Debit</label>
                                    <input type="" class="form-control form-debit text-left"
                                        id="amnt_debit_edit" name="amnt_debit_edit" placeholder="0.00"
                                        value="' . number_format($q->debit, 2) . '">
                                </div>
                                <div class="col-sm-1  form-group"></div>
                                <div class="col-sm-5  form-group">
                                <label class="col-form-label"
                                    for="example-hf-email">Credit</label>
                                    <input type="" class="form-control form-credit text-left"
                                        id="amnt_credit_edit" name="amnt_credit_edit" placeholder="0.00"
                                        value="' . number_format($q->credit, 2) . '">
                                </div>
                                <div class="col-md-12" style="display: flex; justify-content: space-between;">
                                <div class="action-button text-center mr-2">
                                <a href="javacsript:void();" data-toggle="tooltip"
                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Delete" class="btn-delete" data-place="1" data-id="' . $q->edit_no . '"
                                    data-editno="' . $q->editNo . '"><img
                                        src="' . asset('public') . '\icons2\icon-delete-grey.png" alt=""
                                        width="26px"></a>
                            </div>
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
        $clients = DB::table('clients')->where('is_deleted', 0)->orderBy('display_name', 'asc')->get();
        $account = DB::table('clients_gifi')->where('client_id', $q->client)->where('account_no', $q->account_no)->where('is_deleted', 0)->first();
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
                <div id="ajax-overlay"></div>
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
            $html .= '<option remittance="' . $c->tax_remittance . '" fiscal-start="' . $c->fiscal_start . '" value="' . $c->id . '" ' . ($c->id == $q->client ? 'selected' : '') . '>' . $c->display_name . '</option>';
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
        $source = DB::table('source_code')->where('id', $q->source)->where('is_deleted', 0)->first();
        $html .= '</datalist>
                                            </div>
                                    <div class="col-sm-7 form-group fg-evenly">
                                        <div class="col-sm-5 col-form-label text-nowrap translation dt-account-description-edit" style="color: #252525">' . $account->description . '
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">
                                    <label class="col-sm-2 col-form-label" for="example-hf-email">Source &nbsp;  <a href="javascript:void(0);" data-toggle="modal" data-target="#editSourceModel"
                                        id="view-accounts-chart"><img src="' . asset('public') . '/icons2/icon-info.png"
                                            width="16"></a></label>
                                    <div class="col-sm-3  form-group fg-evenly">
                                        <input type="text" name="dt_source_code_edit_" id="dt_source_code_edit_"
                                                class="form-control" maxlength="4" list="sorce_list_edit" value="' . $source->source_code . '">
                                    <input type="hidden" name="dt_source_code_edit" id="dt_source_code_edit"
                                                class="form-control" value="' . $source->id . '">
                                        <datalist id="sorce_list_edit">';
        $sources = DB::table('source_code')->where('is_deleted', 0)->orderBy('source_code', 'ASC')->get();
        foreach ($sources as $s) {
            $html .= '<option value="' . $s->source_code . '" >';
        }
        $date_new = trim($q->date);
        $formattedDate = Carbon::createFromFormat('dmY', $date_new)->format('Y-m-d');
        $formattedDateOutput = Carbon::parse($formattedDate)->format('d-M-Y');
        $html .= '</datalist>
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
                                            placeholder="00000000" value="' . $q->ref_no . '" >
                                    </div>
                                    <div class="col-sm-7 form-group fg-evenly">
                                        <div class="row ">
                                            <label class="col-sm-2 col-form-label"
                                                for="example-hf-email">Date</label>
                                            <div class="col-sm-5  ">
                                                <input type="" class="form-control" id="dt_date_edit" name="dt_date_edit"
                                                    placeholder="DDMMYYYY" value="' . $q->date . '">
                                            </div>
                                            <div class="col-sm-5  ">
                                                <div class="col-sm-5 col-form-label text-nowrap translation translation-edit" style="color: #252525">
                                                    ' . $formattedDateOutput . '
                                                </div>
                                                <input type="hidden" name="translation_edit"
                                                    value="' . $formattedDateOutput . '">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" row ">
                                    <label class="col-sm-2 col-form-label"
                                        for="example-hf-email">Description</label>
                                    <div class="col-sm-10  form-group fg-evenly">
                                        <input type="" class="form-control" list="dt_description_list" id="dt_description_edit"
                                            name="dt_description_edit" placeholder="Journal Description" value="' . $q->description . '">
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
                                            <input type="" class="form-control form-debit text-right"
                                                id="amnt_debit_edit" name="amnt_debit_edit" placeholder="0.00"
                                                value="' . number_format($q->debit, 2, '.', '') . '">
                                        </div>
                                        <label class="col-sm-2 col-form-label"
                                            for="example-hf-email">Credit</label>
                                        <div class="col-sm-4  form-group fg-evenly">
                                            <input type="" class="form-control form-credit text-right"
                                                id="amnt_credit_edit" name="amnt_credit_edit" placeholder="0.00"
                                                value="' . number_format($q->credit, 2, '.', '') . '">
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
            ->select("j.*", "c.firstname", "c.lastname", "c.company", "c.display_name", "sc.source_code", "sc.source_description", "g.description as account_description")
            ->first();
        $user = DB::table('users')->where('id', $q->edit_by)->first();
        $account = DB::table('clients_gifi')->where('client_id', $q->client)->where('account_no', $q->account_no)->where('is_deleted', 0)->first();
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
            $html .= '<a href="javascript:;" d class="text-white   btnEdit MajorEdit" Fyear="' . $q->fyear . '" client="' . $q->client . '" data="' . $q->edit_no . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>
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
        $date_new = trim($q->date);
        $formattedDate = Carbon::createFromFormat('dmY', $date_new)->format('Y-m-d');
        $formattedDateOutput = Carbon::parse($formattedDate)->format('d-M-Y');
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
                         <div class=" -new col-form-label" data="' . $q->edit_no . '">' . $account->description . '</div>
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
                             <div class="bubble-white-new1 bubble-text-first" data="' . $formattedDateOutput . '">' . $formattedDateOutput . '</div>
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
               <div class="bubble-debit bubble-debit-wval provinceText" style="font-family: Jura !important;text-align: right !important;" data="' . $q->edit_no . '">' . number_format($q->debit, 2) . '</div>
            </div>
            <div class="col-sm-2">
                <div class=" -new col-form-label">Credit</div>
            </div>
            <div class="col-sm-4">
                <div class="bubble-credit bubble-credit-wval provinceText" style="font-family: Jura !important;text-align: right !important;" data="' . $q->edit_no . '">' . number_format($q->credit, 2) . '</div>
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
        return response()->json(['html' => $html, 'client' => $q->display_name, 'fiscalYear' => $q->fyear]);
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
    public function undoJournalBatchDelete(Request $request)
    {
        $journals = json_decode($request->post('journals')) ?? [];
        DB::table('journals')->whereIn('edit_no', $journals)->update([
            "deleted_at" => null,
            "is_deleted" => 0,
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
        $account = $request->post('account_no');
        $journals = json_decode($request->post('journals')) ?? [];
        $update = [];
        if ($client != "") {
            $update["client"] = $client;
        }
        if ($month != "") {
            $update["month"] = $month;
        }
        if ($year != "") {
            $update["year"] = $year;
        }
        if ($fyear != "") {
            $update["fyear"] = $fyear;
        }
        if ($period != "") {
            $update["period"] = $period;
        }
        if ($source != "") {
            $update["source"] = $source;
        }
        if ($account != "") {
            $update["account_no"] = $account;
            $update["original_account"] = $account;
        }
        $count = 0;
        $oldstates = [];
        foreach ($journals as $journal) {
            // Store the original old data before updating
            $oldstates[$journal] = DB::table('journals')->where('edit_no', $journal)->first();
            DB::table('journals')->where('edit_no', $journal)->update($update);
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
            return response()->json(['status' => 'error', 'message' => 'Nothing to undo']);
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
        if ($validated) {
            $journals = DB::table('journals')->where('is_deleted', 0)->where('client', $request->input('reindex_client'))->where('fyear', $request->input('reindex_fiscal_year'))->orderBy('created_at', 'asc')->pluck('edit_no')->toArray();
            $edit_no = 1;
            foreach ($journals as $j) {
                DB::table('journals')->where('edit_no', $j)->update([
                    "editNo" => $edit_no,
                ]);
                DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Reindexed ', 'journal_id' => $j]);
                $edit_no++;
            }
            return redirect()->back()->with('success', $edit_no - 1 . ' journals reindexed');
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
                    $query->orWhere('j.editNo', 'like', '%' . $searchVal . '%');
                }
                if (!empty($dateCreated)) {
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
                "c.logo",
                "c.display_name",
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
        $html .= ' <div class="client-journals-view"><!--startblock-->';
        foreach ($journals as $q) {
            $amount_clr = '';
            $border_clr = '';
            $bubble_class = '';
            $amount = 0;
            $symbol = '';
            if ($q->debit > $q->credit) {
                $bubble_class = 'bubble_debit';
                $amount_clr = '#4194F6';
                $border_clr = '#4194F6';
                $amount = $q->debit;
                $symbol = 'DR';
            } else {
                $bubble_class = 'bubble_credit';
                $amount_clr = '#C41E3A';
                $border_clr = '#C41E3A';
                $amount = $q->credit;
                $symbol = 'CR';
            }
            $html .= '
                <div class="block block-rounded   table-block-new mb-2 pb-0 view-------Content  -  journal-view" data="' . $q->edit_no . '"
                    style="cursor:pointer;padding-left: 0px !important;padding-right: 0px !important;">
                    <div class="block-content pt-1 pb-1 pl-1 d-flex  position-relative" style="">
                        <div class=" justify-content-center align-items-center  d-flex mr-1" style="width: 20%;float:: left;padding: 7px;">';
            if ($q->logo != '') {
                // $html .= '<img src="public/client_logos/' . $q->logo . '" class="rounded-circle  "
                //                  style="object-fit: cover;width: 65px;height: 65px;">';
                $html .= '<img src="public/img/icon-bubble-journal.png" class="rounded- circle  " style="object-fit: cover;width: 65px;height: 65px;">';
            } else {
                $html .= '<img src="public/img/icon-bubble-journal.png" class="rounded- circle  " style="object-fit: cover;width: 65px;height: 65px;">';
            }
            $html .= '</div>
                        <div class="d-flex justify-content-between" style="width: 70%;">
                            <div class="d-flex flex-column " style="width:calc(100% - 15px);">
                                <div style="overflow: hidden; text-overflow: ellipsis;">
                                    <span class="font-12pt mb-0 text-truncate font-w600 c1" style="font-family: Calibri;color:#626262 !important;">' . $q->display_name . '</span>
                                </div>
                            <div class="d-flex flex-row" style="padding-top: 3px;">
                                <span class="bubble-account" data-toggle="tooltip" data-trigger="hover"
                                                            data-placement="top" title="" data-original-title="Account #" style="
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size:9pt;width: fit-content;font-family: Calibri;
                                color: #626262;
                                border:1px solid #595959;
                                border-radius: 5px;
                                line-height: 1.6;
                                padding-left: 5px;
                                padding-right: 5px;
                                margin-right: 0.375rem;" >' . $q->account_no . '</span>
                                <span style="overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size:10pt;width: fit-content;font-family: Calibri;
                                color: #626262;
                                min-width: calc(100% - 160px);
                                line-height: 1.6;
                                padding-left: 5px;
                                padding-right: 5px;">' . $q->description . '</span>
                                </div>
                                <div class="d-flex flex-row" style="padding-top: 6px;margin-bottom: 5px;">
                                    <div>
                                                            <div data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                title="" data-original-title="Ref #"
                                                                style="line-height: 1.6;
                                        font-family: Calibri;
                                        width: 75px;
                                        text-align: center;
                                        font-size: 9pt;
                                        color:#989898;
                                        border:1px solid #A6A6A6;
                                        border-radius: 5px;
                                        margin-right: 0.675rem;
                                        white-space: nowrap;
           overflow: hidden;
           text-overflow: ellipsis;"
                                                                class="px-2 bubble_period">
                                                                 ' . $q->ref_no . '
                                                            </div>
                                                        </div>
                                    <div>
                                                            <div data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                title="" data-original-title="Fiscal Year"
                                                                style="line-height: 1.6;
                                        font-family: Calibri;
                                        width: fit-content;
                                        font-size: 9pt;
                                        color:#989898;
                                        border:1px solid #A6A6A6;
                                        border-radius: 5px;
                                        margin-right: 0.675rem;"
                                                                class="px-2 bubble_period">
                                                                ' . $q->fyear . '
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                                title="" data-original-title="Period"
                                                                style="line-height: 1.6;
                                        font-family: Calibri;
                                        width: fit-content;
                                        font-size: 9pt;
                                        color:#989898;
                                        border:1px solid #A6A6A6;
                                        border-radius: 5px;
                                        margin-right: 0.675rem;"
                                                                class="px-2 bubble_period">
                                                                ' . $q->period . '
                                                            </div>
                                                        </div>
                                </div>
                            </div>
                                <div style="position: absolute;right: 10px;top: 6px;">
                                    <span class="bubble_edit_no"
                                                        style="
                                                        float:right;
                                font-family: Calibri;
                                line-height: 1.5 !important;
                                        width: fit-content;
                                        font-size: 9pt;
                                        color:#4EA833;
                                        border:1px solid #4EA833;
                                        border-radius: 5px;
                                        padding-left: 10px;padding-right: 10px;">#' . $q->editNo . '</span>
                                </div>
                                <div class="d-flex flex-row justify-content-end" style="margin-top: 20px;position: absolute;right: 10px;bottom: 5px;">';
            if (Auth::check()) {
                if (@Auth::user()->role != 'read') {
                    $date_new = trim($q->date);
                    $formattedDate = Carbon::createFromFormat('dmY', $date_new)->format('Y-m-d');
                    $formattedDateOutput = Carbon::parse($formattedDate)->format('d/m/Y');
                    $html .= '
                    <div class="ml-1" style="display: flex;align-items: center;">
                                                                <div style="line-height: 1.6;
                                        font-family: Calibri;
                                        width: fit-content;
                                        font-size: 9pt;
                                        color:' . $amount_clr . ';
                                        border:1px solid ' . $border_clr . ';
                                        border-radius: 5px;"
                                                                    class="px-2 ' . $bubble_class . '">
                                                                    $ ' . number_format($amount, 2) . '
                                                                </div>
                                                            </div>
<div class="ActionIcon    ml-1" style="border-radius: 1rem"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title=""
                                                        data-original-title="' . $formattedDateOutput . '">
                                                        <a href="javascript:;" data="' . $q->edit_no . '" class=" ">
                                                            <img src="' . asset('public') . '/img/icon-bubble-date.png?cache=1"
                                                                width="28px">
                                                        </a>
                                                    </div>
                                <div class="ActionIcon px-0 ml-2    " style="border-radius: 5px">
                                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false" href="javascript:;" c>
                                                            <img src="' . asset('public') . '/img/dots.png?cache=1">
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary" style="min-width: 9rem;">
                                                            <a href="javascript:;" data="' . $q->edit_no . '" data-client-id="' . $q->client . '"
                                                                class="dropdown-item d-flex align-items-center px-0 btnEditClientJournal ">
                                                                <div style="width: 32;  padding-left: 2px"><img
                                                                        src="' . asset('public') . '/icons2/icon-edit-grey.png?cache=1"
                                                                        width="25px"> Edit</div>
                                                            </a>
                                                            <a href="javascript:;"
                                                                class="dropdown-item d-flex align-items-center px-0 btnDeleteClientJournal"
                                                                data="' . $q->edit_no . '" data-client-id="' . $q->client . '">
                                                                <div style="width: 32;  padding-left: 2px"><img
                                                                        src="' . asset('public') . '/icons2/icon-delete-grey.png?cache=1"
                                                                        width="25px"> Delete</div>
                                                            </a>
                                                        </div>
                                                    </div>';
                }
            }
            $html .= '</div>
                        </div>
                    </div>
                </div>';
        }
        if ($journals->lastPage() > 1) {
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
            if ($journals->currentPage() > 1) {
                $html .= '<li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                    <a class="page-link" href="javascript:void(0)" style="border-left:1px solid">&lt;</a>
                </li>';
            } else {
                $html .= '<li class="page-item">
                    <a class="page-link journal-page-link" href="javascript:void(0)" page-no="' . ($journals->currentPage() - 1) . '" rel="prev" aria-label="« Previous" style="border-left:1px solid">&lt;</a>
                </li>';
            }
            $html .= '<!-- Pagination Elements -->';
            $html .= '<!--<li class="page-item disabled" aria-disabled="true"><a class="page-link">...</a></li>-->';
            for ($i = max($journals->currentPage() - 2, 1); $i <= min(max($journals->currentPage() - 2, 1) + 4, $journals->lastPage()); $i++) {
                if ($journals->currentPage() == $i) {
                    $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="javascript:void(0);">' . $i . '</a></li>';
                } else {
                    $html .= '<li class="page-item "><a class="page-link journal-page-link" page-no="' . $i . '" href="javascript:void(0)">' . $i . '</a></li>';
                }
            }
            $html .= '<!-- Next Page Link -->';
            if ($journals->currentPage() < $journals->lastPage()) {
                $html .= '<li class="page-item">
                <a class="page-link journal-page-link" href="javascript:;" page-no="' . ($journals->currentPage() + 1) . '" rel="next" aria-label="Next »" style="border-right:1px solid">&gt;</a>
            </li>';
            } else {
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
    //     private function clientJournals(Request $request)
    //     {
    //         $limit = @$request->limitVal != "" ? $request->limitVal : 10;
    //         $client_id = $request->client_id;
    //         $month = $request->month;
    //         $year = $request->year;
    //         $fyear = $request->fyear;
    //         $searchVal = @$request->searchVal;
    //         $dateCreated = $request->dateCreated;
    //         $periodsArr = $request->periodsArr ?? [];
    //         $accountsArr = $request->accountsArr ?? [];
    //         $sourcesArr = $request->sourcesArr ?? [];
    //         $refsArr = $request->refsArr ?? [];
    //         $journals = DB::table('journals as j')
    //             ->where('j.is_deleted', 0)
    //             ->where('j.client', $client_id)
    //             ->where('j.fyear', $fyear)
    //             ->where(function ($query) use ($searchVal, $month, $dateCreated, $periodsArr, $accountsArr, $sourcesArr, $refsArr) {
    //                 if (!empty($searchVal)) {
    //                     $query->where('j.ref_no', 'like', '%' . $searchVal . '%');
    //                     $query->orWhere('j.description', 'like', '%' . $searchVal . '%');
    //                     $query->orWhere('j.editNo', 'like', '%' . $searchVal . '%');
    //                 }
    //                 if (!empty($dateCreated)) {
    //                     $query->where('j.created_at', '>=', date("Y-m-d", strtotime($dateCreated)) . " 00:00:00")
    //                         ->where('j.created_at', '<=', date("Y-m-d", strtotime($dateCreated)) . " 23:59:59");
    //                 }
    //                 if (count($periodsArr) > 0) {
    //                     //    $query->where('j.month', $month);
    //                     $query->whereIn('j.period', $periodsArr);
    //                 }
    //                 if (count($accountsArr) > 0) {
    //                     $query->whereIn("account_no", $accountsArr);
    //                 }
    //                 if (count($sourcesArr) > 0) {
    //                     $query->whereIn("source", $sourcesArr);
    //                 }
    //                 if (count($refsArr) > 0) {
    //                     $query->whereIn("ref_no", $refsArr);
    //                 }
    //             })
    //             ->leftJoin("source_code as sc", function ($join) {
    //                 $join->on("j.source", "=", "sc.id")
    //                     ->where('sc.is_deleted', 0);
    //             })
    //             ->Join("clients as c", function ($join) {
    //                 $join->on("j.client", "=", "c.id")
    //                     ->where("c.is_deleted", 0);
    //             })
    //             ->select(
    //                 "j.*",
    //                 "sc.source_code as source_code",
    //                 "c.logo"
    //             )
    //             ->orderBy("j.edit_no", "desc")
    //             ->paginate($limit, ['*'], 'journalPage', $request->journalPage);
    //         //         $html = '
    //         //             <div class="con   no-print page-header py-1  mb-3" style="border-radius:10px;height:54.2px !important;" id="">
    //         //                 <!-- Full Table -->
    //         //                 <div class="b   mb-0  ">
    //         //                     <div class="block-content pt-0 mt-0">
    //         //                         <div class="TopArea" style="position: sticky;
    //         //                             padding-top: 5px;
    //         //                             z-index: 1000;
    //         //     padding-bottom: 5px;">
    //         //     <div class="row" >
    //         //    <div class="col-sm-6">
    //         //  <!--<form class="push mb-0"   method="get" id="form-search"  >-->
    //         //                                 <div class="input-group">
    //         //                                     <input type="text"  class="form-control searchNew w-75" style="height:36px !important;" name="client-journal-search" value="' . $searchVal . '" data="' . $client_id . '" placeholder="Search Journals">
    //         //                                     <div class="input-group-append" style="width: 31px !important;">
    //         //                                         <span class="input-group-text">
    //         //                                               <img src="' . asset('public/img/ui-icon-search.png') . '" width="15px">
    //         //                                         </span>
    //         //                                     </div>
    //         //                                 </div>
    //         //                                  <div class="    float-left " role="tab" id="accordion2_h1">
    //         //                                    <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->
    //         //                                             </div>
    //         //                             <!--</form>-->
    //         // </div>
    //         // <div class="col-sm-6 pl-0 d-flex align-items-center"  style="">
    //         //      <span data-toggle="modal" id="btnFilterClientJournals" data-client-id="' . $client_id . '" data-bs-target="#filterClientJournalModal" data-target="#filterClientJournalModal">
    //         //       <button type="button" class="btn btn-dual d1   "   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Filter Journals"   >
    //         //                            <img src="' . asset('public/img/ui-icon-filters.png') . '" width="19px">
    //         //                         </button>
    //         //                     </span>
    //         //                     <span>
    //         //                     <button type="button" class="btn btn-dual d1   batch-selection"  data-selected="0"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Select Journals"   >
    //         //                     <img src="' . asset('public/batch_icons/icon-journals-select-all.png') . '" width="19px">
    //         //                  </button></span>
    //         //                  <span>
    //         //                  <button type="button" class="btn btn-dual d1   batch-update"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Batch Update"   >
    //         //                  <img src="' . asset('public/batch_icons/icon-journals-batchupdate.png') . '" width="19px">
    //         //               </button></span>
    //         //               <select name="client-journal-limit" class="float-right form-control ml-auto   px-0" style="width:auto">
    //         //               <option value="10" '.(@$limit==10?'selected':'').'>10</option>
    //         //               <option value="25" '.(@$limit==25?'selected':'').'>25</option>
    //         //               <option value="50" '.(@$limit==50?'selected':'').'>50</option>
    //         //               <option value="100" '.(@$limit==100?'selected':'').'>100</option>
    //         //               <option value="200" '.(@$limit==200?'selected':'').'>200</option>
    //         //               <option value="300" '.(@$limit==300?'selected':'').'>300</option>
    //         //               <option value="400" '.(@$limit==400?'selected':'').'>400</option>
    //         //               <option value="500" '.(@$limit==500?'selected':'').'>500</option>
    //         //           </select>
    //         //                     ';
    //         //         $html .= '</div></div></div></div></div></div></div></div>';
    //         $html = '';
    //         $html .= ' <div class="client-journals-view"><!--startblock-->';
    //         foreach ($journals as $q) {
    //             $amount_clr = '';
    //             $amount = 0;
    //             $symbol = '';
    //             if ($q->debit > $q->credit) {
    //                 $amount_clr = '#4194F6';
    //                 $amount = $q->debit;
    //                 $symbol = 'DR';
    //             } else {
    //                 $amount_clr = '#E54643';
    //                 $amount = $q->credit;
    //                 $symbol = 'CR';
    //             }
    //             $html .= '
    //                 <div class="block block-rounded   table-block-new mb-2 pb-0 view-------Content  -  journal-view" data="' . $q->edit_no . '"
    //                     style="cursor:pointer;padding-left: 0px !important;padding-right: 0px !important;">
    //                     <div class="block-content pt-1 pb-1 pl-1 d-flex  position-relative" style="">
    //                         <div class=" justify-content-center align-items-center  d-flex mr-1" style="width: 20%;float:: left;padding: 7px;">';
    //             if ($q->logo != '') {
    //                 $html .= '<img src="public/client_logos/' . $q->logo . '" class="rounded-circle  "
    //                                  style="object-fit: cover;width: 75px;height: 75px;">';
    //             } else {
    //                 $html .= '<img src="public/img/image-default.png" class="rounded- circle  " style="width: 100%;object-fit: cover;">';
    //             }
    //             $html .= '</div>
    //                         <div class="d-flex justify-content-between" style="width: 70%;">
    //                             <div class="d-flex flex-column " style="width:calc(100% - 15px);">
    //                                 <div>
    //                                     <span class="font-12pt mb-0 text-truncate font-w600 c1" style="font-family: Calibri;color:#4194F6 !important;">' . substr($q->ref_no, 0, 8) . ' / ' . date("d-M-Y", strtotime($q->gl_date)) . '</span>
    //                                 </div>
    //                             <div class="d-flex flex-row">
    //                                 <span style="
    //                                 border-style: dashed !important;
    //                                 text-overflow: ellipsis;
    //                                 white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
    //                                 color: #fff;
    //                                 border:1px solid #fff;
    //                                 background-color: #4194F6;
    //                                 border-radius: 2px;
    //                                 line-height: 1.6;
    //                                 padding-top: 2px;
    //                                 padding-bottom: 2px;
    //                                 padding-left: 5px;
    //                                 padding-right: 5px;
    //                                 margin-right: 0.375rem;" >#' . $q->editNo . '</span>
    //                                 <span style="overflow: hidden;
    //                                 text-overflow: ellipsis;
    //                                 white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
    //                                 color: #262626;
    //                                 border-style: dashed !important;
    //                                 min-width: calc(100% - 160px);
    //                                 border:1px solid #262626;
    //                                 background-color: #BFBFBF;
    //                                 border-radius: 2px;
    //                                 line-height: 1.6;
    //                                 padding-top: 2px;
    //                                 padding-bottom: 2px;
    //                                 padding-left: 5px;
    //                                 padding-right: 5px;">' . $q->fyear . ' - Period ' . $q->period . '</span>
    //                                 </div>
    //                                 <div class="d-flex flex-row" style="padding-top: 3px;">
    //                                     <div>
    //                                         <span style="line-height: 1.6;
    //                                         font-family: Calibri;
    //                                         width: fit-content;
    //                                         font-size: 11pt;
    //                                         color:#3F3F3F;
    //                                         border:1px solid #3F3F3F;
    //                                         border-radius: 2px;
    //                                         margin-right: 0.675rem;" class="px-2">' . $q->account_no . '</span>
    //                                     </div>
    //                                     <div style="overflow: hidden;
    //                                     text-overflow: ellipsis;
    //                                     width: fit-content;
    //                                     line-height: 1.6;
    //                                     white-space: nowrap;
    //                                     font-size: 11pt;
    //                                     font-family: Calibri;">
    //                                         <span>' . $q->description . '</span>
    //                                     </div>
    //                                 </div>
    //                             </div>
    //                                 <div style="position: absolute;right: 10px;top: 10px;">
    //                                     <span style="float:right;
    //                                     font-family: Calibri;
    //                                     line-height: 1.5 !important;
    //                                     color: #FFF;
    //                                     background-color: ' . $amount_clr . ';
    //                                     min-width: 100%;
    //                                 font-weight: 600!important;
    //                                 border: 1px solid #D9D9D9;
    //                                 text-align:center;
    //                                 align-items: center;
    //                                 border-radius: 5px;
    //                                 justify-content: center;
    //                                 display: flex;
    //                                 padding-left: 15px;
    //                                 padding-right: 15px;
    //                                 padding-top: 2px;
    //                                 padding-bottom: 2px;
    //                                 display: block;
    //                                 line-height: 1;
    //                                 text-align: center;
    //                                 border-radius: 3px;
    //                                 font-size: 11pt;">$' . number_format($amount, 2) . '</span>
    //                                 </div>
    //                                 <div class="d-flex flex-row justify-content-end" style="margin-top: 20px;position: absolute;right: 10px;bottom: 9px;">';
    //             if (Auth::check()) {
    //                 if (@Auth::user()->role != 'read') {
    //                     $html .= '<div class="ActionIcon  ml-2    " style="border-radius: 1rem;"  >
    //                                     <a    href="javascript:;" data="' . $q->edit_no . '" data-client-id="' . $q->client . '"   class="btnEditClientJournal"   >
    //                                         <img src="' . asset('public') . '/icons2/icon-edit-grey.png?cache=1" width="25px"  >
    //                                     </a>
    //                                 </div>
    //                                 <div class="ActionIcon  ml-2     " style="border-radius: 1rem;"  >
    //                                     <a    href="javascript:;"  class="btnDeleteClientJournal" data="' . $q->edit_no . '" data-client-id="' . $q->client . '"  >
    //                                         <img src="' . asset('public') . '/icons2/icon-delete-grey.png?cache=1"    width="25px"   >
    //                                     </a>
    //                                 </div>';
    //                 }
    //             }
    //             $html .= '</div>
    //                         </div>
    //                     </div>
    //                 </div>';
    //         }
    //         if ($journals->lastPage() > 1) {
    //             $html .= '<div class="container-fluid journal-view-pagination-footer px-0" style="">
    // <div class="con   no-print page-header py-1  mb-3" style="border-radius:10px;" id="">
    // <!-- Full Table -->
    // <div class="b   mb-0  ">
    //     <div class="block-content pt-0 mt-0">
    // <div class="TopArea" style="position: sticky;
    // padding-top: 5px;
    // z-index: 1000;
    // padding-bottom: 5px;">
    // <div class="row">
    // <div class="col-sm-12 d-flex align-items-center justify-content-center" style="">';
    //             $html .= '
    //         <nav aria-label="Photos Search Navigation">
    //         <ul class="pagination client-journals-pagination">
    //             <!-- Previous Page Link -->';
    //             if ($journals->currentPage() > 1) {
    //                 $html .= '<li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
    //                     <a class="page-link" href="javascript:void(0)" style="border-left:1px solid">&lt;</a>
    //                 </li>';
    //             } else {
    //                 $html .= '<li class="page-item">
    //                     <a class="page-link journal-page-link" href="javascript:void(0)" page-no="' . ($journals->currentPage() - 1) . '" rel="prev" aria-label="« Previous" style="border-left:1px solid">&lt;</a>
    //                 </li>';
    //             }
    //             $html .= '<!-- Pagination Elements -->';
    //             $html .= '<!--<li class="page-item disabled" aria-disabled="true"><a class="page-link">...</a></li>-->';
    //             for ($i = max($journals->currentPage() - 2, 1); $i <= min(max($journals->currentPage() - 2, 1) + 4, $journals->lastPage()); $i++) {
    //                 if ($journals->currentPage() == $i) {
    //                     $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="javascript:void(0);">' . $i . '</a></li>';
    //                 } else {
    //                     $html .= '<li class="page-item "><a class="page-link journal-page-link" page-no="' . $i . '" href="javascript:void(0)">' . $i . '</a></li>';
    //                 }
    //             }
    //             $html .= '<!-- Next Page Link -->';
    //             if ($journals->currentPage() < $journals->lastPage()) {
    //                 $html .= '<li class="page-item">
    //                 <a class="page-link journal-page-link" href="javascript:;" page-no="' . ($journals->currentPage() + 1) . '" rel="next" aria-label="Next »" style="border-right:1px solid">&gt;</a>
    //             </li>';
    //             } else {
    //                 $html .= '<li class="page-item disabled" aria-disabled="true">
    //                     <a class="page-link" href="javascript:void(0)" style="border-right:1px solid">&gt;</a>
    //                 </li>';
    //             }
    //             $html .= '</ul>
    //     </nav>';
    //             $html .= '</div></div></div></div></div></div> </div>';
    //         }
    //         return $html;
    //     }
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
    public function itemcodes()
    {
        return view('itemcodes');
    }
    public function ImportExcelItemcodes(Request $request)
    {
        $import = new ItemcodesImport;
        Excel::import($import, $request->file('file')->store('temp'));
        if (count($import->data) > 0) {
            return redirect()->back()->with('success', count($import->data) . ' Itemcodes added successfully.');
        }
        return redirect()->back()->with('error', "No Itemcodes added.");
    }
    public function ExportItemcodes(Request $request)
    {
        return Excel::download(new ExportItemcodes($request), 'Itemcodes.xlsx');
    }
    public function getItemcodeContent(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('itemcodes as i')->where('i.id', $id)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $colors = DB::table('itemcodes_colors')->where('itemcode_id', $id)->orderBy('color', 'asc')->get();

        $attachments = DB::table('itemcodes_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.itemcode_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('itemcodes_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.itemcode_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('itemcodes_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.itemcode_id', $id)->get();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-10">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Itemcode</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new text-blue fw-600 provinceText" data="' . $q->id . '">' . $q->item_code . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Size</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new field-color fw-300 provinceText" data="' . $q->id . '">' . $q->size . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Chain Code</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new field-color fw-300 provinceText" data="' . $q->id . '">' . $q->chain_code . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Item Category</div>
                    </div>
                    <div class="col-sm-9 pl-0">
                        <div class="field-new field-color fw-300 provinceText" data="' . $q->id . '">' . $q->item_category . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Description</div>
                    </div>
                    <div class="col-sm-9 pl-0">
                        <div class="field-new field-color fw-300 w-100 provinceText" data="' . $q->id . '">' . $q->description . '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        ';

        $colors_img = "";

        if ($colors->count() > 0) {

            $colors_img = 1;

            $html .= '
<div class="mr-2 d-flex">
<div class="col-sm-7 pl-3 block new-block position-relative">
    <div class="block-header py-0 pl-2" style="padding-left:7mm;">
        <a class="section-header">Colors</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content small-box pb-0" style="padding-left: 45px; paddi ng-right: 50px;">
        <div class="row">
        ';
            $html .= '
            <div class="col-sm-11">
                <div class="form-group row fg-evenly">
            ';

            foreach ($colors as $index => $color) {
                $color_id = $color->id;
                $colorValue = htmlspecialchars($color->color);
                $checked = ($index === 0) ? 'check ed' : '';
                $activeClass = ($index === 0) ? ' act ive' : '';
                $imagePath = !empty($color->image) ? 'public/img/itemcolors/' . $color->image : '';

                $html .= '
                    <div class="form-check form-check-inline col-12">
                        <input class="form-check-input visually-hidden-radio" type="radio" name="item_code_color" id="color_' . $index . '" value="' . $colorValue . '" ' . $checked . '>

                        <label
                            class="form-control colors-radio mb-3 text-left' . $activeClass . '"
                            for="color_' . $index . '"
                            data-id="' . $color_id . '"
                            data-item-id="' . $id . '"
                            data-item-code="' . $q->item_code . '"
                            data-color-id="' . $color_id . '"
                            data-img="' . $imagePath . '"
                            style="cursor:pointer;">' . $colorValue . '
                        </label>
                    </div>
                ';
            }
            $html .= '
                </div>
            </div>
            ';
            $html .= '
        </div>
    </div>
</div>
<div class="col-sm-4 colors-img-div pl-3 block new-block position-relative mx-auto" style="display: none;">
    <div class="block-header py-0 pl-2" style="padding-left:7mm;">
        <a class="section-header">Image</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content py-0">
        <div class="row">
            <div class="col-sm-12">
    <img id="color-preview-img" src="" width="100%" style="max-height: 200px; height: 100%; object-fit: contain; display: none;">
            </div>
        </div>
    </div>
</div>

</div>
';
        }
        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
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
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/itemcode_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative" style="">

           <div  class="w-100 d-flex justify-content-between" style="wid th: 70%;">
               <div class="d-flex flex-column" style="width: calc(100% - 50px)">
               <div>
                   <span class="font-signika bubble-item-title" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Itemcode">' . $q->item_code . '</span>
                   <span class="titillium-web-light bubble-item-title fw-300 ml-2"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="Item Category"
                                                        style="font-size: 9pt; border-color: #989698;">
                                                        ' . $q->item_category . '
                                                    </span>
                                                    </div>
                   <div class="d-flex flex-row" style="padding-top: 3px;">
                       <div>
                           <span class="font-signika bubble-item-desc">' . $q->description . '</span>
                       </div>
                   </div>
               </div>
               <div style="position: absolute;right: 10px;top: 10px;">';
        if ($q->status == 1) {
            $itemcodeList .= '
                    <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
                    ';
        } else {
            $itemcodeList .= '<span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>';
        }
        $itemcodeList .= '</div>';

        $html .= '
           </div>
       </div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "colors_img" => $colors_img,
            "item_code" => $q->item_code,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }

    public function EndItemcode(Request $request)
    {
        $check = DB::Table('itemcodes')->where('id', $request->id)->first();
        if ($check->status == 0) {
            DB::Table('itemcodes')->where('id', $request->id)->update(['status' => 1]);
            DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Itemcode successfully reinstated.', 'itemcode_id' => $request->id]);
            return redirect()->back()->with('success', 'Itemcode Activated Successfully');
        } else {
            DB::Table('itemcodes')->where('id', $request->id)->update(['status' => 0]);
            DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Itemcode successfully revoked.', 'itemcode_id' => $request->id]);
            return redirect()->back()->with('success', 'Itemcode Deactivated Successfully');
        }
    }
    public function insertCommentsItemcode(Request $request)
    {
        DB::table('itemcodes_comments')->insert([
            'itemcode_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'itemcode_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function itemcode_color_img(Request $request)
    {
        $itemcode_id = $request->itemcode_id;
        $id = $request->modal_color_id;

        $imageName = "";
        if ($request->hasFile("color_img")) {
            $imageName = mt_rand(1, 1000) . '' . time() . '.' . $request->file("color_img")->getClientOriginalExtension();
            $request->file("color_img")->move(public_path() . '/img/itemcolors/', $imageName);
        }

        $data = [
            'image' => $imageName,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,
        ];

        if (!empty($imageName)) {
            DB::Table('itemcodes_colors')->where('id', $id)->update($data);
        }

        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Image set to itemcode and color', 'itemcode_id' => $itemcode_id]);
        Session::flash("success", " Image set to itemcode and color");
        return redirect()->back()->with('success', 'Image set to itemcode and color');
    }
    public function getCommentsItemcode(Request $request)
    {
        $qry = DB::table('itemcodes_comments as i')
            ->where('i.itemcodes_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }


    public function uploadItemcodeAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadItemcodeAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("itemcode_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("itemcode_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("itemcode_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("itemcode_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertItemcodeAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_itemcode(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('itemcode_attachment/' . $a));
                DB::table('itemcodes_attachments')->insert([
                    'itemcode_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'itemcode_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }
    public function delete_itemcode_attachment(Request $request)
    {
        $itemcode_id = $request->itemcode_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('itemcodes_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'itemcode_id' => $itemcode_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id);
    }
    public function UndoDeleteItemcodeAttachment(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('itemcodes_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted itemcode attachment.', 'itemcode_id' => $request->id]);
        return redirect()->back()->with('success', 'Itemcode attachment undeleted successfully.');
    }
    public function delete_itemcode_comment(Request $request)
    {
        $itemcode_id = $request->itemcode_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('itemcodes_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'itemcode_id' => $itemcode_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id);
    }
    public function UndoDeleteItemcodeComment(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('itemcodes_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted itemcode comment.', 'itemcode_id' => $request->id]);
        return redirect()->back()->with('success', 'Itemcode comment undeleted successfully.');
    }
    public function update_itemcode_comment(Request $request)
    {
        $itemcode_id = $request->itemcode_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('itemcodes_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'itemcode_id' => $itemcode_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }


    public function work_orders()
    {
        return view('work-orders');
    }
    public function ImportExcelWorkorders(Request $request)
    {
        $import = new WorkordersImport;
        Excel::import($import, $request->file('file')->store('temp'));
        if (count($import->data) > 0) {
            return redirect()->back()->with('success', count($import->data) . ' Workorders added successfully.');
        }
        return redirect()->back()->with('error', "No Workorders added.");
    }
    public function ExportWorkorders(Request $request)
    {
        return Excel::download(new ExportWorkorders($request), 'Workorders.xlsx');
    }
    public function getWorkorderContent(Request $request)
    {

        $id = $request->id;
        $html = '';
        $q = DB::table('workorders as w')
            ->leftJoin('itemcodes as i', 'w.itemcode_id', '=', 'i.item_code')
            ->where('w.is_deleted', 0)
            ->where('w.id', $id)
            ->select('w.*', 'i.item_code', 'i.description') // Optional: include itemcode fields
            ->first();

        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;


        $attachments = DB::table('workorder_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.workorder_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('workorder_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.workorder_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('workorder_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.workorder_id', $id)->get();


        $barcodeGenerator = new DNS1D();
        $barcode = $barcodeGenerator->getBarcodePNG($q->workorder_no, 'C128');

        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-9">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-8">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-4 pr-0">
                                <div class="label-new col-form-label">Work Order #</div>
                            </div>
                            <div class="col-sm-8">
                                <div class="field-new text-blue fw-600 provinceText w-100" data="' . $q->id . '">' . $q->workorder_no . '</div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-sm-4 pr-0">
                                <div class="label-new col-form-label">Itemcode</div>
                            </div>
                            <div class="col-sm-8">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->item_code . '</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row h-100">
                            <div class="col-sm-12 text-center">
                                <img src="data:image/png;base64,' . $barcode . '" width="100%" height="80%">
                                <span class="field-color fw-600" style="font-size:9pt;">*' . $q->workorder_no . '*</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Color</div>
                    </div>
                    <div class="col-sm-3 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->itemcode_color_id . '</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Length</div>
                    </div>
                    <div class="col-sm-3 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->length . '"</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Description</div>
                    </div>
                    <div class="col-sm-9 pl-0">
                        <div class="field-new field-color fw-300 w-100 provinceText w-100" data="' . $q->id . '">' . $q->description . '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        ';

        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
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
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/workorder_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative" style="">

           <div  class="w-100 d-flex justify-content-between" style="wid th: 70%;">
               <div class="d-flex flex-column" style="width: calc(100% - 50px)">
                    <div>
                        <span class="font-signika bubble-item-title" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Workorder #">WO# ' . $q->workorder_no . '</span>
                        <span class="font-signika bubble-item-title fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Itemcode" style="font-size: 9pt; border-color: #989698;">' . $q->item_code . '</span>
                        <span class="font-signika bubble-item-title fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Color" style="font-size: 9pt; border-color: #989698;">' . $q->itemcode_color_id . '</span>
                    </div>
                   <div class="d-flex flex-row" style="padding-top: 3px;">
                       <div>
                           <span class="font-signika bubble-item-desc">' . $q->description . '</span>
                       </div>
                   </div>
               </div>
               <div style="position: absolute;right: 10px;top: 10px;">';
        if ($q->status == 1) {
            $itemcodeList .= '
                    <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
                    ';
        } else {
            $itemcodeList .= '<span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>';
        }
        $itemcodeList .= '</div>';

        $html .= '
           </div>
       </div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "workorder_no" => $q->workorder_no,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }
    public function EndWorkorder(Request $request)
    {
        $check = DB::Table('workorders')->where('id', $request->id)->first();
        if ($check->status == 0) {
            DB::Table('workorders')->where('id', $request->id)->update(['status' => 1]);
            DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Workorder successfully reinstated.', 'workorder_id' => $request->id]);
            return redirect()->back()->with('success', 'Workorder Activated Successfully');
        } else {
            DB::Table('workorders')->where('id', $request->id)->update(['status' => 0]);
            DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Workorder successfully revoked.', 'workorder_id' => $request->id]);
            return redirect()->back()->with('success', 'Workorder Deactivated Successfully');
        }
    }
    public function insertCommentsWorkorder(Request $request)
    {
        DB::table('workorder_comments')->insert([
            'workorder_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'workorder_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function getCommentsWorkorder(Request $request)
    {
        $qry = DB::table('workorder_comments as i')
            ->where('i.workorder_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }


    public function uploadWorkorderAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadWorkorderAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("workorder_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("workorder_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("workorder_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("workorder_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertWorkorderAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_workorder(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('workorder_attachment/' . $a));
                DB::table('workorder_attachments')->insert([
                    'workorder_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'workorder_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }
    public function delete_workorder_attachment(Request $request)
    {
        $workorder_id = $request->workorder_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('workorder_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'workorder_id' => $workorder_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id);
    }
    public function UndoDeleteWorkorderAttachment(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('workorder_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted workorder attachment.', 'workorder_id' => $request->id]);
        return redirect()->back()->with('success', 'Workorder attachment undeleted successfully.');
    }
    public function delete_workorder_comment(Request $request)
    {
        $workorder_id = $request->workorder_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('workorder_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'workorder_id' => $workorder_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id . '|' . $workorder_id);
    }
    public function UndoDeleteWorkorderComment(Request $request)
    {
        $workorder_id = $request->workorder_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('workorder_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted workorder comment.', 'workorder_id' => $workorder_id]);
        return redirect()->back()->with('success', 'Workorder comment undeleted successfully.');
    }
    public function update_workorder_comment(Request $request)
    {
        $workorder_id = $request->workorder_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('workorder_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'workorder_id' => $workorder_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }


    public function assets()
    {
        return view('Assets');
    }
    public function insertAssets(Request $request)
    {
        // Validate the input first
        $request->validate([
            'asset_no' => 'required|digits:5|integer',
            'machine_no' => 'required|string|max:12',
            'description' => 'nullable|string|max:128',
        ]);

        // Check if asset_no already exists
        $exists = DB::table('assets')->where('asset_no', $request->asset_no)->where('is_deleted', 0)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Asset number already exists.',
            ], 409); // 409 Conflict
        }

        // Insert asset
        $AssetId = DB::table('assets')->insertGetId([
            'asset_no' => $request->asset_no,
            'machine_no' => strtoupper($request->machine_no), // Force uppercase
            'description' => $request->description,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert audit trail
        DB::table('assets_audit_trail')->insert([
            'user_id' => Auth::id(),
            'description' => 'Asset Saved Successfully',
            'asset_id' => $AssetId
        ]);

        return response()->json([
            'message' => 'Asset Saved Successfully.'
        ]);
    }

    public function updateAssets(Request $request)
    {
        // Validate input
        $request->validate([
            'asset_id' => 'required|integer|exists:assets,id',
            'asset_no' => 'required|digits:5|integer',
            'machine_no' => 'required|string|max:12',
            'description' => 'nullable|string|max:128',
        ]);

        // Check for duplicate asset_no (excluding the current record)
        $exists = DB::table('assets')
            ->where('asset_no', $request->asset_no)
            ->where('id', '!=', $request->asset_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Asset number already exists.',
            ], 409); // Conflict
        }

        // Update asset
        DB::table('assets')
            ->where('id', $request->asset_id)
            ->update([
                'asset_no' => $request->asset_no,
                'machine_no' => strtoupper($request->machine_no), // ensure uppercase
                'description' => $request->description,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        // Insert audit trail
        DB::table('assets_audit_trail')->insert([
            'user_id' => Auth::id(),
            'description' => 'Asset Updated Successfully',
            'asset_id' => $request->asset_id
        ]);

        return response()->json([
            'message' => 'Asset Updated Successfully.'
        ]);
    }

    public function get_assets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:assets,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Asset ID'
            ], 400);
        }

        try {
            // Get the category data
            $category = DB::table('assets')
                ->where('id', $request->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset not found or has been deleted'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch asset data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function ImportAssets(Request $request)
    {
        $import = new AssetsImport;
        Excel::import($import, $request->file('file')->store('temp'));
        if (count($import->data) > 0) {
            return redirect()->back()->with('success', count($import->data) . ' Assets added successfully.');
        }
        return redirect()->back()->with('error', "No Assets added.");
    }
    public function ExportAssets(Request $request)
    {
        return Excel::download(new ExportAssets($request), 'Assets.xlsx');
    }
    public function getAssetsContent(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('assets')->where('id', $id)->where('is_deleted', 0)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $attachments = DB::table('assets_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.asset_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('assets_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.asset_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('assets_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.asset_id', $id)->get();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0" style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-11">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Machine</div>
                    </div>
                    <div class="col-sm-3 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Machine #" data="' . $q->id . '">' . $q->machine_no . '</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Description" data="' . $q->id . '">' . $q->description . '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        ';


        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/assets_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
         ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative pr-2" style="">

<div  class="w-100 ">
    <div  class="d-flex justify-content-between">
        <div>
            <span class="font-signika bubble-item-title" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Asset #">' . $q->asset_no . '</span>
            <span class="font-signika bubble-item-title fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Machine #" style="font-size: 9pt; border-color: #989698;">' . $q->machine_no . '</span>
        </div>
        <div style="position: absolute;right: 10px;top: 10px;">
            ';
        if ($q->status == 1) {
            $itemcodeList .= '
                <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
        ';
        } else {
            $itemcodeList .= '
                <span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>
        ';
        }
        $itemcodeList .= '
        </div>
    </div>
    <div  class="d-flex justify-content-between" style="margin-top: 9px;">
        <div>
           <span class="font-signika bubble-item-desc">' . $q->description . '</span>
       </div>
        <div>
            <div class="dropdown dropdown-3dot">
                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" data-id="' . $q->id . '" data-status="' . $q->status . '" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                  <img src="public/img/cf-menu-icons/3dots.png" width="9">
                </a>
                <div class="dropdown-menu dropdown-menu-3dot">
';
        if ($q->status == 1) {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-deactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-deactivate.png"> Deactivate</a>
';
        } else {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-reactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-activate.png"> Reactivate</a>
';
        }
        $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot edit-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                  <a class="dropdown-item dropdown-item-3dot delete-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "asset_no" => $q->asset_no,
            "machine_no" => $q->machine_no,
            "description" => $q->description,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }
    public function EndAssets(Request $request)
    {
        $check = DB::Table('assets')->where('id', $request->id)->first();
        if ($check->status == 0) {
            DB::Table('assets')->where('id', $request->id)->update(['status' => 1]);
            DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Asset successfully reinstated.', 'asset_id' => $request->id]);
            return redirect()->back()->with('success', 'Asset Activated Successfully');
        } else {
            DB::Table('assets')->where('id', $request->id)->update(['status' => 0]);
            DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Asset successfully revoked.', 'asset_id' => $request->id]);
            return redirect()->back()->with('success', 'Asset Deactivated Successfully');
        }
    }
    public function insertCommentsAssets(Request $request)
    {
        DB::table('assets_comments')->insert([
            'asset_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'asset_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function getCommentsAssets(Request $request)
    {
        $qry = DB::table('assets_comments as i')
            ->where('i.assets_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }


    public function uploadAssetsAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadAssetsAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("assets_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("assets_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("assets_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("assets_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertAssetsAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_assets(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('assets_attachment/' . $a));
                DB::table('assets_attachments')->insert([
                    'asset_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'asset_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }
    public function delete_assets(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Asset deleted | ' . $id, 'asset_id' => $id]);
        return redirect()->back()->with('alert-delete-category', 'Asset deleted|' . $id);
    }
    public function UndoDeleteAssets(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets')->where('id', $id)->update(['is_deleted' => 0]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Asset restored.', 'asset_id' => $request->id]);
        return redirect()->back()->with('success', 'Asset undeleted successfully.');
    }
    public function delete_assets_attachment(Request $request)
    {
        $asset_id = $request->asset_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'asset_id' => $asset_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id . '|' . $asset_id);
    }
    public function UndoDeleteAssetsAttachment(Request $request)
    {
        $asset_id = $request->asset_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted asset attachment.', 'asset_id' => $asset_id]);
        return redirect()->back()->with('success', 'Asset attachment undeleted successfully.');
    }
    public function delete_assets_comment(Request $request)
    {
        $asset_id = $request->asset_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'asset_id' => $asset_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id . '|' . $asset_id);
    }
    public function UndoDeleteAssetsComment(Request $request)
    {
        $asset_id = $request->asset_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted asset comment.', 'asset_id' => $asset_id]);
        return redirect()->back()->with('success', 'Asset comment undeleted successfully.');
    }
    public function update_assets_comment(Request $request)
    {
        $asset_id = $request->asset_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('assets_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('assets_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'asset_id' => $asset_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }


    public function sample_tests()
    {
        return view('sample-tests');
    }
    public function select2_test()
    {
        return view('select2_test');
    }

    public function fetch_workorders(Request $request)
    {
        $query = $request->input('query', '');

        $items = DB::table('workorders as w')
            ->leftJoin('itemcodes as i', 'i.item_code', '=', 'w.itemcode_id')
            ->leftJoin('item_categories_itemcodes as ii', 'ii.itemcode_id', '=', 'i.id')
            ->leftJoin('item_categories as ic', 'ic.id', '=', 'ii.item_category_id')
            ->when($query, function ($qBuilder) use ($query) {
                $qBuilder->whereRaw('LOWER(w.workorder_no) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhere('w.workorder_no', '=', $query);
            })
            ->where('w.status', 1)
            ->where('w.is_deleted', 0)
            ->select(
                'w.id',
                'w.workorder_no',
                'i.description',
                'i.item_category',
                'i.id as item_code_id',
                'i.item_code',
                'w.itemcode_color_id',
                'w.length'
            )
            ->orderBy('w.workorder_no')
            ->groupBy('w.id')
            ->limit(20)
            ->get();

        return response()->json($items);
    }
    public function fetch_workorders_edit(Request $request)
    {
        $query = $request->input('query', '');

        $items = DB::table('workorders as w')
            ->leftJoin('itemcodes as i', 'i.item_code', '=', 'w.itemcode_id')
            ->leftJoin('item_categories_itemcodes as ii', 'ii.itemcode_id', '=', 'i.id')
            ->leftJoin('item_categories as ic', 'ic.id', '=', 'ii.item_category_id')
            ->when($query, function ($qBuilder) use ($query) {
                $qBuilder->whereRaw('LOWER(w.workorder_no) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhere('w.workorder_no', '=', $query);
            })
            ->where('w.status', 1)
            ->where('w.is_deleted', 0)
            ->select(
                'w.id',
                'w.workorder_no',
                'i.description',
                'i.item_category',
                'i.id as item_code_id',
                'i.item_code',
                'w.itemcode_color_id',
                'w.length'
            )
            ->orderBy('w.workorder_no')
            ->groupBy('w.id')
            ->limit(20)
            ->get();

        return response()->json($items);
    }
    public function fetch_itemcode(Request $request)
    {
        $query = $request->input('query', '');

        $items = DB::table('workorders as w')
            ->leftJoin('itemcodes as i', 'i.item_code', '=', 'w.itemcode_id')
            ->leftJoin('item_categories_itemcodes as ii', 'ii.itemcode_id', '=', 'i.id')
            ->leftJoin('item_categories as ic', 'ic.id', '=', 'ii.item_category_id')
            ->when($query, function ($qBuilder) use ($query) {
                // Case-insensitive partial and exact match
                $qBuilder->whereRaw('LOWER(w.workorder_no) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhere('w.workorder_no', '=', $query);
            })
            ->where('w.status', 1)
            ->where('w.is_deleted', 0)
            ->select(
                'w.id',
                'w.workorder_no',
                'i.description',
                'i.item_category',
                'i.id as item_code_id',
                'i.item_code',
                'w.itemcode_color_id',
                'w.length'
            )
            ->orderBy('w.workorder_no')
            ->groupBy('w.id')
            ->limit(20)
            ->get();

        return response()->json($items);
    }
    public function fetchHistorical(Request $request)
    {
        $asset_id = $request->input('asset_id');
        $test_name_id = $request->input('test_name_id');
        $item_category = $request->input('item_category');

        $history = DB::table('sample_tests as st')
            ->join('test_definitions as td', 'td.id', '=', 'st.test_name_id')
            ->join('assets as a', 'a.id', '=', 'st.asset_id')
            ->select(
                'st.*',
                'td.test_name',
                'a.machine_no',
                'a.asset_no',
                'st.item_category'
            )
            ->where('st.asset_id', $asset_id)
            ->where('st.test_name_id', $test_name_id)
            ->where('st.item_category', $item_category)
            ->where('st.is_deleted', 0)
            ->orderBy('st.sample_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json($history);
    }

    // public function fetch_workorders(Request $request)
    //     {
    //         try {
    //             $query = $request->input('query', '');
    //             $limit = $request->input('limit', 20);

    //             Log::info('fetch_workorders called', ['query' => $query, 'limit' => $limit]);

    //             $items = DB::table('workorders as w')
    //                 ->leftJoin('itemcodes as i', 'i.item_code', '=', 'w.itemcode_id')
    //                 ->leftJoin('item_categories_itemcodes as ii', 'ii.itemcode_id', '=', 'i.id')
    //                 ->leftJoin('item_categories as ic', 'ic.id', '=', 'ii.item_category_id')
    //                 ->when($query, function ($qBuilder) use ($query) {
    //                     $qBuilder->whereRaw('LOWER(w.workorder_no) = ?', [strtolower($query)])
    //                             ->orWhereRaw('LOWER(w.workorder_no) LIKE ?', ['%' . strtolower($query) . '%']);
    //                 })
    //                 ->where('w.status', 1)
    //                 ->where('w.is_deleted', 0)
    //                 ->select(
    //                     'w.id',
    //                     'w.workorder_no',
    //                     'i.description',
    //                     'ic.item_category',
    //                     'i.item_code',
    //                     'w.itemcode_color_id',
    //                     'w.length'
    //                 )
    //                 ->orderByRaw("CASE WHEN LOWER(w.workorder_no) = LOWER(?) THEN 0 ELSE 1 END, w.workorder_no", [$query])
    //                 ->groupBy('w.id')
    //                 ->limit($limit)
    //                 ->get();

    //             Log::info('fetch_workorders results', [
    //                 'count' => $items->count(),
    //                 'workorders' => $items->pluck('workorder_no')->toArray()
    //             ]);

    //             $html = '<option value="">Select Workorder #</option>';

    //             if ($items->isEmpty()) {
    //                 $html .= '<option value="" disabled>No matching workorders found</option>';
    //             } else {
    //                 foreach ($items as $item) {
    //                     $optionText = e($item->workorder_no);
    //                     if ($item->description) {
    //                         $optionText .= ' - ' . e($item->description);
    //                     }
    //                     $html .= '<option value="' . e($item->id) . '"
    //                         data-id="' . e($item->id) . '"
    //                         data-workorder="' . e($item->workorder_no) . '"
    //                         data-itemcategory="' . e($item->item_category) . '"
    //                         data-itemcode="' . e($item->item_code) . '"
    //                         data-color="' . e($item->itemcode_color_id) . '"
    //                         data-length="' . e($item->length) . '"
    //                         data-description="' . e($item->description) . '">'
    //                         . $optionText .
    //                         '</option>';
    //                 }
    //             }

    //             return response()->json(['options' => $html]);
    //         } catch (\Exception $e) {
    //             Log::error('fetch_workorders error', ['message' => $e->getMessage()]);
    //             return response()->json(['message' => $e->getMessage()], 500);
    //         }
    //     }


    // public function fetch_workorders(Request $request)
    // {
    //     try {
    //         $query = $request->input('query', '');
    //         $limit = $request->input('limit', 10); // Use limit from request

    //         $items = DB::table('workorders as w')
    //             ->leftJoin('itemcodes as i', 'i.item_code', '=', 'w.itemcode_id')
    //             ->leftJoin('item_categories_itemcodes as ii', 'ii.itemcode_id', '=', 'i.id')
    //             ->leftJoin('item_categories as ic', 'ic.id', '=', 'ii.item_category_id')
    //             ->when($query, function ($qBuilder) use ($query) {
    //                 // Case-insensitive partial and exact match
    //                 $qBuilder->whereRaw('LOWER(w.workorder_no) LIKE ?', ['%' . strtolower($query) . '%'])
    //                         ->orWhere('w.workorder_no', '=', $query);
    //             })
    //             ->where('w.status', 1)
    //             ->where('w.is_deleted', 0)
    //             ->select(
    //                 'w.id',
    //                 'w.workorder_no',
    //                 'i.description',
    //                 'ic.item_category',
    //                 'i.item_code',
    //                 'w.itemcode_color_id',
    //                 'w.length'
    //             )
    //             ->orderBy('w.workorder_no')
    //             ->groupBy('w.id')
    //                ->limit(  20)

    //             ->get();

    //         $html = '<option value="">Select Workorder #</option>';

    //         if ($items->isEmpty()) {
    //             $html .= '<option value="" disabled>No matching workorders found</option>';
    //         } else {
    //             foreach ($items as $item) {
    //                 $html .= '<option value="' . e($item->id) . '"
    //                     data-id="' . e($item->id) . '"
    //                     data-workorder="' . e($item->workorder_no) . '"
    //                     data-itemcategory="' . e($item->item_category) . '"
    //                     data-itemcode="' . e($item->item_code) . '"
    //                     data-color="' . e($item->itemcode_color_id) . '"
    //                     data-length="' . e($item->length) . '"
    //                     data-description="' . e($item->description) . '">'
    //                     . e($item->workorder_no) .
    //                     '</option>';
    //             }
    //         }

    //         return response()->json(['options' => $html]);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }



    public function fetch_assets(Request $request)
    {
        try {
            $query = $request->input('query');

            $items = DB::table('assets')
                ->when($query, function ($q) use ($query) {
                    $q->where('asset_no', 'like', '%' . $query . '%');
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('asset_no')
                ->limit(500)
                ->get();

            $html = '<option value="">Select Asset #</option>';

            if ($items->isEmpty()) {
                $html .= '<option value="" disabled>No matching assets found</option>';
            } else {
                foreach ($items as $item) {
                    $html .= '<option value="' . e($item->id) . '"
                    data-id="' . e($item->id) . '"
                    data-asset="' . e($item->asset_no) . '"
                    data-machine="' . e($item->machine_no) . '"
                    data-description="' . e($item->description) . '">' . e($item->asset_no) . '</option>';
                }
            }

            return response()->json(['options' => $html]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetch_users(Request $request)
    {
        try {
            $query = $request->input('query');

            $items = DB::table('users')
                ->when($query, function ($q) use ($query) {
                    $q->where('asset_no', 'like', '%' . $query . '%');
                })
                ->where('is_deleted', 0)
                ->limit(500)
                ->get();

            $html = '<option value="">Select Users</option>';

            if ($items->isEmpty()) {
                $html .= '<option value="" disabled>No matching assets found</option>';
            } else {
                foreach ($items as $item) {
                    $html .= '<option value="' . e($item->id) . '">' . e($item->firstname) . ' ' . e($item->lastname) . '</option>';
                }
            }

            return response()->json(['options' => $html]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function fetch_threshold(Request $request)
    {
        $request->validate([
            'test_name_id' => 'required|integer',
            'item_category_name' => 'required|string',
        ]);



        $threshold = DB::table('test_threshold_item_categories as ttc')
            ->join('test_thresholds as tt', 'tt.id', '=', 'ttc.test_threshold_id')
            ->join('itemcodes as ic', 'ic.id', '=', 'ttc.item_category_id')
            ->join('test_definitions as td', 'td.id', '=', 'tt.test_name_id')

            ->where('tt.test_name_id', $request->test_name_id)
            ->where('ic.item_category', $request->item_category_name)
            ->where('ttc.is_deleted', 0)
            ->select('ttc.min', 'ttc.max', 'ttc.YFS', 'ttc.YFGS', 'ttc.safety_threshold', 'ttc.absorption', 'td.uom', 'ttc.avg')
            ->first();

        if (!$threshold) {
            return response()->json(['error' => 'Threshold range not found'], 404);
        }

        return response()->json([
            'min' => floatval($threshold->min),
            'max' => floatval($threshold->max),
            'YFS' => floatval($threshold->YFS),
            'YFGS' => floatval($threshold->YFGS),
            'safety_threshold' => floatval($threshold->safety_threshold),
            'absorption' => floatval($threshold->absorption),
            'uom' => $threshold->uom,
            'avg' => $threshold->avg,
        ]);
    }
    public function insert_sample_test(Request $request)
    {
        // Convert dates inline using DateTime
        $productionDate = null;
        $sampleDate = null;

        if (!empty($request->production_date)) {
            $dt = \DateTime::createFromFormat('d-M-Y', $request->production_date);
            if ($dt !== false) {
                $productionDate = $dt->format('Y-m-d');
            }
        }

        if (!empty($request->sample_date)) {
            $dt = \DateTime::createFromFormat('d-M-Y', $request->sample_date);
            if ($dt !== false) {
                $sampleDate = $dt->format('Y-m-d');
            }
        }


        // Clean numeric values inline (e.g. "6.546.54" → "6.54")
        $avg = preg_replace('/[^0-9.]/', '', $request->avg);
        $min = preg_replace('/[^0-9.]/', '', $request->min);
        $max = preg_replace('/[^0-9.]/', '', $request->max);

        $id = DB::table('sample_tests')->insertGetId([
            'test_name_id'     => $request->test_name_id,
            'workorder_id'     => $request->workorder_id,
            'item_category'    => $request->item_category,
            'itemcode'         => $request->itemcode,
            'itemcode_desc'    => $request->itemcode_desc,
            'color'            => $request->color,
            'length'           => $request->length,
            'asset_id'         => $request->asset_id,
            'bosubi'           => $request->bosubi,
            'lot'              => $request->lot,
            'production_date'  => $productionDate,
            'sample_date'      => $sampleDate,
            'sample_number'    => $request->sample_number,
            'min'              => $request->min,
            'min_result'       => $request->min_result,
            'avg'              => $request->avg,
            'avg_result'       => $request->avg_result,
            'max'              => $request->max,
            'max_result'       => $request->max_result,
            'avg_minus'        => $request->avg_minus,
            'avg_plus'         => $request->avg_plus,
            'stdva_value'      => $request->stdva_value,
            'standard_value'   => $request->standard_value,
            'standard_min'   => $request->standard_min,
            'standard_max'   => $request->standard_max,
            'safety_threshold' => $request->safety_threshold,
            'test_standard'    => $request->test_standard,
            'absorption_value' => $request->absorption_value,
            'comments' => $request->comments ?? null,
            'status'       => 1,
            'created_by'   => Auth::id(),
            'updated_by'   => Auth::id(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('sample_tests_audit_trail')->insert([
            'user_id'           => Auth::id(),
            'description'       => 'QC Test added successfully',
            'sample_test_id'    => $id,
            'created_at'        => now()
        ]);

        return response()->json(['inserted_id' => $id]);
    }

    public function insert_sample_test_samples(Request $request)
    {
        DB::table('sample_tests_samples')->insert([
            'sample_test_id' => $request->sample_test_id,
            'sample_number'  => $request->sample_number ?? null,
            'sample_value'   => $request->sample_value ?? null,
            'sample_before'  => $request->sample_before ?? null,
            'sample_after'   => $request->sample_after ?? null,
            'sample_result'  => $request->sample_result ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
            'updated_by'     => Auth::id(),
        ]);

        return response()->json(['status' => 'Sample inserted']);
    }

    public function update_sample_test(Request $request)
    {
        // Validate that an ID is provided
        if (empty($request->id)) {
            return response()->json(['error' => 'Sample Test ID is required.'], 400);
        }

        // Convert production date from 'd-M-Y' to 'Y-m-d'
        $productionDate = null;
        if (!empty($request->production_date)) {
            $dt = \DateTime::createFromFormat('d-M-Y', $request->production_date);
            if ($dt !== false) {
                $productionDate = $dt->format('Y-m-d');
            }
        }

        // Sanitize numeric values (remove invalid characters)
        $avg = preg_replace('/[^0-9.]/', '', $request->avg);
        $min = preg_replace('/[^0-9.]/', '', $request->min);
        $max = preg_replace('/[^0-9.]/', '', $request->max);
        // Perform update
        DB::table('sample_tests')
            ->where('id', $request->id)
            ->update([
                'asset_id'         => $request->asset_id ?? null,
                'bosubi'           => $request->bosubi ?? null,
                'lot'              => $request->lot ?? null,
                'production_date'  => $productionDate,
                'sample_number'    => $request->sample_number ?? 0,
                'standard_value' => $request->standard_value,
                'test_standard'    => $request->test_standard,
                'min'                => $min,
                'min_result'       => $request->min_result ?? null,

                'avg'              => $avg,
                'avg_result'       => $request->avg_result ?? null,

                'max'              => $max,
                'max_result'       => $request->max_result ?? null,

                'avg_minus'        => $request->avg_minus ?? null,
                'avg_plus'         => $request->avg_plus ?? null,
                'stdva_value'      => $request->stdva_value ?? null,
                'comments'      => $request->comments ?? null,

                'updated_by'       => Auth::id(),
                'updated_at'       => now(),
            ]);

        // Log in audit trail
        DB::table('sample_tests_audit_trail')->insert([
            'user_id'        => Auth::id(),
            'description'    => 'QC Test updated successfully',
            'sample_test_id' => $request->id,
            'created_at'     => now()
        ]);

        DB::table('sample_tests_samples')
            ->where('sample_test_id', $request->id)
            ->update([
                'is_deleted' => 1,
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);

        return response()->json(['updated_id' => $request->id]);
    }
    public function update_sample_test_samples(Request $request)
    {

        DB::table('sample_tests_samples')->insert([
            'sample_test_id' => $request->sample_test_id,
            'sample_number'  => $request->sample_number ?? null,
            'sample_value'   => $request->sample_value ?? null,
            'sample_before'  => $request->sample_before ?? null,
            'sample_after'   => $request->sample_after ?? null,
            'sample_result'  => $request->sample_result ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
            'updated_by'     => Auth::id(),
        ]);

        return response()->json(['status' => 'Sample updated']);
    }




    public function get_sample_test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:sample_tests,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Sample Test ID'
            ], 400);
        }

        try {
            // Get the main sample test data
            $sampleTest = DB::table('sample_tests as st')
                ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')
                ->leftJoin('workorders as wo', 'wo.id', '=', 'st.workorder_id')
                ->leftJoin('assets as a', 'a.id', '=', 'st.asset_id')
                ->leftJoin('test_threshold_item_categories as ttic', 'ttic.test_threshold_id', '=', 'st.asset_id')
                ->where('st.id', $request->id)
                ->where('st.is_deleted', 0)
                ->select(
                    'st.*',
                    'td.test_name',
                    'td.test_type',
                    'td.uom',
                    'td.criteria',
                    'td.standard',
                    'wo.workorder_no',
                    'a.asset_no'
                )
                ->first();

            if (!$sampleTest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sample Test not found or has been deleted'
                ], 404);
            }

            // Get associated samples ordered by sample_number
            $samples = DB::table('sample_tests_samples as sts')
                ->where('sts.sample_test_id', $request->id)
                ->where('sts.is_deleted', 0)
                ->orderBy('sts.sample_number', 'asc')
                ->select('sts.*')
                ->get();

            // Format the production date if needed
            if ($sampleTest->production_date) {
                $sampleTest->production_date = date('d-M-Y', strtotime($sampleTest->production_date));
            }

            // Format the sample date if needed
            if ($sampleTest->sample_date) {
                $sampleTest->sample_date = date('d-M-Y', strtotime($sampleTest->sample_date));
            }

            return response()->json([
                'success' => true,
                'test' => $sampleTest,
                'samples' => $samples
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Sample Test data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function get_sample_test_content(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('sample_tests as st')
            ->select('st.*', 'w.workorder_no', 'td.test_name', 'td.test_type', 'td.criteria', 'td.uom', 'td.standard', 'a.asset_no')
            ->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')

            ->leftJoin('workorders as w', 'w.id', '=', 'st.workorder_id')
            ->leftJoin('assets as a', 'a.id', '=', 'st.asset_id')
            ->where('st.is_deleted', 0)
            ->where('st.id', $id) // <-- This was missing
            ->first();

        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $user2 = DB::table('users')->where('id', $q->created_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $sample_tests_samples = DB::table('sample_tests_samples')
            ->select('*')
            ->where('sample_test_id', $q->id)
            ->where('is_deleted', 0)
            ->orderByRaw('CAST(sample_number AS UNSIGNED)')
            ->get();


        $attachments = DB::table('sample_tests_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.sample_test_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('sample_tests_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.sample_test_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('sample_tests_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.sample_test_id', $id)->get();

        $barcodeGenerator = new DNS1D();
        $barcode = $barcodeGenerator->getBarcodePNG($q->workorder_no, 'C128');

        $icons = [
            'pass' => 'greencheck.png',
            'fail' => 'redxcircle.png',
            'warning' => 'yellowexclamationmark.png',
        ];

        $html .= '
        <div class="content-div" style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group row fg-evenly">
                    <div class="col-12 col-lg-7 mb-3">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Work Order #</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new text-blue fw-600 provinceText w-100" data="' . $q->id . '">' . $q->workorder_no . '</div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Test Name</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->test_name . '</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="row h-100">
                            <div class="col-sm-12 text-center">
                                <a href="#" class="barcode-click" data-workorder="' . $q->workorder_no . '" data-barcode="data:image/png;base64,' . $barcode . '">
                                    <img src="data:image/png;base64,' . $barcode . '" width="100%" height="80%">
                                </a>
                                <span class="field-color fw-600" style="font-size:9pt;">*' . $q->workorder_no . '*</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Test Type</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->test_type . '
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 mb-3">
                        <div class="row">
                            <div class="col-sm-3 pr-0" style="">
                                <input type="text" class="form-control text-center px-1 green-box js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test UOM" readonly="" value="' . $q->uom . '">
                            </div>
';

        if ($q->test_type == 'Perf-Str') {
            if ($q->standard != '') {
                $html .= '
                            <div class="col-sm-3 pr-0" style="">
                                <input type="text" class="form-control text-center px-1 green-box js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Standard" readonly="" value="' . $q->standard . '">
                            </div>';
            }
            $html .= '
                            <div class="col-sm-3 pr-0" style="">
                                <input type="text" class="form-control text-center px-1 green-box js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Criteria" readonly="" value="' . $q->criteria . '">
                            </div>
';
        }
        $html .= '
                        </div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-12 col-lg-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">User</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $user2->firstname . '
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Test Date</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" style="white-space: nowrap;" data="' . $q->id . '">' . date('d-M-Y', strtotime($q->sample_date)) . '
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Item Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group row fg-evenly">
                    <div class="col-11 col-lg-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Item Category</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new text-blue fw-600 provinceText w-100" data="' . $q->id . '">' . $q->item_category . '</div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Item Code</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->itemcode . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-8 col-lg-5 pr-0">
                                <div class="label-new col-form-label">Color</div>
                            </div>
                            <div class="col-3 col-lg-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->color . '
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group row fg-evenly">
                            <div class=" col-6 col-lg-5 pr-0">
                                <div class="label-new col-form-label">Length</div>
                            </div>
                            <div class="col-6 col-lg-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->length . '"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group row fg-evenly">
                            <div class="col-5 col-lg-3 itm-desc-div" style="padding-right: 5px;">
                                <div class="label-new col-form-label">Description</div>
                            </div>
                            <div class="col-7 col-lg-9 pl-2 itm-desc-div">
                                <div class="field-new text-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->itemcode_desc . '</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Product Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group row fg-evenly">
                    <div class="col-12 col-lg-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Asset #</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new text-blue fw-600 provinceText w-100" data="' . $q->id . '">' . $q->asset_no . '</div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Bosubi</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . ($q->bosubi ?? 'N/A') . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Production Date</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . date('d-M-Y', strtotime($q->production_date)) . '
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-7">
                        <div class="form-group row fg-evenly">
                            <div class="col-sm-5 pr-0">
                                <div class="label-new col-form-label">Lot</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="field-new field-color fw-300 provinceText w-100" data="' . $q->id . '">' . $q->lot . '
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="block new-block position-relative mr-2 mt-3 sample-data-div">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Sample Data</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 25px;padding-right: 25px;">
        <div class="row">
            ';

        foreach ($sample_tests_samples as $sts) {
            $html .= '
            <div class="col-sm-2 mb-3">
                <div class="display-sample-entry d-flex justify-content-between align-items-center">
                    <div class="">';
            if (isset($icons[$sts->sample_result])) {
                $html .= '<img src="public/img/cf-menu-icons/' . $icons[$sts->sample_result] . '" width="28">';
            }
            $html .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ' . ($sts->sample_number < 10 ? 'ml-1' : 'ml-0') . '">

                        <div class="text-center">
                            <span class="display-sample-value">' . $sts->sample_value . '</span><br>
                            <div class="display-sample-number text-center">SAMPLE #' . $sts->sample_number . '</div>
                        </div>
                    </div>
                </div>
            </div>
        ';
        }
        $html .= '
        </div>
    </div>
</div>
<div class="block new-block position-relative mr-2 mt-3 qc-test-result">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">QC Test Results</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
';
        if ($q->test_type != 'Dimension') {
            $html .= '
            <div class="col-sm-2 pl-0 mb-3">
            <div class="display-sample-entry d-flex justify-content-center align-items-center h-100">';
            if ($q->test_standard != '') {
                $html .= '<div class="align-content-center " style="    width: 29px;display: inline-block;text-align: center;">
                                    <div class="test-standard1" style="">' . $q->test_standard . '</div>
                             </div>';
            }
            $html .= ' <div class="align-content-center">
                                                    <div class="test-standard1" style="">' . $q->standard . '</div>
                                                </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="text-center ml-3">
                            <span class="display-sample-value">' . $q->standard_value . ($q->test_type == 'Perf-Weight' ? '%' : '(' . $q->uom . ')') . '</span>
                            <br>
';
            if ($q->criteria != 'Max') {
                $html .= '
                            <div class="display-sample-number text-center">
                                ' . ($q->safety_threshold != 'MAX ABSORB %' ? '+' : '') . $q->safety_threshold . '
                            </div>
';
            }
            $html .= '
                        </div>
                    </div>
                </div>
            </div>
';
        } else {

            $html .= '    <div class="col-sm-2 dimention-str"  >
                                           <div class="sample-entry d-flex justify-content-center align-items-center ">
                                                    <div class="">
                                                        <div class="text-center">
                                                            <span class="sample-value test-avg-value">' . $q->standard_value . '</span><br>
                                                            <div class="sample-number">-<span class="test-minus">' . $q->standard_min . '</span> /
                                                                +<span class="test-plus">' . $q->standard_max . '</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
        }
        $html .= '
            <div class="col-sm-2 pl-0 mb-3 align-content-center">
                <div class="mx-auto" style="width: 40px; height: 40px;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Total Samples">
                    <div class="rounded-circle bg-primary text-white text-center align-content-center w-100 h-100">
                        ' . $q->sample_number . '
                    </div>
                </div>
            </div>
            <div class="col-sm-2 pl-0 mb-3">
                <div class="display-sample-entry d-flex justify-content-between align-items-center">
                    <div class="">';
        if (isset($icons[$q->min_result])) {
            $html .= '<img src="public/img/cf-menu-icons/' . $icons[$q->min_result] . '" width="28">';
        }
        $html .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ml-1">
                        <div class="text-center">
                            <span class="display-sample-value">' . $q->min . '</span><br>
                            <div class="display-sample-number text-center">MIN (' . $q->uom . ')</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 pl-0 mb-3">
                <div class="display-sample-entry d-flex justify-content-between align-items-center">
                    <div class="">';
        if (isset($icons[$q->avg_result])) {
            $html .= '<img src="public/img/cf-menu-icons/' . $icons[$q->avg_result] . '" width="28">';
        }
        $html .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ml-1">
                        <div class="text-center">
                            <span class="display-sample-value">' . $q->avg . '</span><br>
                            <div class="display-sample-number text-center">AVG (' . $q->uom . ')</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 pl-0 mb-3">
                <div class="display-sample-entry d-flex justify-content-between align-items-center">
                    <div class="">';
        if (isset($icons[$q->max_result])) {
            $html .= '<img src="public/img/cf-menu-icons/' . $icons[$q->max_result] . '" width="28">';
        }
        $html .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ml-1">
                        <div class="text-center">
                            <span class="display-sample-value">' . $q->max . '</span><br>
                            <div class="display-sample-number text-center">MAX (' . $q->uom . ')</div>
                        </div>
                    </div>
                </div>
            </div>
';
        if ($q->test_type == 'Perf-Str') {
            $html .= '
            <div class="col-sm-2 pl-0 mb-3">
                <div class="display-sample-entry d-flex align-items-center h-100">
                    <div class="">
                     <img src="public/img/cf-menu-icons/standard-deviation.png" width="28">
                    </div>
                    <div class="d-flex justify-content-center align-items-center mx-auto">
                        <div class="text-center">
                            <span class="display-sample-value">' . $q->stdva_value . '</span>
                            <br>
                            <div class="display-sample-number text-center">STDV.A</div>
                        </div>
                    </div>
                </div>
            </div>
';
        }
        if ($q->test_type == 'Dimension') {
            $html .= '
            <div class="col-sm-2 pl-0 mb-3">
                <div class="display-sample-entry d-flex align-items-center h-100">
                    <div class="d-flex justify-content-center align-items-center mx-auto">
                        <div class="text-center">
                            <span class="display-sample-value">' . $q->avg . '</span>
                            <br>
                            <div class="display-sample-number text-center">-' . $q->avg_minus . ' / +' . $q->avg_plus . ' (' . $q->uom . ')</div>
                        </div>
                    </div>
                </div>
            </div>
';
        }
        $html .= '
        </div>
    </div>
</div>
';


        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/sample_tests_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
         ';
            }
            $html .= '
</div>
</div>
</div>
';
        }



        $itemcodeList = '
<div class="block-content py-2 pl-3 d-flex position-relative pr-2">
    <div class="w-100">
        <div class="d-flex justify-content-between">
            <div>
                <span class="titillium-web-black bubble-item-title" style="background-color: transparent; border: none; color: #0070C0 !important; padding: 0px 10px 0px 0px !important">
                    ' . $q->test_name . '
                </span>
                <br class="responsive-break">
                <span class="titillium-web-light bubble-item-title fw-300 ml-0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Item Category" style="font-size: 9pt; border-color: #989698;">
                    ' . $q->item_category . '
                </span>
                <span
                                                        class="titillium-web-light bubble-item-title fw-300 ml-2"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="UOM"
                                                        style="font-size: 9pt; border-color: #989698;">
                                                        ' . $q->uom . '
                                                    </span>
            </div>
            <div style="position: absolute;right: 10px;top: 10px;">
                <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Workorder #">WO# ' . $q->workorder_no . '</span>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-end" style="margin-top: 5px;">
            <div class="d-flex align-items-center">
                <div class="mr-2 rounded-circle-div" style="width: 35px; height: 35px;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Total Samples">
                    <div class="rounded-circle bg-primary text-white text-center align-content-center w-100 h-100">
                        ' . $q->sample_number . '
                    </div>
                </div>
';

        // BEGIN MIN
        $itemcodeList .= '
                <div style="background: none;border: 1px solid #C0C0C0;padding: 3px 6px;border-radius: 10px;" class="final-sample-entry d-flex justify-content-between align-items-center mx-1">
                    <div class="">';
        if (isset($icons[$q->min_result])) {
            $itemcodeList .= '<img src="public/img/cf-menu-icons/' . $icons[$q->min_result] . '" width="22">';
        }
        $itemcodeList .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ml-1">
                        <div class="text-center">
                            <span class="final-sample-value">' . $q->min . '</span><br>
                            <div class="final-sample-number text-center">MIN</div>
                            </div>
                    </div>
                </div>';
        // END MIN

        // BEGIN AVG
        $itemcodeList .= '
                <div style="background: none;border: 1px solid #C0C0C0;padding: 3px 6px;border-radius: 10px;" class="final-sample-entry d-flex justify-content-between align-items-center mx-1">
                    <div class="">';
        if (isset($icons[$q->avg_result])) {
            $itemcodeList .= '<img src="public/img/cf-menu-icons/' . $icons[$q->avg_result] . '" width="22">';
        }
        $itemcodeList .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ml-1">
                        <div class="text-center">
                            <span class="final-sample-value">' . $q->avg . '</span><br>
                            <div class="final-sample-number text-center">AVG</div>
                            </div>
                    </div>
                </div>';
        // END AVG

        // BEGIN MAX
        $itemcodeList .= '
                <div style="background: none;border: 1px solid #C0C0C0;padding: 3px 6px;border-radius: 10px;" class="final-sample-entry d-flex justify-content-between align-items-center mx-1">
                    <div class="">';
        if (isset($icons[$q->max_result])) {
            $itemcodeList .= '<img src="public/img/cf-menu-icons/' . $icons[$q->max_result] . '" width="22">';
        }
        $itemcodeList .= '
                    </div>
                    <div class="d-flex justify-content-center align-items-center ml-1">
                        <div class="text-center">
                            <span class="final-sample-value">' . $q->max . '</span><br>
                            <div class="final-sample-number text-center">MAX</div>
                            </div>
                    </div>
                </div>';
        // END MAX

        // 3-Dots Menu
        $itemcodeList .= '
            </div>
            <div class="d-flex">

                <div class="dropdown dropdown-3dot">
                    <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" data-id="' . $q->id . '" data-status="' . $q->status . '" href="#" role="button" data-toggle="dropdown" aria-expanded="false">

                        <i class="fa-light fa-ellipsis-vertical" style="color: #262626; font-size: 22px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-3dot">
                        <a class="dropdown-item dropdown-item-3dot edit-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                        <a class="dropdown-item dropdown-item-3dot delete-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';


        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "test_name" => $q->test_name,
            "workorder_no" => $q->workorder_no,
            "item_category" => $q->item_category,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }

    // public function import_sample_tests(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv'
    //     ]);

    //     $import = new ImportSampleTests();

    //     Excel::import($import, $request->file('file'));

    //     if (isset($import->data) && count($import->data) > 0) {
    //         return redirect()->back()->with('success', count($import->data) . ' Sample Tests imported successfully.');
    //     }

    //     return redirect()->back()->with('error', "No Sample Tests imported.");
    // }
    public function import_sample_tests(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv'
    ]);

    $import = new \App\Imports\ImportSampleTests(auth()->id());

    \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

    $imported = $import->getImportedCount();
    $failedRows = $import->getFailedRows();

    if (count($failedRows) > 0) {
        // create CSV report (same logic you already have)
        $filename = 'sample-test-import-failures-' . now()->format('Ymd_His') . '-' . \Illuminate\Support\Str::random(6) . '.csv';
        $path = 'import_reports/' . $filename;

        $csv = $this->failedRowsToCsv($failedRows); // same helper
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $csv);

        $downloadUrl = route('sample-tests.import.report', ['file' => $filename]);

        return redirect()->back()->with([
            'import_result'     => 'partial',
            'import_imported'   => $imported,
            'import_failed'     => count($failedRows),
            'import_report_url' => $downloadUrl,
        ]);
    }

    return redirect()->back()->with([
        'import_result'   => 'success',
        'import_imported' => $imported,
    ]);
}

    public function downloadImportReport(string $file)
    {
        $file = basename($file);
        $path = 'import_reports/' . $file;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path, $file, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function failedRowsToCsv(array $failed): string
    {
        $out = fopen('php://temp', 'r+');

        // header
        fputcsv($out, ['Testname', 'WO#', 'failure_reason_code']);

        foreach ($failed as $f) {
            fputcsv($out, [$f['testname'], $f['wo'], $f['reason']]);
        }

        rewind($out);
        return stream_get_contents($out);
    }
    public function export_sample_tests(Request $request)
    {
        return Excel::download(new ExportSampleTests($request), 'QC-Sample-Tests.xlsx');
    }
    public function delete_sample_test(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Sample Test deleted | ' . $id, 'sample_test_id' => $id]);
        return redirect()->back()->with('alert-delete-category', 'Sample Test deleted|' . $id);
    }
    public function undo_delete_sample_test(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests')->where('id', $id)->update(['is_deleted' => 0]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Sample Test restored.', 'sample_test_id' => $request->id]);
        return redirect()->back()->with('success', 'Sample Test undeleted successfully.');
    }
    public function get_comments_sample_test(Request $request)
    {
        $qry = DB::table('sample_tests_comments as i')
            ->where('i.sample_tests_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }
    public function insert_comment_sample_test(Request $request)
    {
        DB::table('sample_tests_comments')->insert([
            'sample_test_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'sample_test_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function update_comment_sample_test(Request $request)
    {
        $sample_test_id = $request->sample_test_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'sample_test_id' => $sample_test_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }
    public function delete_comment_sample_test(Request $request)
    {
        $sample_test_id = $request->sample_test_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'sample_test_id' => $sample_test_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id . '|' . $sample_test_id);
    }
    public function undo_delete_comment_sample_test(Request $request)
    {
        $sample_test_id = $request->sample_test_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted comment | ' . $id, 'sample_test_id' => $sample_test_id]);
        return redirect()->back()->with('success', 'Sample Test comment undeleted successfully.');
    }




    public function uploadSampleTestAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadSampleTestAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("sample_tests_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("sample_tests_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("sample_tests_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("sample_tests_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertSampleTestAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_sample_test(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('sample_tests_attachment/' . $a));
                DB::table('sample_tests_attachments')->insert([
                    'sample_test_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'sample_test_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }


    public function delete_attachment_sample_test(Request $request)
    {
        $sample_test_id = $request->sample_test_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'sample_test_id' => $sample_test_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id . '|' . $sample_test_id);
    }
    public function undo_delete_attachment_sample_test(Request $request)
    {
        $sample_test_id = $request->sample_test_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('sample_tests_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('sample_tests_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted attachment.', 'sample_test_id' => $sample_test_id]);
        return redirect()->back()->with('success', 'Sample Test attachment undeleted successfully.');
    }


    public function test_thresholds()
    {
        return view('test-thresholds');
    }
    public function fetch_test_names(Request $request)
    {
        try {
            $query = $request->input('query');

            $items = DB::table('test_definitions')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('test_name', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('test_name')
                ->limit(500)
                ->get();

            $html = '<option value="">Select Test Name</option>';
            foreach ($items as $item) {
                $html .= '<option value="' . e($item->id) . '" data-id="' . e($item->id) . '" data-test-type="' . e($item->test_type) . '" data-criteria="' . e($item->criteria) . '" data-uom="' . e($item->uom) . '" data-standard="' . e($item->standard) . '" data-description="' . e($item->description) . '">' . e($item->test_name) . '</option>';
            }

            return response()->json(['options' => $html]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetch_item_categories(Request $request)
    {
        try {
            $query = $request->input('query');

            // $items = DB::table('item_categories')
            //     ->when($query, function ($q) use ($query) {
            //         $q->where(function ($q2) use ($query) {
            //             $q2->where('item_category', 'like', "%$query%");
            //         });
            //     })
            //     ->where('status', 1)
            //     ->where('is_deleted', 0)
            //     ->orderBy('item_category')
            //     ->limit(500)
            //     ->get();
            $items = DB::table('itemcodes')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('item_category', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('item_category')
                ->limit(500)
                ->get();

            $html = '<option value="">Select Item Category</option>';
            foreach ($items as $item) {
                $html .= '<option value="' . e($item->id) . '" data-id="' . e($item->id) . '">' . e($item->item_category) . '</option>';
            }

            return response()->json(['options' => $html]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function fetch_item_categories_(Request $request)
    {
        try {
            $query = $request->input('query');



            $items = DB::table('itemcodes')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('item_category', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('item_category')
                ->groupBy('item_category')
                ->limit(10)
                ->get();


            // Return directly in Select2 format
            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->item_category,
                    'desc' => $item->description,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetch_test_name_(Request $request)
    {
        try {
            $query = $request->input('query');



            $items = DB::table('itemcodes')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('item_category', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('item_category')
                ->groupBy('item_category')
                ->limit(10)
                ->get();


            // Return directly in Select2 format
            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->item_category,
                    'desc' => $item->description,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetch_item_code_size(Request $request)
    {
        try {
            $query = $request->input('query');
            $items = DB::table('itemcodes')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('size', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('size')
                ->groupBy('size')
                ->limit(10)
                ->get();


            // Return directly in Select2 format
            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->size,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetch_item_chain_code(Request $request)
    {
        try {
            $query = $request->input('query');
            $items = DB::table('itemcodes')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('chain_code', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('chain_code')
                ->groupBy('chain_code')
                ->limit(10)
                ->get();


            // Return directly in Select2 format
            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->chain_code,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetch_item_code(Request $request)
    {
        try {
            $query = $request->input('query');
            $items = DB::table('itemcodes')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('item_code', 'like', "%$query%");
                    });
                })
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->orderBy('item_code')
                ->groupBy('item_code')
                ->limit(10)
                ->get();


            // Return directly in Select2 format
            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->item_code,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function insert_test_threshold(Request $request)
    {
        // $request->validate([
        //     'testNameSelect' => 'required|string|unique:test_thresholds,test_name_id',
        // ], [
        //     'testNameSelect.unique' => 'Test Threshold already exists.',
        //     'testNameSelect.required' => 'Test Name is required.',
        // ]);

        $exists = DB::table('test_thresholds')->where('is_deleted', 0)->where('test_name_id', $request->testNameSelect)->first();

        if ($exists) {
            return response()->json([
                'status' => 'exist',
                'message' => 'Test Threshold already exists.'
            ]);
        }

        DB::beginTransaction();

        try {
            $test_threshold_id = DB::table('test_thresholds')->insertGetId([
                'test_name_id' => $request->testNameSelect,
                'status'       => 1,
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $insertData = array_map(
                function ($item_category_id, $item_category_name, $min, $max, $avg, $yfs, $yfgs, $safety_threshold, $absorption) use ($test_threshold_id) {
                    return [
                        'test_threshold_id'   => $test_threshold_id,
                        'item_category_id'    => $item_category_id,
                        'item_category_name'    => $item_category_name,
                        'min'                 => is_numeric($min) ? $min : null,
                        'max'                 => is_numeric($max) ? $max : null,
                        'avg'                 => is_numeric($avg) ? $avg : null,
                        'YFS'                 => is_numeric($yfs) ? $yfs : null,
                        'YFGS'                => is_numeric($yfgs) ? $yfgs : null,
                        'safety_threshold'    => is_numeric($safety_threshold) ? $safety_threshold : null,
                        'absorption'          => is_numeric($absorption) ? $absorption : null,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                        'updated_by'          => Auth::id(),
                    ];
                },
                (array) $request->item_category_ids,
                (array) $request->item_category_name,
                (array) $request->min_values,
                (array) $request->max_values,
                (array) $request->avg_values,
                (array) $request->yfs_values,
                (array) $request->yfgs_values,
                (array) $request->safety_threshold_values,
                (array) $request->absorption_values
            );

            DB::table('test_threshold_item_categories')->insert($insertData);

            DB::table('test_threshold_audit_trail')->insert([
                'user_id'           => Auth::id(),
                'description'       => 'Test Threshold added successfully',
                'test_threshold_id' => $test_threshold_id,
                'created_at'        => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Test Threshold saved successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to save Test Threshold. Please try again.'
            ], 500);
        }
    }


    public function update_test_threshold(Request $request)
    {
        DB::beginTransaction();

        try {
            $test_threshold_id = $request->category_id;

            DB::table('test_threshold_item_categories')
                ->where('test_threshold_id', $test_threshold_id)
                ->update(['is_deleted' => 1]);

            $insertData = array_map(
                function ($item_category_id, $min, $max, $avg, $yfs, $yfgs, $safety_threshold, $absorption) use ($test_threshold_id) {
                    return [
                        'test_threshold_id'   => $test_threshold_id,
                        'item_category_id'    => $item_category_id,
                        'min'                 => is_numeric($min) ? $min : null,
                        'max'                 => is_numeric($max) ? $max : null,
                        'avg'                 => is_numeric($avg) ? $avg : null,
                        'YFS'                 => is_numeric($yfs) ? $yfs : null,
                        'YFGS'                => is_numeric($yfgs) ? $yfgs : null,
                        'safety_threshold'    => is_numeric($safety_threshold) ? $safety_threshold : null,
                        'absorption'          => is_numeric($absorption) ? $absorption : null,
                        'updated_at'          => now(),
                        'updated_by'          => Auth::id(),
                    ];
                },
                (array) $request->item_category_id,
                (array) $request->min_values,
                (array) $request->max_values,
                (array) $request->avg_values,
                (array) $request->yfs_values,
                (array) $request->yfgs_values,
                (array) $request->safety_threshold_values,
                (array) $request->absorption_values
            );

            DB::table('test_threshold_item_categories')->insert($insertData);

            DB::table('test_threshold_audit_trail')->insert([
                'user_id'           => Auth::id(),
                'description'       => 'Test Threshold updated successfully',
                'test_threshold_id' => $test_threshold_id,
                'created_at'        => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Test Threshold updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update Test Threshold. Please try again.'
            ], 500);
        }
    }


    public function get_test_threshold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:test_thresholds,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Test Threshold ID'
            ], 400);
        }

        try {
            // Get the category data
            $category = DB::table('test_thresholds as tt')
                ->leftJoin('test_definitions as td', 'td.id', '=', 'tt.test_name_id')
                ->where('tt.id', $request->id)
                ->where('tt.is_deleted', 0)
                ->select(
                    'tt.*',
                    'td.test_name',
                    'td.test_type',
                    'td.uom',
                    'td.criteria',
                    'td.standard',
                )
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test Threshold not found or has been deleted'
                ], 404);
            }

            // Get associated itemcodes
            $item_categories = DB::table('test_threshold_item_categories as ttic')
                ->leftJoin('itemcodes as i', 'i.id', '=', 'ttic.item_category_id')
                ->where('ttic.test_threshold_id', $request->id)
                ->where('ttic.is_deleted', 0)
                ->select(
                    'ttic.*',
                    'i.item_category',
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $category,
                'item_categories' => $item_categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Test Threshold data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function get_test_threshold_content(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('test_thresholds as tt')->select('tt.*', 'td.test_name', 'td.test_type', 'td.uom', 'td.criteria', 'td.standard', 'td.description')->leftjoin('test_definitions as td', 'td.id', '=', 'tt.test_name_id')->where('tt.id', $id)->where('tt.is_deleted', 0)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $test_threshold_item_categories = DB::table('test_threshold_item_categories as ttic')
            ->select('ttic.*', 'ic.item_category')
            // ->leftJoin('item_categories as ic', 'ic.id', '=', 'ttic.item_category_id')
            ->leftJoin('itemcodes as ic', 'ic.id', '=', 'ttic.item_category_id')
            ->where('ttic.test_threshold_id', $q->id)
            ->where('ttic.is_deleted', 0)
            ->where('ic.is_deleted', 0)
            ->orderBy('ic.item_category', 'asc')
            ->get();


        $attachments = DB::table('test_threshold_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.test_threshold_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('test_threshold_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.test_threshold_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('test_threshold_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.test_threshold_id', $id)->get();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0" style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-10">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Test Name</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new text-blue fw-600 provinceText w-75" data="' . $q->id . '">' . $q->test_name . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Test Type</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="d-flex align-items-center">
                            <div class="field-new field-color fw-300 provinceText w-75" data="' . $q->id . '">' . $q->test_type . '</div> <span class="ml-3 fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test UOM">' . $q->uom . '</span>
                        </div>
                    </div>
                </div>';


        if ($q->test_type == 'Perf-Str') {
            $html .= '


                    <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Criteria</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new text-blue fw-600 provinceText w-75" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Type" data="' . $q->id . '">' . $q->criteria . '</div>
                    </div>
                    </div>
        ';
        }


        $html .= '         </div>
        </div>
    </div>
    <hr class="modal-hr">
    <div class="block-content pb-0 pt-1" style="padding-left: 50px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-4 mb-3">
                <div class="d-flex align-items-center">
                    <a type="button" class="mr-2 clear_filter_itemcode_detail" id="clear_filter_itemcode_detail" style="display: none;">
                        <img src="public/img/cf-menu-icons/detail-line-remove.png" width="15">
                    </a>
                    <input type="text" class="filter-input-modal mb-1 itemcodeSearch" id="itemcodeSearch" placeholder="" autocomplete="off">
                    <a type="button" class="ml-2 filter_itemcode_detail" id="filter_itemcode_detail" style="display: none;">
                        <img src="public/img/cf-menu-icons/menu-icon-right.png" width="19">
                    </a>
                </div>
                <span class="modal-subheader modal-subheader-text">SEARCH THRESHOLDS</span>
            </div>
            <div class="col-sm-12 pt-1 selectedItemcodes small-box small-box-340 pr-5">
';
        if ($test_threshold_item_categories->count() > 0) {
            foreach ($test_threshold_item_categories as $ic) {

                $typeSpecificHTML = '';

                if ($q->test_type == 'Dimension') {
                    $typeSpecificHTML = '
                <div>
                    <span class="modal-subheader">min <span class="minor-tag">' . $ic->min . '</span></span>
                    <span class="modal-subheader">avg <span class="minor-tag">' . $ic->avg . '</span></span>
                    <span class="modal-subheader">max <span class="minor-tag">' . $ic->max . '</span></span>
                </div>
            ';
                } elseif ($q->test_type == 'Perf-Str' && $q->criteria == 'Min') {
                    $typeSpecificHTML = '
                <div>
                    <span class="modal-subheader">YFS <span class="minor-tag">' . $ic->YFS . '</span></span>
                    <span class="modal-subheader">YFGS <span class="minor-tag">' . $ic->YFGS . '</span></span>
                    <span class="modal-subheader">ST <span class="minor-tag">' . $ic->safety_threshold . '</span></span>
                </div>
            ';
                } elseif ($q->test_type == 'Perf-Str' && $q->criteria == 'Max') {
                    $typeSpecificHTML = '
                <div>
                    <span class="modal-subheader">YFS <span class="minor-tag">' . $ic->YFS . '</span></span>
                    <span class="modal-subheader">YFGS <span class="minor-tag">' . $ic->YFGS . '</span></span>
                </div>
            ';
                } elseif ($q->test_type == 'Perf-Weight') {
                    $typeSpecificHTML = '
                <div>
                    <span class="modal-subheader">max <span class="minor-tag">' . number_format($ic->absorption, 2) . '%</span></span>
                </div>
            ';
                }

                $html .= '
            <div class="selected-cat-itemcodes selected-items-list p-3 d-flex align-items-center" style="margin-bottom: 10px;">
                <span class="selected-itemcode col-2">' . $ic->item_category . '</span>
                <span class="selected-itemcode-desc">' . $typeSpecificHTML . '</span>
            </div>
        ';
            }
        }

        $html .= '
            </div>
        </div>
    </div>
</div>
        ';


        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/test_thresholds_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
         ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative pr-2" style="">

<div  class="w-100 ">
    <div  class="d-flex justify-content-between">
        <div>
            <span class="font-signika bubble-item-title" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name">
                ' . $q->test_name . '
            </span>
            <span class="font-signika bubble-item-title fw-300 ml-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test UOM" style="font-size: 9pt; border-color: #989698;">' . $q->uom . '
            </span>
        </div>
        <div style="position: absolute;right: 10px;top: 10px;">
            ';
        if ($q->status == 1) {
            $itemcodeList .= '
                <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
        ';
        } else {
            $itemcodeList .= '
                <span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>
        ';
        }
        $itemcodeList .= '
        </div>
    </div>
    <div  class="d-flex justify-content-between" style="margin-top: 9px;">
        <div class="mob-minor-tags">
';
        $test_threshold_item_categories = DB::table('test_threshold_item_categories as ttic')
            ->select('ttic.*', 'ic.item_category')
            // ->leftJoin('item_categories as ic', 'ic.id', '=', 'ttic.item_category_id')
            ->leftJoin('itemcodes as ic', 'ic.id', '=', 'ttic.item_category_id')
            ->where('ttic.test_threshold_id', $q->id)
            ->where('ttic.is_deleted', 0)
            ->where('ic.is_deleted', 0)
            ->orderBy('ic.item_category', 'asc')
            ->get();


        $count = 0;

        if ($test_threshold_item_categories->count() > 0) {
            foreach ($test_threshold_item_categories as $ttic) {
                if ($count < 4) {
                    $itemcodeList .= '
            <span class="minor-tag" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Item Category">
                ' . $ttic->item_category . '
            </span>
';
                }
                $count++;
            }
            if ($test_threshold_item_categories->count() > 4) {
                $itemcodeList .= '
                <span class="minor-tag">...</span>
';
            }
        }
        $itemcodeList .= '
        </div>
        <div>
            <div class="dropdown dropdown-3dot">
                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" data-id="' . $q->id . '" data-status="' . $q->status . '" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                  <img src="public/img/cf-menu-icons/3dots.png" width="9">
                </a>
                <div class="dropdown-menu dropdown-menu-3dot">
';
        if ($q->status == 1) {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-deactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-deactivate.png"> Deactivate</a>
';
        } else {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-reactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-activate.png"> Reactivate</a>
';
        }
        $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot edit-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                  <a class="dropdown-item dropdown-item-3dot delete-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "test_name" => $q->test_name,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }
    public function import_test_thresholds(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new ImportTestThresholds();

        Excel::import($import, $request->file('file'));

        if (isset($import->data) && count($import->data) > 0) {
            return redirect()->back()->with('success', count($import->data) . ' Test Thresholds imported successfully.');
        }

        return redirect()->back()->with('error', "No Test Thresholds imported.");
    }
    public function export_test_thresholds(Request $request)
    {
        return Excel::download(new ExportTestThresholds($request), 'Test-Thresholds.xlsx');
    }
    public function delete_test_threshold(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_thresholds')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Threshold deleted | ' . $id, 'test_threshold_id' => $id]);
        return redirect()->back()->with('alert-delete-category', 'Test Threshold deleted|' . $id);
    }
    public function undo_delete_test_threshold(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_thresholds')->where('id', $id)->update(['is_deleted' => 0]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Threshold restored.', 'test_threshold_id' => $request->id]);
        return redirect()->back()->with('success', 'Test Threshold undeleted successfully.');
    }
    public function end_test_threshold(Request $request)
    {
        $check = DB::table('test_thresholds')->where('id', $request->id)->first();
        if ($check->status == 0) {
            DB::table('test_thresholds')->where('id', $request->id)->update(['status' => 1]);
            DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Threshold successfully reinstated.', 'test_threshold_id' => $request->id]);
            return redirect()->back()->with('success', 'Test Threshold Activated Successfully');
        } else {
            DB::table('test_thresholds')->where('id', $request->id)->update(['status' => 0]);
            DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Threshold successfully revoked.', 'test_threshold_id' => $request->id]);
            return redirect()->back()->with('success', 'Test Threshold Deactivated Successfully');
        }
    }
    public function get_comments_test_threshold(Request $request)
    {
        $qry = DB::table('test_threshold_comments as i')
            ->where('i.test_threshold_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }
    public function insert_comment_test_threshold(Request $request)
    {
        DB::table('test_threshold_comments')->insert([
            'test_threshold_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'test_threshold_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function update_comment_test_threshold(Request $request)
    {
        $test_threshold_id = $request->test_threshold_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_threshold_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'test_threshold_id' => $test_threshold_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }
    public function delete_comment_test_threshold(Request $request)
    {
        $test_threshold_id = $request->test_threshold_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_threshold_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'test_threshold_id' => $test_threshold_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id . '|' . $test_threshold_id);
    }
    public function undo_delete_comment_test_threshold(Request $request)
    {
        $test_threshold_id = $request->test_threshold_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_threshold_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted comment | ' . $id, 'test_threshold_id' => $test_threshold_id]);
        return redirect()->back()->with('success', 'Test Threshold comment undeleted successfully.');
    }




    public function uploadTestThresholdAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadTestThresholdAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("test_thresholds_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("test_thresholds_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("test_thresholds_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("test_thresholds_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertTestThresholdAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_test_threshold(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('test_thresholds_attachment/' . $a));
                DB::table('test_threshold_attachments')->insert([
                    'test_threshold_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'test_threshold_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }


    public function delete_attachment_test_threshold(Request $request)
    {
        $test_threshold_id = $request->test_threshold_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_threshold_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'test_threshold_id' => $test_threshold_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id . '|' . $test_threshold_id);
    }
    public function undo_delete_attachment_test_threshold(Request $request)
    {
        $test_threshold_id = $request->test_threshold_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_threshold_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('test_threshold_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted attachment.', 'test_threshold_id' => $test_threshold_id]);
        return redirect()->back()->with('success', 'Test Threshold attachment undeleted successfully.');
    }


    public function test_definitions()
    {
        return view('test-definitions');
    }
    public function insert_test_definition(Request $request)
    {
        // Validate the input first
        $request->validate([
            'test_name' => 'required|string|max:32',
            'test_type' => 'required|string|in:Dimension,Perf-Str,Perf-Weight',
            'description' => 'nullable|string',

            // new validations
            'c_cm' => 'required_if:test_type,Dimension',
            'c_inch' => 'required_if:test_type,Dimension',
            'c_kgf' => 'required_if:test_type,Perf-Str,criteria,Max',
            'c_kg' => 'required_if:test_type,Perf-Weight'
        ]);

        // Check if asset_no already exists
        $exists = DB::table('test_definitions')->where('test_name', $request->test_name)->where('is_deleted', 0)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Test name already exists.',
            ], 409); // 409 Conflict
        }

        // Insert asset
        $TestDefinitionId = DB::table('test_definitions')->insertGetId([
            'test_name'  => strtoupper($request->test_name),
            'test_type'  => $request->test_type,
            'criteria'   => $request->test_type == 'Perf-Str' ? $request->criteria : null,
            'uom'        => $request->uom,
            'standard'   => $request->test_type == 'Perf-Str' ? $request->standard : null,
            'c_kgf'   => $request->test_type == 'Perf-Str' ? $request->c_kgf : null,
            'c_cm'   => $request->test_type == 'Dimension' ? $request->c_cm : null,
            'c_inch'   => $request->test_type == 'Dimension' ? $request->c_inch : null,
            'c_kg'   => $request->test_type == 'Perf-Weight' ? $request->c_kg : null,
            'description' => $request->description,
            'status'     => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert audit trail
        DB::table('test_definition_audit_trail')->insert([
            'user_id' => Auth::id(),
            'description' => 'Test Definition Saved Successfully',
            'test_definition_id' => $TestDefinitionId
        ]);

        return response()->json([
            'message' => 'Test Definition Saved Successfully.'
        ]);
    }
    public function update_test_definition(Request $request)
    {
        // Debug but allow execution to continue
        // \Log::info('Request Data:', $request->all());

        $request->validate([
            'test_name'   => 'required|string|max:32',
            'test_type'   => 'required|string|in:Dimension,Perf-Str,Perf-Weight',
            'description' => 'nullable|string',

            // new validations
            'c_cm' => 'required_if:test_type,Dimension',
            'c_inch' => 'required_if:test_type,Dimension',
            'c_kgf' => 'required_if:test_type,Perf-Str,criteria,Max',
            'c_kg' => 'required_if:test_type,Perf-Weight'
        ]);

        $exists = DB::table('test_definitions')
            ->where('test_name', $request->test_name)
            ->where('id', '!=', $request->test_definition_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Test Definition already exists.',
            ], 409);
        }

        $uom = null;

        if ($request->test_type === 'Perf-Str' && $request->criteria === 'Max') {
            $uom = $request->uom;
        } elseif ($request->test_type === 'Perf-Str' && $request->criteria === 'Min') {
            $allowedUoms = ['N', 'Kgf'];
            if (in_array($request->uom, $allowedUoms)) {
                $uom = $request->uom;
            }
        } elseif ($request->test_type === 'Perf-Weight') {
            $allowedUoms = ['gm', 'Kg'];
            if (in_array($request->uom, $allowedUoms)) {
                $uom = $request->uom;
            }
        } elseif ($request->test_type === 'Dimension') {
            $allowedUoms = ['mm', 'cm', 'in'];
            if (in_array($request->uom, $allowedUoms)) {
                $uom = $request->uom;
            }
        }

        DB::table('test_definitions')
            ->where('id', $request->test_definition_id)
            ->update([
                'uom'        => $uom,
                'c_kgf'   => $request->test_type == 'Perf-Str' ? $request->c_kgf : null,
                'c_cm'   => $request->test_type == 'Dimension' ? $request->c_cm : null,
                'c_inch'   => $request->test_type == 'Dimension' ? $request->c_inch : null,
                'c_kg'   => $request->test_type == 'Perf-Weight' ? $request->c_kg : null,
                'standard'   => $request->test_type === 'Perf-Str' ? $request->standard : null,
                'description' => $request->description,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        DB::table('test_definition_audit_trail')->insert([
            'user_id'            => Auth::id(),
            'description'        => 'Test Definition Updated Successfully',
            'test_definition_id' => $request->test_definition_id
        ]);

        return response()->json([
            'message' => 'Test Definition Updated Successfully.'
        ]);
    }



    public function get_test_definition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:test_definitions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Test Definition ID'
            ], 400);
        }

        try {
            // Get the category data
            $category = DB::table('test_definitions')
                ->where('id', $request->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test Definition not found or has been deleted'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Test Definition data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function get_test_definition_content(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('test_definitions')->where('id', $id)->where('is_deleted', 0)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $attachments = DB::table('test_definition_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.test_definition_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('test_definition_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.test_definition_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('test_definition_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.test_definition_id', $id)->get();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0" style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-11">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Test Name</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name" data="' . $q->id . '">' . $q->test_name . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Description</div>
                    </div>
                    <div class="col-sm-9 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Description" data="' . $q->id . '">' . $q->description . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Test Type</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Type" data="' . $q->id . '">' . $q->test_type . '</div>
                    </div>
                    </div>
        ';
        if ($q->test_type == 'Perf-Str') {
            $html .= '
                   <!--  <div class="col-sm-2 pl-0">
                        <div class="fw-600 label-new field-color shadow-none text-center" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Criteria" data="' . $q->id . '"><b>' . $q->criteria . '</b></div>
                    </div> -->

                    <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Criteria</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Type" data="' . $q->id . '">' . $q->criteria . '</div>
                    </div>
                    </div>
        ';
        }
        $html .= '

                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Unit of Measure</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                        <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test UOM" data="' . $q->id . '">' . $q->uom . '</div>
                    </div>
                </div>';
        // new fields
        if ($q->test_type == 'Dimension') {
            $html .= '
                    <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                    <div class="label-new col-form-label">Conversion rate to cm</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                    <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name" data="' . $q->id . '">' . ($q->c_cm ? $q->c_cm : "&nbsp") . '</div>
                    </div>
                    </div>
                    <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                    <div class="label-new col-form-label">Conversion rate to inch</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                    <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name" data="' . $q->id . '">' . ($q->c_inch ? $q->c_inch : "&nbsp") . '</div>
                    </div>
                    </div>
                    ';
        }
        if ($q->test_type == 'Perf-Str' && $q->criteria == "Min") {
            $html .= '
                    <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                    <div class="label-new col-form-label">Conversion rate to Kgf</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                    <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name" data="' . $q->id . '">' . ($q->c_kgf ? $q->c_kgf : "&nbsp") . '</div>
                    </div>
                    </div>
                    ';
        }
        if ($q->test_type == 'Perf-Weight') {
            $html .= '
                    <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                    <div class="label-new col-form-label">Conversion rate to Kg</div>
                    </div>
                    <div class="col-sm-4 pl-0">
                    <div class="field-new field-color fw-300 provinceText w-100" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name" data="' . $q->id . '">' . ($q->c_kg ? $q->c_kg : "&nbsp") . '</div>
                    </div>
                    </div>
                    ';
        }
        // new fields end
        // if ($q->test_type == 'Perf-Str') {
        //     $html .= '
        //             <div class="col-sm-2 pl-0">
        //                 <div class="fw-600 label-new field-color shadow-none text-center" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Standard" data="' . $q->id . '"><b>' . $q->standard . '</b></div>
        //             </div>
        // ';
        // }
        $html .= '


            </div>
        </div>
    </div>
</div>
        ';


        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/test_definitions_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
         ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative pr-2" style="">

<div  class="w-100 ">
    <div  class="d-flex justify-content-between">
        <div>
            <span class="font-signika bubble-item-title" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Name">' . $q->test_name . '</span>
            <span class="font-signika bubble-item-title fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test UOM" style="font-size: 9pt; border-color: #989698;">' . $q->uom . '</span>
        ';
        if ($q->test_type == 'Perf-Str') {
            $itemcodeList .= '
            <span class="font-signika bubble-item-title fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Criteria" style="font-size: 9pt; border-color: #989698;">' . $q->criteria . '</span>
        ';
        }
        $itemcodeList .= '

        </div>
        <div style="position: absolute;right: 10px;top: 10px;">
            ';
        if ($q->status == 1) {
            $itemcodeList .= '
                <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
        ';
        } else {
            $itemcodeList .= '
                <span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>
        ';
        }
        $itemcodeList .= '
        </div>
    </div>
    <div  class="d-flex justify-content-between align-items-center" style="margin-top: 5px;">
        <div>
        <span class="font-signika bubble-item-title fw-300" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Test Type" style="font-size: 9pt; border-color: #989698;">' . $q->test_type . '</span>
           <span class="font-signika bubble-item-desc ml-1">' . $q->description . '</span>
       </div>
        <div>
            <div class="dropdown dropdown-3dot">
                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" data-id="' . $q->id . '" data-status="' . $q->status . '" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                  <img src="public/img/cf-menu-icons/3dots.png" width="9">
                </a>
                <div class="dropdown-menu dropdown-menu-3dot">
';
        if ($q->status == 1) {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-deactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-deactivate.png"> Deactivate</a>
';
        } else {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-reactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-activate.png"> Reactivate</a>
';
        }
        $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot edit-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                  <a class="dropdown-item dropdown-item-3dot delete-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "test_name" => $q->test_name,
            "test_type" => $q->test_type,
            "description" => $q->description,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }
    public function import_test_definitions(Request $request)
    {
        $import = new ImportTestDefinitions;
        Excel::import($import, $request->file('file')->store('temp'));
        if (count($import->data) > 0) {
            return redirect()->back()->with('success', count($import->data) . ' Test Definitions imported successfully.');
        }
        return redirect()->back()->with('error', "No Test Definitions imported.");
    }
    public function export_test_definitions(Request $request)
    {
        return Excel::download(new ExportTestDefinitions($request), 'Test-Definitions.xlsx');
    }
    public function delete_test_definition(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definitions')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Definition deleted | ' . $id, 'test_definition_id' => $id]);
        return redirect()->back()->with('alert-delete-category', 'Test Definition deleted|' . $id);
    }
    public function undo_delete_test_definition(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definitions')->where('id', $id)->update(['is_deleted' => 0]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Definition restored.', 'test_definition_id' => $request->id]);
        return redirect()->back()->with('success', 'Test Definition undeleted successfully.');
    }
    public function end_test_definition(Request $request)
    {
        $check = DB::table('test_definitions')->where('id', $request->id)->first();
        if ($check->status == 0) {
            DB::table('test_definitions')->where('id', $request->id)->update(['status' => 1]);
            DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Definition successfully reinstated.', 'test_definition_id' => $request->id]);
            return redirect()->back()->with('success', 'Test Definition Activated Successfully');
        } else {
            DB::table('test_definitions')->where('id', $request->id)->update(['status' => 0]);
            DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Test Definition successfully revoked.', 'test_definition_id' => $request->id]);
            return redirect()->back()->with('success', 'Test Definition Deactivated Successfully');
        }
    }
    public function get_comments_test_definition(Request $request)
    {
        $qry = DB::table('test_definition_comments as i')
            ->where('i.test_definition_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }
    public function insert_comment_test_definition(Request $request)
    {
        DB::table('test_definition_comments')->insert([
            'test_definition_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'test_definition_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function update_comment_test_definition(Request $request)
    {
        $test_definition_id = $request->test_definition_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definition_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'test_definition_id' => $test_definition_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }
    public function delete_comment_test_definition(Request $request)
    {
        $test_definition_id = $request->test_definition_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definition_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'test_definition_id' => $test_definition_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id . '|' . $test_definition_id);
    }
    public function undo_delete_comment_test_definition(Request $request)
    {
        $test_definition_id = $request->test_definition_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definition_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted comment | ' . $id, 'test_definition_id' => $test_definition_id]);
        return redirect()->back()->with('success', 'Test Definition comment undeleted successfully.');
    }




    public function uploadTestDefinitionAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadTestDefinitionAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("test_definitions_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("test_definitions_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("test_definitions_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("test_definitions_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertTestDefinitionAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_test_definition(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('test_definitions_attachment/' . $a));
                DB::table('test_definition_attachments')->insert([
                    'test_definition_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'test_definition_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }


    public function delete_attachment_test_definition(Request $request)
    {
        $test_definition_id = $request->test_definition_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definition_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'test_definition_id' => $test_definition_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id . '|' . $test_definition_id);
    }
    public function undo_delete_attachment_test_definition(Request $request)
    {
        $test_definition_id = $request->test_definition_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('test_definition_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('test_definition_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Restored deleted attachment.', 'test_definition_id' => $test_definition_id]);
        return redirect()->back()->with('success', 'Test Definition attachment undeleted successfully.');
    }




    public function item_categories()
    {
        return view('item-categories');
    }
    public function ImportItemCategories(Request $request)
    {
        $import = new ItemCategoriesImport;
        Excel::import($import, $request->file('file')->store('temp'));

        $message = [];

        if ($import->insertedCategories > 0) {
            $message[] = $import->insertedCategories . ' new categories added';
        }

        if ($import->insertedItemCodes > 0) {
            $message[] = $import->insertedItemCodes . ' new item codes added';
        }

        if ($import->createdRelationships > 0) {
            $message[] = $import->createdRelationships . ' relationships created';
        }

        if (empty($message)) {
            return redirect()->back()->with('info', 'No new data was imported - all items already exist');
        }

        return redirect()->back()->with('success', implode(', ', $message) . '.');
    }
    public function ExportItemCategories(Request $request)
    {
        return Excel::download(new ExportItemCategories($request), 'Item-categories.xlsx');
    }
    // public function fetchItemcodes(Request $request)
    // {
    //     try {
    //         $query = $request->input('query');

    //         $items = DB::table('itemcodes as i')
    //             ->when($query, function ($q) use ($query) {
    //                 $q->where('i.item_code', 'like', "%$query%")
    //                   ->orWhere('i.description', 'like', "%$query%");
    //             })
    //             ->orderBy('i.item_code')
    //             ->limit(500)
    //             ->get();

    //         $html = '<option value="">Select Itemcode</option>';
    //         foreach ($items as $item) {
    //             $html .= '<option value="' . e($item->item_code) . '" data-id="' . e($item->id) . '" data-description="' . e($item->description) . '">' . e($item->item_code) . '</option>';
    //         }

    //         return response()->json(['options' => $html]);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }
    // old
    public function fetchItemcodes(Request $request)
    {
        try {
            $query = $request->input('query');

            $items = DB::table('itemcodes as i')
                ->when($query, function ($q) use ($query) {
                    $q->where('i.item_code', 'like', "%$query%");
                })
                ->orderBy('i.item_code')
                ->limit(10)
                ->get();

            $html = '<option value="">Select Itemcode</option>';
            foreach ($items as $item) {
                $html .= '<option value="' . e($item->item_code) . '" data-id="' . e($item->id) . '" data-description="' . e($item->description) . '">' . e($item->item_code) . '</option>';
            }

            return response()->json(['options' => $html]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function fetchItemcodes_(Request $request)
    {
        try {
            $query = $request->input('query');

            $items = DB::table('itemcodes as i')
                ->when($query, function ($q) use ($query) {
                    $q->where('i.item_code', 'like', "%$query%");
                })
                ->orderBy('i.item_code')
                ->limit(10)
                ->get();

            // Return directly in Select2 format
            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->item_code,
                    'description' => $item->description
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_item_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:item_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid item category ID'
            ], 400);
        }

        try {
            // Get the category data
            $category = DB::table('item_categories')
                ->where('id', $request->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found or has been deleted'
                ], 404);
            }

            // Get associated itemcodes
            $itemcodes = DB::table('item_categories_itemcodes')
                ->leftJoin('itemcodes', 'itemcodes.id', '=', 'item_categories_itemcodes.itemcode_id')
                ->where('item_categories_itemcodes.item_category_id', $request->id)
                ->where('item_categories_itemcodes.is_deleted', 0)
                ->select(
                    'itemcodes.id',
                    'itemcodes.item_code',
                    'itemcodes.description'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $category,
                'itemcodes' => $itemcodes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch item category data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function insertItemCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_category' => 'required|string|max:24|unique:item_categories,item_category',
            'itemcode_ids' => 'required|array|min:1',
            'itemcode_ids.*' => 'integer|exists:itemcodes,id',
        ], [
            'item_category.unique' => 'This item category already exists.',
            'itemcode_ids.required' => 'Please select at least one item.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'This item category already exists',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Insert item category
            $itemCategoryId = DB::table('item_categories')->insertGetId([
                'item_category' => $request->item_category,
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert related itemcodes
            $insertData = array_map(function ($id) use ($itemCategoryId) {
                return [
                    'item_category_id' => $itemCategoryId,
                    'itemcode_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ];
            }, $request->itemcode_ids);

            DB::table('item_categories_itemcodes')->insert($insertData);

            // Add to audit trail
            DB::table('item_categories_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Item Category added successfully',
                'item_category_id' => $itemCategoryId,
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Item category created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create item category. Please try again.'
            ], 500);
        }
    }


    public function updateItemCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:item_categories,id',
            'item_category' => 'required|string|max:24|unique:item_categories,item_category,' . $request->category_id,
            'itemcode_ids' => 'required|array|min:1',
            'itemcode_ids.*' => 'integer|exists:itemcodes,id,is_deleted,0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Check for duplicate itemcodes in the request
        if (count($request->itemcode_ids) !== count(array_unique($request->itemcode_ids))) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'itemcode_ids' => ['Duplicate itemcodes found in the request.']
                ]
            ], 422);
        }


        try {
            DB::beginTransaction();

            // 1. Soft delete all existing itemcode associations
            DB::table('item_categories_itemcodes')
                ->where('item_category_id', $request->category_id)
                ->update([
                    'is_deleted' => 1
                ]);

            // 2. Prepare new itemcode associations
            $insertData = array_map(function ($id) use ($request) {
                return [
                    'item_category_id' => $request->category_id,
                    'itemcode_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id()
                ];
            }, $request->itemcode_ids);

            // 3. Insert new associations (using updateOrInsert to handle existing soft-deleted records)
            foreach ($insertData as $data) {
                DB::table('item_categories_itemcodes')->insert(
                    [
                        'item_category_id' => $data['item_category_id'],
                        'itemcode_id' => $data['itemcode_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'updated_by' => Auth::id()
                    ],
                    $data
                );
            }

            DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Item Category updated successfully', 'item_category_id' => $request->category_id]);

            DB::commit();

            return response()->json([
                'message' => 'Item category updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update item category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getItemCategoriesContent(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('item_categories as i')->where('i.id', $id)->where('i.is_deleted', 0)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $item_cat_itemcodes = DB::table('item_categories_itemcodes as ici')->select('ici.*', 'i.item_code', 'i.description')->leftjoin('itemcodes as i', 'i.id', '=', 'ici.itemcode_id')->where('i.is_deleted', 0)->where('ici.item_category_id', $q->id)->where('ici.is_deleted', 0)->orderBy('ici.item_category_id', 'asc')->get();

        $attachments = DB::table('item_categories_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.item_category_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('item_categories_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.item_category_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('item_categories_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.item_category_id', $id)->get();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0" style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-10">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-3">
                        <div class="label-new col-form-label">Item Category</div>
                    </div>
                    <div class="col-sm-6 pl-0">
                        <div class="field-new text-blue fw-600 provinceText text-uppercase" data="' . $q->id . '">' . $q->item_category . '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="modal-hr">
    <div class="block-content pb-0 pt-1" style="padding-left: 50px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-4 mb-3">
                <div class="d-flex align-items-center">
                    <a type="button" class="mr-2 clear_filter_itemcode_detail" id="clear_filter_itemcode_detail" style="display: none;">
                        <img src="public/img/cf-menu-icons/detail-line-remove.png" width="15">
                    </a>
                    <input type="text" class="filter-input-modal mb-1 itemcodeSearch" id="itemcodeSearch" placeholder="" autocomplete="off">
                    <a type="button" class="ml-2 filter_itemcode_detail" id="filter_itemcode_detail" style="display: none;">
                        <img src="public/img/cf-menu-icons/menu-icon-right.png" width="19">
                    </a>
                </div>
                <span class="modal-subheader modal-subheader-text">FILTER ITEMCODES</span>
            </div>
            <div class="col-sm-12 pt-1 selectedItemcodes small-box small-box-340 pr-5">
';
        if ($item_cat_itemcodes->count() > 0) {
            foreach ($item_cat_itemcodes as $ic) {
                $html .= '
                <div class="selected-cat-itemcodes selected-items-list p-3 d-flex align-items-center" style="margin-bottom: 10px;">
                    <span class="selected-itemcode">' . $ic->item_code . '</span>
                    <span class="selected-itemcode-desc ml-4">' . $ic->description . '</span>
                </div>
';
            }
        }
        $html .= '
            </div>
        </div>
    </div>
</div>
        ';


        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/item_category_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
         ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative pr-2" style="">

<div  class="w-100 ">
    <div  class="d-flex justify-content-between">
        <span class="font-signika bubble-item-title text-uppercase" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Item Category">
            ' . $q->item_category . '
        </span>
        <div style="position: absolute;right: 10px;top: 10px;">
            ';
        if ($q->status == 1) {
            $itemcodeList .= '
                <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
        ';
        } else {
            $itemcodeList .= '
                <span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>
        ';
        }
        $itemcodeList .= '
        </div>
    </div>
    <div  class="d-flex justify-content-between" style="margin-top: 9px;">
        <div class="mob-minor-tags">
';
        $item_categories_itemcodes = DB::table('item_categories_itemcodes as ici')->select('ici.*', 'i.item_code', 'i.description')->leftjoin('itemcodes as i', 'i.id', '=', 'ici.itemcode_id')->where('i.is_deleted', 0)->where('ici.item_category_id', $q->id)->where('ici.is_deleted', 0)->orderBy('ici.item_category_id', 'asc')->get();

        $count = 0;

        if ($item_categories_itemcodes->count() > 0) {
            foreach ($item_categories_itemcodes as $ic) {
                if ($count < 5) {
                    $itemcodeList .= '
            <span class="minor-tag" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Itemcode">
                ' . $ic->item_code . '
            </span>
';
                }
                $count++;
            }
            if ($item_categories_itemcodes->count() > 5) {
                $itemcodeList .= '
                <span class="minor-tag">...</span>
';
            }
        }
        $itemcodeList .= '
        </div>
        <div>
            <div class="dropdown dropdown-3dot">
                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" data-id="' . $q->id . '" data-status="' . $q->status . '" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                  <img src="public/img/cf-menu-icons/3dots.png" width="9">
                </a>
                <div class="dropdown-menu dropdown-menu-3dot">
';
        if ($q->status == 1) {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-deactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-deactivate.png"> Deactivate</a>
';
        } else {
            $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot status-reactivate status-active-a btnEnd" href="#" data="' . $q->status . '" data-id="' . $q->id . '"><img src="public/img/cf-menu-icons/3dot-activate.png"> Reactivate</a>
';
        }
        $itemcodeList .= '
                  <a class="dropdown-item dropdown-item-3dot edit-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                  <a class="dropdown-item dropdown-item-3dot delete-dot-icon" data-item-id="' . $q->id . '" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "item_category" => $q->item_category,
            "id" => $q->id,
            "status" => $q->status,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }

    public function EndItemCategories(Request $request)
    {
        $check = DB::Table('item_categories')->where('id', $request->id)->first();
        if ($check->status == 0) {
            DB::Table('item_categories')->where('id', $request->id)->update(['status' => 1]);
            DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Item category successfully activated.', 'item_category_id' => $request->id]);
            return redirect()->back()->with('success', 'Item category Activated Successfully');
        } else {
            DB::Table('item_categories')->where('id', $request->id)->update(['status' => 0]);
            DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Itemcode successfully deactivated.', 'item_category_id' => $request->id]);
            return redirect()->back()->with('success', 'Item category Deactivated Successfully');
        }
    }
    public function insertCommentsItemCategories(Request $request)
    {
        DB::table('item_categories_comments')->insert([
            'item_category_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'item_category_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function getCommentsItemCategories(Request $request)
    {
        $qry = DB::table('item_categories_comments as i')
            ->where('i.item_categories_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }


    public function uploadItemCategoriesAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadItemCategoriesAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("item_category_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("item_category_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("item_category_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("item_category_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertItemCategoriesAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_item_categories(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('item_category_attachment/' . $a));
                DB::table('item_categories_attachments')->insert([
                    'item_category_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'item_category_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }
    public function delete_item_category(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Item category deleted | ' . $id, 'item_category_id' => $id]);
        return redirect()->back()->with('alert-delete-category', 'Item category deleted|' . $id);
    }
    public function UndoDeleteItemCategory(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories')->where('id', $id)->update(['is_deleted' => 0]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Item category restored.', 'item_category_id' => $request->id]);
        return redirect()->back()->with('success', 'Item category undeleted successfully.');
    }
    public function delete_item_categories_attachment(Request $request)
    {
        $item_category_id = $request->item_category_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'item_category_id' => $item_category_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id);
    }
    public function UndoDeleteItemCategoriesAttachment(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Item category attachment restored.', 'item_category_id' => $request->id]);
        return redirect()->back()->with('success', 'Item category attachment undeleted successfully.');
    }
    public function delete_ItemCategories_comment(Request $request)
    {
        $item_category_id = $request->item_category_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'item_category_id' => $item_category_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id);
    }
    public function UndoDeleteItemCategoriesComment(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Item category comment restored.', 'item_category_id' => $request->id]);
        return redirect()->back()->with('success', 'Item category comment undeleted successfully.');
    }
    public function update_ItemCategories_comment(Request $request)
    {
        $item_category_id = $request->item_category_id;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('item_categories_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('item_categories_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'item_category_id' => $item_category_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
    }

    public function ImportUsers(Request $request)
    {
        $import = new UsersImport;
        Excel::import($import, $request->file('file')->store('temp'));

        $message = [];

        if ($import->insertedUsers > 0) {
            $message[] = $import->insertedUsers . ' new users added';
        }

        if ($import->insertedModules > 0) {
            $message[] = $import->insertedModules . ' module access entries added';
        }

        if (empty($message)) {
            return redirect()->back()->with('info', 'No new data was imported - all users and modules already exist.');
        }

        return redirect()->back()->with('success', implode(', ', $message) . '.');
    }
    public function ExportUsers(Request $request)
    {
        return Excel::download(new ExportUsers($request), 'Users.xlsx');
    }
    public function get_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid User ID'
            ], 400);
        }

        try {
            // Get the users data
            $users = DB::table('users')
                ->where('id', $request->id)
                ->where('is_deleted', 0)
                ->first();

            if (!$users) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or has been deleted'
                ], 404);
            }

            // Get associated modules
            $modules = DB::table('user_modules')
                ->select('id', 'module_name', 'access_type')
                ->where('user_id', $request->id)
                ->where('is_deleted', 0)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users,
                'modules' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function InsertUser(Request $request)
    {
        DB::beginTransaction();

        $check = DB::table('users')->where('is_deleted', 0)->where('email', $request->email)->first();
        if ($check != '') {
            return response()->json(['message' => 'Email Already Exist'], 422);
        }
        $image = '';
        if ($request->logo != '') {
            $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('client_logos'), $image);
        }

        $password = uniqid();

        try {
            // Insert item category
            $UserId = DB::table('users')->insertGetId([
                'role' => 'admin',
                'name' => $password,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($password),
                'portal_access' => 1,
                'user_image' => $image,
                'must_change' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert related modules
            $insertData = array_map(function ($module_name, $module_access_type) use ($UserId) {
                return [
                    'user_id' => $UserId,
                    'module_name' => $module_name,
                    'access_type' => $module_access_type,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ];
            }, $request->module_name, $request->module_access_type);

            DB::table('user_modules')->insert($insertData);

            // Add to audit trail
            DB::table('user_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'User added successfully',
                'client_id' => $UserId,
                'created_at' => now()
            ]);

            DB::commit();
            $data2 = array(
                'email' => $request->email,
                'password' => $password,
                'name' => $request->firstname . ' ' . $request->lastname,
                'subject' => 'Password Reset Notification'
            );
            Mail::send('emails.password', ["data" => $data2], function ($message) use ($data2) {
                $message->to($data2['email'])
                    ->subject('Create Your Password');
            });
            return response()->json([
                'message' => 'New user added and notified by e-mail'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user. Please try again.'
            ], 500);
        }
    }
    public function updateUser(Request $request)
    {

        // Check for duplicate itemcodes in the request
        if (count($request->module_name) !== count(array_unique($request->module_name))) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'modules' => ['Duplicate modules found in the request.']
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            $check = DB::table('users')->where('is_deleted', 0)->where('email', $request->email)->where('id', '!=', $request->id)->first();

            if ($check != '') {
                return response()->json(['message' => 'Email Already Exist'], 422);
            }

            $check_img = DB::table('users')->where('id', $request->id)->where('is_deleted', 0)->first();

            $image = $check_img->user_image;
            if ($request->logo != '') {
                $image = mt_rand(1, 1000) . '' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move(public_path('client_logos'), $image);
            }

            // 1. Soft delete all existing modules
            DB::table('user_modules')
                ->where('user_id', $request->id)
                ->update([
                    'is_deleted' => 1
                ]);

            DB::table('users')
                ->where('id', $request->id)
                ->where('is_deleted', 0)
                ->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'user_image' => $image,
                    'updated_at' => now(),
                    'updated_by' => Auth::id()
                ]);

            $user_id = $request->id;

            // 2. Prepare new user modules
            $insertData = array_map(function ($module_name, $module_access_type) use ($user_id) {
                return [
                    'user_id' => $user_id,
                    'module_name' => $module_name,
                    'access_type' => $module_access_type,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ];
            }, $request->module_name, $request->module_access_type);

            DB::table('user_modules')->insert($insertData);

            DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User updated successfully', 'client_id' => $request->id]);

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'new_password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',          // Uppercase
                'regex:/[a-z]/',          // Lowercase
                'regex:/[0-9]/',          // Digit
                'regex:/[@$!%*?&#^(){}\[\]<>~+=|\/.,:;\'"-]/', // Special char
            ],
            'confirm_password' => 'required|same:new_password',
        ], [
            'new_password.required' => 'Please fill all the fields',
            'confirm_password.required' => 'Please fill all the fields',
            'confirm_password.same' => 'Confirm password must match new password',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // If all fields are filled but still validation fails, return generic message
            if (!$errors->has('new_password') && !$errors->has('confirm_password')) {
                return response()->json(['errors' => ['validation' => 'Reset password failed validation']], 422);
            }

            return response()->json(['errors' => $errors], 422);
        }

        DB::table('users')
            ->where('id', $request->id)
            ->update([
                'name' => $request->new_password,
                'password' => Hash::make($request->new_password),
                'must_change' => $request->must_change ? 1 : 0,
            ]);

        $user = DB::table('users')
            ->where('id', $request->id)
            ->first();

        $name = $user->firstname . " " . $user->lastname;

        // If must_change is checked, send email
        if ($request->must_change) {
            $emailData = [
                'name' => $name,
                'email' => $user->email,
                'new_password' => $request->new_password,
                'login_url' => url('/login'),
            ];

            Mail::send('emails.password_reset_notification', $emailData, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Password Has Been Reset');
            });

            return response()->json(['message' => 'Password reset and user notified by e-mail.']);
        } else {
            return response()->json(['message' => 'Password reset successfully.']);
        }
    }
    public function get_users_content(Request $request)
    {
        $id = $request->id;
        $html = '';
        $q = DB::table('users')->where('id', $id)->where('is_deleted', 0)->first();
        $user = DB::table('users')->where('id', $q->updated_by)->first();
        $last_updated_at = date('Y-M-d', strtotime($q->updated_at));
        $last_updated_by = @$user->firstname . " " . @$user->lastname;

        $user_modules = DB::table('user_modules')->select('*')->where('is_deleted', 0)->where('user_id', $q->id)->orderBy('id', 'asc')->get();

        $attachments = DB::table('user_attachments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.user_id', $id)->where('v.is_deleted', 0)->get();

        $comments = DB::table('user_comments as v')->select('v.*', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'v.added_by')->where('v.user_id', $id)->where('v.is_deleted', 0)->get();

        $audit_trail = DB::table('user_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.client_id', $id)->get();


        // $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
        $html .= '
        <div  style="overflow-y: auto;height:82vh;">
<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">General Information</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0" style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-9">
                <div class="form-group row fg-evenly">
                    <div class="col-sm-4">
                        <div class="label-new col-form-label">Email</div>
                    </div>
                    <div class="col-sm-8 pl-0">
                        <div class="field-new text-blue fw-600 provinceText w-100" data="' . $q->id . '">' . $q->email . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-4">
                        <div class="label-new col-form-label">First Name</div>
                    </div>
                    <div class="col-sm-8 pl-0">
                        <div class="field-new field-color fw-500 provinceText w-100" data="' . $q->id . '">' . $q->firstname . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-4">
                        <div class="label-new col-form-label">Last Name</div>
                    </div>
                    <div class="col-sm-8 pl-0">
                        <div class="field-new field-color fw-500 provinceText w-100" data="' . $q->id . '">' . $q->lastname . '</div>
                    </div>
                </div>
            </div>
        ';


        if (!empty($q->user_image)) {

            $html .= '
            <div class="col-sm-3">
                <div class="">
                    <img src="public/client_logos/' . $q->user_image . '" width="100%" class="rounded">
                </div>
            </div>
        ';
        }
        $html .= '
        </div>
    </div>
</div>

<div class="block new-block position-relative mr-2 mt-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Access</a>
        <div class="block-options"></div>
    </div>
    <div class="block-content pb-0 pt-1" style="padding-left: 50px;padding-right: 30px;">
        <div class="row">
            <div class="col-sm-12 pt-1 selectedItemcodes small-box small-box-340 pr-5">
';
        if ($user_modules->count() > 0) {
            foreach ($user_modules as $um) {
                $html .= '
                <div class="selected-cat-itemcodes selected-items-list p-3 d-flex align-items-center" style="margin-bottom: 10px;">
                    <span class="selected-itemcode text-capitalize col-2">' . $um->access_type . '</span>
                    <span class="selected-itemcode-desc ml-4 col-10">' . $um->module_name . '</span>
                </div>
';
            }
        }
        $html .= '
            </div>
        </div>
    </div>
</div>
        ';


        if ($attachments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sma ll-box sma ll-box-400 mr-3">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Attachments</a>
        <div class="block-options"></div>
    </div>
<div class="block-content pb-0 row" style="padding-left: 20px;padding-right: 20px;">
    ';
            foreach ($attachments as $a) {
                $f = explode('.', $a->attachment);
                $fileExtension = end($f);
                $icon = 'attach-icon.png';
                if ($fileExtension == 'pdf') {
                    $icon = 'attch-Icon-pdf.png';
                } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'txt') {
                    $icon = 'attch-word.png';
                } else if ($fileExtension == 'zip') {
                    $icon = 'icon-zip.png';
                } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                    $icon = 'attch-excel.png';
                } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                    $icon = 'attch-powerpoint.png';
                }
                $filename = htmlspecialchars($a->attachment);
                $max_length = 20; // or any number you prefer

                $short_filename = (strlen($filename) > $max_length)
                    ? substr($filename, 0, $max_length - 3) . '...'
                    : $filename;
                $html .= '
<div class="col-sm-4">
    <table class="table table-borderless table-vcenter attachment-box mb-4">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-2  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" aclass="' . ($a->user_image == '' ? 'bg-dark' : '') . '" src="' . ($a->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $a->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $a->name . '
                    <br>
                    <span class="comments-subtext">' . date('Y-M-d', strtotime($a->date)) . ' at ' . date('h:i:s A', strtotime($a->date)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-2 d-flex align-items-center justify-content-between my-0 comments-section-text text-truncate attachmentDivNew w-100">
<a href="public/item_category_attachment/' . nl2br($a->attachment) . '" download="" target="_blank" class="">
<img src="public/img/' . $icon . '" width="25px"><span class="text-truncate text-grey ml-2">' . $short_filename . '</span>
</a>
<a class="float-right delete-cross delete-attachment" data-id="' . $a->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="11"></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
</div>
';
        }

        if ($comments->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2">
<div class="sm all-box sm all-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Comments</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($comments as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->name . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
                    </span>
                    </h2>
                </td>
                <td>
                    <a class="float-right delete-cross delete-comment mb-2" data-id="' . $c->id . '" data-item-id="' . $id . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete"><img src="public/img/cf-menu-icons/detail-line-remove.png" width="15"></a>
                    <a class="float-right edit-pen edit-comment mb-2 mr-1" data-id="' . $c->id . '" data-item-id="' . $id . '" data-comment="' . nl2br($c->comment) . '" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15"></a>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->comment) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                                    ';
            }
            $html .= '
</div>
</div>
';
        }

        if ($audit_trail->count() > 0) {

            $html .= '
<div class="block new-block position-relative mr-2 mb-5">
<div class="small-box small-box-400 mr-3 pb-2">
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="section-header">Audit Trail</a>
        <div class="block-options"></div>
    </div>
    ';
            foreach ($audit_trail as $c) {
                $html .= '
<div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
    <table class="table table-borderless table-vcenter comment-box mb-0">
        <tbody>
            <tr>
                <td class="text-center pr-0" style="width: 38px;">
                    <h1 class="mb-0 mr-3  text-white rounded px-1" style=""><b>
                        <img width="40px" height="40" style="border-radius: 50%;" class="' . ($c->user_image == '' ? 'bg-dark' : '') . '" src="' . ($c->user_image == '' ? 'public/img/profile-white.png' : 'public/client_logos/' . $c->user_image) . '"> </b>
                    </h1>
                </td>
                <td class="js-task-content  pl-0">
                    <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '
                    <br>
                    <span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
                    </span>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="pt-0">
                    <p class="px-1 mb-0 comments-section-text">  ' . nl2br($c->description) . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
         ';
            }
            $html .= '
</div>
</div>
';
        }

        $itemcodeList = '
        <div class="block-content py-2 pl-3 d-flex position-relative pr-2" style="">

<div  class="w-100 d-flex justify-content-between align-items-center">
    <div style="width: 14%;">
            ';
        if (empty($q->user_image)) {
            $itemcodeList .= '
            <img src="public/img/cf-menu-icons/user-icon.png" width="50">
        ';
        } else {
            $itemcodeList .= '
            <img src="public/client_logos/' . $q->user_image . '" width="50" height="50" class="rounded-circle">
        ';
        }
        $itemcodeList .= '
    </div>
    <div style="width: 84%;">
    <div  class="d-flex justify-content-between">
        <div class="font-signika bubble-item-title bubble-item-title-email" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Email">
            ' . $q->email . '
        </div>
        <div style="position: absolute;right: 10px;top: 10px;">
            ';
        if ($q->portal_access == 1) {
            $itemcodeList .= '
                <span class="font-signika bubble-status-active" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Active</span>
        ';
        } else {
            $itemcodeList .= '
                <span class="font-signika bubble-status-inactive" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Status">Inactive</span>
        ';
        }
        $itemcodeList .= '
        </div>
    </div>
    <div  class="d-flex justify-content-between" style="margin-top: 5px;">
<div class="d-flex flex-row" style="padding-top: 3px;">
                       <div>
                           <span class="font-signika bubble-item-desc ml-1">' . $q->firstname . " " . $q->lastname . '</span>
                       </div>
                   </div>
        <div>
            <div class="dropdown dropdown-3dot">
                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" data-id="' . $q->id . '" data-status="' . $q->portal_access . '" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                  <img src="public/img/cf-menu-icons/3dots.png" width="9">
                </a>
';
        $portalAccess = $q->portal_access;
        $portalAction = $portalAccess == 1 ? 'Deactivate' : 'Reactivate';
        $portalClass  = $portalAccess == 1 ? 'status-deactivate' : 'status-reactivate';
        $portalIcon   = $portalAccess == 1 ? '3dot-deactivate.png' : '3dot-activate.png';

        $itemcodeList .= '
    <div class="dropdown-menu dropdown-menu-3dot">
        <a class="dropdown-item dropdown-item-3dot ' . $portalClass . ' status-active-a btnEnd" href="#" data="' . $portalAccess . '" data-id="' . $q->id . '">
            <img src="public/img/cf-menu-icons/' . $portalIcon . '"> ' . $portalAction . '
        </a>
        <a class="dropdown-item dropdown-item-3dot edit-dot-icon" data-item-id="' . $q->id . '" href="#">
            <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit
        </a>
        <a class="dropdown-item dropdown-item-3dot delete-dot-icon" data-item-id="' . $q->id . '" href="#">
            <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete
        </a>
    </div>
';
        $itemcodeList .= '

            </div>
        </div>
    </div>
</div>
       </div>
       ';
        return response()->json([
            "editContent" => $html,
            "viewContent" => $itemcodeList,
            "firstname" => $q->firstname,
            "lastname" => $q->lastname,
            "email" => $q->email,
            "id" => $q->id,
            "status" => $q->portal_access,
            "last_updated_at" => $last_updated_at,
            "last_updated_by" => $last_updated_by,
        ]);
    }
    public function EndUser(Request $request)
    {
        $check = DB::Table('users')->where('id', $request->id)->first();
        if ($check->portal_access == 0) {
            DB::Table('users')->where('id', $request->id)->update(['portal_access' => 1]);
            DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User successfully activated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'User Activated Successfully');
        } else {
            DB::Table('users')->where('id', $request->id)->update(['portal_access' => 0]);
            DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User successfully deactivated.', 'client_id' => $request->id]);
            return redirect()->back()->with('success', 'User Deactivated Successfully');
        }
    }


    public function uploadUserAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = $fileExt[0] . uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        return response()->json($key);
    }


    public function LoadUserAttachment(Request $request)
    {
        $request->header('Access-Control-Allow-Origin: *');
        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');
        // Load our configuration for this server

        $uniqueFileID = $_GET["key"];
        $imagePointer = public_path("item_category_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("item_category_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }

        $imageName = $uniqueFileID;

        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("item_category_attachment/" . $uniqueFileID);
        $fileObject = null;
        if ($imageName != '' && file_exists($imagePointer)) {
            $fileObject = file_get_contents($imagePointer);
        }
        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
        if ($fileBlob) {
            $imagePointer = public_path("item_category_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);
            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));
            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");
            return $response;
        } else {
            http_response_code(500);
        }
    }

    public function revertUserAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);
        unlink(public_path('temp_uploads/' . $key));
        echo json_encode(1);
    }
    public function insert_attachment_user(Request $request)
    {
        $attachment_array = explode(',', $request->attachment_array);

        if (isset($request->attachment_array)) {
            foreach ($attachment_array as $a) {

                copy(public_path('temp_uploads/' . $a), public_path('item_category_attachment/' . $a));
                DB::table('user_attachments')->insert([
                    'user_id' => $request->id,
                    'date' => date('Y-m-d H:i:s'),
                    'attachment' => $a,
                    'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment Added', 'client_id' => $request->id]);

        return redirect()->back()->with('success', 'Attachment Added Successfully');
    }
    public function delete_user(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('users')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User deleted | ' . $id, 'client_id' => $id]);
        return redirect()->back()->with('alert-delete-category', 'User deleted|' . $id);
    }
    public function UndoDeleteUser(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('users')->where('id', $id)->update(['is_deleted' => 0]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User restored.', 'client_id' => $request->id]);
        return redirect()->back()->with('success', 'User undeleted successfully.');
    }
    public function delete_user_attachment(Request $request)
    {
        $user_id = $request->user_id;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('user_attachments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Attachment deleted | ' . $id, 'client_id' => $user_id]);
        return redirect()->back()->with('alert-delete-attachment', 'Attachment deleted|' . $id);
    }
    public function UndoDeleteUserAttachment(Request $request)
    {
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('user_attachments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User attachment restored.', 'client_id' => $request->id]);
        return redirect()->back()->with('success', 'User attachment undeleted successfully.');
    }
    public function insertCommentsUser(Request $request)
    {
        DB::table('user_comments')->insert([
            'user_id' => $request->id,
            'date' => date('Y-m-d H:i:s'),
            'comment' => $request->comment,
            'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
            'added_by' => Auth::id(),
        ]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment Added Successfully', 'client_id' => $request->id]);
        return redirect()->back()->with('success', 'Comment Added Successfully');
    }
    public function getCommentsUser(Request $request)
    {
        $qry = DB::table('user_comments as i')
            ->where('i.item_categories_comments', $request->id)
            ->leftJoin("users as u", function ($join) {
                $join->on("i.added_by", "=", "u.id");
            })
            ->select("i.*", "u.user_image")
            ->get();
        return response()->json($qry);
    }
    public function delete_User_comment(Request $request)
    {
        $user_id = $request->userId;
        $id = $request->id;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('user_comments')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment deleted | ' . $id, 'client_id' => $user_id]);
        return redirect()->back()->with('alert-delete', 'Comment deleted|' . $id . '|' . $user_id);
    }
    public function UndoDeleteUserComment(Request $request)
    {
        $id = $request->id;
        $userId = $request->userId;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('user_comments')->where('id', $id)->update(['is_deleted' => 0,]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User comment restored.', 'client_id' => $userId]);
        return redirect()->back()->with('success', 'User comment undeleted successfully.');
    }
    public function update_User_comment(Request $request)
    {
        $user_id = $request->userId;
        $id = $request->id;
        $comment = $request->comment;
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }
        DB::table('user_comments')->where('id', $id)->update(['comment' => $comment, 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Comment updated | ' . $id, 'client_id' => $user_id]);
        return redirect()->back()->with('success', 'Comment updated | ' . $id);
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
        $acct_no = DB::Table('clients_gifi')->where('id', $request->post('client_account_id'))->first();
        DB::Table('clients_gifi')->where('account_no', $acct_no->account_no)->update($data);
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
        if ($request->all == 1) {
            $clients = DB::table('clients')->where('is_deleted', 0)->get();
            foreach ($clients as $c) {
                $check_previous = DB::table('clients_gifi')->where('account_no', $request->account_no)->where('client_id', $c->id)->exists();
                if (!$check_previous) {
                    DB::table('clients_gifi')->insert([
                        'account_type' => $request->account_type,
                        'sub_type' => $request->sub_account_type,
                        'account_no' => $request->account_no,
                        'description' => $request->description,
                        'note' => $request->note,
                        'client_id' => $c->id
                    ]);
                }
            }
            DB::Table('gifi')->insert($data);
        } else {
            DB::Table('gifi')->insert($data);
        }
        $id = DB::getPdo()->lastInsertId();
        DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Gifi Account Added', 'gifi_id' => $id]);
        return redirect()->back()->with('success', 'Gifi account created successfully');
    }

    public function UpdateGifi(Request $request)
    {
        $id = $request->id;
        $all = $request->update_all;
        $data = [
            'account_type' => $request->account_type_edit,
            'sub_type' => $request->sub_account_type_edit,
            'account_no' => $request->account_no_edit,
            'description' => $request->description_edit,
            'note' => $request->note_edit,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,
        ];
        if ($all == 1) {
            DB::Table('clients_gifi')->where('account_no', $request->account_no_edit)->update([
                'account_type' => $request->account_type_edit,
                'sub_type' => $request->sub_account_type_edit,
                'account_no' => $request->account_no_edit,
                'description' => $request->description_edit,
                'note' => $request->note_edit,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
            ]);
            DB::Table('gifi')->where('id', $id)->update($data);
        } else {
            DB::Table('gifi')->where('id', $id)->update($data);
        }
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
        if ($request->hasFile("logo")) {
            $imageName = mt_rand(1, 1000) . '' . time() . '.' . $request->file("logo")->getClientOriginalExtension();
            $request->file("logo")->move(public_path() . '/company_logos', $imageName);
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
            'corporation_no' => $request->corporation_no,
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
        if ($validated) {
            try {
                $import = new JournalImport(DB::table('clients')->where('is_deleted', 0)->where('id', $request->input('client'))->first());
                Excel::import($import, $request->file('file')->store('temp'));
                if (count($import->data) > 0) {
                    return redirect()->back()->with('success', count($import->data) . ' journals are added successfully');
                }
            } catch (\Exception $e) {
            }
        }
        return redirect()->back()->with('error', "No journals added");
    }
    public function ImportExcelStandardJournals(Request $request)
    {
        $validated = $request->validate([
            "client" => 'required',
        ]);
        if ($validated) {
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
        // return Excel::download(new ExportJournals($request), 'journals.csv');
        $fileName = 'journals.csv';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportJournals($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
    }
    public function ExportExcelJournalsBySource(Request $request)
    {
        $fileName = 'journals by Source.csv';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportJournalsBySource($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
    }
    public function ExportExcelJournalsByPeriod(Request $request)
    {
        $fileName = 'journals by Period.csv';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportJournalsByPeriod($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
    }
    public function ExportExcelJournalsTrailBalance(Request $request)
    {
        $fileName = 'journals Trial Balance.csv';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportJournalsTrialBalance($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
    }
    public function ExportExcelJournalsByAccount(Request $request)
    {
        $fileName = 'journals by Account.csv';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportJournalsByAccount($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
    }
    public function ExportExcelJournalsBySourceAll(Request $request)
    {
        $fileName = 'journals by Source.csv';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportJournalsBySource($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
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
        if ($request->corporation_checkbox) {
            $use_corporation_no = 1;
        } else {
            $use_corporation_no = 0;
        }
        $data = array(
            'salutation' => $request->salutation,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'company' => $request->company,
            'display_name' => $request->display_name,
            'type' => $request->type,
            'business' => $request->business,
            'federal_no' => $request->federal_no,
            'provincial_no' => $request->provincial_no,
            'neq_no' => $request->neq_no,
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
            'corporation_no' => $request->corporation_no,
            'use_corporation_no' => $use_corporation_no
        );
        if ($data['tax_remittance'] == 'Quarterly') {
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
        if ($request->corporation_checkbox_edit) {
            $use_corporation_no = 1;
        } else {
            $use_corporation_no = 0;
        }
        $data = array(
            'salutation' => $request->salutation_edit,
            'firstname' => $request->firstname_edit,
            'lastname' => $request->lastname_edit,
            'firstname' => $request->firstname_edit,
            'company' => $request->company_edit,
            'display_name' => $request->display_name_edit,
            'type' => $request->type_edit,
            'business' => $request->business_edit,
            'federal_no' => $request->federal_no_edit,
            'neq_no' => $request->neq_no_edit,
            'provincial_no' => $request->provincial_no_edit,
            'website' => $request->website_edit,
            'corporation_no' => $request->corporation_no_edit,
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
            'use_corporation_no' => $use_corporation_no
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
    public function UsersOld()
    {
        return view('usersOld');
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
        Session::flash('delete', 'User deleted successfully. | ' . $request->id);
        return redirect()->back(); //->with('alert-delete', 'User deleted successfully.|' . $request->id);
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
    public function UpdateUserDefaultSession(Request $request)
    {
        DB::Table('users')->where('id', $request->id)->update(
            ['default_client' => $request->client_id, 'default_fiscal_year' => $request->fiscal_year]
        );
        return redirect()->back()->with('success', 'Defaults Changed Successfully');
    }
    public function UpdateUserPassword(Request $request)
    {
        DB::Table('users')->where('id', $request->id)->update(
            ['password' => Hash::make($request->password), 'password_verified' => date('Y-m-d H:i:s')]
        );
        return redirect()->back()->with('success', 'Password Changed Successfully');
    }
    // public function ExportExcelUsers(Request $request)
    // {
    //     return Excel::download(new ExportUsers($request), 'Users.xlsx');
    // }
    public function InsertUsers(Request $request)
    {
        $access_to_client =  $request->access_to_client != '' ? implode(',', $request->access_to_client) : '';
        $check = DB::table('users')->where('is_deleted', 0)->where('email', $request->email)->first();
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
        $data2 = array(
            'email' => $request->email,
            'password' => $password,
            'name' => $request->firstname . ' ' . $request->lastname,
            'subject' => 'Access your Contracts and Assets'
        );
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
        Mail::send('emails.password', ["data" => $data2], function ($message) use ($data2) {
            $message->to($data2['email'])
                ->subject('Create Your Password');
        });
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
        return redirect()->back()->with('alert-delete', 'Journal Deleted Successfully|' . $id);
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
                                         ';
            if ($q->applied_to_tax1 == 1) {
                $html .= '<div class="col-sm-4 pl-0">
                                               <div class="bubble-white-new2  " data="' . $q->id . '">Applied to ' . $q->tax_label_1 . '</div>
                                                </div>';
            }
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
 <option value="Revenue" ' . ($q->account_type == 'Revenue' ? 'selected' : '') . '>Revenue</option>
 <option value="Expense" ' . ($q->account_type == 'Expense' ? 'selected' : '') . '>Expense</option>
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
    <input type="" name="account_no_edit" class="form-control" placeholder="4-digit numeric code" value="' . $q->account_no . '" readonly>
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
        $image_html = '';
        if ($q->account_type == 'Liability') {
            $image_html = '<img src="' . asset('public') . '/icons/icon-account-liability.png"
           class="rounded-circle  "
           style="object-fit: cover;width: 70px;height: 70px;">';
        } elseif ($q->account_type == 'Asset') {
            $image_html = '<img src="' . asset('public') . '/icons/icon-accounts-asset.png"
           class="rounded-circle  "
           style="object-fit: cover;width: 70px;height: 70px;">';
        } elseif ($q->account_type == 'Retained Earning') {
            $image_html = '<img src="' . asset('public') . '/icons/icon-account-retained-earning.png"
              class="rounded-circle  "
              style="object-fit: cover;width: 70px;height: 70px;">';
        } elseif ($q->account_type == 'Income' && ($q->sub_type == 'Operating expense' || $q->sub_type == 'Cost of sale')) {
            $image_html = '<img src="' . asset('public') . '/icons/icon-account-expense.png"
           class="rounded-circle  "
           style="object-fit: cover;width: 70px;height: 70px;">';
        } elseif ($q->account_type == 'Income' && $q->sub_type == 'Revenue') {
            $image_html = '<img src="' . asset('public') . '/icons/icon-account-revenue.png"
           class="rounded-circle  "
           style="object-fit: cover;width: 70px;height: 70px;">';
        } else {
            $image_html = '<img src="' . asset('public') . '/icons/icon-gifi-grey.png"
           class="rounded-circle  "
           style="object-fit: cover;width: 70px;height: 70px;">';
        }
        $html2 = '
       <div class="block-content pt-1 pb-1  pl-1 d-flex position-relative" style="">
           <div class=" mr-1 d-flex justify-content-center align-items-center" style="width: 20%;float: left;padding: 7px;">
               ' . $image_html . '
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
                   padding-right: 5px;">' . $q->account_no . ' - ' . $q->sub_type . '</span>
                   <div class="d-flex flex-row" style="padding-top: 3px;">
                       <div>
                           <span style="line-height: 1.6;
                           font-family: Calibri;
                           width: fit-content;
                           font-size: 11pt;
                           color:#3F3F3F;
                           border:1px solid #3F3F3F;
                           border-radius: 2px;
                           margin-right: 0.675rem;" class="px-2">' . $q->account_type[0] . '</span>
                       </div>
                       <div style="overflow: hidden;
                       text-overflow: ellipsis;
                       width: fit-content;
                       line-height: 1.6;
                       white-space: nowrap;
                       font-size: 11pt;
                       font-family: Calibri;">
                           <span>' . $q->description . '</span>
                       </div>
                   </div>
               </div>
               <div style="position: absolute;right: 10px;top: 10px;">';
        if ($q->gifi_status == 1) {
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
        } else {
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
                   <div class="ActionIcon px-0 ml-2  client-info" data-notes="' . $q->note . '"
                       data="{{$q->id}}" style="border-radius: 1rem;position: relative;">
                       <a href="javascript:;" class="" >
                           <img src="' . asset('public') . '/icons2/icon-comments-grey-2.png?cache=1"
                               width="25px">
                       </a>
                   </div>';
        if (Auth::check()) {
            if (@Auth::user()->role != 'read') {
                $html2 .= '<div class="ActionIcon ml-2   px-0 " style="border-radius: 1rem">
                    <a href="javascript:;" data="' . $q->id . '" class="btnEdit ">
                        <img src="public/icons2/icon-edit-grey.png?cache=1"
                        width="25px">
                    </a>
                </div>
                <div class="ActionIcon ml-2 px-0 " style="border-radius: 1rem">
                    <a href="javascript:;" class="btnDelete" data="' . $q->id . '">
                        <img src="public/icons2/icon-delete-grey.png?cache=1"
                            width="25px">
                    </a>
                </div>';
            }
        }
        $html .= ' </div>
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
            ->where('is_deleted', 0)
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
    public function getClientsAccounts(Request $request)
    {
        $id = $request->id;
        return response()->json(DB::table('clients_gifi')
            ->where('client_id', $id)
            ->where('is_deleted', 0)
            ->orderBy('account_no', 'asc')
            ->get());
    }
    public function getClientsAccount2(Request $request)
    {
        $id = $request->id;
        $searchVal = @$request->searchVal;
        $account_type = @$request->account_type;
        $sub_account_type = @$request->sub_account_type ?? [];
        $account_no = @$request->account ?? [];
        $description = @$request->description;
        // $qry = DB::table('clients_gifi')
        //     ->where('clients_gifi.client_id', $id)
        //     ->orWhere('clients_gifi.client_id', 'all')
        //     ->where(function ($query) use ($searchVal, $account_type, $sub_account_type, $account_no, $description) {
        //         $query->where('clients_gifi.is_deleted', 0);
        //         if (!empty($account_type)) {
        //             $query->where('clients_gifi.account_type', $account_type);
        //         }
        //         if (count($sub_account_type) > 0) {
        //             $query->whereIn('clients_gifi.sub_type', $sub_account_type);
        //         }
        //         if (count($account_no) > 0) {
        //             $query->whereIn('clients_gifi.account_no', $account_no);
        //         }
        //         if (!empty($description)) {
        //             $query->where('clients_gifi.description', $description);
        //         }
        //         // if (!empty($searchVal)) {
        //         //     $query->where('clients_gifi.account_no', 'like', '%' . $searchVal . '%')
        //         //         ->orWhere('clients_gifi.sub_type', 'like', '%' . $searchVal . '%')
        //         //         ->orWhere('clients_gifi.account_type', 'like', '%' . $searchVal . '%')
        //         //         ->orWhere('clients_gifi.description', 'like', '%' . $searchVal . '%')
        //         //         ->orWhere('clients_gifi.note', 'like', '%' . $searchVal . '%');
        //         // }
        //     })
        //     ->where(function($qry) use ($searchVal) {
        //         dd($searchVal);
        //         if (!empty($searchVal)) {
        //             $qry->where('clients_gifi.account_no', 'like', '%' . $searchVal . '%')
        //                 ->orWhere('clients_gifi.sub_type', 'like', '%' . $searchVal . '%')
        //                 ->orWhere('clients_gifi.account_type', 'like', '%' . $searchVal . '%')
        //                 ->orWhere('clients_gifi.description', 'like', '%' . $searchVal . '%')
        //                 ->orWhere('clients_gifi.note', 'like', '%' . $searchVal . '%');
        //         }
        //     })
        //     ->Join("clients", function ($join) {
        //         $join->on("clients_gifi.client_id", "=", "clients.id")
        //             ->where("clients.is_deleted", 0);
        //     })
        //     ->select("clients_gifi.*", "clients.logo", "clients.company")
        //     ->orderBy('clients_gifi.account_no', 'asc')
        //     ->get();
        $qry = DB::table('clients_gifi')
            ->where(function ($query) use ($id) {
                $query->where('clients_gifi.client_id', $id)
                    ->orWhere('clients_gifi.client_id', 'all');
            })
            ->where(function ($query) use ($searchVal, $account_type, $sub_account_type, $account_no, $description) {
                $query->where('clients_gifi.is_deleted', 0);
                if (!empty($account_type)) {
                    $query->where('clients_gifi.account_type', $account_type);
                }
                if (!empty($sub_account_type) && count($sub_account_type) > 0) {
                    $query->whereIn('clients_gifi.sub_type', $sub_account_type);
                }
                if (!empty($account_no) && count($account_no) > 0) {
                    $query->whereIn('clients_gifi.account_no', $account_no);
                }
                if (!empty($description)) {
                    $query->where('clients_gifi.description', $description);
                }
            })
            ->where(function ($query) use ($searchVal) {
                if (!empty($searchVal)) {
                    $query->where(function ($searchQuery) use ($searchVal) {
                        $searchQuery->where('clients_gifi.account_no', 'like', '%' . $searchVal . '%')
                            ->orWhere('clients_gifi.sub_type', 'like', '%' . $searchVal . '%')
                            ->orWhere('clients_gifi.account_type', 'like', '%' . $searchVal . '%')
                            ->orWhere('clients_gifi.description', 'like', '%' . $searchVal . '%')
                            ->orWhere('clients_gifi.note', 'like', '%' . $searchVal . '%');
                    });
                }
            })
            ->join("clients", function ($join) {
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
        // return Excel::download(new ExportClientGifi($request), 'Client Gifi.xlsx');
        // return Excel::download(new ExportJournals($request), 'journals.csv');
        $fileName = 'Client Gifi.xlsx';
        $filePath = storage_path('app/public/' . $fileName);
        Excel::store(new ExportClientGifi($request), 'public/' . $fileName);
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully.',
            'download_url' => asset('storage/app/public/' . $fileName)
        ]);
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
                                            <label class="col-sm-3 col-form-label mandatory" for="example-hf-client_id">Contact</label>
                                            <?php
                                                                                  ?>
<div class="col-sm-2" style="padding-left: 19px !important;">
    <select type="text" class="form-control" id="salutation_edit" name="salutation_edit" placeholder="Salutation">
        <option value="Mr" ' . ($q->salutation == ' Mr' ? 'selected' : '') . '>Mr.</option>
                                                    <option value="Mrs" ' . ($q->salutation == 'Mrs' ? 'selected' : '')
            . '>Mrs.</option>
        <option value="Ms" ' . ($q->salutation == ' Ms' ? 'selected' : '') . '>Ms.</option>
                                                    <option value="Miss" ' . ($q->salutation == 'Miss' ? 'selected' :
                '') . '>Miss.</option>
        <option value="Dr" ' . ($q->salutation == ' Dr' ? 'selected' : '')
            . '>Dr.</option>
                                                 </select>
                                            </div>
                                            <div class="col-sm-4">
                                                 <input type="text" class="form-control" id="firstname_edit" name="firstname_edit" placeholder="First Name"  value="' . $q->firstname .
            '">
</div>
<div class="col-sm-3  ">
    <input type="text" class="form-control" id="lastname_edit" name="lastname_edit" placeholder="Last Name"
        value="' . $q->lastname . '">
</div>
</div>
</div>
<div class="col-sm-11">
    <div class="form-group row fg-evenly">
        <label class="col-sm-3 col-form-label mandatory" for="example-hf-client_id">Client Name </label>
        <div class="col-sm-9 " style="padding-left: 19px !important;">
            <input type="text" class="form-control" id="company_edit" name="company_edit" placeholder="Company name"
                value="' . $q->company . '">
        </div>
    </div>
</div>
<div class="col-sm-11">
    <div class="form-group row fg-evenly">
        <label class="col-sm-3 col-form-label mandatory" for="example-hf-client_id">Display Name </label>
        <div class="col-sm-9 " style="padding-left: 19px !important;">
            <input type="text" class="form-control" id="display_name_edit" name="display_name_edit"
                placeholder="Display name" value="' . $q->display_name . '" maxlength="21">
        </div>
    </div>
</div>
<div class="col-lg-11" style="position:relative;">
    <div class="form-group row fg-evenly">
        <label class="col-sm-3 col-form-label mandatory " for="example-hf-client_id">Type</label>
        <div class="col-sm-5  " style="padding-left: 19px;">
            <select type="text" class="form-control" id="type_edit" name="type_edit" placeholder="Salutation">
                <option value="">Select enterprise type</option>
                <option value="Corporation" ' . ($q->type == 'Corporation' ? 'selected' : '') . '>Corporation</option>
                <option value="Cooperative" ' . ($q->type == 'Cooperative' ? 'selected' : '') . '>Cooperative</option>
                <option value="Franchise" ' . ($q->type == 'Franchise' ? 'selected' : '') . '>Franchise</option>
                <option value="Joint Proprietorship" ' . ($q->type == 'Joint Proprietorship' ? 'selected' : '') .
            '>Joint Proprietorship</option>
                <option value="Joint venture" ' . ($q->type == 'Joint venture' ? 'selected' : '') . '>Joint venture</option>
                <option value="Limited liability partnership" ' . ($q->type == 'Limited liability partnership' ?
                'selected' : '') . '>Limited liability partnership</option>
                <option value="Limited partnership" ' . ($q->type == 'Limited partnership' ? 'selected' : '') . '>Limited partnership</option>
                <option value="Non-profit organization" ' . ($q->type == 'Non-profit organization' ? 'selected' : '') .
            '>Non-profit organization</option>
                <option value="Partnership" ' . ($q->type == 'Partnership' ? 'selected' : '') . '>Partnership</option>
                <option value="Professional corporation" ' . ($q->type == 'Professional corporation' ? 'selected' : '')
            . '>Professional corporation</option>
                <option value="Sole proprietorship" ' . ($q->type == 'Sole proprietorship' ? 'selected' : '') . '>Sole proprietorship</option>
            </select>
        </div>
    </div>
<div class="form-group row fg-evenly CorporationDivEdit ' . ($q->type === 'Corporation' ? '' : 'd-none') . '">
                    <label class="col-sm-3  col-form-label mandatory" for="example-hf-client_id">Corporation #</label>
                    <div class="col-sm-5  " style="padding-left: 19px;">
                        <input type="text" class="form-control" id="corporation_no_edit" name="corporation_no_edit"
                            placeholder="Corporation #" value="' . $q->corporation_no . '">
                    </div>
                    <div class="col-sm-1 text-center custom-control custom-  custom-control-  custom-control-lg mt-2 "
                        style="" data-toggle="tooltip" data-title="Use for reports" data-original-title="" title="">
                        <input type="checkbox" class="custom-control-input" id="corporation_checkbox_edit"
                            name="corporation_checkbox_edit" value="1" ' . ($q->use_corporation_no == 1 ? ' checked'
                : '') . '>
                                                                        <label class="custom-control-label" for="corporation_checkbox_edit" style="right: -8px;"></label>
                                                                    </div>
                                                                </div>
    <div class="form-group row fg-evenly">
        <label class="col-sm-3 col-form-label mandatory " for="example-hf-client_id">Business</label>
        <div class="col-sm-5" style="padding-left: 19px;">
            <select type="text" class="form-control" id="business_edit" name="business_edit" placeholder="Salutation">
                <option value="">Select business type</option>
                <option value="Agriculture and Farming" ' . ($q->business == 'Agriculture and Farming' ? 'selected'
                : '') . '>Agriculture and Farming
                </option>
                <option value="Automotive" ' . ($q->business == 'Automotive' ? 'selected' : '') . '>Automotive</option>
                <option value="Construction" ' . ($q->business == 'Construction' ? 'selected' : '') . '>Construction
                </option>
                <option value="Consulting" ' . ($q->business == 'Consulting' ? 'selected' : '') . '>Consulting</option>
                <option value="Education and Training" ' . ($q->business == 'Education and Training' ? 'selected' : ''
            ) . '>Education and Training</option>
                <option value="Energy and Utilities" ' . ($q->business ==
                'Energy and Utilities' ? 'selected' : '') . '>Energy and Utilities</option>
                <option value="Entertainment" ' . ($q->business == 'Entertainment' ? 'selected' : '') . '>Entertainment
                </option>
                <option value="Finance and Banking" ' . ($q->business ==
                'Finance and Banking' ? 'selected' : '') . '>Finance and Banking</option>
                <option value="Food and Beverage" ' . ($q->business == 'Food and Beverage' ? 'selected' : '') . '>Food
                    and Beverage</option>
                <option value="Government and Non-Profit" ' . ($q->business
                == ' Government and Non-ProfitHealthcare' ? 'selected' : '') . '>Government and Non-Profit</option>
                <option value="Healthcare" ' . ($q->business == 'Healthcare' ? 'selected' : '') . '>Healthcare</option>
                <option value="Hospitality and Tourism" ' . ($q->business ==
                'Hospitality and Tourism' ? 'selected' : '') . '>Hospitality and Tourism</option>
                <option value="Information Technology" ' . ($q->business == 'Information Technology' ? 'selected' : ''
            ) . '>Information Technology</option>
                <option value="Marketing and Advertising" ' . ($q->business == 'Marketing and Advertising' ? 'selected' : '') . '>Marketing and Advertising</option>
                <option value="Media and Communications" ' . ($q->business == 'Media and Communications' ? 'selected'
                : '') . '>Media and Communications</option>
                <option value="Professional Services" ' . ($q->business ==
                'Professional Services' ? 'selected' : '') . '>Professional Services</option>
                <option value="Real Estate and Property Management" ' . ($q->business == 'Real Estate and Property
                    Management' ? 'selected' : '') . '>Real Estate and Property Management</option>
                <option value="Retail and Consumer Goods" ' . ($q->business
                == 'Retail and Consumer Goods' ? 'selected' : '') . '>Retail and Consumer Goods</option>
                <option value="Transportation and Logistics" ' . ($q->business == 'Transportation and Logistics'
                ? 'selected' : '')
            . '>Transportation and Logistics</option>
            </select>
        </div>
    </div>
    <div class="form-group row fg-evenly">
        <label class="col-sm-3  col-form-label mandatory" for="example-hf-client_id">Federal Tax #</label>
        <div class="col-sm-5 " style="padding-left: 19px;">
            <input type="text" class="form-control" id="federal_no_edit" name="federal_no_edit"
                placeholder="Federal enterprise number" value="'
            . $q->federal_no . '">
        </div>
    </div>
    <div class="form-group row fg-evenly">
        <label class="col-sm-3  col-form-label mandatory" for="example-hf-client_id">Provincial Tax #</label>
        <div class="col-sm-5  " style="padding-left: 19px;">
            <input type="text" class="form-control" id="provincial_no_edit" name="provincial_no_edit"
                placeholder="Provincial enterprise number" value="' . $q->provincial_no . '">
        </div>
    </div>
    <div class="form-group row fg-evenly">
        <label class="col-sm-3  col-form-label  " for="example-hf-client_id">NEQ #</label>
        <div class="col-sm-5  " style="padding-left: 19px;">
            <input type="text" class="form-control" id="neq_no_edit" name="neq_no_edit" placeholder="NEQ #"
                value="' . $q->neq_no . '">
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
                    <label class="col-sm-3 mandatory col-form-label" for="email">Email</label>
                    <div class="col-sm-9" style="padding-left: 19px !important;">
                        <input type="" class="form-control" id="email_edit" name="email_edit"
                            placeholder="Company e-mail address" value="' . $q->email . '">
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-3 mandatory  col-form-label" for="example-hf-email">Telephone </label>
                    <div class="col-sm-3" style="padding-left: 19px !important;">
                        <input type="" class="form-control" id="telephone_edit" name="telephone_edit"
                            placeholder="555-555-5555" value="' . $q->telephone . '">
                    </div>
                    <label class="col-sm-3    col-form-label" for="example-hf-email">Fax </label>
                    <div class="col-sm-3 ">
                        <input type="" class="form-control" id="fax_edit" name="fax_edit" value="' . $q->fax . '"
                            placeholder="555-555-5555">
                    </div>
                </div>
                <div class="form-group f p  row fg-evenly">
                    <label class="col-sm-3   col-form-label" for="example-hf-email">Website </label>
                    <div class="col-sm-9" style="padding-left: 19px !important;">
                        <input type="url" class="form-control" id="website_edit" name="website_edit"
                            placeholder="https://www.web.url" value="' . $q->website . '">
                    </div>
                </div>
                <div class="row form-group fg-evenly">
                    <label class="col-sm-3  mandatory col-form-label" for="example-hf-email">Country </label>
                    <div class="col-sm-3" style="padding-left:19px !important;">
                        <select type="text" class="form-control select2" id="country_edit" name="country_edit"
                            placeholder="">
                            <option value="">Country</option>';
        $use = DB::Table('countries')->get();
        foreach ($use as $u) {
            $html .= '<option value="' . $u->name . '" ' . ($u->name == $q->country ? ' selected' : '')
                . '>' . $u->name . '</option>';
        }
        $html .= '
                        </select>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-3 mandatory col-form-label" for="example-hf-email">Address</label>
                    <div class="col-sm-9 " style="padding-left: 19px !important;">
                        <textarea style="min-height:100px;" class="form-control" rows="5" id="address_edit"
                            name="address_edit" placeholder="Address">' . $q->address . '</textarea>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-3 mandatory col-form-label" for="example-hf-email">City</label>
                    <div class="col-sm-9 " style="padding-left: 19px !important;">
                        <input class="form-control" id="city_edit" name="city_edit" placeholder="City"
                            value="' . $q->city . '">
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-3 mandatory col-form-label" for="example-hf-email">Province</label>
                    <div class="col-sm-3 " style="padding-left: 19px !important;">
                        <select class="form-control select2" id="province_edit" name="province_edit"
                            value="' . $q->province . '">
                            <option value=""> Province</option>';
        $city_qry = DB::Table('cities')->where('country_name', ($q->country == 'United States' ?
            $q->country : 'Canada'))->groupBy('state_name')->get();
        foreach ($city_qry as $c) {
            $html .= '<option value="' . $c->state_name . '" ' . ($c->state_name == $q->province ? '
                                selected' : '') . '>' . $c->state_name . '</option>';
        }
        $html .= '
                        </select>
                        </select>
                    </div>
                    <label class="col-sm-3 mandatory col-form-label" for="example-hf-email">Postal Code</label>
                    <div class="col-sm-3">
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
                    <label class="col-sm-3 mandatory col-form-label" for=" ">Fiscal Start</label>
                    <div class="col-sm-3" style="padding-left: 19px !important;">
                        <input class="form-control fiscal_start_edit js-flatpickr bg-white" id="fiscal_start_edit"
                            name="fiscal_start_edit" placeholder="Fiscal start date" data-alt-input="true"
                            data-date-format="Y-m-d" data-alt-format="Y-M-d" value="' . $q->fiscal_start . '">
                    </div>
                    <label class="col-sm-3   col-form-label" for=" ">Fiscal Year End</label>
                    <div class="col-sm-3 ">
                        <div class="bubble-white-new2  fiscalEnd w-100">' . $q->fiscal_year_end . '</div><input
                            type="hidden" name="fiscal_year_end_edit" id="fiscal_year_end_edit"
                            value="' . $q->fiscal_year_end . '">
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-3 mandatory col-form-label " for="example-hf-email">Default Prov</label>
                    <div class="col-sm-3" style="padding-left: 19px !important;">
                        <select class="form-control select2" id="default_province_edit" name="default_province_edit"
                            value="' . $q->province . '">
                            <option value=""> Province</option>';
        $city_qry = DB::Table('cities')->where('country_name', ($q->country == 'United States' ?
            $q->country : 'Canada'))->groupBy('state_name')->get();
        foreach ($city_qry as $c) {
            $html .= '<option value="' . $c->state_name . '" ' . ($c->state_name == $q->default_prov ? '
                                selected' : '') . '>' . $c->state_name . '</option>';
        }
        $html .= '
                        </select>
                        </select>
                    </div>
                    <label class="col-sm-3   col-form-label" for=" ">Frequency</label>
                    <div class="col-sm-3 ">
                        <input class="js-rangeslider" id="tax_remittance_edit" name="tax_remittance_edit"
                            value="' . $q->tax_remittance . '" data-values="No,Yearly,Quarterly,Monthly"
                            data-from="' . $remittance . '">
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <label class="col-sm-3 mandatory  col-form-label" for="example-hf-email">Federal Tax Acct</label>
                    <div class="col-sm-3 " style="padding-left: 19px !important;">
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
                    <label class="col-sm-3 mandatory col-form-label" for="example-hf-email">Provincial Tax Acct</label>
                    <div class="col-sm-3">
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
                    <label class="col-sm-3 mandatory  col-form-label" for="example-hf-email">Dividends Account</label>
                    <div class="col-sm-3 " style="padding-left: 19px !important;">
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
        return response()->json(@DB::table('source_code')->where('is_deleted', 0)->where(
            'source_code',
            $request->get('source_code')
        )->where('source_code_status', 1)->first() ? 1 : 0);
    }
    public function getCloseYearContent(Request $request)
    {
        $client = $request->client;
        $year = $request->year;
        $count = DB::table('journals')->where('is_deleted', 0)->where('client', $client)->where('fyear', $year)->count();
        $total_retained_credit = 0;
        $total_retained_debit = 0;
        $total_retained_earnings = 0;
        $retained_credit = DB::table('journals')
            ->where('credit', '>', 0)
            ->where('is_deleted', 0)
            ->where('client', $client)
            ->where('fyear', $year)
            ->get();
        $retained_debit = DB::table('journals')
            ->where('debit', '>', 0)
            ->where('is_deleted', 0)
            ->where('client', $client)
            ->where('fyear', $year)
            ->get();

        foreach ($retained_credit as $re) {
            $total_retained_credit += $re->credit;
        }
        foreach ($retained_debit as $re) {
            $total_retained_debit += $re->debit;
        }
        $total_retained_earnings = $total_retained_credit = $total_retained_debit;

        $_Rev_debit = DB::table('journals as j')
            ->where('j.debit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.is_deleted', 0)
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients_gifi as cg", function ($join) use ($client) {
                $join->on("j.account_no", "=", "cg.account_no")
                    ->where('cg.client_id', $client)
                    ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
            })
            ->select("j.*", "sc.source_code")
            ->get();
        $_Rev_credit = DB::table('journals as j')
            ->where('j.credit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.is_deleted', 0)

            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients_gifi as cg", function ($join) use ($client) {
                $join->on("j.account_no", "=", "cg.account_no")
                    ->where('cg.client_id', $client)
                    ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
            })
            ->select("j.*", "sc.source_code")
            ->get();

        $_Exp_debit = DB::table('journals as j')
            ->where('j.debit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.is_deleted', 0)
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients_gifi as cg", function ($join) use ($client) {
                $join->on("j.account_no", "=", "cg.account_no")
                    ->where('cg.is_deleted', 0)
                    ->where('cg.client_id', $client)
                    ->where(function ($qry) {
                        $qry->where('cg.sub_type', "Cost of sale")
                            ->orWhere('cg.sub_type', "Operating expense");
                    });
            })
            ->select("j.*", "sc.source_code")
            ->get();
        $_Exp_credit = DB::table('journals as j')
            ->where('j.credit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.is_deleted', 0)
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients_gifi as cg", function ($join) use ($client) {
                $join->on("j.account_no", "=", "cg.account_no")
                    ->where('cg.is_deleted', 0)
                    ->where('cg.client_id', $client)
                    ->where(function ($qry) {
                        $qry->where('cg.sub_type', "Cost of sale")
                            ->orWhere('cg.sub_type', "Operating expense");
                    });
            })
            ->select("j.*", "sc.source_code")
            ->get();
        $_item_credit = DB::table('journals as j')
            ->where('j.credit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.account_no', '!=', '3100')
            ->where('j.account_no', '!=', '3200')
            ->where('j.is_deleted', 0)
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients_gifi as cg", function ($join) use ($client) {
                $join->on("j.account_no", "=", "cg.account_no")
                    ->where('cg.is_deleted', 0)
                    ->where('cg.client_id', $client)
                    ->where('cg.sub_type', "Retained earning/deficit");
            })
            ->select("j.*", "sc.source_code")
            ->get();
        $_item_debit = DB::table('journals as j')
            ->where('j.debit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.account_no', '!=', '3100')
            ->where('j.account_no', '!=', '3200')
            ->where('j.is_deleted', 0)
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where('sc.is_deleted', 0);
            })
            ->Join("clients_gifi as cg", function ($join) use ($client) {
                $join->on("j.account_no", "=", "cg.account_no")
                    ->where('cg.is_deleted', 0)
                    ->where('cg.client_id', $client)
                    ->where('cg.sub_type', "Retained earning/deficit");
            })
            ->select("j.*", "sc.source_code")
            ->get();
        $_dividend_debit = DB::table('journals as j')
            ->where('j.debit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.account_no', '3100')
            ->where('j.is_deleted', 0)
            ->select("j.*")
            ->get();
        $_dividend_credit = DB::table('journals as j')
            ->where('j.credit', '>', 0)
            ->where('j.client', $client)
            ->where('j.fyear', $year)
            ->where('j.account_no', '3100')
            ->where('j.is_deleted', 0)
            ->select("j.*")
            ->get();

        $total_dividend_debit = 0;
        foreach ($_dividend_debit as $p) {
            $total_dividend_debit += $p->debit;
        }
        $total_dividend_credit = 0;
        foreach ($_dividend_credit as $p) {
            $total_dividend_credit += $p->credit;
        }
        $total_dividend = $total_dividend_credit - $total_dividend_debit;
        $total_item_debit = 0;
        foreach ($_item_debit as $p) {
            $total_item_debit += $p->debit;
        }
        $total_item_credit = 0;
        foreach ($_item_credit as $p) {
            $total_item_credit += $p->credit;
        }
        $total_items = $total_item_credit - $total_item_debit;
        $total_rev_debit = 0;
        foreach ($_Rev_debit as $p) {
            $total_rev_debit += $p->debit;
        }
        $total_rev_credit = 0;
        foreach ($_Rev_credit as $p) {
            $total_rev_credit += $p->credit;
        }

        $total_exp_debit = 0;
        foreach ($_Exp_debit as $p) {
            $total_exp_debit += $p->debit;
        }
        $total_exp_credit = 0;
        foreach ($_Exp_credit as $p) {
            $total_exp_credit += $p->credit;
        }

        $total_rev = $total_rev_credit - $total_rev_debit;
        $total_exp = $total_exp_debit - $total_exp_credit;
        $net_income = $total_rev - $total_exp;

        $total = $total_retained_earnings + $net_income + $total_dividend + $total_items;

        return response()->json([
            'no_of_journals' => $count,
            'total_retained_earnings' => $total_retained_earnings,
            'net_income' => $net_income,
            'total_dividend' => $total_dividend,
            'total_items' => $total_items,
            'total' => $total
        ]);
    }
    public function insertCloseYear(Request $request)
    {
        $client_id = $request->client_id;
        $close_year = $request->close_year;
        $no_of_journals = $request->no_of_journals;
        $retained_earnings = $request->retained_earnings;
        $net_income = $request->close_net_income;
        $items_effecting = $request->items_effecting;
        $dividends = $request->dividends;
        $end_of_period = $request->end_of_period;

        if (isset($client_id)) {
            $closeYear = DB::table('clients_close_years')->where('client_id', $client_id)->where('year', $close_year)->exists();
            if (!$closeYear) {
                $client = DB::table('clients')->where('id', $client_id)->first();
                $newFiscalYear = $close_year + 1;
                $period = 1;
                $fiscal_start = explode('-', $client->fiscal_start);
                $fiscal_start_month = $fiscal_start[1];
                $year = $fiscal_start_month == 01 ? $newFiscalYear : $close_year;
                $source = 22;
                $ref_no = "OPENFIG";
                $formattedDateDashed = '01-' . $this->getShortMonthName(intval($fiscal_start_month)) . '-' . date('y', strtotime($newFiscalYear . '-01-01'));
                $formattedDateCompact = '01' . $fiscal_start_month . date('y', strtotime($newFiscalYear . '-01-01'));
                $description = 'Opening Figure for fiscal year ' . $newFiscalYear;

                $reports_assets = DB::table('journals as j')
                    ->where('j.is_deleted', 0)
                    ->where('j.client', $client_id)
                    ->where('j.fyear', $close_year)
                    ->Join("clients_gifi as g", function ($join) use ($client_id) {
                        $join->on("j.account_no", "=", "g.account_no")
                            ->where('g.account_type', "Asset")
                            ->where('g.is_deleted', 0);
                        $join->where('g.client_id', $client_id);
                    })
                    ->groupBy("j.account_no")
                    ->select(
                        "j.account_no",
                        "g.description",
                        "j.client",
                        "j.fyear"
                    )
                    ->orderBy("j.account_no", 'asc')
                    ->get();

                foreach ($reports_assets as $j) {
                    $total_debits = DB::table('journals')
                        ->where('account_no', $j->account_no)
                        ->where('fyear', $j->fyear)
                        ->where('client', $j->client)
                        ->where('is_deleted', 0)
                        ->sum('debit');
                    $total_credits = DB::table('journals')
                        ->where('account_no', $j->account_no)
                        ->where('fyear', $j->fyear)
                        ->where('client', $j->client)
                        ->where('is_deleted', 0)
                        ->sum('credit');
                    $debit = 0.00;
                    $credit = 0.00;

                    if ($total_debits > $total_credits) {
                        $debit = round($total_debits - $total_credits, 2);
                    } else {
                        $credit = round($total_credits - $total_debits, 2);
                    }

                    DB::table('journals')->insert([
                        'client' => $client_id,
                        'month' => $fiscal_start_month,
                        'year' => $year,
                        'period' => $period,
                        'account_no' => $j->account_no,
                        'original_account' => $j->account_no,
                        'source' => $source,
                        'ref_no' => $ref_no,
                        'description' => $description,
                        'gl_date' => $formattedDateDashed,
                        'date' => $formattedDateCompact,
                        'debit' => $debit,
                        'credit' => $credit,
                    ]);
                }

                $reports_liability = DB::table('journals as j')
                    ->where('j.is_deleted', 0)
                    ->where('j.client', $client_id)
                    ->where('j.fyear', $close_year)
                    ->Join("clients_gifi as g", function ($join) use ($client_id) {
                        $join->on("j.account_no", "=", "g.account_no")
                            ->where('g.account_type', "Liability")
                            ->where('g.is_deleted', 0);
                        $join->where('g.client_id', $client_id);
                    })
                    ->groupBy("j.account_no")
                    ->select(
                        "j.account_no",
                        "g.description",
                        "j.client",
                        "j.fyear"
                    )
                    ->orderBy("j.account_no", 'asc')
                    ->get();

                foreach ($reports_liability as $j) {
                    $total_debits = DB::table('journals')
                        ->where('account_no', $j->account_no)
                        ->where('fyear', $j->fyear)
                        ->where('client', $j->client)
                        ->where('is_deleted', 0)
                        ->sum('debit');
                    $total_credits = DB::table('journals')
                        ->where('account_no', $j->account_no)
                        ->where('fyear', $j->fyear)
                        ->where('client', $j->client)
                        ->where('is_deleted', 0)
                        ->sum('credit');
                    $debit = 0.00;
                    $credit = 0.00;

                    if ($total_debits > $total_credits) {
                        $debit = round($total_debits - $total_credits, 2);
                    } else {
                        $credit = round($total_credits - $total_debits, 2);
                    }

                    DB::table('journals')->insert([
                        'client' => $client_id,
                        'month' => $fiscal_start_month,
                        'year' => $year,
                        'period' => $period,
                        'account_no' => $j->account_no,
                        'original_account' => $j->account_no,
                        'source' => $source,
                        'ref_no' => $ref_no,
                        'description' => $description,
                        'gl_date' => $formattedDateDashed,
                        'date' => $formattedDateCompact,
                        'debit' => $debit,
                        'credit' => $credit,
                    ]);
                }


                $reports_retained_earning = DB::table('journals as j')
                    ->where('j.is_deleted', 0)
                    ->where('j.client', $client_id)
                    ->where('j.fyear', $close_year)
                    ->Join("clients_gifi as g", function ($join) use ($client_id) {
                        $join->on("j.account_no", "=", "g.account_no")
                            ->where('g.account_type', "Retained Earning")
                            ->where('g.is_deleted', 0);
                        $join->where('g.client_id', $client_id);
                    })
                    ->groupBy("j.account_no")
                    ->select(
                        "j.account_no",
                        "g.description",
                        "j.client",
                        "j.fyear"
                    )
                    ->orderBy("j.account_no", 'asc')
                    ->get();

                foreach ($reports_retained_earning as $j) {
                    $total_debits = DB::table('journals')
                        ->where('account_no', $j->account_no)
                        ->where('fyear', $j->fyear)
                        ->where('client', $j->client)
                        ->where('is_deleted', 0)
                        ->sum('debit');
                    $total_credits = DB::table('journals')
                        ->where('account_no', $j->account_no)
                        ->where('fyear', $j->fyear)
                        ->where('client', $j->client)
                        ->where('is_deleted', 0)
                        ->sum('credit');
                    $debit = 0.00;
                    $credit = 0.00;

                    if ($total_debits > $total_credits) {
                        $debit = round($total_debits - $total_credits, 2);
                    } else {
                        $credit = round($total_credits - $total_debits, 2);
                    }

                    DB::table('journals')->insert([
                        'client' => $client_id,
                        'month' => $fiscal_start_month,
                        'year' => $year,
                        'period' => $period,
                        'account_no' => 3100,
                        'original_account' => 3100,
                        'source' => $source,
                        'ref_no' => $ref_no,
                        'description' => $description,
                        'gl_date' => $formattedDateDashed,
                        'date' => $formattedDateCompact,
                        'debit' => $debit,
                        'credit' => $credit,
                    ]);
                }


                DB::table('clients_close_years')->insert([
                    'client_id' => $client_id,
                    'year' => $close_year,
                    'no_of_journals' => $no_of_journals,
                    'retained_earning' => $retained_earnings,
                    'net_income' => $net_income,
                    'items_affecting' => $items_effecting,
                    'dividends' => $dividends,
                    'end_of_period' => $end_of_period,
                    'closed_by' => Auth::user()->id
                ]);
                return redirect()->back()->with(['success' => 'Fiscal year closed.']);
            } else {
                return redirect()->back()->with(['error' => 'Close year $close_year already exists for requested client.']);
            }
        }
        return redirect()->back()->with(['error' => 'Client not found.']);
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
                    <p class="mb-0  header-new-subtext" style="line-height:17px">' . date(
            'Y-M-d',
            strtotime($q->updated_at)
        ) . ' by ' . @$user->firstname . ' ' . @$user->lastname . '</p>
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
    <input type="hidden" name="closing_year" id="closing_year" value="' . $q->fiscal_year_end . '">
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
                                <label class="col-sm-3 col-form-label  " for="example-hf-client_id"> Contact</label>
                                <?php
                                                                                  ?>
                                <div class="col-sm-2" style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->salutation . '</div>
                                </div>
                                <div class="col-sm-7 d-flex">
                                    <div class="bubble-white-new1 bubble-text-first mr-2">' . $q->firstname . '</div>
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->lastname . '</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-11">
                            <div class="form-group row fg-evenly">
                                <label class="col-sm-3  col-form-label  " for="example-hf-client_id">Client Name</label>
                                <div class="col-sm-9" style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->company . '</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-11">
                            <div class="form-group row fg-evenly">
                                <label class="col-sm-3  col-form-label  " for="example-hf-client_id">Display
                                    Name</label>
                                <div class="col-sm-9" style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->display_name . '</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-11" style="position:relative;">
                            <div class="form-group row fg-evenly">
                                <label class="col-sm-3  col-form-label   " for="example-hf-client_id">Type</label>
                                <div class="col-sm-5  " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->type . '</div>
                                </div>
                            </div>
                            <div class="form-group row fg-evenly ' . ($q->type == 'Corporation' ? '' : 'd-none') . '">
                                <label class="col-sm-3  col-form-label   " for="example-hf-client_id">Corporation
                                    #</label>
                                <div class="col-sm-5  " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->corporation_no . '</div>
                                </div>
                                <div class="col-sm-1 text-center custom-control custom-  custom-control-  custom-control-lg mt-2 "
                                    data-toggle="tooltip" data-title="Use for reports" data-original-title="" title="">
                                    <input type="checkbox" class="custom-control-input" id="corporation_checkbox_"
                                        name="corporation_checkbox_" value="1"
                                        disabled ' . ($q->use_corporation_no == 1 ? ' checked' : '') . '>
                                                                        <label class="custom-control-label" for="corporation_checkbox_" style="right: -8px;"></label>
                                                                    </div>
                                </div>
                            <div class="form-group row fg-evenly">
                                <label class="col-sm-3  col-form-label   " for="example-hf-client_id">Business</label>
                                <div class="col-sm-5  " style="padding-left: 19px !important;">
                                    <div class="bubble-white-new1 bubble-text-first">' . $q->business . '
                                </div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-3  col-form-label   " for="example-hf-client_id">Federal Tax #</label>
                            <div class="col-sm-5 " style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->federal_no . '</div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-3 col-form-label   " for="example-hf-client_id">Provincial Tax
                                #</label>
                            <div class="col-sm-5 " style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->provincial_no . '</div>
                            </div>
                        </div>
                        <div class="form-group row fg-evenly">
                            <label class="col-sm-3 col-form-label   " for="example-hf-client_id">NEQ
                                #</label>
                            <div class="col-sm-5 " style="padding-left: 19px !important;">
                                <div class="bubble-white-new1 bubble-text-first">' . $q->neq_no . '</div>
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
                        <label class="col-sm-3    col-form-label" for="example-hf-email">Email </label>
                        <div class="col-sm-9" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->email . '</div>
                        </div>
                    </div>
                    <div class="form-group row fg-evenly">
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Telephone</label>
                        <div class="col-sm-3" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->telephone . '</div>
                        </div>
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Fax</label>
                        <div class="col-sm-3">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->fax . '</div>
                        </div>
                    </div>
                    <div class="form-group row fg-evenly">
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Website</label>
                        <div class="col-sm-9" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->website . '</div>
                        </div>
                    </div>
                    <div class="form-group row fg-evenly">
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Country</label>
                        <div class="col-sm-3" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->country . '</div>
                        </div>
                    </div>
                    <div class="form-group row fg-evenly">
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Address</label>
                        <div class="col-sm-9" style="padding-left: 19px !important;">
                            <div style="min-height:100px;" class="bubble-white-new1 bubble-text-first">' .
            $q->address . '</div>
                        </div>
                    </div>
                    <div class="form-group row fg-evenly">
                        <label class="col-sm-3   col-form-label" for="example-hf-email">City</label>
                        <div class="col-sm-9" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->city . '</div>
                        </div>
                    </div>
                    <div class="form-group row fg-evenly">
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Province</label>
                        <div class="col-sm-3" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->province . '</div>
                        </div>
                        <label class="col-sm-3   col-form-label" for="example-hf-email">Postal Code</label>
                        <div class="col-sm-3">
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
                        <label class="col-sm-3    col-form-label" for="example-hf-email">Fiscal Start </label>
                        <div class="col-sm-3 " style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->fiscal_start . '</div>
                        </div>
                        <label class="col-sm-3    col-form-label" for="example-hf-email">Fiscal Year End </label>
                        <div class="col-sm-3">
                            <div class=" w-100 bubble-white-new2  ">' . $q->fiscal_year_end . '</div>
                        </div>
                    </div>
                    <div class="row form-group fg-evenly">
                        <label class="col-sm-3 col-form-label" for="example-hf-email">Default Province </label>
                        <div class="col-sm-3" style="padding-left: 19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->default_prov . '</div>
                        </div>
                        <label class="col-sm-3    col-form-label" for="example-hf-email">Frequency </label>
                        <div class="col-sm-3">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->tax_remittance . '</div>
                        </div>
                    </div>
                    <div class="row form-group fg-evenly">
                        <label class="col-sm-3 col-form-label" for="example-hf-email">Federal Tax Acct</label>
                        <div class="col-sm-3" style="padding-left:19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->federal_tax . '</div>
                        </div>
                        <label class="col-sm-3     pr-0 col-form-label" for="example-hf-email">Provincial Tax Acct
                        </label>
                        <div class="col-sm-3">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->provincial_tax . '</div>
                        </div>
                    </div>
                    <div class="row form-group fg-evenly">
                        <label class="col-sm-3 col-form-label" for="example-hf-email">Dividends Account</label>
                        <div class="col-sm-3" style="padding-left:19px !important;">
                            <div class="bubble-white-new1 bubble-text-first">' . $q->dividends_account . '</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
        $closeYear = DB::table('clients_close_years')->where('client_id', $id)->get();
        $html .= '<div class="block new-block   ">
        <div class="block-header py-0" style="padding-left:7mm;">
        <a class="  section-header">Closed Fiscal Years
        </a>
        <div class="block-options">
        </div>
        </div>
        <div class="block-content new-block-content">
        <div class="row">
        <div class="col-sm-12" class="close-year-content">';
        foreach ($closeYear as $item) {
            $closedBy = DB::table('users')->where('id', $item->closed_by)->first();
            $date = $item->created_at;

            $formatted = Carbon::parse($date)
                ->timezone('GMT')
                ->format('Y-M-d \a\t g:i:s A \G\M\T');

            $btnAttr = 'data-img="' . $closedBy->user_image . '" data-closed-by="' . $closedBy->firstname . ' ' . $closedBy->lastname . '" data-date="' . $formatted . '" data-year="' . $q->fiscal_year_end . ', ' . $item->year . '" data-count="' . number_format($item->no_of_journals) . '" data-retained-earnings="' . number_format($item->retained_earning, 2) . '" data-net-income="' . number_format($item->net_income, 2) . '" data-items-effecting="' . number_format($item->items_affecting, 2) . '" data-dividends="' . number_format($item->dividends, 2) . '" data-end-of-period="' . number_format($item->end_of_period, 2) . '" ';
            $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                        <table class="table table-borderless table-vcenter mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-center pr-0" style="width: 50px;padding-top: 0.5rem;padding-bottom: 0.5rem;padding-left: 20px;">
                                        <div class="d-flex align-items-center " style="width: 160px;text-align: center;">
                                        <p class="ml-1 mb-0 mr-1 rounded  MainTags" data-toggle="tooltip" data-title="Fiscal Year" data-original-title="" title="">' . $item->year . '</p>
                                    </div>
                                    </td>
                                    <td class="js-task-content  pl-0" style="padding-bottom: 0.5rem;padding-top: 0.5rem;">
                                        <h2 class="mb-0 comments-text"><div style="display: flex;align-items: center;">For the fiscal year ending ' . $q->fiscal_year_end . ', ' . $item->year . ' &nbsp;&nbsp;</div>
                                        <span class="comments-subtext ml-0 mb-0 mt-0" style="display: block;">Closed by ' . $closedBy->firstname . ' ' . $closedBy->lastname . ' on ' . $formatted . '</span></h2>
                                    </td>
                                    <td style="width: 20%;padding-right:18px;padding-bottom: 0.5rem;padding-top: 0.5rem;" class="text-right  ">
                                        <div class="d-flex align-items-center">
                                        <div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="No of Journals" data-original-title="" title="">
                                        <span class=" ">' . number_format($item->no_of_journals) . '</span>
                                    </div><div class="text-center LineTags mr-2" data-toggle="tooltip" data-title="Retained Earnings Close" data-original-title="" title="">
                                        <span class=" ">$ ' . number_format($item->retained_earning, 2) . '</span>
                                    </div>
                                    <a type="button" class="js- btn btn-sm text-warning btn-detail" ' . $btnAttr . '>
                                            <img src="' . asset('public') . '/img/gl-menu-icons/icon-view-year.png" style="width: 30px;"
                                            data-toggle="tooltip" data-title="View" data-original-title="" title="">
                                        </a>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>';
        }
        $html .= '</div>
                <div class="col-sm-12 text-right">
                    <button type="button" class="btn ml-5 btn-new select-close-year" data-client="' . $id . '">Close a Fiscal Year</button>
                </div>
            </div>
        </div>
    </div>';
        $closeYears = DB::table('clients_close_years')
            ->where('client_id', $id)
            ->select('year')
            ->distinct()
            ->pluck('year');
        $availYears = DB::table('journals')
            ->where('is_deleted', 0)
            ->where('client', $id)
            ->whereNotIn('fyear', $closeYears)
            ->select('fyear')
            ->distinct()
            ->get();


        $html .= '<form class="mb-0 pb-0" id="form-close-year" action="' . url('insert-close-year') . '" method="post">
        <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <div class="modal fade" id="AddCloseYearModal" tabindex="-1" role="dialog" data-backdrop="static"
                        aria-labelledby="modal-block-large" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document"
                            style="width: 550px;">
                            <div class="modal-content">
                                <div class="block  block-transparent mb-0">
                                    <div class="block-header pb-0  " style="padding-top:20px;">
                                        <span class="b e section-header" style="margin-left: 12px;">Close Fiscal Year</span>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option close-modal"
                                                target-modal="#AddCloseYearModal" aria-label="Close">
                                                <i class="fa fa-fw fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block-content new-block-content pt-0 pb-0 ">
                                        <div class="block-content pb-0 new-block-content px-2">
                                            <div class="row justify-content-  push">
                                                <div class="col-sm-12 m-">
                                                    <input type="hidden" name="client_id" id="client_id" value="' . $id . '">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group row">
                                                                <label class="col-sm-6 pr-0 col-form-label"
                                                                    style="line-height: 1;" for="example-hf-client_id">Fiscal
                                                                    Year to Close </label>
                                                                <?php ?>
                                                                <div class="col-sm-6">
                                                                    <select type="text" class="form-control"
                                                                        id="close_year" name="close_year"
                                                                        placeholder="close_year">';
        foreach ($availYears as $key => $y) {
            $html .= '<option data-client="' . $id . '" value="' . $y->fyear . '" ' . ($key == 0 ? 'selected' : '') . '>' . $y->fyear . '</option>';
        }
        $html .= '</select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-6  pr-0 col-form-label"
                                                                    style="line-height: 1;" for="example-hf-client_id">Number
                                                                    of Journals</label>
                                                                <div class="col-sm-6 ">
                                                                    <div class="bubble-new no_of_journals">

                                                                    </div>
                                                                    <input type="hidden" id="no_of_journals" name="no_of_journals">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12 py-2">
                                                            <h3 class="  section-header ml-n2"
                                                                style="margin-left: -9px !important;">Statement of Retained
                                                                Earnings</h3>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 pr-0 col-form-label"
                                                                    style="line-height: 1;"
                                                                    for="example-hf-client_id">Retained Earnings,
                                                                    beginning of period</label>
                                                                <div class="col-sm-2"><a href="javascript:;" style="position: absolute;



                                    top: 10px;" class="hover-info" data-standard="Retained Earnings, beginning of period" data-translated="Opening balance of retained earnings (Account #3100)" data-original-title="" title="">

                                                        <img src="' . asset('public') . '/icons2/icon-info.png?cache=1" width="20px">

                                                    </a></div>
                                                                <div class="col-sm-6">
                                                                    <div class="bubble-new retained_earnings">

                                                                    </div>
                                                                    <input type="hidden" id="retained_earnings" name="retained_earnings">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 pr-0 col-form-label"
                                                                    style="line-height: 1;" for="example-hf-client_id">Net
                                                                    Income</label>
                                                                <div class="col-sm-2"><a href="javascript:;" style="position: absolute;



                                    top: 10px;" class="hover-info" data-standard="Net Income" data-translated="Total Revenue minus Expenses" data-original-title="" title="">

                                                        <img src="' . asset('public') . '/icons2/icon-info.png?cache=1" width="20px">

                                                    </a></div>
                                                                <div class="col-sm-6">
                                                                    <div class="bubble-new close_net_income">

                                                                    </div>
                                                                    <input type="hidden" id="close_net_income" name="close_net_income">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 pr-0 col-form-label"
                                                                    style="line-height: 1;" for="example-hf-client_id">Items
                                                                    affecting
                                                                    retained earnings</label>
                                                                <div class="col-sm-2"><a href="javascript:;" style="position: absolute;



                                    top: 10px;" class="hover-info" data-standard="Items affecting retained earnings" data-translated="All journals where Sub-Type = ‘Retained earning/deficit’ with the exception of Account#’s 3100 and 3200" data-original-title="" title="">

                                                        <img src="' . asset('public') . '/icons2/icon-info.png?cache=1" width="20px">

                                                    </a></div>
                                                                <div class="col-sm-6">
                                                                    <div class="bubble-new items_effecting">

                                                                    </div>
                                                                    <input type="hidden" id="items_effecting" name="items_effecting">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-4 pr-0 col-form-label"
                                                                    style="line-height: 1;"
                                                                    for="example-hf-client_id">Dividends (owner’s
                                                                    equity)</label>
                                                                <div class="col-sm-2"><a href="javascript:;" style="position: absolute;



                                    top: 10px;" class="hover-info" data-standard="Dividends (owner’s equity)" data-translated="All journals posted to Account# 3200" data-original-title="" title="">

                                                        <img src="' . asset('public') . '/icons2/icon-info.png?cache=1" width="20px">

                                                    </a></div>
                                                                <div class="col-sm-6">
                                                                    <div class="bubble-new dividends">

                                                                    </div>
                                                                    <input type="hidden" id="dividends" name="dividends">
                                                                </div>
                                                            </div>
                                                            <div class="dashed-seperater form-group"></div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-4 pr-0 col-form-label"
                                                                    style="line-height: 1;"
                                                                    for="example-hf-client_id">Retained earnings, end of
                                                                    period</label>
                                                                <div class="col-sm-2"><a href="javascript:;" style="position: absolute;



                                    top: 10px;" class="hover-info-2" data-standard="Retained earnings, end of period" data-translated="Retained earnings, start
+ Net Income
+ Items affecting retained earnings
+ Dividends (owner’s equity)" data-original-title="" title="">

                                                        <img src="' . asset('public') . '/icons2/icon-info.png?cache=1" width="20px">

                                                    </a></div>
                                                                <div class="col-sm-6">
                                                                    <div class="bubble-new end_of_period">

                                                                    </div>
                                                                    <input type="hidden" id="end_of_period" name="end_of_period">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-right  pt-4"
                                    style="padding-left: 9mm;padding-right: 9mm">
                                    <button type="button" class="btn mr-3 btn-new btn-close-proceed">Close Year</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded"
                                            src="public/client_logos/' . $c->user_image . '"> </b></h1>';
                } else {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                        <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                }
                $html .= '</td>
                            <td class="js-task-content  pl-0">
                                <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On
                                        ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date(
                    'h:i:s A',
                    strtotime($c->date)
                ) . ' GMT
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
                } else if (
                    $fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' ||
                    $fileExtension == 'xlsb' || $fileExtension == 'xltx'
                ) {
                    $icon = 'attch-excel.png';
                } else if (
                    $fileExtension == 'png' || $fileExtension == 'gif' || $fileExtension == 'webp' ||
                    $fileExtension == 'svg'
                ) {
                    $icon = 'attch-png icon.png';
                } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                    $icon = 'attch-jpg-icon.png';
                } else if (
                    $fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' ||
                    $fileExtension == 'thmx'
                ) {
                    $icon = 'attch-powerpoint.png';
                }
                $html .= '<div class="col-sm-6 px-0  attach-other-col">
                    <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9"
                        data-task-completed="false" data-task-starred="false">
                        <table class="table table-borderless table-vcenter mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">';
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded" style=""><b>
                                                <img width="34px" height="34px" style="object-fit:cover;"
                                                    class="rounded" src="public/client_logos/' . $c->user_image . '">
                                            </b></h1>';
                } else {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                <img width="30px" src="public/img/profile-white.png"> </b></h1>';
                }
                $html .= '</td>
                                    <td class="js-task-content  pl-0">
                                        <h2 class="mb-0 comments-text ">' . $c->name . '<br><span
                                                class="comments-subtext text-truncate">' . date(
                    'Y-M-d',
                    strtotime($c->date)
                ) . ' at ' . date('h:i:s A', strtotime($c->date))
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
        $contract = DB::table('client_audit_trail as c')->select(
            'c.*',
            'u.firstname',
            'u.lastname',
            'u.user_image'
        )->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.client_id', $q->id)->get();
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                        <img width="34px" height="34px" style="object-fit:cover;" class="rounded"
                                            src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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

    function getFiscalYearEnd_($fiscalStart)

    {

        $parts = explode('-', $fiscalStart);

        $year = intval($parts[0]);

        $month = intval($parts[1]);

        $day = intval($parts[2]);



        $fiscalYearEnd = new DateTime(($year + 1) . '-' . $month . '-' . $day);

        $fiscalYearEnd->modify('-1 day');



        $fiscalYear = $fiscalYearEnd->format('Y');

        $fiscalMonth = $fiscalYearEnd->format('n');

        $fiscalDay = $fiscalYearEnd->format('j');



        //$fiscalYearEndFormatted = 'Fiscal Year End ' . $this->monthToStringShort($fiscalMonth) . ' ' . $fiscalYear;

        return $fiscalYear . '-' . $fiscalMonth . '-' . $fiscalDay;
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
    public function markAsPaid(Request $request)
    {
        $remit_id = $request->update_remit_id;
        $payment_method = $request->payment_method;
        $ref_no = $request->ref_no;
        $date = date('Y-m-d', strtotime($request->date));
        if ($remit_id) {
            DB::table('remittances')->where('id', $remit_id)->update([
                'remit_status' => 'paid',
                'payment_method' => $payment_method,
                'ref_no' => $ref_no,
                'paid_date' => $date
            ]);
            return redirect()->back()->with('success', 'Sales Tax Remittance Marked as paid by ' . $payment_method . ' ' . $ref_no . '.');
        }
        return redirect()->back()->with('error', 'Unable to mark Sales Tax Remittance as paid.');
    }
    public function getRemitContent(Request $request)
    {
        $remit_id = $request->id;
        if ($remit_id) {
            $data = DB::table('remittances as r')
                ->select('r.*', 'c.display_name')
                ->join('clients as c', function ($join) {
                    $join->on('c.id', 'r.client');
                })
                ->where('r.id', $remit_id)
                ->first();
            return response()->json($data);
        }
    }
    public function updateRemittance(Request $request)
    {
        $id = $request->update_id;
        $remit_start = date('Y-m-d', strtotime($request->remit_start_edit));
        $remit_end = date('Y-m-d', strtotime($request->remit_end_edit));
        $due_date = date('Y-m-d', strtotime($request->due_date_edit));
        if ($id) {
            DB::table('remittances')->where('id', $id)->update([
                'remit_start' => $remit_start,
                'remit_end' => $remit_end,
                'due_date' => $due_date
            ]);
            return redirect()->back()->with('success', 'Remittance Updated Successfully.');
        }
        return redirect()->back()->with('error', 'Update Failed.');
    }
    public function updateRemittanceContent()
    {
        $remitances = DB::table('remittances')->where('is_deleted', 0)->get();
        foreach ($remitances as $value) {
            $id = $value->id;
            $remit_id = $value->id;
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
                    "c.display_name",
                    "c.corporation_no",
                    "c.type",
                    "c.neq_no",
                    "c.use_corporation_no",
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                        ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
                })
                ->select("j.*", "sc.source_code")
                ->get();
            $_Rev_credit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.credit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                        ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
                })
                ->select("j.*", "sc.source_code")
                ->get();
            $_Exp_debit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.debit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
            $_Exp_credit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.credit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
            if ($taxes == 'Both') {
                // $_provincial_debit = DB::table('remit_provincial_debit')->where('remit_id', $id)->get();
                // $_provincial_credit = DB::table('remit_provincial_credit')->where('remit_id', $id)->get();
                $_provincial_debit = DB::table('journals as j')
                    ->where($whereClauses)
                    ->where('j.debit', '>', 0)
                    ->where(function ($query) use (
                        $tax_remittance,
                        $fiscal_year_end,
                        $month,
                        $year,
                        $calender,
                        $calender_month,
                        $calender_year,
                        $provincial_tax
                    ) {
                        if ($tax_remittance == 'Monthly') {
                            $query->where('j.month', $month)
                                ->where('j.year', $year);
                        }
                        if ($tax_remittance == 'Quarterly') {
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
                        if ($tax_remittance == 'Yearly') {
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
                    ->where(function ($query) use (
                        $tax_remittance,
                        $fiscal_year_end,
                        $month,
                        $year,
                        $calender,
                        $calender_month,
                        $calender_year,
                        $provincial_tax
                    ) {
                        if ($tax_remittance == 'Monthly') {
                            $query->where('j.month', $month)
                                ->where('j.year', $year);
                        }
                        if ($tax_remittance == 'Quarterly') {
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
                        if ($tax_remittance == 'Yearly') {
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
            foreach ($_Rev_debit as $p) {
                $total_rev_debit += $p->debit;
            }
            $total_rev_credit = 0;
            foreach ($_Rev_credit as $p) {
                $total_rev_credit += $p->credit;
            }
            $total_exp_debit = 0;
            foreach ($_Exp_debit as $p) {
                $total_exp_debit += $p->debit;
            }
            $total_exp_credit = 0;
            foreach ($_Exp_credit as $p) {
                $total_exp_credit += $p->credit;
            }
            $provincial_remit = $provincial_credit - $provincial_debit;
            $total_remittance = $federal_remit + $provincial_remit;
            $remit_status = null;
            if ($total_remittance <= 0) {
                $remit_status = 'refund';
            } else if ($total_remittance > 0) {
                $remit_status = 'not paid';
            }
            DB::table('remittances')->where('id', $remit_id)->update([
                'remit_status' => $remit_status
            ]);
            DB::table('remit_federal_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_federal_credit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_rev_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_rev_credit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_exp_credit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_exp_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_provincial_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_provincial_credit')->where('remit_id', $remit_id)->delete();
            if (count($_federal_debit) > 0) {
                foreach ($_federal_debit as $fd) {
                    $qry = DB::table('remit_federal_debit')->where('edit_no', @$fd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_federal_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$fd->edit_no,
                        'client' => @$fd->client,
                        'month' => @$fd->month,
                        'year' => @$fd->year,
                        'fyear' => @$fd->fyear,
                        'period' => @$fd->period,
                        'account_no' => @$fd->account_no,
                        'original_account' => @$fd->original_account,
                        'source' => @$fd->source,
                        'ref_no' => @$fd->ref_no,
                        'description' => @$fd->description,
                        'gl_date' => @$fd->gl_date,
                        'date' => @$fd->date,
                        'debit' => @$fd->debit,
                        'credit' => @$fd->credit,
                        'taxable' => @$fd->taxable,
                        'original_debit' => @$fd->original_debit,
                        'original_credit' => @$fd->original_credit,
                        'net' => @$fd->net,
                        'tax1' => @$fd->tax1,
                        'tax2' => @$fd->tax2,
                        'portion' => @$fd->portion,
                        'wo_portion_net' => @$fd->wo_portion_net,
                        'wo_portion_tax1' => @$fd->wo_portion_tax1,
                        'wo_portion_tax2' => @$fd->wo_portion_tax2,
                        'province' => @$fd->province,
                        'pr_tax1' => @$fd->pr_tax1,
                        'pr_tax2' => @$fd->pr_tax2,
                        'created_by' => @$fd->created_by,
                        'edit_by' => @$fd->edit_by,
                        'editNo' => @$fd->editNo,
                        'journal_status' => @$fd->journal_status,
                        'source_code' => @$fd->source_code,
                        'created_at' => @$fd->created_at,
                        'updated_at' => @$fd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_federal_credit) > 0) {
                foreach ($_federal_credit as $fc) {
                    $qry = DB::table('remit_federal_credit')->where('edit_no', @$fc->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_federal_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$fc->edit_no,
                        'client' => @$fc->client,
                        'month' => @$fc->month,
                        'year' => @$fc->year,
                        'fyear' => @$fc->fyear,
                        'period' => @$fc->period,
                        'account_no' => @$fc->account_no,
                        'original_account' => @$fc->original_account,
                        'source' => @$fc->source,
                        'ref_no' => @$fc->ref_no,
                        'description' => @$fc->description,
                        'gl_date' => @$fc->gl_date,
                        'date' => @$fc->date,
                        'debit' => @$fc->debit,
                        'credit' => @$fc->credit,
                        'taxable' => @$fc->taxable,
                        'original_debit' => @$fc->original_debit,
                        'original_credit' => @$fc->original_credit,
                        'net' => @$fc->net,
                        'tax1' => @$fc->tax1,
                        'tax2' => @$fc->tax2,
                        'portion' => @$fc->portion,
                        'wo_portion_net' => @$fc->wo_portion_net,
                        'wo_portion_tax1' => @$fc->wo_portion_tax1,
                        'wo_portion_tax2' => @$fc->wo_portion_tax2,
                        'province' => @$fc->province,
                        'pr_tax1' => @$fc->pr_tax1,
                        'pr_tax2' => @$fc->pr_tax2,
                        'created_by' => @$fc->created_by,
                        'edit_by' => @$fc->edit_by,
                        'editNo' => @$fc->editNo,
                        'journal_status' => @$fc->journal_status,
                        'source_code' => @$fc->source_code,
                        'created_at' => @$fc->created_at,
                        'updated_at' => @$fc->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Rev_debit) > 0) {
                foreach ($_Rev_debit as $rd) {
                    $qry = DB::table('remit_rev_debit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_rev_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Rev_credit) > 0) {
                foreach ($_Rev_credit as $rd) {
                    $qry = DB::table('remit_rev_credit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_rev_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Exp_credit) > 0) {
                foreach ($_Exp_credit as $rd) {
                    $qry = DB::table('remit_exp_credit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_exp_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Exp_debit) > 0) {
                foreach ($_Exp_debit as $rd) {
                    $qry = DB::table('remit_exp_debit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_exp_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_provincial_debit) > 0) {
                foreach ($_provincial_debit as $rd) {
                    $qry = DB::table('remit_provincial_debit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_provincial_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_provincial_credit) > 0) {
                foreach ($_provincial_credit as $rd) {
                    $qry = DB::table('remit_provincial_credit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_provincial_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
        }
        echo 'Completed';
    }
    public function updateRemitContent(Request $request)
    {
        $id = $request->remit_id;
        $remit_id = $request->remit_id;
        if ($remit_id) {
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
                    "c.display_name",
                    "c.corporation_no",
                    "c.type",
                    "c.neq_no",
                    "c.use_corporation_no",
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                        ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
                })
                ->select("j.*", "sc.source_code")
                ->get();
            $_Rev_credit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.credit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                        ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
                })
                ->select("j.*", "sc.source_code")
                ->get();
            $_Exp_debit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.debit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
            $_Exp_credit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.credit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
            if ($taxes == 'Both') {
                // $_provincial_debit = DB::table('remit_provincial_debit')->where('remit_id', $id)->get();
                // $_provincial_credit = DB::table('remit_provincial_credit')->where('remit_id', $id)->get();
                $_provincial_debit = DB::table('journals as j')
                    ->where($whereClauses)
                    ->where('j.debit', '>', 0)
                    ->where(function ($query) use (
                        $tax_remittance,
                        $fiscal_year_end,
                        $month,
                        $year,
                        $calender,
                        $calender_month,
                        $calender_year,
                        $provincial_tax
                    ) {
                        if ($tax_remittance == 'Monthly') {
                            $query->where('j.month', $month)
                                ->where('j.year', $year);
                        }
                        if ($tax_remittance == 'Quarterly') {
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
                        if ($tax_remittance == 'Yearly') {
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
                    ->where(function ($query) use (
                        $tax_remittance,
                        $fiscal_year_end,
                        $month,
                        $year,
                        $calender,
                        $calender_month,
                        $calender_year,
                        $provincial_tax
                    ) {
                        if ($tax_remittance == 'Monthly') {
                            $query->where('j.month', $month)
                                ->where('j.year', $year);
                        }
                        if ($tax_remittance == 'Quarterly') {
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
                        if ($tax_remittance == 'Yearly') {
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
            foreach ($_Rev_debit as $p) {
                $total_rev_debit += $p->debit;
            }
            $total_rev_credit = 0;
            foreach ($_Rev_credit as $p) {
                $total_rev_credit += $p->credit;
            }
            // dd($total_rev_debit, $total_rev_credit);
            $total_exp_debit = 0;
            foreach ($_Exp_debit as $p) {
                $total_exp_debit += $p->debit;
            }
            $total_exp_credit = 0;
            foreach ($_Exp_credit as $p) {
                $total_exp_credit += $p->credit;
            }
            $provincial_remit = $provincial_credit - $provincial_debit;
            $total_remittance = $federal_remit + $provincial_remit;
            $remit_status = null;
            if ($total_remittance <= 0) {
                $remit_status = 'refund';
            } else if ($total_remittance > 0) {
                $remit_status = 'not paid';
            }
            // DB::table('remittances')->where('id', $remit_id)->update([
            //     'remit_status' => $remit_status
            // ]);
            DB::table('remit_federal_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_federal_credit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_rev_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_rev_credit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_exp_credit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_exp_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_provincial_debit')->where('remit_id', $remit_id)->delete();
            DB::table('remit_provincial_credit')->where('remit_id', $remit_id)->delete();
            if (count($_federal_debit) > 0) {
                foreach ($_federal_debit as $fd) {
                    // $qry = DB::table('remit_federal_debit')->where('edit_no', @$fd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_federal_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$fd->edit_no,
                        'client' => @$fd->client,
                        'month' => @$fd->month,
                        'year' => @$fd->year,
                        'fyear' => @$fd->fyear,
                        'period' => @$fd->period,
                        'account_no' => @$fd->account_no,
                        'original_account' => @$fd->original_account,
                        'source' => @$fd->source,
                        'ref_no' => @$fd->ref_no,
                        'description' => @$fd->description,
                        'gl_date' => @$fd->gl_date,
                        'date' => @$fd->date,
                        'debit' => @$fd->debit,
                        'credit' => @$fd->credit,
                        'taxable' => @$fd->taxable,
                        'original_debit' => @$fd->original_debit,
                        'original_credit' => @$fd->original_credit,
                        'net' => @$fd->net,
                        'tax1' => @$fd->tax1,
                        'tax2' => @$fd->tax2,
                        'portion' => @$fd->portion,
                        'wo_portion_net' => @$fd->wo_portion_net,
                        'wo_portion_tax1' => @$fd->wo_portion_tax1,
                        'wo_portion_tax2' => @$fd->wo_portion_tax2,
                        'province' => @$fd->province,
                        'pr_tax1' => @$fd->pr_tax1,
                        'pr_tax2' => @$fd->pr_tax2,
                        'created_by' => @$fd->created_by,
                        'edit_by' => @$fd->edit_by,
                        'editNo' => @$fd->editNo,
                        'journal_status' => @$fd->journal_status,
                        'source_code' => @$fd->source_code,
                        'created_at' => @$fd->created_at,
                        'updated_at' => @$fd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_federal_credit) > 0) {
                foreach ($_federal_credit as $fc) {
                    // $qry = DB::table('remit_federal_credit')->where('edit_no', @$fc->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_federal_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$fc->edit_no,
                        'client' => @$fc->client,
                        'month' => @$fc->month,
                        'year' => @$fc->year,
                        'fyear' => @$fc->fyear,
                        'period' => @$fc->period,
                        'account_no' => @$fc->account_no,
                        'original_account' => @$fc->original_account,
                        'source' => @$fc->source,
                        'ref_no' => @$fc->ref_no,
                        'description' => @$fc->description,
                        'gl_date' => @$fc->gl_date,
                        'date' => @$fc->date,
                        'debit' => @$fc->debit,
                        'credit' => @$fc->credit,
                        'taxable' => @$fc->taxable,
                        'original_debit' => @$fc->original_debit,
                        'original_credit' => @$fc->original_credit,
                        'net' => @$fc->net,
                        'tax1' => @$fc->tax1,
                        'tax2' => @$fc->tax2,
                        'portion' => @$fc->portion,
                        'wo_portion_net' => @$fc->wo_portion_net,
                        'wo_portion_tax1' => @$fc->wo_portion_tax1,
                        'wo_portion_tax2' => @$fc->wo_portion_tax2,
                        'province' => @$fc->province,
                        'pr_tax1' => @$fc->pr_tax1,
                        'pr_tax2' => @$fc->pr_tax2,
                        'created_by' => @$fc->created_by,
                        'edit_by' => @$fc->edit_by,
                        'editNo' => @$fc->editNo,
                        'journal_status' => @$fc->journal_status,
                        'source_code' => @$fc->source_code,
                        'created_at' => @$fc->created_at,
                        'updated_at' => @$fc->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Rev_debit) > 0) {
                foreach ($_Rev_debit as $rd) {
                    // $qry = DB::table('remit_rev_debit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_rev_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Rev_credit) > 0) {
                foreach ($_Rev_credit as $rd) {
                    // $qry = DB::table('remit_rev_credit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_rev_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Exp_credit) > 0) {
                foreach ($_Exp_credit as $rd) {
                    // $qry = DB::table('remit_exp_credit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_exp_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_Exp_debit) > 0) {
                foreach ($_Exp_debit as $rd) {
                    // $qry = DB::table('remit_exp_debit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_exp_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_provincial_debit) > 0) {
                foreach ($_provincial_debit as $rd) {
                    // $qry = DB::table('remit_provincial_debit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_provincial_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            if (count($_provincial_credit) > 0) {
                foreach ($_provincial_credit as $rd) {
                    // $qry = DB::table('remit_provincial_credit')->where('edit_no', @$rd->edit_no)->exists();
                    // if (!$qry) {
                    DB::table('remit_provincial_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                    // }
                }
            }
            $date = date("Y-M-d \a\\t h:i:s A \G\M\T");
            $user_name = Auth::user()->firstname . ' ' . Auth::user()->lastname;
            $des = 'Remittance Updated by ' . $user_name . ' on ' . $date;
            DB::table('remittance_audit_trail')->insert([
                'remit_id' => $remit_id,
                'description' => $des,
                'user_id' => Auth::user()->id
            ]);
            return response()->json('success');
        }
        return response()->json('error');
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
                "c.display_name",
                "c.corporation_no",
                "c.type",
                "c.neq_no",
                "c.use_corporation_no",
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
        $_federal_debit = DB::table('remit_federal_debit')->where('remit_id', $id)->get();
        $_federal_credit = DB::table('remit_federal_credit')->where('remit_id', $id)->get();
        $_Rev_debit = DB::table('remit_rev_debit')->where('remit_id', $id)->get();
        $_Rev_credit = DB::table('remit_rev_credit')->where('remit_id', $id)->get();
        $_Exp_credit = DB::table('remit_exp_credit')->where('remit_id', $id)->get();
        $_Exp_debit = DB::table('remit_exp_debit')->where('remit_id', $id)->get();
        $_provincial_debit = array();
        $_provincial_credit = array();
        if ($taxes == 'Both') {
            $_provincial_debit = DB::table('remit_provincial_debit')->where('remit_id', $id)->get();
            $_provincial_credit = DB::table('remit_provincial_credit')->where('remit_id', $id)->get();
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
        foreach ($_Rev_debit as $p) {
            $total_rev_debit += $p->debit;
        }
        $account_no_ = '';
        $total_rev_credit = 0;
        foreach ($_Rev_credit as $p) {
            $total_rev_credit += $p->credit;
            $account_no_ = $p->account_no;
        }
        $total_exp_debit = 0;
        foreach ($_Exp_debit as $p) {
            $total_exp_debit += $p->debit;
        }
        $total_exp_credit = 0;
        foreach ($_Exp_credit as $p) {
            $total_exp_credit += $p->credit;
        }
        $provincial_remit = $provincial_credit - $provincial_debit;
        $total_remittance = $federal_remit + $provincial_remit;
        $total_debit = $federal_debit + $provincial_debit;
        $total_credit = $federal_credit + $provincial_credit;
        $account_type = DB::table('clients_gifi')->where('account_no', $account_no_)->first();
        // if($account_type->account_type == "Expense") {
        //     $total_rev = $total_rev_debit - $total_rev_credit;
        // } else {
        //     $total_rev = $total_rev_credit - $total_rev_debit;
        // }
        $total_rev = $total_rev_credit - $total_rev_debit;
        $total_exp = $total_exp_debit - $total_exp_credit;
        $net_revenue_before_exp = $total_rev;
        $net_revenue = $total_rev - $total_exp;
        $tax_rate = DB::table('tax_rate')->where('province', $q->default_prov)->where('is_deleted', 0)->first();
        $user = DB::table('users')->where('id', $q->created_by)->first();
        $back_color = '';
        if ($q->remit_status == 'refund') {
            $back_color = '0070DD !important;';
        } else if ($q->remit_status == 'not paid') {
            $back_color = 'F8B700 !important;';
        } else if ($q->remit_status == 'paid') {
            $back_color = '4EA833 !important;';
        }
        $html .= '<div style="margin-bottom: 0.875rem !important;' . (isset($back_color) ? 'background-color: #' . $back_color : '') . '"
    class="block card-round   ' . ($q->remittance_status == 1 ? 'read-mode-active' : 'read-mode-inactive') . '  new-nav">
    <div class="block-header   py-new-header">
        <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
            <div class="d-flex justify-content-center align-items-center">
                <img src="public/icons2/icon-remit-white.png" style="width: 36px;height: 36px;">
                <div class="" style="margin-left: 0.91rem;">
                    <h4 class="mb-0 header-new-text " style="line-height:24px">Sales Tax Remittance</h4>
                    <p class="mb-0  header-new-subtext" style="line-height:17px">' . (@$q->updated_at ? date(
            'Y-M-d',
            strtotime($q->updated_at)
        ) : ($q->created_at ? date("Y-M-d", strtotime($q->created_at)) : '')) . ' by ' .
            @$user->firstname . ' ' . @$user->lastname . '</p>
                </div>
            </div>';
        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">';
        if (Auth::user()->role != 'read') {
            $html .= '
                <a href="javascript:;" data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover"
                    data-placement="top" title="" data-original-title="Update" class="btn-update">
                    <img src="public/icons/rotate-right.png" width="20px">
                </a>
                <a href="javascript:;" d class="text-white   btnEdit" data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="' . asset('public') . '/icons2/icon-edit-white.png" width="24px">  </a>
                <a href="javascript:void;" onclick="window.print()" data-toggle="tooltip" data-trigger="hover"
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
            ($q->use_corporation_no == 1 ? $q->corporation_no : $q->display_name) . '</div>
                    </div>
                </div>
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
        if ($q->tax_remittance == "Quarterly" || $q->tax_remittance == "Yearly") {
            $calender = array_reverse($calender);
        }
        if ($q->tax_remittance == 'Yearly') {
            $_e1 = explode("-", $calender[0]);
            $_e2 = explode("-", $calender[count($calender) - 1]);
            $date1 = date("M-Y", strtotime($_e1[1] . "-" . $_e1[0]));
            $date2 = date("M-Y", strtotime($_e2[1] . "-" . $_e2[0]));
            $html .= '<span class="range-capsule px-3 rounded mr-2 text-nowrap">' . $date1 . ' to ' .
                $date2 . '</span>';
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
                ';
        // if ($q->type == "Corporation") {
        //     $html .= '<div class="form-group row fg-evenly">
        //             <div class="col-sm-2">
        //                 <div class=" -new col-form-label">Corporation #</div>
        //             </div>
        //             <div class="col-sm-10" style="padding-left:32px !important;">
        //                 <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' .
        //         $q->corporation_no . '</div>
        //             </div>
        //         </div>';
        // }
        // $html .= '<div class="form-group row fg-evenly">
        //             <div class="col-sm-2">
        //                 <div class=" -new col-form-label" data="' . $q->id . '">Federal#</div>
        //             </div>
        //             <div class="col-sm-4" style="padding-left:32px !important;">
        //                 <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->federal_no . '
        //                 </div>
        //             </div>
        //             <div class="col-sm-2">
        //                 <div class=" -new col-form-label" data="' . $q->id . '">Provincial#</div>
        //             </div>
        //             <div class="col-sm-4">
        //                 <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->provincial_no .
        //     '</div>
        //             </div>
        //         </div>';
        $html .= '<div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Federal Account#</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->federal_tax . '
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Provincial Account#</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->provincial_tax .
            '</div>
                    </div>
                </div>';
        if ($q->remit_status == "paid") {
            $html .= '<div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Pay Method / Ref#</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . $q->payment_method . ' / ' . $q->ref_no . '</div>
                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Payment Date</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bubble-white-new1 bubble-text-first" data="' . $q->id . '">' . date('d-M-Y', strtotime($q->paid_date)) .
                '</div>
                    </div>
                </div>';
        }
        $html .= '</div>
        </div>
    </div>
</div>
<!--End-->';
        $html .= ' <div class="block new-block position-relative  5" style="overflow: hidden;">';
        if (isset($q->remit_status)) {
            if ($q->remit_status == 'refund') {
                $rbnClass = "ribbon-blue";
            } else if ($q->remit_status == 'not paid') {
                $rbnClass = "ribbon-orange";
            } else if ($q->remit_status == 'paid') {
                $rbnClass = "ribbon-green";
            }
            $html .= "<div class='ribbon " . $rbnClass . "'>" . ucfirst($q->remit_status) . "</div>";
        }
        $html .= '
    <div class="block-header py-0" style="padding-left:7mm;">
        <a class="  section-header">Remittance Summary</a>
        <div class="block-options">
        </div>
    </div>
    <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
        <div class="row">
            <div class="col-sm-12">
<div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label">Remittance Period</div>
                    </div>
                    <div class="col-sm-10">
<div class="d-flex justify-content-between w-100">
                    <div class="w-100" style="padding-left:19px !important;">
                    <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' .
            (isset($q->remit_start) ? date('d-M-Y', strtotime($q->remit_start)) : '') . '</div>
                    </div>
                    <div class="w-25 text-center">
                        <div class=" -new col-form-label">to</div>
                    </div>
                    <div class="w-100" style="padding-left:32px !important;">
                    <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' .
            (isset($q->remit_end) ? date('d-M-Y', strtotime($q->remit_end)) : '') . '</div>
                    </div>
                    <div class="w-50 text-center">
                        <div class=" -new col-form-label">Due Date</div>
                    </div>
                    <div class="w-100" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' .
            (isset($q->due_date) ? date('d-M-Y', strtotime($q->due_date)) : '') . '</div>
                    </div>
                    </div>
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
                        <div class=" -new col-form-label">NEQ #</div>
                    </div>
                    <div class="col-sm-10" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first provinceText" data="' . $q->id . '">' .
            ($q->neq_no) . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_1 . ' Received
                        </div>
                    </div>
                    <div class="col-sm-4" style="padding-left: 32px !important;">
                        <div class="bubble-white-new1 bubble-text-first"
                            style="text-align: right !important;background: #fff;color: #E54643 !important; border-color: #E54643 !important"
                            data="' . $q->id . '">' .
            number_format($federal_credit, 2) . '</div>
                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_2 . ' Received
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bubble-white-new1 bubble-text-first"
                            style="text-align: right !important;background:#fff;color: #E54643 !important;border-color: #E54643 !important;"
                            data="' . $q->id . '">' .
            number_format($provincial_credit, 2) . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_1 . ' Paid
                        </div>
                    </div>
                    <div class="col-sm-4" style="padding-left: 32px!important;">
                        <div class="bubble-white-new1 bubble-text-first"
                            style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;"
                            data="' . $q->id . '">' .
            number_format($federal_debit, 2) . '</div>
                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_2 . ' Paid
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bubble-white-new1 bubble-text-first"
                            style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;"
                            data="' . $q->id . '">' .
            number_format($provincial_debit, 2) . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly ">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Total ' . @$tax_rate->tax_label_1 . '
                            Remit</div>
                    </div>
                    <div class="col-sm-4" style="padding-left: 32px!important;">
                        <div class="bubble-white-new1 bubble-text-first" ';
        if ($federal_remit > 0) {
            $html .= ' style="text-align: right !important;background: #fff;color: #E54643 !important;border-color: #E54643  !important;"';
        } else {
            $html .= ' style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;"';
        }
        $html .= ' data="' . $q->id . '">' . ($federal_remit < 0 ? '(' . number_format(abs($federal_remit), 2) . ')' :
            number_format($federal_remit, 2)) . '</div>
                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Total ' . @$tax_rate->tax_label_2 . '
                                Remit</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bubble-white-new1 bubble-text-first"';
        if ($provincial_remit > 0) {
            $html .= ' style="text-align: right !important;background: #fff;color: #E54643 !important;border-color: #E54643  !important;"';
        } else {
            $html .= ' style="text-align: right !important;background: #fff;color: #4194F6 !important;border-color: #4194F6  !important;"';
        }
        $html .= ' data="' . $q->id . '">' . ($provincial_remit < 0 ? '(' . number_format(abs($provincial_remit), 2)
            . ')' : number_format($provincial_remit, 2)) . '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Total Remittance</div>
                    </div>
                    <div class="col-sm-4" style="padding-left: 32px!important;">
                        <div class="bubble-white-new1 bubble-text-first"
                            style="text-align: right !important;background: #fff;color: #4EA833 !important;border-color: #4EA833  !important;"
                            data="' . $q->id . '">' . ($total_remittance >= 0 ? number_format($total_remittance, 2) :
            '(' . number_format(abs($total_remittance), 2) . ')') . '</div>
                    </div>
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Net Revenue</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bubble-white-new1 bubble-text-first"
                            style="text-align: right !important;background: #fff;color: #4EA833 !important;border-color: #4EA833  !important;"
                            data="' . $q->id . '">' . ($net_revenue_before_exp >= 0 ?
            number_format($net_revenue_before_exp, 2) : '(' .
            number_format(abs($net_revenue_before_exp), 2) . ')') . '</div>
                    </div>
                </div>';
        if ($q->remit_status == 'not paid' || $q->remit_status == '' || $q->remit_status == null) {
            $html .= '<div class="form-group row fg-evenly d-flex justify-content-end">
                        <button type="button" class="btn ml-5 btn-new mark-as-paid" data="' .  $q->id . '">Mark as Paid</button>
                    </div>';
        }
        $html .= '</div>
        </div>
    </div>
</div>
<!--End-->';
        $system_settings = DB::table('system_settings')->where('id', 1)->first();
        $federal_corp_tax = 0;
        $provincial_corp_tax = 0;
        if ($net_revenue >= 0) {
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
                        <div class=" -new col-form-label">Net Revenue</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first provinceText"
                            style="text-align: right !important" data="' . $q->id . '">' . ($net_revenue >= 0 ? '$' .
            number_format($net_revenue, 2) : '($' . number_format(abs($net_revenue), 2) . ')') . '</div>
                    </div>
                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="42">(after expenses)</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . @$tax_rate->tax_label_1 . '/' .
            @$tax_rate->tax_label_2 . '</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important"
                            data="' . $q->id . '">
                            ' . ($total_remittance <= 0 ? '$0.00' : '$' . number_format($total_remittance, 2)) . '
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . $system_settings->tax_remittance .
            '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Federal Corp Tax</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important"
                            data="' . $q->id . '">' . ($federal_corp_tax >= 0 ? '$' . number_format(
                $federal_corp_tax,
                2
            ) : '($' . number_format(abs($federal_corp_tax), 2) . ')') . '</div>
                    </div>
                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="' . $q->id . '">' . $system_settings->federal_corp_tax .
            '</div>
                    </div>
                </div>
                <div class="form-group row fg-evenly">
                    <div class="col-sm-2">
                        <div class=" -new col-form-label" data="' . $q->id . '">Provincial Corp Tax</div>
                    </div>
                    <div class="col-sm-4" style="padding-left:32px !important;">
                        <div class="bubble-white-new1 bubble-text-first" style="text-align: right !important"
                            data="' . $q->id . '">' . ($federal_corp_tax >= 0 ? '$' .
                number_format($provincial_corp_tax, 2) : '($' . number_format(abs($provincial_corp_tax), 2)
                . ')') . '</div>
                    </div>
                    <div class="col-sm-6">
                        <div class=" -new col-form-label" data="' . $q->id . '">' .
            $system_settings->provincial_corp_tax . '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End-->';
        // dd($_federal_credit);
        // dd($_federal_debit, $_federal_credit, $_Rev_debit, $_Rev_credit, $_Exp_credit, $_Exp_debit);
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
        $contract = DB::table('remittance_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname', 'u.user_image')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->orderByDesc('c.created_at')->where('c.remit_id', $q->id)->get();
        // dd($contract);
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
                if ($c->user_image) {
                    $html .= '<h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                            <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="public/client_logos/' . $c->user_image . '"> </b></h1>';
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
        return response()->json($html);
    }
    private function getFiscalYearEnd($period, $month, $year)
    {
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
    private function findPeriod($fiscalStart, $dateString)
    {
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
        $defaultClient = "No client selected";
        $defaultFyear = Auth::user()->default_fiscal_year ?? 0;
        if (Auth::user()->default_client) {
            $data = DB::table('clients')->where('id', Auth::user()->default_client)->first();
            $defaultClient = $data->display_name;
        }
        $clients = DB::table('clients')->where('is_deleted', 0)
            ->where('tax_remittance', '!=', "No")
            ->orderBy("display_name", "asc")
            ->get();
        return view("Remittance", compact("clients", "defaultClient", "defaultFyear"));
    }
    private function remittanceCalender($remittance, $month_no, $year)
    {
        $result = [];
        if ($remittance == "Quarterly") {
            for ($i = 0; $i < 3; $i++) {
                $result[] = $month_no . '-' . $year;
                if ($month_no == 1) {
                    $month_no = 12;
                    $year--;
                } else {
                    $month_no--;
                }
            }
        } elseif ($remittance == "Yearly") {
            for ($i = 0; $i < 12; $i++) {
                $result[] = $month_no . '-' .
                    $year;
                if ($month_no == 1) {
                    $month_no = 12;
                    $year--;
                } else {
                    $month_no--;
                }
            }
        } elseif ($remittance == "Monthly") {
            $result[] = $month_no . '-' . $year;
        }
        return $result;
    }
    public function PreInsertRemittance(Request $request)
    {
        $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $client_id = $request->post('client');
        $year = $request->post('year');
        $month = $request->post('month');
        $taxes = $request->post('taxes');
        $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
        if ($client) {
            $month = intval($month);
            $year = intval($year);
            if (DB::table('remittances')->where('client', $client->id)->where('year', $year)->where('month', $month)
                ->where('is_deleted', 0)->exists()
            ) {
                return response()->json([
                    "status" => "error",
                    "message" => "A remittance for " . $client->company . " and " . $year . " and " . $monthNames[$month - 1] . "
                    already exists."
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
    public function InsertRemittance(Request $request)
    {
        $client_id = $request->input('client');
        $year = $request->input('year');
        $month = $request->input('month');
        $taxes = $request->input('taxes');
        $remit_start = $request->input('remit_start');
        $remit_end = $request->input('remit_end');
        $due_date = $request->input('due_date');
        $remit_start = date('Y-m-d', strtotime($remit_start));
        $remit_end = date('Y-m-d', strtotime($remit_end));
        $due_date = date('Y-m-d', strtotime($due_date));
        $client = DB::table('clients')->where('is_deleted', 0)->where("id", $client_id)->first();
        // $calender = $this->remittanceCalender($client->tax_remittance, $month, $year);
        // dd($calender);
        if (@$client) {
            $month = intval($month);
            $year = intval($year);
            $federal_tax = $client->federal_tax;
            $provincial_tax = $client->provincial_tax;
            $tax_remittance = $client->tax_remittance;
            $calender = $this->remittanceCalender($tax_remittance, $month, $year);
            $calender_month = [];
            $calender_year = [];
            foreach ($calender as $c) {
                $arr = explode("-", $c);
                array_push($calender_month, intval($arr[0]));
                array_push($calender_year, intval($arr[1]));
            }
            $calender_month = array_values(array_unique($calender_month));
            $calender_year = array_values(array_unique($calender_year));
            $fs = $client->fiscal_start;
            $fiscal_start = explode("-", $fs);
            $fiscal_start = $fiscal_start[0];
            $period = $this->findPeriod($fs, $fiscal_start . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01");
            $fiscal_year_end = $this->getFiscalYearEnd(intval($period), intval($month), intval($year));
            // dd($fiscal_year_end, $calender);
            $whereClauses = [
                ['j.client', $client_id],
                ['j.is_deleted', 0]
            ];
            $_federal_debit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.debit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
                        $query->where('j.fyear', $fiscal_year_end);
                    }
                })
                ->leftJoin("source_code as sc", function ($join) {
                    $join->on("j.source", "=", "sc.id")
                        ->where('sc.is_deleted', 0);
                })
                ->Join("clients_gifi as cg", function ($join) use ($client_id) {
                    $join->on("j.account_no", "=", "cg.account_no")
                        ->where('cg.client_id', $client_id)
                        ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
                })
                ->select("j.*", "sc.source_code")
                ->get();
            $_Rev_credit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.credit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
                        $query->where('j.fyear', $fiscal_year_end);
                    }
                })
                ->leftJoin("source_code as sc", function ($join) {
                    $join->on("j.source", "=", "sc.id")
                        ->where('sc.is_deleted', 0);
                })
                ->Join("clients_gifi as cg", function ($join) use ($client_id) {
                    $join->on("j.account_no", "=", "cg.account_no")
                        ->where('cg.client_id', $client_id)
                        ->where('cg.is_deleted', 0)->where('cg.account_type', "Revenue");
                })
                ->select("j.*", "sc.source_code")
                ->get();
            $_Exp_debit = DB::table('journals as j')
                ->where($whereClauses)
                ->where('j.debit', '>', 0)
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
                        $query->where('j.fyear', $fiscal_year_end);
                    }
                })
                ->leftJoin("source_code as sc", function ($join) {
                    $join->on("j.source", "=", "sc.id")
                        ->where('sc.is_deleted', 0);
                })
                ->Join("clients_gifi as cg", function ($join) use ($client_id) {
                    $join->on("j.account_no", "=", "cg.account_no")
                        ->where('cg.is_deleted', 0)
                        ->where('cg.client_id', $client_id)
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
                ->where(function ($query) use (
                    $tax_remittance,
                    $fiscal_year_end,
                    $month,
                    $year,
                    $calender,
                    $calender_month,
                    $calender_year,
                    $federal_tax
                ) {
                    if ($tax_remittance == 'Monthly') {
                        $query->where('j.month', $month)
                            ->where('j.year', $year);
                    }
                    if ($tax_remittance == 'Quarterly') {
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
                    if ($tax_remittance == 'Yearly') {
                        $query->where('j.fyear', $fiscal_year_end);
                    }
                })
                ->leftJoin("source_code as sc", function ($join) {
                    $join->on("j.source", "=", "sc.id")
                        ->where('sc.is_deleted', 0);
                })
                ->Join("clients_gifi as cg", function ($join) use ($client_id) {
                    $join->on("j.account_no", "=", "cg.account_no")
                        ->where('cg.is_deleted', 0)
                        ->where('cg.client_id', $client_id)
                        ->where(function ($qry) {
                            $qry->where('cg.sub_type', "Cost of sale")
                                ->orWhere('cg.sub_type', "Operating expense");
                        });
                })
                ->select("j.*", "sc.source_code")
                ->get();
            if ($taxes == 'Both') {
                $_provincial_debit = DB::table('journals as j')
                    ->where($whereClauses)
                    ->where('j.debit', '>', 0)
                    ->where(function ($query) use (
                        $tax_remittance,
                        $fiscal_year_end,
                        $month,
                        $year,
                        $calender,
                        $calender_month,
                        $calender_year,
                        $provincial_tax
                    ) {
                        if ($tax_remittance == 'Monthly') {
                            $query->where('j.month', $month)
                                ->where('j.year', $year);
                        }
                        if ($tax_remittance == 'Quarterly') {
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
                        if ($tax_remittance == 'Yearly') {
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
                    ->where(function ($query) use (
                        $tax_remittance,
                        $fiscal_year_end,
                        $month,
                        $year,
                        $calender,
                        $calender_month,
                        $calender_year,
                        $provincial_tax
                    ) {
                        if ($tax_remittance == 'Monthly') {
                            $query->where('j.month', $month)
                                ->where('j.year', $year);
                        }
                        if ($tax_remittance == 'Quarterly') {
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
                        if ($tax_remittance == 'Yearly') {
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
            $federal_credit = 0;
            $federal_debit = 0;
            $federal_remit = 0;
            $provincial_credit = 0;
            $provincial_debit = 0;
            $provincial_remit = 0;
            $total_remittance = 0;
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
            foreach ($_Rev_debit as $p) {
                $total_rev_debit += $p->debit;
            }
            $total_rev_credit = 0;
            foreach ($_Rev_credit as $p) {
                $total_rev_credit += $p->credit;
            }
            $total_exp_debit = 0;
            foreach ($_Exp_debit as $p) {
                $total_exp_debit += $p->debit;
            }
            $total_exp_credit = 0;
            foreach ($_Exp_credit as $p) {
                $total_exp_credit += $p->credit;
            }
            $provincial_remit = $provincial_credit - $provincial_debit;
            $total_remittance = $federal_remit + $provincial_remit;
            $remit_status = null;
            if ($total_remittance <= 0) {
                $remit_status = 'refund';
            } else if ($total_remittance > 0) {
                $remit_status = 'not paid';
            }
            $remit_id = DB::table('remittances')->insertGetId([
                "client" => $client->id,
                "year" => $year,
                "month" => $month,
                "taxes" => $taxes,
                "remit_start" => $remit_start,
                "remit_end" => $remit_end,
                "due_date" => $due_date,
                'remit_status' => $remit_status,
                // "calender_month" => serialize($calender_month),
                // "calender_year" => serialize($calender_year),
                // "calender" => serialize($calender),
                "created_by" => Auth::user()->id
            ]);
            if (count($_federal_debit) > 0) {
                foreach ($_federal_debit as $fd) {
                    DB::table('remit_federal_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$fd->edit_no,
                        'client' => @$fd->client,
                        'month' => @$fd->month,
                        'year' => @$fd->year,
                        'fyear' => @$fd->fyear,
                        'period' => @$fd->period,
                        'account_no' => @$fd->account_no,
                        'original_account' => @$fd->original_account,
                        'source' => @$fd->source,
                        'ref_no' => @$fd->ref_no,
                        'description' => @$fd->description,
                        'gl_date' => @$fd->gl_date,
                        'date' => @$fd->date,
                        'debit' => @$fd->debit,
                        'credit' => @$fd->credit,
                        'taxable' => @$fd->taxable,
                        'original_debit' => @$fd->original_debit,
                        'original_credit' => @$fd->original_credit,
                        'net' => @$fd->net,
                        'tax1' => @$fd->tax1,
                        'tax2' => @$fd->tax2,
                        'portion' => @$fd->portion,
                        'wo_portion_net' => @$fd->wo_portion_net,
                        'wo_portion_tax1' => @$fd->wo_portion_tax1,
                        'wo_portion_tax2' => @$fd->wo_portion_tax2,
                        'province' => @$fd->province,
                        'pr_tax1' => @$fd->pr_tax1,
                        'pr_tax2' => @$fd->pr_tax2,
                        'created_by' => @$fd->created_by,
                        'edit_by' => @$fd->edit_by,
                        'editNo' => @$fd->editNo,
                        'journal_status' => @$fd->journal_status,
                        'source_code' => @$fd->source_code,
                        'created_at' => @$fd->created_at,
                        'updated_at' => @$fd->updated_at,
                    ]);
                }
            }
            if (count($_federal_credit) > 0) {
                foreach ($_federal_credit as $fc) {
                    DB::table('remit_federal_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$fc->edit_no,
                        'client' => @$fc->client,
                        'month' => @$fc->month,
                        'year' => @$fc->year,
                        'fyear' => @$fc->fyear,
                        'period' => @$fc->period,
                        'account_no' => @$fc->account_no,
                        'original_account' => @$fc->original_account,
                        'source' => @$fc->source,
                        'ref_no' => @$fc->ref_no,
                        'description' => @$fc->description,
                        'gl_date' => @$fc->gl_date,
                        'date' => @$fc->date,
                        'debit' => @$fc->debit,
                        'credit' => @$fc->credit,
                        'taxable' => @$fc->taxable,
                        'original_debit' => @$fc->original_debit,
                        'original_credit' => @$fc->original_credit,
                        'net' => @$fc->net,
                        'tax1' => @$fc->tax1,
                        'tax2' => @$fc->tax2,
                        'portion' => @$fc->portion,
                        'wo_portion_net' => @$fc->wo_portion_net,
                        'wo_portion_tax1' => @$fc->wo_portion_tax1,
                        'wo_portion_tax2' => @$fc->wo_portion_tax2,
                        'province' => @$fc->province,
                        'pr_tax1' => @$fc->pr_tax1,
                        'pr_tax2' => @$fc->pr_tax2,
                        'created_by' => @$fc->created_by,
                        'edit_by' => @$fc->edit_by,
                        'editNo' => @$fc->editNo,
                        'journal_status' => @$fc->journal_status,
                        'source_code' => @$fc->source_code,
                        'created_at' => @$fc->created_at,
                        'updated_at' => @$fc->updated_at,
                    ]);
                }
            }
            if (count($_Rev_debit) > 0) {
                foreach ($_Rev_debit as $rd) {
                    DB::table('remit_rev_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                }
            }
            if (count($_Rev_credit) > 0) {
                foreach ($_Rev_credit as $rd) {
                    DB::table('remit_rev_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                }
            }
            if (count($_Exp_credit) > 0) {
                foreach ($_Exp_credit as $rd) {
                    DB::table('remit_exp_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                }
            }
            if (count($_Exp_debit) > 0) {
                foreach ($_Exp_debit as $rd) {
                    DB::table('remit_exp_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                }
            }
            if (count($_provincial_debit) > 0) {
                foreach ($_provincial_debit as $rd) {
                    DB::table('remit_provincial_debit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                }
            }
            if (count($_provincial_credit) > 0) {
                foreach ($_provincial_credit as $rd) {
                    DB::table('remit_provincial_credit')->insert([
                        'remit_id' => $remit_id,
                        'edit_no' => @$rd->edit_no,
                        'client' => @$rd->client,
                        'month' => @$rd->month,
                        'year' => @$rd->year,
                        'fyear' => @$rd->fyear,
                        'period' => @$rd->period,
                        'account_no' => @$rd->account_no,
                        'original_account' => @$rd->original_account,
                        'source' => @$rd->source,
                        'ref_no' => @$rd->ref_no,
                        'description' => @$rd->description,
                        'gl_date' => @$rd->gl_date,
                        'date' => @$rd->date,
                        'debit' => @$rd->debit,
                        'credit' => @$rd->credit,
                        'taxable' => @$rd->taxable,
                        'original_debit' => @$rd->original_debit,
                        'original_credit' => @$rd->original_credit,
                        'net' => @$rd->net,
                        'tax1' => @$rd->tax1,
                        'tax2' => @$rd->tax2,
                        'portion' => @$rd->portion,
                        'wo_portion_net' => @$rd->wo_portion_net,
                        'wo_portion_tax1' => @$rd->wo_portion_tax1,
                        'wo_portion_tax2' => @$rd->wo_portion_tax2,
                        'province' => @$rd->province,
                        'pr_tax1' => @$rd->pr_tax1,
                        'pr_tax2' => @$rd->pr_tax2,
                        'created_by' => @$rd->created_by,
                        'edit_by' => @$rd->edit_by,
                        'editNo' => @$rd->editNo,
                        'journal_status' => @$rd->journal_status,
                        'source_code' => @$rd->source_code,
                        'created_at' => @$rd->created_at,
                        'updated_at' => @$rd->updated_at,
                    ]);
                }
            }
            return redirect('/remittances')->with('success', 'Remittance added successfully');
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
                // if ($show_remitted == 0) {
                // $query->whereNotExists(function ($subquery) {
                // $subquery->select(DB::raw(1))
                // ->from('remittances')
                // ->whereColumn('clients.id', 'remittances.client');
                // });
                // }
                // })->where('fiscal_year_end', date("F d", strtotime($this->getPreviousMonth(date("Y-m-d")))))
                ->orderBy("firstname", 'asc')->get();
            $filtered_clients = [];
            foreach ($clients as $client) {
                if ($client->tax_remittance == 'Monthly') {
                    array_push($filtered_clients, $client);
                } else {
                    if ($show_remitted == 0) {
                        if ($client->tax_remittance == 'Quarterly') {
                            $this_month = date("m", strtotime("-5 days"));
                            $this_year = date("Y", strtotime("-5 days"));
                            $fiscal_start = date("Y-m-d", strtotime($client->fiscal_start));
                            $q1 = date("m", strtotime($fiscal_start . " +3 months"));
                            $q2 = date("m", strtotime($fiscal_start . " +6 months"));
                            $q3 = date("m", strtotime($fiscal_start . " +9 months"));
                            $q4 = date("m", strtotime($fiscal_start . " +12 months"));
                            if ($this_month == $q1 || $this_month == $q2 || $this_month == $q3 || $this_month == $q4) {
                                if (!DB::table('remittances')->where('is_deleted', 0)->where('client', $client->id)->where(
                                    'remittanceMonth',
                                    $this_month
                                )->where('remittanceYear', $this_year)->exists()) {
                                    array_push($filtered_clients, $client);
                                }
                            }
                        }
                        if ($client->tax_remittance == 'Yearly') {
                            $this_month = date("m", strtotime("-5 days"));
                            $this_year = date("Y", strtotime("-5 days"));
                            $fiscal_start = date("Y-m-d", strtotime($client->fiscal_start));
                            $remitMonth = date("m", strtotime($fiscal_start . " +12 months"));
                            if ($this_month == $remitMonth) {
                                if (!DB::table('remittances')->where('is_deleted', 0)->where('client', $client->id)->where(
                                    'remittanceMonth',
                                    $this_month
                                )->where('remittanceYear', $this_year)->exists()) {
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

    // public function checkItemcodeCategory(Request $request)
    // {
    //     $itemcodeId = $request->itemcode_id;
    //     $currentCategory = $request->item_category;

    //     $exists = DB::table('item_category_items as ici')
    //         ->join('item_categories as ic', 'ic.id', '=', 'ici.category_id')
    //         ->where('ici.itemcode_id', $itemcodeId)
    //         ->where('ic.name', '!=', $currentCategory) // check other categories
    //         ->select('ic.name')
    //         ->first();

    //     return response()->json([
    //         'exists' => $exists ? true : false,
    //         'category_name' => $exists->name ?? null
    //     ]);
    // }

    public function checkItemcodeCategory(Request $request)
    {
        $itemcodeId = $request->itemcode_id;
        $currentCategory = $request->item_category;

        $exists = DB::table('item_categories_itemcodes as ici')
            ->join('item_categories as ic', 'ic.id', '=', 'ici.item_category_id')
            ->where('ici.itemcode_id', $itemcodeId)
            ->where('ic.item_category', '!=', $currentCategory) // exclude current category
            ->select('ic.item_category')
            ->first();

        return response()->json([
            'exists' => $exists ? true : false,
            'category_name' => $exists->item_category ?? null
        ]);
    }
    public function checkItemcodeCategory_new(Request $request)
    {
        // dd($request->all());
        $itemcodeId = $request->item_code_id;
        $item_category = $request->item_category;

        $exists = DB::table('test_threshold_item_categories')->where('item_category_name', $item_category)->where('is_deleted', 0)->first();
        $exists = DB::table('test_threshold_item_categories as ttic')
            ->select('ttic.*', 'ic.item_category')
            // ->leftJoin('item_categories as ic', 'ic.id', '=', 'ttic.item_category_id')
            ->leftJoin('itemcodes as ic', 'ic.id', '=', 'ttic.item_category_id')

            ->where('ttic.is_deleted', 0)
            ->where('ic.is_deleted', 0)->where('item_category', $item_category)
            ->orderBy('ic.item_category', 'asc')
            ->get();
        return response()->json([
            'exists' => $exists ? true : false
        ]);
    }
    public function update_names(Request $request)
    {
        $exists = DB::table('test_threshold_item_categories')->where('item_category_name', null)->where('is_deleted', 0)->get();
        foreach ($exists as $key => $value) {
            $category_name = DB::table('itemcodes')->where('id', $value->item_category_id)->first();
            // dd($category_name);
            DB::table('test_threshold_item_categories')->where('id', $value->id)->update([
                'item_category_name' => $category_name->item_category
            ]);
        }
    }
}
