<?php

namespace App\Http\Controllers;

use App\Coffee;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class CoffeeController extends BaseController
{
    public function startBrew(Request $request)
    {
    	$coffee = Coffee::create(array('ip_address' => $request->ip()));
    	return json_encode($coffee);
    }

    public function getLatestBrew()
    {
    	if ($coffee = Coffee::orderBy('created_at', 'desc')->first()) {
    		return json_encode($coffee);
    	} else {
    		return null;
    	}
    }

    public function notify()
    {
    	
    }
}
