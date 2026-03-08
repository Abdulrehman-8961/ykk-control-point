<?php $sno = 1;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>WORKORDER #</th>
            <th>ITEMCODE ID</th>
            <th>ITEMCOLOR</th>
            <th>ZIPLENGTH</th>
            <th>STATUS</th>
        </tr>
    </thead>

    <tbody id="showdata">
        @foreach ($qry as $q)
            <tr>
                <td>{{ $q->workorder_no }}</td>
                <td>{{ $q->item_code }}</td>
                <td>{{ $q->itemcode_color_id }}</td>
                <td>{{ $q->length }}</td>
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
