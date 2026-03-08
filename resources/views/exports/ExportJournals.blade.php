<?php $sno=1;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Client</th>
            <th>EditNo</th>
            <th>Year</th>
            <th>Month</th>
            <th>Period</th>

            <th>Fiscal Year</th>
            <th>AccountNo</th>
            <th>Source</th>
            <th>Date</th>
            <th>RefNo</th>
            <th>Description</th>
            <th>DR</th>
            <th>CR</th>


        </tr>


    </thead>

    <tbody id="showdata">
        @foreach($qry as $q)
        <tr>



            <td>{{$q->company}}</td>
            <td>{{$q->editNo}}</td>
            <td>{{$q->year}}</td>
            <td>{{$q->month}}</td>
            <td>{{$q->period}}</td>

            <td>{{$q->fyear}}</td>
            <td>{{$q->account_no}}</td>

            <td>{{$q->source_code}}</td>
            <td>{{$q->gl_date}}</td>
            <td>{{$q->ref_no}}</td>
            <td>{{$q->description}}</td>
            <td>{{$q->debit}}</td>
            <td>{{$q->credit}}</td>

        </tr>
        @endforeach
    </tbody>
</table>