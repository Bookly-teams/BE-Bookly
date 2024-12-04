<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if (!$image) {
            return null;
        }

        $filename = time() . '.png';

        // Save image
        Storage::disk('public')->put($path . '/' . $filename, base64_decode($image));

        // Return to path
        return URL::to('/') . '/storage/' . $path . '/' . $filename;
    }
}
