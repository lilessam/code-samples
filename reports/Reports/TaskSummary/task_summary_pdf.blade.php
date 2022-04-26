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
            <h2 style="letter-spacing: 3px;">Task Summary REPORT</h2>
        </div>
        <br>
        <div style="display:inline-block; width:50%; margin-top: 0%">
            <b>TASK ID</b>: {{ $tasks_name }}
        </div>
        <div style="display:inline-block; width:50%; margin-top: 0%">
            <h4>DATE: {{ $todays_date }}</h4>
        </div>
        @if ($estimator)
        <div style="display:inline-block; width:50%; margin-top: -2%">
            <h4>Estimator: {{ $estimator }}</h4>
        </div>
        @endif
    </div>
    <table class="table table-hover" cellpadding="15" cellspacing="0" border="1" width="100%">
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
</div>