<table>
    <thead>
        <tr>
            <th>Test Name</th>
            <th>Item Category</th>
            <th>Min</th>
            <th>Max</th>
            <th>Avg</th>
            <th>YFS</th>
            <th>YFGS</th>
            <th>Safety Threshold</th>
            <th>Max % Absorption</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $lastId = null; @endphp

        @foreach($qry as $row)
            @if($lastId !== $row->id)
                {{-- First Row: Only Test Name & Status --}}
                <tr>
                    <td>{{ $row->test_name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $row->status == 1 ? 'Active' : 'Inactive' }}</td>
                </tr>
            @endif

            {{-- Second Row: Only Item Category Details --}}
            <tr>
                <td></td>
                <td>{{ $row->item_category }}</td>
                <td>{{ $row->min }}</td>
                <td>{{ $row->max }}</td>
                <td>{{ $row->avg }}</td>
                <td>{{ $row->YFS }}</td>
                <td>{{ $row->YFGS }}</td>
                <td>{{ $row->safety_threshold }}</td>
                <td>{{ $row->absorption }}</td>
                <td></td>
            </tr>

            @php $lastId = $row->id; @endphp
        @endforeach
    </tbody>
</table>
