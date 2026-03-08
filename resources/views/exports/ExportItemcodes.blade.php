<?php $sno = 1;

?>
<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Itemcode</th>
            <th>Description</th>
            <th>Status</th>
            <th>Colors</th>
        </tr>
    </thead>

    <tbody id="showdata">
        @foreach ($qry as $q)
            <tr>
                <td>{{ $q->item_code }}</td>
                <td>{{ $q->description }}</td>
                <td>
                    @if ($q->status == 1)
                        Active
                    @else
                        Inactive
                    @endif
                </td>
                <td>{{ $q->colors }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
