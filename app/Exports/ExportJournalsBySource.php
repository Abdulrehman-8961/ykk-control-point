<?php



namespace App\Exports;



use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Support\Facades\DB;



class ExportJournalsBySource implements FromView

{

    protected $request;



    function __construct($request)

    {

        $this->request = $request;

    }



    public function view(): View

    {

        $qry = DB::table('journals as j')

            ->where('j.is_deleted', 0)

            ->leftJoin('clients as c', function ($join) {

                $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);

            })

            ->leftJoin('source_code as sc', function ($join) {

                $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

            })

            ->select('j.*', 'c.firstname', 'c.lastname', 'c.display_name', 'c.logo', 'sc.source_code', 'c.company')

            ->where('j.fyear', $this->request->client_fyear)

            ->where('j.client', $this->request->client_id)

            ->where('j.source', $this->request->source_id)

            ->where(function($q) {
                if(!empty($this->request->period)) {
                    $q->where('j.period', $this->request->period);
                }
            })

            ->orderBy('j.' . $this->request->sort_column, $this->request->sort_order)

            ->get();

        $credit = DB::table('journals as j')

            ->where('j.is_deleted', 0)

            ->leftJoin('clients as c', function ($join) {

                $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);

            })

            ->leftJoin('source_code as sc', function ($join) {

                $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

            })

            ->where('j.credit', '>', 0)

            ->where('j.fyear', $this->request->client_fyear)

            ->where('j.client', $this->request->client_id)

            ->where('j.source', $this->request->source_id)

            ->where(function($q) {
                if(!empty($this->request->period)) {
                    $q->where('j.period', $this->request->period);
                }
            })

            ->count();

        $debit = DB::table('journals as j')

            ->where('j.is_deleted', 0)

            ->leftJoin('clients as c', function ($join) {

                $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);

            })

            ->leftJoin('source_code as sc', function ($join) {

                $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

            })

            ->where('j.debit', '>', 0)

            ->where('j.fyear', $this->request->client_fyear)

            ->where('j.client', $this->request->client_id)

            ->where('j.source', $this->request->source_id)

            ->where(function($q) {
                if(!empty($this->request->period)) {
                    $q->where('j.period', $this->request->period);
                }
            })

            ->count();



        $client = DB::table('clients')->where('id', $this->request->client_id)->first();

        $total_credit = 0;

        $total_debit = 0;

        $period = $this->request->period ?? "All";

        $source_text = $this->request->source_text;





        return view('exports.ExportJournalsBySource', [

            'qry' => $qry,

            'credit' => $credit,

            'debit' => $debit,

            'client' => $client,

            'total_credit' => $total_credit,

            'total_debit' => $total_debit,

            'period' => $period,

            'source_text' => $source_text,

        ]);

    }

}

