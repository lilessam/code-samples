<?php

namespace App\Http\Controllers\Accounts;

use App\Repositories\Accounts;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\Update as UpdateRequest;

class Update extends Controller
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
     * Update an account.
     *
     * @param  \App\Http\Requests\Accounts\Update  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateRequest $request)
    {
        if ($request->has('id')) {
            $account = $this->repository->get($request->id);
            $account = $this->repository->update($account->id, $request->except('matches', 'created_at', 'updated_at', 'id'));
        } else {
            $account = $this->repository->store($request->except('matches', 'created_at', 'updated_at', 'id'));
        }

        return response()->json(['status' => true, 'data' => $account], 200);
    }
}
