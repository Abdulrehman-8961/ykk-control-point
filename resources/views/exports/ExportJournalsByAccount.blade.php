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



    //$fiscalYearEndFormatted = 'Fiscal Year End ' . $this->monthToStringShort($fiscalMonth) . ' ' . $fiscalYear;

    return $fiscalYear . '-' . $fiscalMonth . '-' . $fiscalDay;

}

?>

<table class="table table-bordered" id="example2">

    <thead class="thead thead-dark">

        <tr>

            <th>Edit No</th>

            <th>Date</th>

            <th>Source</th>

            <th>RefNo</th>

            <th>Debit</th>



            <th>Credit</th>

            <th>Description</th>





        </tr>





    </thead>



    <tbody id="showdata">

        @foreach ($qry as $q)

            @php
$source = DB::table('source_code')->where('id', $q->source)->first();
                $total_credit += $q->credit;

                $total_debit += $q->debit;

            @endphp

            <tr>

                <td>{{ $q->editNo }}</td>

                <td>{{ $q->date }}</td>

                <td>{{ $source->source_code }}</td>

                <td>{{ $q->ref_no }}</td>

                <td>{{ $q->debit }}</td>

                <td>{{ $q->credit }}</td>

                <td>{{ $q->description }}</td>

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

        <tr>

            <th>Client</th>

            <th>Period</th>

            <th>No of Dr Journals</th>

            <th>No of Cr Journals</th>

            <th>Year Ending</th>

            <th>Total Debit</th>

            <th>Total Crdit</th>

            <th></th>

        </tr>

        <tr>

            <td>{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}</td>

            <td>{{ $period }}</td>

            <td>{{ $debit }}</td>

            <td>{{ $credit }}</td>

            <td>{{ date('M d,', strtotime(getFiscalYearEnd($client->fiscal_start))) }} {{ date('Y') }}</td>

            <td>{{ $total_debit }}</td>

            <td>{{ $total_credit }}</td>

            @php

                if ($total_debit > $total_credit) {

                    $total = $total_credit - $total_debit;

                    $total = $total * -1;

                } elseif ($total_debit < $total_credit) {

                    $total = $total_credit - $total_debit;

                } else {

                    $total = 0;

                }

            @endphp

            <td>{{ $total }}</td>

        </tr>

    </tbody>

</table>

