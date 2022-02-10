<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsTemplate;
use App\Models\Load;
use App\Models\MessageRecord;
// use App\Models\SmsController;

class SmsTemplateController extends Controller
{
    //
    public function edit($id)
    {
        $data = SmsTemplate::where('load_id', $id)
                            ->where('status', 'active')
                            ->first();
     
        $load = Load::with("driver", "shipper", "tractor", "trailer", "consignee")->where('id', $id)->first();
        $pageTitle= "Preview and edit SMS message";
        $url  = route('smstemplate-store');
        $msg = "Hi " . $load->driver->name . "\n";
        foreach ($load->shipper as $key => $value) {
            $msg = $msg . "\nPickup " . ($key + 1) . ":\n\nPickup date and time:\n";
            
            if(!empty($value->pickup_date))
            {
                if ($value->start_periode !== null && $value->start_periode !== "") {
                    $msg = $msg . date("m/d/Y h:i.a", strtotime($value->start_periode)) ." - ". date("m/d/Y h:i.a", strtotime($value->end_periode)). "\n";
                }else
                {
                    $msg = $msg .  date("m/d/Y h:i.a", strtotime($value->pickup_date))."\n";
                }
               
            }else
            {
                if ($value->start_periode !== null && $value->start_periode !== "") {
                    $msg = $msg . date("m/d/Y h:i.a", strtotime($value->start_periode)) ." - ". date("m/d/Y h:i.a", strtotime($value->end_periode)). "\n";
                }
            }
          
            $msg = $msg . "Location address:\n". $value->customer->company ."\n". $value->pickup_address . "\nPhone:\n" . $value->contact_number . "\nPickup #: ".$value->shipper_hash. "\n";
            if ($value->note !== null && $value->note !== "") {
                $msg = $msg . "Pickup Note:\n" . $value->note . "\n";
            }

        }

        foreach ($load->consignee as $key => $value) {
            $msg = $msg . "\nDelivery " . ($key + 1) . ":\n\nDelivery date and time:\n";
            if(!empty($value->dropoff_time))
            {
                if ($value->start_periode !== null && $value->start_periode !== "") {
                    $msg = $msg  . date("m/d/Y h:i.a", strtotime($value->start_periode)) ." - ". date("m/d/Y h:i.a", strtotime($value->end_periode)). "\n";
                }else
                {
                    $msg = $msg . date("m/d/Y h:i.a", strtotime($value->dropoff_time)). "\n";
                }
                
            }else
            {
                if ($value->start_periode !== null && $value->start_periode !== "") {
                    $msg = $msg  . date("m/d/Y h:i.a", strtotime($value->start_periode)) ." - ". date("m/d/Y h:i.a", strtotime($value->end_periode)). "\n";
                }
            }
            $msg =   $msg. "\nLocation address:\n" . $value->customer->company ."\n". $value->dropoff_address . "\nPhone:\n" . $value->contact_number  . "\nDelivery #: ".$value->hash."\n";
            if ($value->note !== null && $value->note !== "") {
                $msg = $msg . "Delivery Note:\n" . $value->note . "\n";
            }

         
        }

       
        return view('smstemplate', compact('id', 'load', 'data', 'pageTitle', 'url', 'msg')); 
    }

    public function store(Request $request)
    {
        $id = $request->load_id;
        $load = Load::with("driver", "shipper", "tractor", "trailer", "consignee")->where('id', $id)->first();
        SmsTemplate::updateOrCreate(['load_id'=>$id], ['content'=>$request->content, 'status'=> 'deactive']);
        $data = array("contact" => $load->driver->contact, "message" => $request->content);
        $response = HomeController::twillio_sms_bulk($data);

        $details = $response["message"];
        $message_record = new MessageRecord();
        if (!$response["status"])
            $details = $response["error"] . $details;
        $message_record->message = $details;
        $message_record->status = $response["status"];
        $message_record->save();
       
        if (!$response["status"]) {
            return redirect()->back()->with("error", __("tran.Error in sending message.") . "\n" . $response["error"]);
        }
        return redirect()->route('loads-index')->with("message", "Text message sent to driver!");
    }
}
