<?php $sno = 1;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Asset #</th>
            <th>Machine #</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody id="showdata">
        @foreach ($qry as $q)
            <tr>
                <td>{{ $q->asset_no }}</td>
                <td>{{ $q->machine_no }}</td>
                <td>{{ $q->description }}</td>
                <td>
                    @if ($q->status == 1)
                        Active
                    @else
                        Inactive
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
