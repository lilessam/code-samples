<?php

namespace App\Http\Controllers\Accounts\Matches;

use Illuminate\Http\Request;
use App\Repositories\Matches;
use App\Http\Controllers\Controller;

class Get extends Controller
{
    /**
     * @var App\Repositories\Matches
     */
    protected $repository;

    /**
     * Make a new instance of the controller.
     *
     * @param \App\Repositories\Matches $matches
     */
    public function __construct(Matches $matches)
    {
        $this->repository = $matches;
    }

    /**
     * Get all accounts.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $account_id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $account_id)
    {
        $data = $this->repository->paginate($account_id, 10, $request->keyword, $request->driver);

        return response()->json(['status' => true, 'data' => $data], 200);
    }
}
