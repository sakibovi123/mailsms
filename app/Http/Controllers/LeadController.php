<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class LeadController extends Controller
{

    public function sendSms($sender, $phone, $body){
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_token = getenv("TWILIO_TOKEN");

        $client = new Client($twilio_sid, $twilio_token);
        $client->messages->create(
            $phone,
            [
                "from" => $sender,
                "body" => $body,
            ]
        );
    }

    public function sendEmail($name, $email){

    }

    public function store(Request $request){
        $data = new Customer();

        $data->first_name = $request->input("first_name");
        $data->last_name = $request->input("last_name");
        $data->phone = $request->input("phone");
        $data->email = $request->input("email");
        $data->zip_code = $request->input("zip_code");

        $data->save();

        // sending sms
        $this->sendSms("+14696198904", $data->phone, "");

        // sending email
        $emailData = ["name" => $data->first_name, "Data" => "Hello World"];

        Mail::send(
            "mail", $emailData, function($msg) use ($data){
                $msg->to($data->email);
                $msg->from("noreply@sixtydayscreditrepair.com");
                $msg->subject("Thank you for filling up the form");
        }
        );
        return response()->json([
            "status" => true,
            "message" => "success",
            "data" => $data
        ], 201);
    }
}
