<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function isMobileView()
    {
        try {
            // Guard against cases where request() helper might fail if called before kernel handles the request
            if (! app()->bound('request')) {
                return false;
            }

            if (request()->has('desktop')) {
                return false;
            }
            if (request()->has('mobile')) {
                return true;
            }

            $userAgent = request()->header('User-Agent');

            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
        } catch (\Throwable $th) {
            return false;
        }
    }
}
