<?php $sno = 1;
$amount = 0;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Name</th>
            <th>Account No</th>
            <th>Account Type</th>
            <th>Sub Type</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Amount</th>
            <th>Amount Type</th>
        </tr>


    </thead>

    <tbody id="showdata">
        @foreach ($qry as $q)
        <?php
          $debits = DB::table('journals as j')
        ->where('j.account_no', $q->account_no)
        ->where('j.client', $q->client_id)
        ->where('is_deleted', 0)
        ->sum('j.debit');
        $credits = DB::table('journals as j')
        ->where('j.account_no', $q->account_no)
        ->where('j.client', $q->client_id)
        ->where('is_deleted', 0)
        ->sum('j.credit');
        $amount_clr = '';
        $amount = 0;
        $symbol = '';
        if ($debits > $credits) {
        $amount = $debits - $credits;
        $amount_clr = '#4194F6';
        $symbol = 'DR';
        } else {
        $amount = $credits - $debits;
        $amount_clr = '#E54643';
        $symbol = 'CR';
        }
        ?>
            <tr>
                <td>Client Account</td>
                <td>{{ $q->account_no }}</td>
                <td>{{ $q->account_type }}</td>
                <td>{{ $q->sub_type }}</td>
                <td>{{ $q->description }}</td>
                <td>{{ $q->note }}</td>
                <td>
                    {{ $amount }}
                </td>
                <td>
                    {{ $symbol }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
