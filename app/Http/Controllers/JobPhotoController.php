<?php

namespace App\Http\Controllers;

use App\Models\JobPhoto;
use Illuminate\Http\Request;

class JobPhotoController extends Controller
{
    public function destroy($id)
    {
        $photo = JobPhoto::findOrFail($id);
        $photo->delete();

        return response()->json(['ok' => true]);
    }

}
