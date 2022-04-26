<?php

namespace App\Http\Controllers\Accounts\Matches;

use Illuminate\Http\Request;
use App\Repositories\Matches;
use App\Http\Controllers\Controller;

class Delete extends Controller
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
     * Delete a match.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $id)
    {
        $match = $this->repository->get($id);
        if ($match) {
            $match->delete();

            return response()->json(['status' => true, 'data' => []], 200);
        } else {
            return response()->json(['status' => false, 'data' => []], 200);
        }
    }
}
