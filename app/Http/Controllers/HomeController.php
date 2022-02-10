<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Load;
use App\Models\Tractor;
use App\Models\Trailer;
use App\Models\Document;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

//twilio sms required
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class HomeController extends Controller
{
    //    public function __construct()
    //    {
    //        $this->middleware('auth');
    //    }

    public static function mail_help($data)
    {
        $data = array('name' => "NAME HERE");
        Mail::send('invoice', $data, function ($message) {
            $message->to('rananadeemsports@gmail.com', 'To NAME')->subject('Laravel Testing Mail with Attachment');
            $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            //            $message->sender('email@example.com', 'Mr. Example');
            //            $message->returnPath('email@example.com');
            //            $message->cc('email@example.com', 'Mr. Example');
            //            $message->bcc('email@example.com', 'Mr. Example');
            //            $message->replyTo('email@example.com', 'Mr. Example');
            //            $message->priority(2);

            $message->from('rananadeemsports@gmail.com', 'testing');
        });
    }

    public function test()
    {
   dd("test");
    }

    public static function twillio_sms_bulk($data)
    {
        //composer require twilio/sdk
        //TWILIO_SID = AC0bfe0dc89c0bea1e7b11e4d2102ad08f
        //TWILIO_TOKEN = 3cb547583dd2ece4764fffd0e8a5db4a
        //TWILIO_FROM = "+16303150175"
        // Your Account SID and Auth Token from twilio.com/console
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        
        try {
            $client = new Client($sid, $token);
        } catch (ConfigurationException $e) {
            return array("status" => false, "error" => $e->getMessage(), "message" => "To:" . $data["contact"] . " Message:" . $data["message"]);
        }
        try {
            $client->messages->create(
                (string)$data["contact"],
                //                "+923106043285",
                [
                    'from' => env('TWILIO_FROM'),
                    'body' => $data["message"],
                ]
            );
        } catch (TwilioException $e) {
            return array("status" => false, "error" => $e->getMessage(), "message" => "To:" . $data["contact"] . " Message:" . $data["message"]);
        }
        return array("status" => true, "message" => "To:" . $data["contact"] . " Message:" . $data["message"]);

    }

    public function index()
    {
        $loads_data = Load::all();
        $loads = $loads_data->count();
        $total_revenue = $loads_data->sum("value");
        $miles = $loads_data->sum("miles");
        $total_cost = round($loads_data->sum("cost"));

        if ($loads){
            $avg_revenue = $total_revenue / $loads;
            if($miles != 0 )
                $avg_revenue_per_mile = round($total_revenue / $miles, 2);
            else
                $avg_revenue_per_mile  = 0;
            $avg_miles = round($miles / $loads, 2);
            $avg_cost = round($total_cost / $loads, 2);
        }
        else{
            $avg_revenue = 0;
            $avg_revenue_per_mile = 0;
            $avg_miles = 0;
            $avg_cost = 0;
        }
        
        $customers = Customer::all()->count();
        $trailers = Trailer::all()->count();
        $tractors = Tractor::all()->count();
        $drivers = Driver::all()->count();
        $title = __("Dashboard");
        $icon_name = "fa-dashboard";
        $page = "home";
        return view('home', compact("loads", "page", "customers", "tractors", "trailers", "drivers", "total_revenue", "avg_revenue"
            , "avg_revenue_per_mile", "miles", "avg_miles", "total_cost", "avg_cost", 'title', 'icon_name'));
    }

    public function options(Request $request, $model, $selected = null)
    {
        $model2 = "App\Models\\" . $model;
        $data2 = $model2::where("status", "active")->get();
        if($model == "Accessorial")
        {
            $query = "Income";

            switch($request->accessorial_type)
            {
                case 0: 
                    $query = "Income";
                    break;
                case 1:
                    $query = "Expense";
                    break;
                case 2:
                    $query = "Deductions";
                    break;
                default:
                    $query = "Income";
                    break;
            }
                
            $data2 = $model2::where('status', 'active')
                                ->where('group', $query)
                                ->get();
            
            if($selected == 0)
            {
                // $accessorial = $model2::where('status', 'active')
                // ->where('group', $query)
                // ->first();
                // if($accessorial)
                // {
                //     $selected = $accessorial->id;
                // }else
                // {
                    $selected = null;
                // }
            }


        }

        switch ($model) {
            case "Driver":
                $data = array_reduce($data2->sortby('name')->all(),
                    function ($result, $item) {
                        $result[$item->id] = $item->name;
                        return $result;
                    },
                    array()
                );
                break;
            case "Trailer":
                $data = array_reduce($data2->sortby('number')->all(),
                    function ($result, $item) {
                        $result[$item->id] = $item->number;
                        return $result;
                    },
                    array()
                );
                break;
            case "Tractor":
                $data = array_reduce($data2->sortby('pun')->all(),
                    function ($result, $item) {
                        $result[$item->id] = $item->pun;
                        return $result;
                    },
                    array()
                );
                break;
            case "Accessorial":
                $data = array_reduce($data2->all(),
                    function ($result, $item) {
                        $result[$item->id] = $item->title;
                        return $result;
                    },
                    array()
                );
                break;
            case "Customer":
                $data = array_reduce($data2->sortby('company')->all(),
                    function ($result, $item) {
                        $result[$item->id] = $item->company;
                        return $result;
                    },
                    array()
                );
                break;
            case "Broker":
                $data = array_reduce($data2->sortby('company')->all(),
                    function ($result, $item) {
                        $result[$item->id] = $item->company;
                        return $result;
                    },
                    array()
                );
                break;
            default :
                $data = false;
                break;
        }

        return view("options_partial", compact("data", "selected"));
    }

    public function send_mail()
    {
        $data = array('name' => "NAME HERE");
        Mail::send('invoice', $data, function ($message) {
            $message->to('rananadeemsports@gmail.com', 'To NAME')->subject('Laravel Testing Mail with Attachment');
            $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            //            $message->sender('email@example.com', 'Mr. Example');
            //            $message->returnPath('email@example.com');
            //            $message->cc('email@example.com', 'Mr. Example');
            //            $message->bcc('email@example.com', 'Mr. Example');
            //            $message->replyTo('email@example.com', 'Mr. Example');
            //            $message->priority(2);

            $message->from('rananadeemsports@gmail.com', 'testing');
        });
        echo "Email Sent with attachment. Check your inbox.";
    }

    public function pdf($id)
    {
        
        return self::pdf_create($id);
    }

    public function showInvoice($id)
    {
        $loads = Load::with('accessories')->where("id", $id)->get();
        
        $html = view('invoice.invoice', compact('loads'))->render();
        $selected_template = str_replace(["[\$invitoremail]", "[\$login]"], [Auth::user()->name, route('login')], $html);
        //    dd($selected_template);
        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('debugPng', true);
        $options->set('isJavascriptEnabled', true);
        $options->set('debugKeepTemp', true);
        //        $options->set('',true);
        //        $options->set('',true);

        $dompdf = new Dompdf();
        $dompdf->setOptions($options);

        //$dompdf->loadHtmlFile("https://codebeautify.org/html-to-php-converter");

        $dompdf->loadHtml($selected_template);
        $dompdf->render();

        $output = $dompdf->output();
        //        dd($output);

        $time = time();
        $name = "Invoice_" . $time . ".pdf";
        //file will be stored in storage/app/
        $created_file = Storage::disk('local')->put('pdf_created/' . $name, $output);

        // check project running from public or not
        if (explode("/", asset('/'))[count(explode("/", asset('/'))) - 2] == "public") {
            $file_location = explode("/public", asset('/'))[0] . '/storage/app/pdf_created/' . $name;
        } else
            $file_location = Url('/') . '/storage/app/pdf_created/' . $name;


            return redirect($file_location);
    }

    public function showInvoiceMultiple(Request $request)
    {
        $request->validate([
            'id' => 'required|array|min:1',
        ]);

        $ids = $request->id;
        $result = [];
        $drivers = Load::with('accessories', 'driver')
                        ->whereIn("id", $request->id)
                        ->distinct()
                        ->groupBy("driver_id")
                        ->get();

        
        
        for ($outer = 0; $outer < count($drivers); $outer++) {
            for ($inner = 0; $inner < count($drivers)-1; $inner++) {

                $inner_driver_name_array = explode(" ", $drivers[$inner]['driver']["name"]);
                $outer_driver_name_array = explode(" ", $drivers[$outer]['driver']["name"]);
                
                $inner_name_length = count($inner_driver_name_array);
                $outer_name_length = count($outer_driver_name_array);

                $inner_last_name = $inner_driver_name_array[$inner_name_length-1];
                $outer_last_name = $outer_driver_name_array[$outer_name_length-1];

                $inner_last_name_length = strlen($inner_last_name);
                $outer_last_name_length = strlen($outer_last_name);

                $short_name_length = $inner_last_name_length;
                $short_name_index = 0;
                if($inner_last_name_length > $outer_last_name_length)
                {
                    $inner_last_name_length = $outer_last_name_length;
                }
                
                for(;$short_name_index < $short_name_length -1; $short_name_index++)
                {
                    if($inner_last_name[$short_name_index] !=  $outer_last_name[$short_name_index])
                    {
                        break;
                        
                    }
                }
                

                if ($outer_last_name[$short_name_index] < $inner_last_name[$short_name_index]) {
                    $tmp = $drivers[$outer];
                    $drivers[$outer] = $drivers[$inner];
                    $drivers[$inner] = $tmp;
                }
            }
        }

        $loadsList = [];
        foreach ($drivers as $driver) {
            $loads = Load::whereIn("id", $request->id)->where("driver_id", $driver->driver_id)
            ->get();
            
            if (!$loads)
                return redirect()->route("drivers-paymanagement-index")->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
            
            array_push($loadsList, $loads);
        }
        

       
        
        $html = view('invoice.multiinvoice', compact('loadsList'))->render();
        $selected_template = str_replace(["[\$invitoremail]", "[\$login]"], [Auth::user()->name, route('login')], $html);
        //    dd($selected_template);
        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('debugPng', true);
        $options->set('isJavascriptEnabled', true);
        $options->set('debugKeepTemp', true);
        //        $options->set('',true);
        //        $options->set('',true);

        $dompdf = new Dompdf();
        $dompdf->setOptions($options);

        //$dompdf->loadHtmlFile("https://codebeautify.org/html-to-php-converter");

        $dompdf->loadHtml($selected_template);
        $dompdf->render();

        $output = $dompdf->output();
        //        dd($output);

        $time = time();
        $name = "Invoice_" . $time . ".pdf";
        //file will be stored in storage/app/
        $created_file = Storage::disk('local')->put('pdf_created/' . $name, $output);

        // check project running from public or not
        if (explode("/", asset('/'))[count(explode("/", asset('/'))) - 2] == "public") {
            $file_location = explode("/public", asset('/'))[0] . '/storage/app/pdf_created/' . $name;
        } else
            $file_location = Url('/') . '/storage/app/pdf_created/' . $name;


        return redirect($file_location);
    }


    public static function pdf_create($ids)
    {
       
       
        $loads = Load::with('accessories')->whereIn("id", $ids)->get();
        
        $html = view('invoice.invoice', compact('loads'))->render();
        $selected_template = str_replace(["[\$invitoremail]", "[\$login]"], [Auth::user()->name, route('login')], $html);
        //    dd($selected_template);
        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('debugPng', true);
        $options->set('isJavascriptEnabled', true);
        $options->set('debugKeepTemp', true);
        //        $options->set('',true);
        //        $options->set('',true);

        $dompdf = new Dompdf();
        $dompdf->setOptions($options);

        //$dompdf->loadHtmlFile("https://codebeautify.org/html-to-php-converter");

        $dompdf->loadHtml($selected_template);
        $dompdf->render();

        $output = $dompdf->output();
        //        dd($output);

        $time = time();
        $name = "Settlement_" . $time . ".pdf";
        //file will be stored in storage/app/
        $created_file = Storage::disk('local')->put('pdf_created/' . $name, $output);

        // check project running from public or not
        if (explode("/", asset('/'))[count(explode("/", asset('/'))) - 2] == "public") {
            $file_location = explode("/public", asset('/'))[0] . '/storage/app/pdf_created/' . $name;
        } else
            $file_location = Url('/') . '/storage/app/pdf_created/' . $name;

        
        return $file_location;
        //return redirect($file_location);
        //        this is for direct download
        //        $dompdf->stream( "Invoice",array("Attachment" => 1));
    }

    public function html($id)
    {
        $load = Load::whereId($id)->first();
        return view("invoice.invoice", compact("load"));
    }

    public function profile()
    {
        $url = route("profile_update");
        $pageTitle = "Profile";
        $data = auth()->user();
        return view("profile", compact("url", "pageTitle", "data"));
    }

    public function profile_update(Request $request)
    {

        $request->validate([
            'name' => 'required|min:3|max:50',
        ]);
        if (auth()->user()->role == "admin")
            $request->validate([
                'email' => 'required|min:3|max:50|email',
            ]);

        auth()->user()->name = $request->name;
        if (auth()->user()->role == "admin")
            auth()->user()->email = $request->email;
        if (isset($request->password)) {
            $request->validate([
                'password' => 'required|confirmed|min:6',
            ]);
            auth()->user()->password = Hash::make($request->password);
        }
        auth()->user()->save();

        return redirect()->back()->with("message", __("tran.Data Updated Successfully"));
    }

    public function stats_index()
    {
        $title_tag = "Reporting";
        $title_on_page = "Reporting";
        $form_url = route("stats-process");
        $title = "Reporting";
        $icon_name = "fa-file";
        $brokers = Broker::all();
        $page = "stats-index";
        return view("stats", compact('title', 'icon_name',"brokers","page", "title_on_page", "title_tag", "form_url"));
    }

    public function stats_process(Request $request)
    {
        $title_tag = "Reporting";
        $title_on_page = "Reporting";
        $form_url = route("stats-process");
        $range_start = "2000-01-01";
        $range_end = "2099-12-31 23:59:59";
        $result = collect();
        $range_type = "FPD";

        if($request->has('range_type')&&!empty($range_type))
        {
            $range_type = $request->range_type;
        }
        if(!empty($request->min))
        {
            $range_start = $request->min;
        }

        if(!empty($request->max))
        {
            $range_end = $request->max;
        }
        $loads_data = Load::with(['consignee', 'shipper']);
        
        
        if (isset($request->broker_id) && $request->broker_id)
            $loads_data = $loads_data->where("broker_id", $request->broker_id);
        $loads_data = $loads_data->get();

        
        switch($request->range_type)
        {
            case "FPD":
                foreach($loads_data as $load)
                {
                    if($load->shipper&&$load->shipper->count())
                    {
                        $shipper = $load->shipper[0];
                        
                        if(!empty($shipper->start_periode))
                        {
                            
                            if(Carbon::parse($shipper->start_periode) >= Carbon::parse($range_start)&&Carbon::parse($shipper->start_periode) <= Carbon::parse($range_end))
                            {
                                $result->push($load);
                            }
                        }
                        else
                        {
                            //dd(Carbon::parse($range_start));
                            if(!empty($shipper->pickup_date))
                            {
                                if(Carbon::parse($shipper->pickup_date) >= Carbon::parse($range_start)&&Carbon::parse($shipper->pickup_date) <= Carbon::parse($range_end))
                                {
                                    $result->push($load);
                                }
                            }else
                            {
                                if(empty($request->min)&&empty($reqeust->max))
                                {
                                    $result->push($load);
                                }
                            }
                            
                        }
                    }

                }
            break;

            case "LDD":
                foreach($loads_data as $load)
                {
                    
                    if($load->consignee&&$load->consignee->count())
                    {
                        
                        $consignees = $load->consignee;
                        
                        $consignee = $consignees[$consignees->count()-1];
                       
                        if($consignee->start_periode)
                        {
                            if(Carbon::parse($consignee->start_periode) >= Carbon::parse($range_start)&&Carbon::parse($consignee->start_periode) <= Carbon::parse($range_end))
                            {
                                $result->push($load);
                            }
                        }else
                        {
                            if($consignee->dropoff_time)
                            {
                                
                                if(Carbon::parse($consignee->dropoff_time) >= Carbon::parse($range_start)&&Carbon::parse($consignee->dropoff_time) <= Carbon::parse($range_end))
                                { 
                                    //return $consignees;
                                    $result->push($load);
                                }
                            }else
                            {
                                if(empty($request->min)&&empty($reqeust->max))
                                {
                                    $result->push($load);
                                }
                            }
                            
                        }
                        
                    }
                }
            break;
            
            
            default:
                foreach($loads_data as $load)
                {
                    
                    if($load->shipper&&$load->shipper->count())
                    {
                        $shipper = $load->shipper[0];
                        if(!empty($shipper->start_periode))
                        {
                            
                            if(Carbon::parse($shipper->start_periode) >= Carbon::parse($range_start)&&Carbon::parse($shipper->start_periode) <= Carbon::parse($range_end))
                            {
                                $result->push($load);
                            }
                        }
                        else
                        {
                            if(!empty($shipper->pickup_date))
                            {
                                if(Carbon::parse($shipper->pickup_date) >= Carbon::parse($range_start)&&Carbon::parse($shipper->pickup_date) <= Carbon::parse($range_end))
                                {
                                    $result->push($load);
                                }
                            }else
                            {
                                if(empty($request->min)&&empty($reqeust->max))
                                {
                                    $result->push($load);
                                }
                            }
                            
                        }
                    }

                }
            break;
        }
        $loads = $result->count();

        if ($loads) {
            $total_revenue = $result->sum("cost")+$result->sum("profit");
            $avg_revenue = round($total_revenue / $loads, 2);
            $miles = $result->sum("miles");
            $avg_revenue_per_mile = round($total_revenue / $miles, 2);
            $avg_miles = round($miles / $loads, 2);
            $total_cost = round($result->sum("cost"));
            $avg_cost = round($total_cost / $loads, 2);
            $dead_head_miles = $result->sum("dead_head_miles");
        } else {
            $total_revenue = 0;
            $avg_revenue = 0;
            $miles = 0;
            $avg_revenue_per_mile = 0;
            $avg_miles = 0;
            $total_cost = 0;
            $avg_cost = 0;
            $dead_head_miles = 0;
        }

        $min = $request->min;
        $max = $request->max;

        $brokers = Broker::all();
        $broker_selected = $request->broker_id;


        return view("stats", compact("dead_head_miles", "broker_selected", "brokers", "title_on_page", "title_tag", "form_url", "loads", "total_revenue", "avg_revenue"
            , "avg_revenue_per_mile", "miles", "avg_miles", "total_cost", "avg_cost", "min", "max", "range_type"
        ));
    }


    public function send_document_email(Request $request)
    {
        $toEmail = $request->email_to_factoring;
        $data = [];
        if(empty($toEmail))
        {
            return response()->json(['status'=>'error', 'message'=> 'Please Insert a valid Email']);
        }

        
        $reference = $request->loadReference;
        $brokerId = $request->brokerId;
        $totalIncome = $request->totalIncome;
        $borkerCompany = "";
        $msgs = "";
        $broker = Broker::where('id', $brokerId)->first();
        $input = [];
        $urls = [];

        if ($request->hasFile('rate_con')) {
            $fileSize = $request->file('rate_con')->getSize();

         
           
            if($fileSize>7000000)
            {
                return response()->json(['status'=>'error', 'message'=> 'Maxium file size of Rate Con  limited 2MB.']);
            }
            
        }
        if ($request->hasFile('bill_of_loading')) {
            $fileSize = $request->file('bill_of_loading')->getSize();
            if($fileSize>7000000)
            {
                return response()->json(['status'=>'error', 'message'=> 'Maxium file size of Bill of Loading  limited 2MB.']);
            }
        }
        if ($request->hasFile('proof_delivery')) {
            $fileSize = $request->file('proof_delivery')->getSize();
            if($fileSize>7000000)
            {
                return response()->json(['status'=>'error', 'message'=> 'Maxium file size of Proof of Delivery  limited 2MB.']);
            }
        }
        if ($request->hasFile('lumper_recepit')) {
            $fileSize = $request->file('lumper_recepit')->getSize();
            if($fileSize>7000000)
            {
                return response()->json(['status'=>'error', 'message'=> 'Maxium file size of Lumper Recepit  limited 2MB.']);
            }
        }
        if ($request->hasFile('other')) {
            $fileSize = $request->file('other')->getSize();
            if($fileSize>7000000)
            {
                return response()->json(['status'=>'error', 'message'=> 'Maxium file size of Other limited 2MB.']);
            }
        }
        if ($request->hasFile('scale_ticket')) {
            $fileSize = $request->file('scale_ticket')->getSize();
            if($fileSize>7000000)
            {
                return response()->json(['status'=>'error', 'message'=> 'Maxium file size of Scale Ticket  limited 2MB.']);
            }
        }
        
        
        if($broker)
        {
            $borkerCompany = $broker->company;
        }

        if ($request->hasFile('rate_con')) {
            
        
            $fileName = time().'rate_con.'.$request->file('rate_con')->extension();  
           
         
            $request->file('rate_con')->move(public_path('documents'), $fileName);
            
            $input['rate_con'] = '/public/documents/'.$fileName;
            array_push($urls, url('/').$input['rate_con']);
            $data['rate_con'] = '/public/documents/'.$fileName;
        }else
        {
            $load = Document::where('load_id', $request->load_id)->first();
            if($load&&!empty($load->rate_con))
            {
                array_push($urls, url('/').$load->rate_con);
            }

        }
       
        if ($request->hasFile('bill_of_loading')) {
            
        
            $fileName = time().'bill_of_loading.'.$request->file('bill_of_loading')->extension();  
           
         
            $request->file('bill_of_loading')->move(public_path('documents'), $fileName);
            
            $input['bill_of_loading'] = '/public/documents/'.$fileName;
            array_push($urls, url('/').$input['bill_of_loading']);
            $data['bill_of_loading'] = '/public/documents/'.$fileName;
        }else
        {
            $load = Document::where('load_id', $request->load_id)->first();
            if($load&&!empty($load->bill_of_loading))
            {
                array_push($urls, url('/').$load->bill_of_loading);
            }
        }


        if ($request->hasFile('proof_delivery')) {
            
        
            $fileName = time().'proof_delivery.'.$request->file('proof_delivery')->extension();  
           
         
            $request->file('proof_delivery')->move(public_path('documents'), $fileName);
            
            $input['proof_delivery'] = '/public/documents/'.$fileName;
            array_push($urls, url('/').$input['proof_delivery']);
            $data['proof_delivery'] = '/public/documents/'.$fileName;
        }else
        {
            $load = Document::where('load_id', $request->load_id)->first();
            if($load&&!empty($load->proof_delivery))
            {
                array_push($urls, url('/').$load->proof_delivery);
            }
        }

        if ($request->hasFile('lumper_recepit')) {
            
        
            $fileName = time().'lumper_recepit.'.$request->file('lumper_recepit')->extension();  
           
         
            $request->file('lumper_recepit')->move(public_path('documents'), $fileName);
            
            $input['lumper_recepit'] = '/public/documents/'.$fileName;
            array_push($urls, url('/').$input['lumper_recepit']);
            $data['lumper_recepit'] = '/public/documents/'.$fileName;
        }else
        {
            $load = Document::where('load_id', $request->load_id)->first();
            if($load&&!empty($load->lumper_recepit))
            {
                array_push($urls, url('/').$load->lumper_recepit);
            }
        }

        if ($request->hasFile('other')) {
            
        
            $fileName = time().'other.'.$request->file('other')->extension();  
           
         
            $request->file('other')->move(public_path('documents'), $fileName);
            
            $input['other'] = '/public/documents/'.$fileName;
            array_push($urls, url('/').$input['other']);
            $data['other'] = '/public/documents/'.$fileName;
        }else
        {
            $load = Document::where('load_id', $request->load_id)->first();
            if($load&&!empty($load->other))
            {
                array_push($urls, url('/').$load->other);
            }
        }

        if ($request->hasFile('scale_ticket')) {
            
        
            $fileName = time().'scale_ticket.'.$request->file('scale_ticket')->extension();  
           
         
            $request->file('scale_ticket')->move(public_path('documents'), $fileName);
            
            $input['scale_ticket'] = '/public/documents/'.$fileName;
            array_push($urls, url('/').$input['scale_ticket']);
            $data['scale_ticket'] = '/public/documents/'.$fileName;
        }else
        {
            $load = Document::where('load_id', $request->load_id)->first();
            if($load&&!empty($load->scale_ticket))
            {
                array_push($urls, url('/').$load->scale_ticket);
            }
        }


        $data['email_to_factoring']  = $request->email_to_factoring;
        $data['POD']  = $request->POD;
        $data['CC']  = $request->CC;
        $data['load_id'] = $request->load_id;

        Document::updateOrCreate(['load_id'=>$request->load_id],$data);
        
        
        if(!$request->has('disable_email'))
        {
            $data['email'] = $toEmail;
            $data['subject'] = $request->load_id." ".$borkerCompany." Load # ".$reference ." - $".$totalIncome;
    

            $msgs = $msgs."Hello, <br> <br> Attached you’ll find the legible documents for POD and Rate Confirmation for the following load<br>";
            $msgs = $msgs."<ul><li>".$borkerCompany." Load # ".$reference." - $".$totalIncome."</li></ul><br>";

            // foreach($urls as $val)
            // {
            //     $msgs =  $msgs.$val."<br>";
            // }
            
            $msgs = $msgs."<br><br>If you have any questions or concerns regarding the legibility of the attached documents, please let us
            know and we’ll be glad to help out.<br><br><br><br>";
            
            
            $msgs = $msgs."Thank you,<br><br><br><br>";
            
            $msgs = $msgs."<b>ACCOUNTING</b><br><br>";
            $msgs = $msgs."<b>MILAM TRANSPORT, LLC<br><br>";
            $msgs = $msgs."<b>PO BOX 47083 TAMPA, FL 33646<br><br>";
            $msgs = $msgs."<b>P: 1(888)433-0331 Ext 3 | F: (813)315-6260<br><br>";
            $msgs = $msgs."<b>ACCOUNTING@MILAMTRANS.COM<br><br>";
            $msgs = $msgs."<b>MC 051238 | USDOT 3053736<br><br>";
            $msgs = $msgs."<b>“Change starts with us…”<br><br>";
            $params = [
                'msgs'=>$msgs
            ];
            Mail::send('emails.mailevent', $params, function($message) use ($data, $urls){
                //dd($data);
            $message->to([$data['email'],$data['CC'],$data['POD']], '')->subject
                ($data['subject']);
            $message->from('accounting@milamtrans.com','');

            foreach ($urls as $file){
                    $message->attach($file);
                }
            });
            return response()->json(['status'=>'success', 'message'=> 'Email Sent Successfully.']);
        }
        
        return response()->json(['status'=>'success', 'message'=> 'Document Saved successfully.']);
    }

    public function remove_document($id, $query)
    {
        Document::where('id', $id)->update([$query=>NULL]);

        return response()->json(['status'=>'success']);
    }

    public function load_invoice($id)
    {
        $load = Load::with(['accessories', 'customer'])->where("id", $id)->first();
        //return view('invoice.load_invoice', compact('load'));
        $html = view('invoice.load_invoice', compact('load'))->render();
        $selected_template = str_replace(["[\$invitoremail]", "[\$login]"], [Auth::user()->name, route('login')], $html);
        //    dd($selected_template);
        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('debugPng', true);
        $options->set('isJavascriptEnabled', true);
        $options->set('debugKeepTemp', true);
        //        $options->set('',true);
        //        $options->set('',true);

        $dompdf = new Dompdf();
        $dompdf->setOptions($options);

        //$dompdf->loadHtmlFile("https://codebeautify.org/html-to-php-converter");

        $dompdf->loadHtml($selected_template);
        $dompdf->render();

        $output = $dompdf->output();
        //        dd($output);

        $time = time();
        $name = "Settlement_" . $time . ".pdf";
        //file will be stored in storage/app/
        $created_file = Storage::disk('local')->put('pdf_created/' . $name, $output);

        // check project running from public or not
        if (explode("/", asset('/'))[count(explode("/", asset('/'))) - 2] == "public") {
            $file_location = explode("/public", asset('/'))[0] . '/storage/app/pdf_created/' . $name;
        } else
            $file_location = Url('/') . '/storage/app/pdf_created/' . $name;

        
            return redirect($file_location);
    }
}
