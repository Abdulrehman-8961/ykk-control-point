<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use DB;

class ExportJournalReport implements FromView
{
    protected $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $request = $this->request;
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
        return view('exports.ExportJournalReport',  compact("rollups", "client", "accounts", "sources", "periods", "filters"));
    }
}
