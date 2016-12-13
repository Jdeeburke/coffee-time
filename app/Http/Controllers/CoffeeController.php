<?php

namespace App\Http\Controllers;

use App\Coffee;
use App\Notification;
use Carbon\Carbon;
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

        $notification = Notification::inRandomOrder()->first();

        $message = $notification->message;
        $user = $notification->user;
        
        $payload = '{"text": "' . $message . '", "icon_emoji": ":coffee:", "username": "coffeebot"}';
        $this->sendSlackNotification($slack_url, $payload);
    }

    public function sendSlackNotification($slack_url, $payload)
    {
        $ch = curl_init($slack_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );

        return curl_exec($ch);
    }

    public function slackLatest(Request $request)
    {
        $token = $request->input('token');

        if ($token == "RoQl5NlfRd9l3v03a9bZ2yfO") {

            if ($coffee = Coffee::orderBy('created_at', 'desc')->first()) {
                $text = "The last pot was made " . Carbon::now()->diffForHumans($coffee->created_at, true) . " ago";
            } else {
                $text = "Hmm... I'm having trouble looking that up.";
            }

            return response()->json(['text' => $text]);

        }
    }

    public function slackAddNotification(Request $request)
    {
        $token = $request->input('token');

        if ($token == "m9UYMtG7FJNFRK5eDtl5yrwU") {
            $message = $request->input('text');
            $user = $request->input('user_name');

            $message = ltrim(substr($message, strlen("!addmessage")));

            $text = "";

            if (Notification::create(['user' => $user, 'message' => $message])) {
                $text = "Notification Added!";
            } else {
                $text = "There was a problem adding that notification...";
            }

            return response()->json(['text' => $text]);
        }
    }
}
