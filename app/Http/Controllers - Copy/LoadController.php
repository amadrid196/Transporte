<?php

namespace App\Http\Controllers;

use App\Models\AccessorialLoad;
use App\Models\Consignee;
use App\Models\Deduction;
use App\Models\Load;
use App\Models\MessageRecord;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SmsTemplate;
use Auth;
use stdClass;

class LoadController extends Controller
{
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("loads-create");

        $title_tag = "Loads";

        $title = "Load Management";
        $icon_name = "fa-list-alt";

        $table_headers = [
            __("Load ID"), __("Load Status"), __('Last Contact'), __("Customers"), __("Picks"), __("Pick Date"),
            __("Drops"),  __("Drop Date"),  __("tran.Driver"), __("tran.Power Unit"), //tractor
            __("tran.Trailer"), __("Distance"),
            __("tran.Income"), __("tran.Expenses"), __("tran.Reference")
        ];
        $ajax_data_getting_url = route("loads-ajax");
        //        $min_max_filter = ["value" => true, "min" => 8, "max" => 8, "type" => "date"];

        $multi_select = true;
        $from1 = new stdClass();
        $from1->route = route("loads-change-status-select");
        $from1->method = "post";
        $from1->btn_class = "btn btn-xs btn-success";
        $from1->button_txt = __("tran.Change") . " " . __("tran.Status");
        $multiselect_forms[0] = $from1;
        return view("loads.index", compact("multi_select", "show_add_button", "add_button_link", "title_tag", "table_headers", "ajax_data_getting_url", "title", "icon_name"));
    }

    public function loads_ajax($categoryId = 0)
    {
        $ACTIVE_LOADS = 0;
        $PLANNING_LOADS = 1;
        $READY_LOADS = 2;
        $ALL_LOADS = 3;
        $MY_LOADS = 4;


        $table_data = Load::with("driver", "shipper", "tractor", "trailer", "consignee", 'broker', 'messageRecords');

        //        dd($table_data);
        switch ($categoryId) {
            case $ACTIVE_LOADS:
                $table_data = $table_data->where('status', '!=', 'Completed')->get();
                break;
            case $PLANNING_LOADS:
                $table_data = $table_data->where('status', 'Pending')->get();
                break;
            case $READY_LOADS:
                $table_data = $table_data->where('status', 'Completed')->get();
                break;
            case $ALL_LOADS:
                $table_data = $table_data->get();
                break;
            case $MY_LOADS:
                $table_data = $table_data->where('user_id', Auth::user()->id)->get();
                break;

            default:
                $table_data = $table_data->where('status', '!=', 'Compeleted')->get();
                break;
        }
        $data = [];
        $data["columns"] = [
            array("data" => "id", 'render' => "
            \"<a href=\"+base_url+\"/loads-edit/\"" . '+ data + ' . "\"> \"+data+\"</a>\" "),

            array("data" => "status"),

            array("data" => "id", "render" => "
            var options = {
                year: \"numeric\",
                month: \"2-digit\",
                day: \"numeric\",
                hour: \"numeric\",
                minute: \"numeric\"
            };
            if(row.message_records.length != 0)
            {
              var legnth = row.message_records.length-1;
              var date = new Date(row.message_records[legnth].created_at).toLocaleDateString(\"en\", options);

              date;
            }else
            ''
            "),
            array(
                "data" => "id", "render" =>
                "
                if(row.broker)
                row.broker.company;
                else
                ''
                "
            ),
            //            pickup adddress
            array(
                "data" => "id", "render" =>
                "
                 if(row.shipper){
                   var shippers=\"\";
                    row.shipper.forEach(function (currentValue, index, arr) {
                         var current_address= currentValue.pickup_address;

                         if(current_address!= null && current_address !=\"\")
                         {
                         var splited=current_address.split(\",\");
                            if(splited.length>3)
                              shippers=shippers+(index+1)+'. '+splited[splited.length-3]+' '+splited[splited.length-2]+' '+splited[splited.length-1]+'<br>'
                         }else
                         {
                             ''
                         }

                    });
                   shippers;
                 }
                 else
                  ''
                 "
            ),
            //            pickup_date
            array(
                "data" => "id", "render" =>
                "
                if(row.shipper){
                 var shippers=\"\";
                 var options = {
                    year: \"numeric\",
                    month: \"2-digit\",
                    day: \"numeric\",
                    hour: \"numeric\",
                    minute: \"numeric\"
                };
                var date = \"\"
                  if(row.shipper.length>0)
                  {

                    row.shipper.forEach(function (currentValue, index, arr) {

                        if(currentValue.start_periode&&currentValue.end_periode)
                        {
                            date = date + new Date(currentValue.start_periode).toLocaleDateString(\"en\", options)+'<br>'
                        }else{
                            date = date + new Date(currentValue.pickup_date).toLocaleDateString(\"en\", options)+'<br>'
                        }
                    })

                    date
                  }

                  else
                    ''
                } else
                  ''
            "
            ),

            //            dropoff_address
            array(
                "data" => "id", "render" =>
                "
                if(row.consignee){
                var consignee=\"\";
                    row.consignee.forEach(function (currentValue, index, arr) {
                        var current_address= currentValue.dropoff_address;

                        if(current_address!= null && current_address !=\"\")
                        {
                        var splited=current_address.split(\",\");

                            if(splited.length>3)
                            {
                                if(splited.length>4)
                                    consignee=consignee+(index+1)+'. '+splited[splited.length-4]+' '+splited[splited.length-3]+' '+splited[splited.length-2]+' '+splited[splited.length-1]+'<br>'
                                else
                                    consignee=consignee+(index+1)+'. '+splited[splited.length-3]+' '+splited[splited.length-2]+' '+splited[splited.length-1]+'<br>'
                            }



                        }else
                        {
                            ''
                        }

                    });
                    consignee;
                }
                else
                ''
                "
            ),
            //            dropoff_time
            array(
                "data" => "id", "render" =>
                "
                if(row.consignee){
                 var consignee=\"\";
                 var options = {
                    year: \"numeric\",
                    month: \"2-digit\",
                    day: \"numeric\",
                    hour: \"numeric\",
                    minute: \"numeric\"
                };
                var date = \"\"
                  if(row.consignee.length>0)
                  {

                    row.consignee.forEach(function (currentValue, index, arr) {

                        if(currentValue.start_periode&&currentValue.end_periode)
                        {
                            date = date + new Date(currentValue.start_periode).toLocaleDateString(\"en\", options)+'<br>'
                        }else{
                            date = date + new Date(currentValue.dropoff_time).toLocaleDateString(\"en\", options)+'<br>'
                        }
                    })

                    date
                  }

                  else
                    ''
                } else
                  ''
                 "
            ),
            array(
                "data" => "id", "render" =>
                "
                     if(row.driver)
                     row.driver.name;
                     else
                     ''
                     "
            ),



            array(
                "data" => "id", "render" =>
                "
                if(row.tractor)
                row.tractor.pun;
                else
                ''
                "
            ),
            array(
                "data" => "id", "render" =>
                "
                if(row.trailer)
                row.trailer.number;
                else
                ''
                "
            ),

            array("data" => "miles", "render" => "
            if(row.miles == null)
            0
            else
            {
                var miles =  row.miles.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                miles;
            }

            "),
            array("data" => "profit", "render" => "
                if(row.profit == null||row.profit == 0)
                '$'+0
                else
                {
                    var profit = '$'+ row.profit.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                    profit;
                }
            "),

            //            consignee
            //            array("data" => "id"
            //            , "render" =>
            //                "
            //                 if(row.consignee){
            //                var consignees=\"\";
            //                 row.consignee.forEach(function (currentValue, index, arr) {
            //                 console.log(currentValue.consignee);
            //                   if (consignees === \"\")
            //                  consignees = currentValue.customer.fname+' '+currentValue.customer.lname;
            //                  else
            //                  consignees = currentValue.customer.fname+' '+currentValue.customer.lname + \" , \" + consignees;
            //
            //                  });
            //                  consignees;
            //                }
            //                else
            //                ''
            //                "
            //            ),
            //
            //            array("data" => "customer.groups",
            //                "render" =>
            //                    "let groups = \"No Group\";
            //                            data.forEach(function (currentValue, index, arr) {
            //                                if (groups === \"No Group\") {
            //                                    groups = currentValue.title;
            //                                } else {
            //                                    groups = currentValue.title + \", \" + groups;
            //                                }
            //                            });
            //                            groups;"
            //            ),
            //            array("data" => "gender",
            //                "render" => "
            //                if(row.gender=='m')
            //                'Male';
            //                else if(row.gender=='f')
            //                'Female';
            //                else
            //                '';
            //                "
            //            ),
            //            array("data" => "customer.groups",
            //                "render" =>
            //                    "let groups = \"No Group\";
            //                            data.forEach(function (currentValue, index, arr) {
            //                                if (groups === \"No Group\") {
            //                                    groups = currentValue.title;
            //                                } else {
            //                                    groups = currentValue.title + \", \" + groups;
            //                                }
            //                            });
            //                            groups;"
            //            ),
            //            array("data" => "category.title",
            //                "render" =>
            //                    "if(data){
            //                data;
            //                }
            //                else{
            //                \"Not Changed\"
            //                }"
            //
            //            ),
            //            array("data" => "hide"),
            //            array("data" => "agent_assign_to.name",
            //                "render" =>
            //                    "if(data){
            //                data;
            //                }
            //                else{
            //                \"Not Assigned\"
            //                }"
            //
            //            ),
            //            array("data" => "created_at",
            //                "render" =>
            //                    "var month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            //var x=new Date(data);
            //var y='AM';
            //var hr=x.getHours();
            //var min=x.getMinutes();
            //var sec=x.getSeconds();
            //var month=(x.getMonth()+1);
            //var date=x.getDate();
            //
            //if(date<10){
            //date='0'+date;
            //}
            //if(month<10){
            //month='0'+month;
            //}
            //if(x.getHours()>12){
            // y='PM';
            // hr=hr-12;
            //}
            //if(hr<10){
            //hr='0'+hr;
            //}
            //if(min<10){
            //min='0'+min;
            //}
            //if(sec<10){
            //sec='0'+sec;
            //}
            ////date + '/' + month + '/' + x.getFullYear() + ' ' +hr + ':' +min+':'+sec+' '+y
            //x"
            //            ),
            //            array("data" => "value"),
            //for Expenses

            array("data" => "cost", "render" => "
            if(row.cost == null||row.cost == 0)
            '$'+0
            else
            {
                var data = '$'+ row.cost.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                data;
            }
            "),


            array("data" => "reference"),

            // array("data" => "id",
            //     "render" =>

            //         "console.log(row)
            //          var classes='btn btn-block btn-success btn-xs sms-button';
            //          var classes2 = 'btn btn-block btn-success btn-xs'
            //          var disabled = ''
            //          if(row.driver_id == null)
            //          {
            //             classes = 'btn btn-block btn-muted btn-xs sms-button'
            //             disabled = 'disabled'
            //          }

            //         \"<a href=\"+base_url+\"/loads-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
            //         //                        "\"<a href=\"+base_url+\"/loads-deduction-single/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>Add Deduction</button></a>" .
            //        "<a href=\"+base_url+\"/smstemplate/\" + data + \"><button type='button' class='\"+classes2+\"' \"+disabled+\">Edit SMS</button></a>".
            //         "<a href=\"+base_url+\"/loads-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>".
            //         "<button type='button' class='\"+classes+\"' \"+disabled+\" value='\"+row.id+\"' >".__("tran.Send SMS")."</button>\""


            // )
        ];
        $data["columnDefs"] = [

            array("targets" => 4, "createdCell" => "", "width" => 150),
            array("width" => 100, "targets" => 5),
            array("width" => 150, "targets" => 6),
            array("width" => 100, "targets" => 7),
        ];


        // $data["buttons"] = [
        //     array(
        //         "text" => 'Select All',
        //         "className" => 'btn btn-xs btn-info',
        //         "action" => "table.rows({search: 'applied'}).select();"
        //     ),
        //     array("extend" => 'selectNone', "className" => 'btn btn-xs btn-danger')
        // ];

        //        $data["createdRow"] = "
        //         if (data.category && data.category.reminder==\"true\")
        //        $(row).addClass('reminder');
        //        ";

        $data["data"] = $table_data;

        return json_encode($data);
    }

    public function create()
    {
        $pageTitle = "Load Create";
        $url = route("loads-store");
        return view("loads.create", compact("pageTitle", "url"));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        // $request->validate([
        // 'driver_id' => 'required|string|min:1',
        // 'trailer_id' => 'required|string|min:1',
        // 'tractor_id' => 'required|string|min:1',
        // 'value' => 'required|string|min:1',
        // 'miles' => 'required|string|min:1',
        // "accesorials" => "required|array|min:1",
        // "accesorials2" => "required|array|min:1",

        // "consignee_number" => "required|array|min:1",
        //    old Comment
        //"payable" => "required|array|min:1",
        //"consignee_number.*" => "required|string|min:1|max:20",
        //"shipper_number" => "required|array|min:1",
        //"shipper_number.*" => "required|string|min:1|max:20",
        //"broker_id" => "required|string|min:1",
        //"shipper_id" => "required|array|min:1",
        //"shipper_id.*" => "required|string|min:1",
        //"consignee_id" => "required|array|min:1",
        //"consignee_id.*" => "required|string|min:1",
        // ]);


        if (empty($request->broker_id)) {
            $request->broker_id = 0;
        }
        $load = new Load();

        $load->driver_id = $request->driver_id;
        $load->trailer_id = $request->trailer_id;
        $load->tractor_id = $request->tractor_id;
        $load->reference = $request->reference;
        $load->value = $request->value;
        $load->broker_id = $request->broker_id;
        $load->status = $request->status;
        $load->miles = $request->miles;
        $load->cost = preg_replace("/[^0-9.]/", "", $request->cost);
        $load->profit = preg_replace("/[^0-9.]/", "", $request->profit);
        $load->address = $request->current_address;
        $load->dead_head_miles = $request->dead_head_miles;

        $load->save();

        //accessorials
        //        $load->accessories = $request->accesorials;
        //        if ($request->accessories != 1) {
        //            $load->accessory_value = $request->accessory_value;
        //        }
        $accessorials = [];
        //income
        foreach ($request->accesorials as $key => $value) {
            $accessory = new AccessorialLoad([
                "accessorial_id" => $request->accesorials[$key],
                'note' => isset($request->note_a1ccesorial[$key]) ? $request->note_a1ccesorial[$key] : null,
                "type" => "income",
                "payable_to_driver" => false,
                "value" => isset($request->accessory_value[$key]) ? preg_replace("/[^0-9.]/", "", $request->accessory_value[$key]) : null,
                "quantity" => isset($request->quantity_a1ccesorial[$key]) ? $request->quantity_a1ccesorial[$key] : 0,
                "rate" => isset($request->rate_a1ccesorial[$key]) ?  preg_replace("/[^0-9.-]/", "", $request->rate_a1ccesorial[$key]) : 0
            ]);
            array_push($accessorials, $accessory);
        }
        //expense
        foreach ($request->accesorials2 as $key => $value) {
            $accessory = new AccessorialLoad([
                "accessorial_id" => $request->accesorials2[$key],
                "type" => "expense",
                "quantity" => isset($request->quantity_a2ccesorial[$key]) ? $request->quantity_a2ccesorial[$key] : 0,
                "rate" => isset($request->rate_a2ccesorial[$key]) ? preg_replace("/[^0-9.-]/", "", $request->rate_a2ccesorial[$key]) : 0,
                'note' => isset($request->note_a2ccesorial[$key]) ? $request->note_a2ccesorial[$key] : null,
                "payable_to_driver" => isset($request->payable[$key]) ? true : false,
                "value" => isset($request->accessory_value2[$key]) ? preg_replace("/[^0-9.]/", "", $request->accessory_value2[$key]) : null
            ]);
            array_push($accessorials, $accessory);
        }

        $load->accessories()->saveMany($accessorials);

        //deductions
        $deductions = [];
        foreach ($request->accesorials3 as $key => $value) {

            $deduction = new Deduction([
                "accessorial_id" => $request->accesorials3[$key],

                'title' => isset($request->note_a3ccesorial[$key]) ? $request->note_a3ccesorial[$key] : "",
                "quantity" => isset($request->quantity_a3ccesorial[$key]) ? $request->quantity_a3ccesorial[$key] : 0,
                "rate" =>  isset($request->rate_a3ccesorial[$key]) ? preg_replace("/[^0-9.-]/", "", $request->rate_a3ccesorial[$key]) : 0,
                "value" => isset($request->accessory_value3[$key]) ? preg_replace("/[^0-9.-]/", "", $request->accessory_value3[$key]) : 0
            ]);
            array_push($deductions, $deduction);
        }

        $load->deductions()->saveMany($deductions);

        //consignees
        $consignees = [];
        if (!empty($request->consignee_id) && $request->consignee_id[0] != null) {
            foreach ($request->consignee_id as $key => $value) {
                $consignee = new Consignee([
                    "customer_id" => $request->consignee_id[$key],
                    "dropoff_time" => Carbon::create($request->dropoff_date[$key]),
                    "dropoff_address" => $request->consignee_address[$key],
                    "note" => $request->consignee_notes[$key],
                    "hash" => $request->consignee_hash[$key],
                    "start_periode" => $request->consignee_start_periode[$key],
                    "end_periode" => $request->consignee_end_periode[$key],
                    "contact_number" => $request->consignee_number[$key],
                    "start_periode" => $request->consignee_start_periode[$key],
                    "end_periode" => $request->consignee_end_periode[$key]
                ]);
                array_push($consignees, $consignee);
            }

            $load->consignee()->saveMany($consignees);
        }

        //shippers
        $shippers = [];
        if (!empty($request->shipper_id) && $request->shipper_id[0] != null) {
            foreach ($request->shipper_id as $key => $value) {

                $shipper = new Shipper([
                    "customer_id" => $request->shipper_id[$key],
                    "pickup_date" => Carbon::create($request->pickup_date[$key]),
                    "pickup_address" => $request->shipper_address[$key],
                    "contact_number" => $request->shipper_number[$key],
                    "shipper_hash" => $request->shipper_hash[$key],
                    "start_periode" => $request->shipper_start_periode[$key],
                    "end_periode" => $request->shipper_end_periode[$key],
                    "note" => $request->shipper_notes[$key],
                    "start_periode" => $request->shipper_start_periode[$key],
                    "end_periode" => $request->shipper_end_periode[$key]
                ]);
                array_push($shippers, $shipper);
            }


            $load->shipper()->saveMany($shippers);
        }

        if (env("SEND_SMS") && !empty($load->driver_id) && $request->send_sms != "false") {
            //send sms to driver
            $sms = SmsTemplate::where('load_id', $load->id)->first();
            $msg = "";
            if ($sms) {
                $msg = $sms->content;
            } else {
                $msg = "Hi " . $load->driver->name . "\n";
                foreach ($load->shipper as $key => $value) {
                    $msg = $msg . "\nPickup " . ($key + 1) . ":\n\nPickup date and time:\n";
                    if (!empty($value->pickup_date)) {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        } else {
                            $msg = $msg .  date("m/d/Y h:i.a", strtotime($value->pickup_date)) . "\n";
                        }
                    } else {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        }
                    }

                    $msg = $msg . "Location address:\n" . $value->customer->company . "\n" . $value->pickup_address . "\nPhone:\n" . $value->contact_number . "\nPickup #: " . $value->shipper_hash . "\n";
                    if ($value->note !== null && $value->note !== "") {
                        $msg = $msg . "Pickup Note:\n" . $value->note . "\n";
                    }
                }

                foreach ($load->consignee as $key => $value) {
                    $msg = $msg . "\nDelivery " . ($key + 1) . ":\n\nDelivery date and time:\n";
                    if (!empty($value->dropoff_time)) {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg  . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        } else {
                            $msg = $msg . date("m/d/Y h:i.a", strtotime($value->dropoff_time)) . "\n";
                        }
                    } else {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg  . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        }
                    }
                    $msg =   $msg . "\nLocation address:\n" . $value->customer->company . "\n" . $value->dropoff_address . "\nPhone:\n" . $value->contact_number  . "\nnDelivery #: " . $value->hash . "\n";
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

            if (!$response["status"]) {
                return redirect()->back()->with("error", __("tran.Error in sending message.") . "\n" . $response["error"]);
            }
        }

        if ($load) {

            if ($request->ajax()) {
                return response()->json(['success' => true, 'id' => $load->id]);
            } else {
                return redirect()->back()->with("message", __("tran.Data saved successfully"));
            }
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = Load::where("id", $id)->with("driver", "accessories", "shipper", "tractor", "trailer", "consignee", "deductions")->first();
        //        dd($data);
        $apiKey = env("MAP_API");
        $pageTitle = "Load Edit";
        $url = route("loads-update", $id);
        $lat = 0;
        $long = 0;

        if ($data->address != "") {
            $address = $data->address;
            $address = urlencode($address);
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";

            // get the json response
            $resp_json = file_get_contents($url);
            $resp = json_decode($resp_json, true);


            if ($resp['status'] == 'OK') {

                // get the important data
                $lat = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
                $long = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
            }
        }

        return view("loads.create", compact("pageTitle", "url", "data", 'lat', 'long'));
    }

    public function update(Request $request, $id)
    {
        $send_sms = false;
        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }

        // $request->validate([
        // 'driver_id' => 'required|string|min:1',
        // 'trailer_id' => 'required|string|min:1',
        // 'tractor_id' => 'required|string|min:1',
        // 'value' => 'required|string|min:1',
        // 'miles' => 'required|string|min:1',
        //old
        //"consignee_number" => "required|array|min:1",
        //"consignee_number.*" => "required|string|min:1|max:20",
        //"shipper_number" => "required|array|min:1",
        //"shipper_number.*" => "required|string|min:1|max:20",
        //"shipper_id" => "required|array|min:1",
        //"shipper_id.*" => "required|string|min:1",
        //"consignee_id" => "required|array|min:1",
        //"consignee_id.*" => "required|string|min:1",
        // ]);
        if (empty($request->broker_id)) {
            $request->broker_id = 0;
        }
        $load = Load::findorfail($id);

        $load->driver_id = $request->driver_id;
        $load->trailer_id = $request->trailer_id;
        $load->tractor_id = $request->tractor_id;
        $load->broker_id = $request->broker_id;
        $load->reference = $request->reference;
        $load->start_periode  = $request->start_periode;
        $load->end_periode = $request->end_periode;
        $load->value = 0;
        //        $load->accessories = $request->accesorials;
        //        if ($request->accessories != 1) {
        //            $load->accessory_value = $request->accessory_value;
        //        } else {
        //            $load->accessory_value = null;
        //        }
        if (env("SEND_SMS") && !empty($request->driver_id) && $request->send_sms && $request->send_sms != "false") {
            //send sms to driver
            $send_sms = true;
        }

        $load->status = $request->status;
        $load->miles = $request->miles;
        $load->cost = preg_replace("/[^0-9.]/", "", $request->cost);
        $load->profit = preg_replace("/[^0-9.]/", "", $request->profit);
        $load->address = $request->current_address;
        $load->dead_head_miles = $request->dead_head_miles;
        $load->save();

        $load->accessories()->delete();
        $load->deductions()->delete();
        // accessorials
        //        $load->accessories = $request->accesorials;
        //        if ($request->accessories != 1) {
        //            $load->accessory_value = $request->accessory_value;
        //        }
        $accessorials = [];
        foreach ($request->accesorials as $key => $value) {
            $accessory = new AccessorialLoad([
                "accessorial_id" => $request->accesorials[$key],
                'note' => isset($request->note_a1ccesorial[$key]) ? $request->note_a1ccesorial[$key] : null,
                "type" => "income",
                "payable_to_driver" => false,
                "value" => isset($request->accessory_value[$key]) ? preg_replace("/[^0-9.]/", "", $request->accessory_value[$key]) : null,
                "quantity" => isset($request->quantity_a1ccesorial[$key]) ? $request->quantity_a1ccesorial[$key] : 0,
                "rate" => isset($request->rate_a1ccesorial[$key]) ?  preg_replace("/[^0-9.-]/", "", $request->rate_a1ccesorial[$key]) : 0
            ]);
            array_push($accessorials, $accessory);
        }

        foreach ($request->accesorials2 as $key => $value) {
            $accessory = new AccessorialLoad([
                "accessorial_id" => $request->accesorials2[$key],
                "type" => "expense",
                'note' => isset($request->note_a2ccesorial[$key]) ? $request->note_a2ccesorial[$key] : null,
                "quantity" => isset($request->quantity_a2ccesorial[$key]) ? $request->quantity_a2ccesorial[$key] : 0,
                "rate" => isset($request->rate_a2ccesorial[$key]) ? preg_replace("/[^0-9.-]/", "", $request->rate_a2ccesorial[$key]) : 0,
                "payable_to_driver" => isset($request->payable[$key]) ? true : false,
                "value" => isset($request->accessory_value2[$key]) ? preg_replace("/[^0-9.]/", "", $request->accessory_value2[$key]) : null
            ]);
            array_push($accessorials, $accessory);
        }

        $load->accessories()->saveMany($accessorials);
        $deductions = [];
        foreach ($request->accesorials3 as $key => $value) {

            $deduction = new Deduction([
                "accessorial_id" => $request->accesorials3[$key],

                'title' => isset($request->note_a3ccesorial[$key]) ? $request->note_a3ccesorial[$key] : "",
                "quantity" => isset($request->quantity_a3ccesorial[$key]) ? $request->quantity_a3ccesorial[$key] : 0,
                "rate" =>  isset($request->rate_a3ccesorial[$key]) ?  preg_replace("/[^0-9.-]/", "", $request->rate_a3ccesorial[$key]) : 0,
                "value" => isset($request->accessory_value3[$key]) ? preg_replace("/[^0-9.-]/", "", $request->accessory_value3[$key]) : 0
            ]);


            array_push($deductions, $deduction);
        }
        $load->deductions()->saveMany($deductions);
        //        consignees
        $load->consignee()->delete();
        if (!empty($request->consignee_id) && $request->consignee_id[0] != null) {
            $consignees = [];
            foreach ($request->consignee_id as $key => $value) {
                $active = Consignee::where("customer_id", $request->consignee_id[$key])->where("dropoff_time", Carbon::create($request->dropoff_date[$key]))
                    ->where("dropoff_address", $request->consignee_address[$key])
                    ->where("note", $request->consignee_notes[$key])
                    ->where("hash", $request->consignee_hash[$key])
                    ->where("contact_number", $request->consignee_number[$key])
                    ->where("start_periode", $request->consignee_start_periode[$key])
                    ->where('end_periode', $request->consignee_end_periode[$key])
                    ->withTrashed()->update([
                        "deleted_at" => null
                    ]);
                if ($active)
                    continue;
                $consignee = new Consignee([
                    "customer_id" => $request->consignee_id[$key],
                    "dropoff_time" => Carbon::create($request->dropoff_date[$key]),
                    "dropoff_address" => $request->consignee_address[$key],
                    "note" => $request->consignee_notes[$key],
                    "hash" => $request->consignee_hash[$key],
                    "contact_number" => $request->consignee_number[$key],
                    "start_periode" => $request->consignee_start_periode[$key],
                    "end_periode" => $request->consignee_end_periode[$key]
                ]);
                array_push($consignees, $consignee);
            }

            $load->consignee()->saveMany($consignees);
        }

        $load->shipper()->delete();
        if (!empty($request->shipper_id) && $request->shipper_id[0] != null) {
            $shippers = [];
            foreach ($request->shipper_id as $key => $value) {
                $active = Shipper::where("customer_id", $request->shipper_id[$key])
                    ->where("pickup_date", Carbon::create($request->pickup_date[$key]))
                    ->where("pickup_address", $request->shipper_address[$key])
                    ->where("contact_number", $request->shipper_number[$key])
                    ->where("shipper_hash", $request->shipper_hash[$key])
                    ->where("note", $request->shipper_notes[$key])
                    ->where("start_periode", $request->shipper_start_periode[$key])
                    ->where("end_periode", $request->shipper_end_periode[$key])
                    ->withTrashed()->update([
                        "deleted_at" => null
                    ]);

                if ($active)
                    continue;

                $shipper = new Shipper([
                    "customer_id" => $request->shipper_id[$key],
                    "pickup_date" => Carbon::create($request->pickup_date[$key]),
                    "pickup_address" => $request->shipper_address[$key],
                    "contact_number" => $request->shipper_number[$key],
                    "shipper_hash" => $request->shipper_hash[$key],
                    "note" => $request->shipper_notes[$key],
                    "start_periode" => $request->shipper_start_periode[$key],
                    "end_periode" => $request->shipper_end_periode[$key]
                ]);
                array_push($shippers, $shipper);
            }

            $load->shipper()->saveMany($shippers);
        }
        if ($send_sms) {
            //send sms to driver
            $sms = SmsTemplate::where('load_id', $load->id)
                ->where('status', 'active')
                ->first();
            $msg = "";
            if ($sms) {
                $msg = $sms->content;
            } else {
                $msg = "Hi " . $load->driver->name . "\n";


                foreach ($load->shipper as $key => $value) {
                    $msg = $msg . "\nPickup " . ($key + 1) . ":\n\nPickup date and time:\n";
                    if (!empty($value->pickup_date)) {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        } else {
                            $msg = $msg .  date("m/d/Y h:i.a", strtotime($value->pickup_date)) . "\n";
                        }
                    } else {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        }
                    }

                    $msg = $msg . "Location address:\n" . $value->customer->company . "\n" . $value->pickup_address . "\nPhone:\n" . $value->contact_number . "\nPickup #: " . $value->shipper_hash . "\n";
                    if ($value->note !== null && $value->note !== "") {
                        $msg = $msg . "Pickup Note:\n" . $value->note . "\n";
                    }
                }

                foreach ($load->consignee as $key => $value) {
                    $msg = $msg . "\nDelivery " . ($key + 1) . ":\n\nDelivery date and time:\n";
                    if (!empty($value->dropoff_time)) {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg  . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        } else {
                            $msg = $msg . date("m/d/Y h:i.a", strtotime($value->dropoff_time)) . "\n";
                        }
                    } else {
                        if ($value->start_periode !== null && $value->start_periode !== "") {
                            $msg = $msg  . date("m/d/Y h:i.a", strtotime($value->start_periode)) . " - " . date("m/d/Y h:i.a", strtotime($value->end_periode)) . "\n";
                        }
                    }
                    $msg =   $msg . "\nLocation address:\n" . $value->customer->company . "\n" . $value->dropoff_address . "\nPhone:\n" . $value->contact_number  . "\nDelivery #: " . $value->hash . "\n";
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
            SmsTemplate::where('load_id', $load->id)->update(['status' => 'deactive']);

            if (!$response["status"]) {
                return redirect()->back()->with("error", __("tran.Error in sending message.") . "\n" . $response["error"]);
            }
        }

        if ($load) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'id' => $load->id]);
            } else {
                return redirect()->route("loads-index")->with("message", __("tran.Data saved successfully"));
            }
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete(Request $request)
    {
        $load = Load::whereIn('id', $request->ids)->delete();
        if ($load) {
            return redirect()->route("loads-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $load = Load::findOrFail($id);
        $load->forceDelete();
    }

    public function swap_payment_status($id)
    {
        $load = Load::findOrFail($id);
        $load->driver_payment = ($load->driver_payment == "Pending" ? "Paid" : "Pending");
        if ($load->driver_payment == "Paid") {
            $load->payment_time = Carbon::now();
        } else {
            $load->payment_time = null;
        }
        //        dd( $load->payment_time,$load->driver_payment);
        $load->save();
        return redirect()->back()->with("message", __(trans("tran.Payment Status Changed Successfully.")));
    }

    public function add_deductions(Request $request)
    {
        if (count($request->id) == 1)
            return redirect()->route("loads-deduction-single", $request->id[0]);
        $id = $request->id;
        $pageTitle = "Add Deductions";
        $url = route("loads-deduction-process");
        return view("loads.add_deductions", compact("id", "pageTitle", "url"));
    }

    public function add_deduction_single_load($load_id)
    {
        $id = [$load_id];
        $data = false;
        $load = Load::whereId($load_id)->get()->first();
        if ($load && $load->deductions->count())
            $data = $load->deductions;
        $pageTitle = "Add Deductions";
        $url = route("loads-deduction-process");
        return view("loads.add_deductions", compact("id", "pageTitle", "url", "data"));
    }

    public function deduction_process(Request $request)
    {
        $loads = Load::whereIn("id", $request->id)->get();
        foreach ($loads as $load) {
            $load->deductions()->delete();
            $deductions = [];
            foreach ($request->value as $key => $value) {
                $deduction = new Deduction([
                    "title" => $request->title[$key],
                    "value" => $value
                ]);
                array_push($deductions, $deduction);
            }
            $load->deductions()->saveMany($deductions);
        }
        return redirect()->route("drivers-paymanagement-index")->with("message", __("tran.Data Updated Successfully"));
    }

    public function change_status_multi_view(Request $request)
    {

        $loads = Load::whereIn("id", $request->id)->whereNotIn("status", ["Pending", "Needs Driver"])->get();
        $id = $loads->pluck("id");
        if (!count($id)) {
            return redirect()->back()->with("error", "No Load is selected without 'Pending' & 'Needs Driver' status.");
        }
        $pageTitle = "Change Status";
        $url = route("loads-change-status-process");
        return view("loads.change_status", compact("id", "pageTitle", "url"));
    }

    public function change_status_multi_process(Request $request)
    {
        //        $loads_dispatched = Load::whereIn("id", $request->id)->where("status", "!=", "Dispatched")->get();
        //
        //        if ($request->status == "Dispatched" && env("SEND_SMS")) {
        //            foreach ($loads_dispatched as $load) {
        //                $msg = "Hi " . $load->driver->name . "\n";
        //                foreach ($load->shipper as $key => $value) {
        //                    $msg = $msg . "\nPickup " . ($key + 1) . ":\n\nPickup date and time:\n" . $value->pickup_date . "\nLocation address:\n" . $value->pickup_address . "\nPhone:\n" . $value->contact_number . "\n";
        //                    if ($value->note !== null && $value->note !== "") {
        //                        $msg = $msg . "Pickup Note:\n" . $value->note . "\n";
        //                    }
        //                }
        //
        //                foreach ($load->shipper as $key => $value) {
        //                    $msg = $msg . "\nDelivery " . ($key + 1) . ":\n\nDelivery date and time:\n" . $value->pickup_date . "\nLocation address:\n" . $value->pickup_address . "\nPhone:\n" . $value->contact_number . "\n";
        //                    if ($value->note !== null && $value->note !== "") {
        //                        $msg = $msg . "Delivery Note:\n" . $value->note . "\n";
        //                    }
        //                }
        //
        //                $data = array("contact" => $load->driver->contact, "message" => $msg);
        //                $response = HomeController::twillio_sms_bulk($data);
        //
        //                $details = $response["message"];
        //                $message_record = new MessageRecord();
        //                if (!$response["status"])
        //                    $details = $response["error"] . $details;
        //                $message_record->message = $details;
        //                $message_record->status = $response["status"];
        //                $message_record->save();
        //
        //                if (!$response["status"]) {
        //                    return redirect()->back()->with("error", __("tran.Error in sending message.") . "\n" . $response["error"]);
        //                }
        //            }
        //        }
        //
        //        $loads_dispatched = Load::whereIn("id", $request->id)->where("status", "Dispatched")->update([
        //            "status" => $request->status
        //        ]);
        $loads = Load::whereIn("id", $request->id)->whereNotIn("status", ["Pending", "Needs Driver"])->update([
            "status" => $request->status
        ]);
        if ($loads)
            return redirect()->route("loads-index")->with("message", "Data Updated Successfully");
        else
            return redirect()->route("loads-index")->with("error", "Data Updated Successfully");
    }
}
