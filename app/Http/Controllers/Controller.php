<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /* Add these two methods:
    protected function encryptId($id)
    {
        return Crypt::encryptString($id);
    }

    protected function decryptId($encryptedId)
    {
        try
        {
            return Crypt::decryptString($encryptedId);
        }
        catch (DecryptException $e)
        {
            abort(404, 'Invalid resource identifier');
        }
    }*/




    function ShowHomePage()
    {
        // Available halls are shared with the home view via a view composer
        // in AppServiceProvider.
        return view('home');
    }
}
