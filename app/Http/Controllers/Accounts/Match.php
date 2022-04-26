<?php

namespace App\Http\Controllers\Accounts;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Match extends Controller
{
    /**
     * Get a matching account.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $account = Account::match($request->driver, $request->name, $request->city, $request->state, $request->zipcode);

        return response()->json(['status' => true, 'data' => [
            'account' => $account,
        ]
        ], 200);
    }
}
