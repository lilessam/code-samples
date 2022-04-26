<?php

namespace App\Http\Controllers\Accounts\Matches;

use Illuminate\Http\Request;
use App\Repositories\Matches;
use App\Http\Controllers\Controller;

class Update extends Controller
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
     * Save a match.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->has('id')) {
            $match = $this->repository->get($request->id);
            $match = $this->repository->update($match->id, $request->except('id', 'created_at', 'updated_at'));
        } else {
            $match = $this->repository->store($request->except('id', 'created_at', 'updated_at'));
        }

        if ($match) {
            return response()->json(['status' => true, 'data' => $match], 200);
        } else {
            return response()->json(['status' => false, 'data' => []], 200);
        }
    }
}
