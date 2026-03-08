<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Test Date</th>
            <th>Test Name</th>
            <th>WO #</th>
            <th>Asset #</th>
            <th>Bosubi</th>
            <th>Production Date</th>
            <th>Lot</th>
            <th>ItemCode</th>
            <th>ItemColor</th>
            <th>ZipperLength</th>
            <th>ItemDescription</th>
            <th>ItemCategory</th>
            <th>TestType</th>
            <th>TestMinMax</th>
            <th>TestUOM</th>
            <th>TestStandard</th>
            <th>TestMIN</th>
            <th>TestMAX</th>
            <th>TestAVG</th>
            <th>TestYFS</th>
            <th>TestYGS</th>
            <th>TestSafetyThreshold</th>
            @for ($i = 1; $i <= 20; $i++)
                <th>Sample {{ $i }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($qry as $q)
        <tr>
            <td>{{ $q->user_name }}</td>
            <td>{{ \Carbon\Carbon::parse($q->sample_date)->format('d-M-Y') }}</td>
            <td>{{ $q->test_name }}</td>
            <td>{{ $q->workorder_no }}</td>
            <td>{{ $q->asset_no }}</td>
            <td>{{ $q->bosubi }}</td>
            <td>{{ \Carbon\Carbon::parse($q->production_date)->format('d-M-Y') }}</td>
            <td>{{ $q->lot }}</td>
            <td>{{ $q->itemcode }}</td>
            <td>{{ $q->color }}</td>
            <td>{{ $q->length }}</td>
            <td>{{ $q->item_description }}</td>
            <td>{{ $q->item_category }}</td>
            <td>{{ $q->test_type }}</td>
            <td>{{ $q->criteria }}</td>
            <td>{{ $q->uom }}</td>
            <td>{{ $q->standard }}</td>

            {{-- Test Thresholds --}}
            <td>{{ $q->test_type === 'Dimension' ? ($q->threshold_item->min ?? '') : '' }}</td>
            <td>{{ $q->test_type === 'Dimension' ? ($q->threshold_item->max ?? '') : '' }}</td>
            <td>{{ $q->test_type === 'Dimension' ? ($q->threshold_item->avg ?? '') : '' }}</td>

            <td>{{ $q->test_type === 'Perf-Str' ? ($q->threshold_item->YFS ?? '') : '' }}</td>
            <td>{{ $q->test_type === 'Perf-Str' ? ($q->threshold_item->YFGS ?? '') : '' }}</td>
            <td>{{ $q->test_type === 'Perf-Str' && $q->criteria === 'Min' ? ($q->threshold_item->safety_threshold ?? '') : '' }}</td>

            {{-- Sample Values --}}
            @for ($i = 1; $i <= 20; $i++)
                <td>{{ $q->samples[$i] ?? '' }}</td>
            @endfor
        </tr>
        @endforeach
    </tbody>
</table>
