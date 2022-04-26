<?php

namespace App\Http\Controllers\Dashboard;

use Auth;
use Illuminate\Http\Request;
use App\Jobs\Process as ProcessJob;
use App\Http\Controllers\Controller;

class Process extends Controller
{
    /**
     * Run the processor.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // @TODO: To test later ...
        // converter()->guessDriver($request->input_paths[0]['name'])->input($request->input_paths[0]['path'])->process();
        ProcessJob::dispatch($request->input_paths[0], Auth::user()->id);

        return response()->json(['status' => true, 'data' => []], 200);
    }
}
