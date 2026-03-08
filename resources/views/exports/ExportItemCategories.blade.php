<table class="table table-bordered" id="example2">
    <thead class="thead thead-dark">
        <tr>
            <th>Item Category</th>
            <th>Itemcode</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="showdata">
        @php
            $currentCategory = null;
        @endphp
        
        @foreach ($qry as $q)
            @if ($currentCategory != $q->item_category)
                <tr>
                    <th>{{ $q->item_category }}</th>
                    <td></td>
                    <td></td>
                    <td>
                        @if ($q->status == 1)
                            Active
                        @else
                            Inactive
                        @endif
                    </td>
                </tr>
                @php
                    $currentCategory = $q->item_category;
                @endphp
            @endif
            
            <tr>
                <td></td>
                <td>{{ $q->item_code }}</td>
                <td>{{ $q->description }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>