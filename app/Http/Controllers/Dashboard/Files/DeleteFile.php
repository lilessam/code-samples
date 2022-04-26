<?php

namespace App\Http\Controllers\Dashboard\Files;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteFile extends Controller
{
    /**
     * Delete files.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $files = glob(public_path() . "/storage/files/".$request->qquuid.".*");
        unlink($files[0]);
        return response()->json([
            'success' => true
        ]);
    }
}
