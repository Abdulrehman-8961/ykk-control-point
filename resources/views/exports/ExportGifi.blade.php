<?php $sno = 1;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Name</th>
            <th>Account Type</th>
            <th>Sub Type</th>
            <th>Account No</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody id="showdata">
        @foreach ($qry as $q)
            <tr>
                <td>Gifi</td>
                <td>{{ $q->account_type }}</td>
                <td>{{ $q->sub_type }}</td>
                <td>{{ $q->account_no }}</td>
                <td>{{ $q->description }}</td>
                <td>{{ $q->note }}</td>
                <td>
                    @if ($q->gifi_status == 1)
                        Active
                    @else
                        Inactive
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
