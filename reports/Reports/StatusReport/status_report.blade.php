<table class="table table-hover">
    <thead>
        <th>Office Name</th>
        <th>Activity Name</th>
        <th>Task Name</th>
        <th>Task Status</th>
        <th>Overall Task Total</th>
    </thead>
    <tbody>
        @foreach($data as $task)
        <tr>
            <td>{{ $task->wbsNumber->activity->name }}</td>
            <td>{{ $task->wbsNumber->activity->division->program->name }}</td>
            <td>{{ $task->task_name }}</td>
            <td>{{ $task->status->name }}</td>
            <td>{{ '$' . array_sum($task->stats()) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
Total Records: <span style="font-size: 18px">{{ $data->count() }}</span>
