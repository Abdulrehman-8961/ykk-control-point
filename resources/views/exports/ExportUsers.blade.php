<table class="table table-bordered">
    <thead>
        <tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Email</th>
            <th>Module</th>
            <th>Access Type</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $currentUser = null; @endphp

        @foreach ($qry as $q)
            @if ($currentUser != $q->email)
                <tr>
                    <td>{{ $q->firstname }}</td>
                    <td>{{ $q->lastname }}</td>
                    <td>{{ $q->email }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $q->portal_access == 1 ? 'Active' : 'Inactive' }}</td>
                </tr>
                @php $currentUser = $q->email; @endphp
            @endif

            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $q->module_name }}</td>
                <td>{{ $q->access_type }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
