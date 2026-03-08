<?php



namespace App\Exports;



use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Support\Facades\DB;



class ExportJournalsTrialBalance implements FromView

{

    protected $request;



    function __construct($request)

    {

        $this->request = $request;
    }



    public function view(): View

    {
        $client_id = $this->request->client_id;
        $fiscal_year = $this->request->fiscal_year;
        $sort_column = $this->request->sort_column;
        $sort_order = $this->request->sort_order;

        $sort_column = $sort_column ?? 'j.account_no';
        $sort_order = $sort_order ?? 'asc';

        $reports = DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->Join('clients_gifi as g', function ($join) use ($client_id) {
                $join->on('j.account_no', '=', 'g.account_no')->where('g.is_deleted', 0);
                $join->where('g.client_id', $client_id);
            })
            ->groupBy('j.account_no')
            ->select(
                'j.account_no',
                'g.description',
                'g.account_type',
                'j.client',
                'j.fyear',
                DB::raw('SUM(j.debit) as total_debit'),
                DB::raw('SUM(j.credit) as total_credit'),
            )
            ->where('j.fyear', $fiscal_year)
            ->where('j.client', $client_id)
            ->get();

        $journalSums = $reports->groupBy('account_no');

        function getJournalSum($journalSums, $account_no, $type, $column = 'total_debit')
        {
            return optional($journalSums[$account_no]->firstWhere('account_type', $type))?->$column ?? 0;
        }

        $groupA = ['Asset', 'Liability', 'Retained Earning'];
        $groupB = ['Revenue', 'Expense'];

        if (in_array($sort_column, ['column-a-debit', 'column-a-credit', 'columnba-debit', 'column-b-credit'])) {
            $sorted = $reports->sortBy(function ($item) use ($sort_column, $journalSums, $groupA, $groupB) {
                switch ($sort_column) {
                    case 'column-a-debit':
                        return collect($groupA)->sum(
                            fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_debit'),
                        );
                    case 'column-a-credit':
                        return collect($groupA)->sum(
                            fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_credit'),
                        );
                    case 'columnba-debit':
                        return collect($groupB)->sum(
                            fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_debit'),
                        );
                    case 'column-b-credit':
                        return collect($groupB)->sum(
                            fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_credit'),
                        );
                }
            });

            if (strtolower($sort_order) === 'desc') {
                $sorted = $sorted->reverse();
            }

            $reports = $sorted->values();
        } else {
            if ($sort_column === 'j.account_no') {
                $reports = $reports->sortBy(function ($item) {
                    return (int) $item->account_no;
                });
            } elseif ($sort_column === 'g.description') {
                $reports = $reports->sortBy(function ($item) {
                    return strtolower($item->description);
                });
            }

            if (strtolower($sort_order) === 'desc') {
                $reports = $reports->reverse();
            }

            $reports = $reports->values();
        }



        $client = DB::table('clients')->where('id', $this->request->client_id)->first();

        $total_credit_a = 0;
        $total_debit_a = 0;
        $total_credit_b = 0;
        $total_debit_b = 0;

        $period = $this->request->period ?? "All";

        $source_text = $this->request->source_text;





        return view('exports.ExportJournalsTrialBalance', [

            'qry' => $reports,

            'client' => $client,

            'total_credit_a' => $total_credit_a,

            'total_debit_a' => $total_debit_a,
            'total_credit_b' => $total_credit_b,

            'total_debit_b' => $total_debit_b,

            'period' => $period,

            'source_text' => $source_text,

        ]);
    }
}
