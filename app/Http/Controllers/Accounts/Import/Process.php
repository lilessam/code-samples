<?php

namespace App\Http\Controllers\Accounts\Import;

use App\Jobs\BulkImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class Process extends Controller
{
    /**
     * Process importing the file.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function __invoke(Request $request)
    {
        //
        BulkImport::dispatch($request->input_paths[0], Auth::user()->id);

        //
        return response()->json(['status' => true, 'data' => []], 200);
    }
}
