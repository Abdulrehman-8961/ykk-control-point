<?php $sno = 1;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Test Name</th>
            <th>Test Type</th>
            <th>Test UOM</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody id="showdata">
        @foreach ($qry as $q)
            <tr>
                <td>{{ $q->test_name }}</td>
                <td>{{ $q->test_type }}</td>
                <td>{{ $q->uom }}</td>
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
