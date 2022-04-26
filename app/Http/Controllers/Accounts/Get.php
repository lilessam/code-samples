<?php

namespace App\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Repositories\Accounts;
use App\Http\Controllers\Controller;

class Get extends Controller
{
    /**
     * @var App\Repositories\Accounts
     */
    protected $repository;

    /**
     * Make a new instance of the controller.
     *
     * @param \App\Repositories\Accounts $accounts
     */
    public function __construct(Accounts $accounts)
    {
        $this->repository = $accounts;
    }

    /**
     * Get all accounts.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $id = null)
    {
        if ($id) {
            return response()->json(['status' => true, 'data' => $this->repository->get($id)]);
        }

        return response()->json(['status' => true, 'data' => $this->repository->all()], 200);
    }
}
