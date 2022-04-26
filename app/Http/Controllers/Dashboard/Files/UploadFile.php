<?php

namespace App\Http\Controllers\Dashboard\Files;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadFile extends Controller
{
    /**
     * Upload files.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $path = $request->file('uploadFileObj')->storeAs('public/files', $request->qquuid . '.' . pathinfo($request->qqfilename, PATHINFO_EXTENSION));
        // To generate the downloading URL out of $path
        // str_replace('/public', '', url('/storage/' . $path))
        return response()->json([
            'success' => true,
            'error' => null,
            'path' => 'storage/app/'.$path,
            'originName' => $request->qqfilename
        ]);
    }
}
