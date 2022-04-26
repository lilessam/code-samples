<table class="table table-hover">
    <thead>
        <th colspan="10">DATA</th>
    </thead>
    <tbody>
        <!-- One TASK -->
        @forelse ($subtasks as $subtask)
        <tr style="border-top: 5px solid #ccc;">
            <td colspan="10"><b>SUBTASK: </b> {{ $subtask->name }}</td>
        </tr>

        @forelse($subtask->getEntriesExpenseTypes($types_ids) as $type)
        <tr>
                <td colspan="10"><b>EXPENSE TYPE: </b>{{ $type->name }}</td>
                @foreach ($subtask->getEntries($type) as $entry)
                    <tr>
                    @foreach($entry->expenses as $expense)
                        <td>{{ $expense->property->key }} <br> &rarr; <b>{{ $expense->value }}</b></td>
                    @endforeach
                    </tr>
                @endforeach
        </tr>
        @empty
        <tr><td>There's no data for this subtask.</td></tr>
        @endforelse

            {{-- @foreach ($subtask->entries()->whereHas('expenseType', function($query) use($types_ids){
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
            @endforeach --}}
        @empty
        <tr><td>There's no subtasks for this task.</td></tr>
        @endforelse
    </tbody>
</table>
