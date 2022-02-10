<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Models\MessageRecord;
use App\Models\Driver;
use App\Models\EmailRecord;
use App\Models\User;
use App\Models\SmsTemplate;
use Mail;

class SMSController extends Controller
{
    //
    public function sendSms(Request $request)
    {
        $loadId = $request->loadId;
        
        $load = Load::with("driver", "shipper", "tractor", "trailer", "consignee")->where('id', $loadId)->first();

        if($load)
        {
            // if ( && env("SEND_SMS")&&!empty($load->driver_id)) {
                //send sms to driver
                $sms = SmsTemplate::where('load_id', $load->id)
                                ->where('status', 'active')
                                ->first();
                $msg = "";
                if($sms)
                {
                    $msg = $sms->content;
                }else
                {
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
                       
                      
                        $msg = $msg . "Location address:\n". $value->customer->company ."\n".$value->pickup_address."\n". "\nPhone:\n" . $value->contact_number . "\nPickup #: ".$value->shipper_hash. "\n";
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
                        
                        $msg =   $msg. "\nLocation address:\n".$value->customer->company."\n". $value->dropoff_address . "\nPhone:\n" . $value->contact_number  . "\nDelivery #: ".$value->hash."\n";
                        if ($value->note !== null && $value->note !== "") {
                            $msg = $msg . "Delivery Note:\n" . $value->note . "\n";
                        }

                        
                    }

                  
                }
               
    
                $data = array("contact" => $load->driver->contact, "message" => $msg);
                $response = HomeController::twillio_sms_bulk($data);
    
                $details = $response["message"];
                $message_record = new MessageRecord();
                if (!$response["status"])
                    $details = $response["error"] . $details;
                $message_record->message = $details;
                $message_record->status = $response["status"];
                $message_record->load_id = $load->id;
                $message_record->save();
                SmsTemplate::where('load_id', $load->id)->update(['status'=>'deactive']);
                if (!$response["status"]) {
                    return response()->json(['status'=>"error",'message'=> __("tran.Error in sending message.") . "\n" . $response["error"]]);
                }

                return response()->json(['status'=> 'success', 'message'=> __("tran.SMS sent successfully")]);
            // }else
            // {
              
            // }
        }else
        {
            return  response()->json(['status'=>'error', 'message'=> __("tran.Error in sending message.") . "\n" . __("tran.Load Not Found")]);
        }
        
    }

    public function sendExpirationEmail($driverId)
    {
   
        $currentDate = strtotime(Date("Y-m-d"));
        $driver = Driver::where('id', $driverId)
                        ->where('status', 'active')    
                        ->first();
        $admin = User::where('role', 'admin')->first();
        $medicalsendEmail = false;
        $cardSendEmail = false;
        if(!$driver)
        {
            return "false";
        }
        
    
        $medicalExpirationDate = strtotime($driver->dexpiration);
        $driverExpirationDate = strtotime($driver->lexpiration);

      
        $diffDays = abs((int)(($medicalExpirationDate - $currentDate)/86400));

        if($diffDays >= 30)
        {
            $emailRecord = EmailRecord::where('driver_id', $driverId)
                                    ->where('type', 'medic')
                                    ->orderBy('created_at', 'desc')->first();
            if(!$emailRecord)
            {
                $medicalsendEmail = true;
            }else
            {
                $lastEmailDate = strtotime($emailRecord->created_at);
                
                $emailDiffDays = abs((int)(($lastEmailDate - $currentDate)/86400));

                if($emailDiffDays >= 10)
                {
                    $medicalsendEmail = true;
                }
            }
        }

        $diffDays = abs((int)(($driverExpirationDate - $currentDate)/86400));

        if($diffDays >= 30)
        {
            $emailRecord = EmailRecord::where('driver_id', $driverId)
                                    ->where('type', 'card')
                                    ->orderBy('created_at', 'desc')->first();
            if(!$emailRecord)
            {
                $cardSendEmail = true;
            }else
            {
                $lastEmailDate = strtotime($emailRecord->created_at);
                
                $emailDiffDays = abs((int)(($lastEmailDate - $currentDate)/86400));

                if($emailDiffDays >= 10)
                {
                    $cardSendEmail = true;
                }
            }
        }

        
        if($cardSendEmail)
        {
           
            //send to driver
            $license = "medical card";
            $expire_date = Date("Y-m-d", strtotime($driver->dexpiration));
          
            $license= "Driver’s License";
            $expire_date =  Date("Y-m-d", strtotime($driver->lexpiration));
           
           
            $param = [
                'drive_name'=> $driver->name,
                'license'=> $license, 
                'expire_date'=>$expire_date
            ];

           
            $data['email'] = $driver->email;
            //$data['email'] = "milanb2020milan@gmail.com";
            Mail::send('emails.driver_expire_email', $param, function($message) use ($data){
                //dd($data);
               $message->to($data['email'], '')->subject
                  ("30 Days Expiration Notification");
               $message->from('donotreply@milamtrans.com','');
            });
        
            EmailRecord::create(['driver_id'=>$driver->id, 'type'=>'card']);

            $param = [
                'drive_name'=> $driver->name,
                'license'=> $license, 
                'expire_date'=>$expire_date,
                'admin_name'=>$admin->name,
                'driver_phone' => $driver->contact
            ];

            $data['email'] = $admin->email;
            Mail::send('emails.admin_expire_notify_email', $param, function($message) use ($data){
                //dd($data);
               $message->to($data['email'], '')->subject
                  ("30 Days Expiration Notification");
               $message->from('donotreply@milamtrans.com','');
            });
            EmailRecord::create(['user_id'=>$admin->id, 'type'=>'card']);
        }

        if($medicalsendEmail)
        {

            $license = "medical card";
            $expire_date = Date("Y-m-d", strtotime($driver->dexpiration));
          
           
           
            $param = [
                'drive_name'=> $driver->name,
                'license'=> $license, 
                'expire_date'=>$expire_date
            ];
            $data['email'] = $driver->email;
            //$data['email'] = "milanb2020milan@gmail.com";
            Mail::send('emails.driver_expire_email', $param, function($message) use ($data){
                //dd($data);
               $message->to($data['email'], '')->subject
                  ("30 Days Expiration Notification");
               $message->from('donotreply@milamtrans.com','');
            });
            EmailRecord::create(['driver_id'=>$driver->id, 'type'=>'medic']);
            $param = [
                'drive_name'=> $driver->name,
                'license'=> $license, 
                'expire_date'=>$expire_date,
                'admin_name'=>$admin->name,
                'driver_phone' => $driver->contact
            ];

            $data['email'] = $admin->email;
            Mail::send('emails.admin_expire_notify_email', $param, function($message) use ($data){
                //dd($data);
               $message->to($data['email'], '')->subject
                  ("30 Days Expiration Notification");
               $message->from('donotreply@milamtrans.com','');
            });
            EmailRecord::create(['user_id'=>$admin->id, 'type'=>'medic']);
        
        }

        return true;
        // $emailRecord = EmailRecord::where('driver_id', $driverId)->orderBy('created_at', 'desc')->first();
        
    }
}
