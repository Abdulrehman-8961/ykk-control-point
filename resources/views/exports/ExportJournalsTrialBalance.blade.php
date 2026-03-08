<?php $sno = 1;

function getFiscalYearEnd($fiscalStart)
{
    $parts = explode('-', $fiscalStart);

    $year = intval($parts[0]);

    $month = intval($parts[1]);

    $day = intval($parts[2]);

    $fiscalYearEnd = new DateTime($year + 1 . '-' . $month . '-' . $day);

    $fiscalYearEnd->modify('-1 day');

    $fiscalYear = $fiscalYearEnd->format('Y');

    $fiscalMonth = $fiscalYearEnd->format('n');

    $fiscalDay = $fiscalYearEnd->format('j');

    return $fiscalYear . '-' . $fiscalMonth . '-' . $fiscalDay;
}

$journalSums = $qry->groupBy('account_no');

function getJournalSum($journalSums, $account_no, $type, $column = 'total_debit')
{
    return optional($journalSums[$account_no]->firstWhere('account_type', $type))?->$column ?? 0;
}

$groupA = ['Asset', 'Liability', 'Retained Earning'];
$groupB = ['Revenue', 'Expense'];

?>

<table class="table table-bordered" id="example2">

    <thead class="thead thead-dark">
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody id="showdata">
        @foreach ($qry as $q)
            @php
                $column_a_debit = collect($groupA)->sum(
                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_debit'),
                );
                $column_a_credit = collect($groupA)->sum(
                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_credit'),
                );
                $column_b_debit = collect($groupB)->sum(
                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_debit'),
                );
                $column_b_credit = collect($groupB)->sum(
                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_credit'),
                );
                $total_credit_a += $column_a_credit;
                $total_debit_a += $column_a_debit;
                $total_credit_b += $column_b_credit;
                $total_debit_b += $column_b_debit;
            @endphp

            <tr>
                <td>{{ $q->account_no }}</td>
                <td>{{ $q->description }}</td>
                <td>{{ $column_a_debit > 0 ? number_format($column_a_debit, 2, '.') : '' }}</td>
                <td>{{ $column_a_credit > 0 ? number_format($column_a_credit, 2, '.') : '' }}</td>
                <td>{{ $column_b_debit > 0 ? number_format($column_b_debit, 2, '.') : '' }}</td>
                <td>{{ $column_b_credit > 0 ? number_format($column_b_credit, 2, '.') : '' }}</td>
            </tr>
        @endforeach



        <tr>

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <th></th>

            <th></th>

        </tr>

        @php
            $final_credit_a = 0;
            $final_debit_a = 0;
            $final_credit_b = 0;
            $final_debit_b = 0;
            if (bccomp($total_debit_a, $total_credit_a, 2) > 0) {
                $color_a = '#0B66D5';
                $total_a = $total_credit_a - $total_debit_a;
                $total_a = $total_a * -1;
                $final_credit_a = $total_credit_a + $total_a;
                $final_debit_a = $total_debit_a;
            } elseif (bccomp($total_debit_a, $total_credit_a, 2) < 0) {
                $total_a = $total_credit_a - $total_debit_a;
                $color_a = '#C41E3A';
                $final_credit_a = $total_credit_a;
                $final_debit_a = $total_debit_a + $total_a;
            } else {
                $total_a = 0;
                $color_a = '#4EA833';
                $final_credit_a = $total_credit_a;
                $final_debit_a = $total_debit_a;
            }

            if (bccomp($total_debit_b, $total_credit_b, 2) > 0) {
                $color_b = '#0B66D5';
                $total_b = $total_credit_b - $total_debit_b;
                $total_b = $total_b * -1;
                $final_credit_b = $total_credit_b + $total_b;
                $final_debit_b = $total_debit_b;
            } elseif (bccomp($total_debit_b, $total_credit_b, 2) < 0) {
                $total_b = $total_credit_b - $total_debit_b;
                $color_b = '#C41E3A';
                $final_credit_b = $total_credit_b;
                $final_debit_b = $total_debit_b + $total_b;
            } else {
                $total_b = 0;
                $color_b = '#4EA833';
                $final_credit_b = $total_credit_b;
                $final_debit_b = $total_debit_b;
            }
        @endphp

        <tr>

            <th colspan="2">Sub-Total</th>

            <th>{{ number_format($total_debit_a, 2, '.') }}</th>
            <th>{{ number_format($total_credit_a, 2, '.') }}</th>
            <th>{{ number_format($total_debit_b, 2, '.') }}</th>
            <th>{{ number_format($total_credit_b, 2, '.') }}</th>

        </tr>
        <tr>

            <th colspan="2">Balance</th>

            <th>{{ number_format($total_a, 2, '.') }}</th>
            <th></th>
            <th></th>
            <th>{{ number_format($total_b, 2, '.') }}</th>

        </tr>
        <tr>

            <th colspan="2">Total</th>

            <th>{{ number_format($final_debit_a, 2, '.') }}</th>
            <th>{{ number_format($final_credit_a, 2, '.') }}</th>
            <th>{{ number_format($final_debit_b, 2, '.') }}</th>
            <th>{{ number_format($final_credit_b, 2, '.') }}</th>

        </tr>
        <tr>

            <th>Client</th>

            <th>Year Ending</th>

        </tr>

        <tr>

            <td>{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}</td>

            <td>{{ date('M d,', strtotime(getFiscalYearEnd($client->fiscal_start))) }} {{ date('Y') }}</td>

        </tr>

    </tbody>

</table>
