<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Operation;
use App\Http\Controllers\Controller;

class Operations extends Controller
{
    /**
     * Return all previous operations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        return response()->json(['status' => true, 'data' => Operation::latest()->get()], 200);
    }
}
