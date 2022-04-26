<style type="text/css">
    .page {
        overflow: hidden;
        page-break-after: always;
    }
</style>

<div class="page">
    <div style="border-bottom: 5 solid #000;">
        <div style="display:inline-block; width:50%">
            <img src="{{ asset('/img/logo.png') }}" style="width: 40%;">
        </div>
        <div style="display:inline-block; width:50%; margin-left: -5%">
            <h2 style="letter-spacing: 3px;">Task Status Report</h2>
        </div>
        <br>
        <div style="display:inline-block; width:50%; margin-top: 0%">
            <h4>DATE: {{ $todays_date }}</h4>
        </div>
    </div>
    <table class="table table-hover" cellpadding="15" cellspacing="0" border="1" width="100%">
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
    <span style="font-size: 20px">Total Records:</span> <span style="font-size: 28px">{{ $data->count() }}</span>
</div>