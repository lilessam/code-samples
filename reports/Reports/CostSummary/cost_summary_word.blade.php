<style type="text/css">
    .page {
        overflow: hidden;
        page-break-after: always;
    }
    table {
        font-size: larger;
    }
</style>

<div class="page">
    <div style="border-bottom: 5 solid #000;">
        <div style="display:inline-block; width:50%">
            <img src="{{ asset('/img/logo.png') }}" style="width: 40%;"></img>
        </div>
        <div style="display:inline-block; width:50%; margin-left: -5%">
            <h2 style="letter-spacing: 3px;">COST SUMMARY REPORT</h2>
        </div>
        <br></br>
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
            <th>Category</th>
            <th>Amount</th>
            <th>Total</th>
        </thead>
        <tbody>
            @if ($status != null)
            @foreach ($expensesByCategory as $expenseCategory)
                @foreach ($expenseCategory->except('total', 'task_name') as $type)
                    @foreach ($type as $category => $properties)
                    <tr>
                        <td  style="font-weight:500; color: #387ab7;">
                            {{ $category }}
                        </td>
                        <td></td>
                        <td>{{ $properties['total'] }}</td>
                    </tr>
                        @foreach (collect($properties)->except('total') as $property => $total)
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp; &rarr; {{ $property }}</td>
                                <td>{{ $total }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
                <tr><td colspan="2"><h5>Total => {{ $expensesByCategory['total'] }}</h5></td></tr>
            @endforeach
            @else
            @foreach ($expensesByCategory->except('total', 'task_name') as $type)
                @foreach ($type as $category => $properties)
                <tr>
                    <td  style="font-weight:500; color: #387ab7;">
                        {{ $category }}
                    </td>
                    <td></td>
                    <td>{{ $properties['total'] }}</td>
                </tr>
                    @foreach (collect($properties)->except('total') as $property => $total)
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; &rarr; {{ $property }}</td>
                            <td>{{ $total }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
            <tr><td colspan="2"><h2>Total => {{ $expensesByCategory['total'] }}</h2></td></tr>
            @endif
        </tbody>
    </table>
</div>
