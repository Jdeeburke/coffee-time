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
    	$slack_url = "https://hooks.slack.com/services/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";
        $payload = '{"text": "Coffee is brewed and ready!", "icon_emoji": ":coffee:", "username": "coffeebot"}';

        $ch = curl_init($slack_url);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );

        $result = curl_exec($ch);
    }
}
