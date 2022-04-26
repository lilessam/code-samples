<table class="table table-hover">
    <thead>
        <th>Category</th>
        <th>Amount</th>
        <th>Total</th>
    </thead>
    <tbody>
        <!-- Multiple tasks -->
        @if ($task_id == null)
            @foreach ($expensesByCategory as $expenseCategory)
                @foreach (collect($expenseCategory)->except('total', 'task_name') as $type)
                    @foreach ($type as $category => $properties)
                    <tr>
                        <td>
                            {{ $category }}
                        </td>
                        <td></td>
                        <td align="center"><strong>{{ $properties['total'] }}</strong></td>
                    </tr>
                        @foreach (collect($properties)->except('total') as $property => $total)
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp; &rarr; {{ $property }}</td>
                                <td>{{ $total }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
                <tr><td colspan="2"><h5>Total: <span class="ml-3">{{ $expensesByCategory['total'] }}</span></h5></td></tr>
            @endforeach
        @else
            <!-- One TASK -->
            @foreach ($expensesByCategory->except('total', 'task_name') as $type)
                @foreach ($type as $category => $properties)
                <tr>
                    <td style="font-weight:500; color: #387ab7;">
                        {{ $category }}
                    </td>
                    <td></td>
                    <td align="center"><strong>{{ $properties['total'] }}</strong></td>
                </tr>
                    @foreach (collect($properties)->except('total') as $property => $total)
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; &rarr; {{ $property }}</td>
                            <td>{{ $total }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
            <tr><td colspan="2"><h5 >Total: <span class="ml-3">{{ $expensesByCategory['total'] }}</span></h5></td></tr>
        @endif
    </tbody>
</table>


