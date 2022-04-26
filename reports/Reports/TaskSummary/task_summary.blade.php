<table class="table table-hover">
    <thead>
        <th>TYPE</th>
        @foreach ($data as $subtasks)
        @if($loop->first)
            @foreach($subtasks as $subtask => $value)
            <th>{{ $subtask }}</th>
            @endforeach
        @endif
        @endforeach
    </thead>
    <tbody>
        @foreach($data as $type => $subtasks)
        <tr>
            <td>{{ $type }}</td>
            @foreach($subtasks as $value)
            <td>{{ $value }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
