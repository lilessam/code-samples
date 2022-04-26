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
            <h2 style="letter-spacing: 3px;">EXPENSE REPORT</h2>
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

    <table cellpadding="15" cellspacing="0" border="1" width="100%">
        <thead>
            <th colspan="10">DATA</th>
        </thead>
        <tbody>
            <!-- One TASK -->
            @foreach ($subtasks as $subtask)
            <tr>
                <td colspan="10"><b>TASK: </b> {{ $subtask->name }}</td>
            </tr>

                @foreach ($subtask->entries()->whereHas('expenseType', function($query) use($types_ids){
                    return $query->whereIn('id', $types_ids);
                })->get()->pluck('expenseType')->unique()->all() as $type)
                    <tr>
                        <td colspan="10"><b>EXPENSE TYPE: </b>{{ $type->name }}</td>
                    </tr>
                    @foreach ($subtask->entries->where('expense_types_id', $type->id) as $entry)
                        <tr>
                        @foreach($entry->expenses as $expense)
                            <td>{{ $expense->property->key }} <br> &rarr; <b>{{ $expense->value }}</b></td>
                        @endforeach
                        </tr>
                    @endforeach
                @endforeach

            @endforeach
        </tbody>
    </table>
</div>
