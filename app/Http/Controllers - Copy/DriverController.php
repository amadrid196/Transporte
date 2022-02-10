<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Load;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use stdClass;

class DriverController extends Controller
{
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("drivers-create");
        $title_tag = "Drivers";
        $title_on_page = "Drivers";

        $title = "Driveres";
        $icon_name = "fa-users";
        $table_headers = ["#", __("tran.Name"), __("tran.Email"), __("tran.Contact"), __("tran.RPM"), __("tran.License No."), __("tran.Status"), __("tran.Action")];
        $ajax_data_getting_url = route("drivers-ajax");
        //        $min_max_filter = ["value" => true, "min" => 8, "max" => 8, "type" => "date"];
        return view("data_table", compact("title","icon_name","show_add_button", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    }

    public function drivers_ajax()
    {
        $table_data = Driver::all();

        $data = [];
        $data["columns"] = [
            array("data" => "id"),
            array("data" => "name"
                //            , "render" =>
                //                    "
                //                    row.fname+\" \"+row.lname;"

            ),
            array("data" => "email"),
            array("data" => "contact"),
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
            array("data" => "rate"),
            array("data" => "license_no"),
            array("data" => "status"),
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
            array("data" => "id",
                "render" =>
                    "\"<a href=\"+base_url+\"/drivers-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/view/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>View</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/call/\" + data + \"><button type='button' class='btn btn-block btn-success btn-xs'>Call</button></a>\""
                    "<a href=\"+base_url+\"/drivers-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\""
            )
        ];
        $data["columnDefs"] = [
            array("targets" => 7, "className" => "button_inline", "searchable" => false, "orderable" => false),
            //            array("targets" => 5,"searchable" => false)
        ];

        //        $data["createdRow"] = "
        //         if (data.category && data.category.reminder==\"true\")
        //        $(row).addClass('reminder');
        //        ";

        $data["data"] = $table_data;

        return json_encode($data);
    }

    public function create()
    {
        $pageTitle = "Driver Create";
        $url = route("drivers-store");
        return view("drivers.create", compact("pageTitle", "url"));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|min:3',
            'status' => 'required|string|min:3',
            'contact' => 'required|string|min:3',
            'email' => 'required|string|min:3',
            'rate' => 'required|string|min:1',
        ]);

        $driver = new Driver();

        $driver->name = $request->name;
        $driver->dob = Carbon::create($request->dob);
        $driver->hire_date = Carbon::create($request->hire_date);
        $driver->emp_id = $request->emp_id;
        $driver->ownership = $request->ownership;
        $driver->rate = $request->rate;
        $driver->license_no = $request->license_no;
        //        $driver->expiration = Carbon::create($request->expiration);
        //        $driver->issue = Carbon::create($request->issue);
        $driver->termination = Carbon::create($request->termination);
        $driver->emergency = $request->emergency;
        $driver->status = $request->status;
        $driver->email = $request->email;
        $driver->address = $request->address;
        $driver->contact = $request->contact;

        //        if (isset($request->img)) {
        //            $name = auth()->user()->id . "-" . "driver_image-" . Carbon::now()->timestamp . "." . $request->file('img')->extension();
        //            $request->img->move(public_path('images'), $name);
        //            $driver->img = $name;
        //        }
        if (isset($request->dimg)) {

            $request->validate([
                'dimg' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
            ]);

            $name = auth()->user()->id . "-" . "document-" . Carbon::now()->timestamp . "." . $request->file('dimg')->extension();
            $request->dimg->move(public_path('images'), $name);
            $driver->dimg = $name;
        }

        if (isset($request->limg)) {
            $request->validate([
                'limg' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
            ]);
            $name = auth()->user()->id . "-" . "license-" . Carbon::now()->timestamp . "." . $request->file('limg')->extension();
            $request->limg->move(public_path('images'), $name);
            $driver->limg = $name;
        }

        $driver->save();

        if ($driver) {

            return redirect()->back()->with("message", __("tran.Data saved successfully"));
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
        $data = Driver::findorfail($id);
        $pageTitle = "Driver Edit";
        $url = route("drivers-update", $id);
        return view("drivers.create", compact("pageTitle", "url", "data"));
    }

    public function update(Request $request, $id)
    {

        //        dd($request->all());

        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }

        $driver = Driver::findorfail($id);

        $request->validate([
            'name' => 'required|string|min:3',
            'status' => 'required|string|min:3',
            'contact' => 'required|string|min:3',
            'email' => 'required|string|min:3',
            'rate' => 'required|string|min:1',
        ]);

        $driver->name = $request->name;
        $driver->dob = Carbon::create($request->dob);
        $driver->hire_date = Carbon::create($request->hire_date);
        $driver->emp_id = $request->emp_id;
        $driver->ownership = $request->ownership;
        $driver->rate = $request->rate;
        $driver->license_no = $request->license_no;
        $driver->termination = Carbon::create($request->termination);
        $driver->emergency = $request->emergency;
        $driver->status = $request->status;
        $driver->email = $request->email;
        $driver->address = $request->address;
        $driver->contact = $request->contact;

        //        if (isset($request->img)) {
        //            $name = auth()->user()->id . "-" . "driver_image-" . Carbon::now()->timestamp . "." . $request->file('img')->extension();
        //            $request->img->move(public_path('images'), $name);
        //            $driver->img = $name;
        //        }

        $driver->dexpiration = Carbon::create($request->dexpiration);
        $driver->dissue = Carbon::create($request->dissue);
        if (isset($request->dimg)) {
            $request->validate([
                'dimg' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
            ]);
            $name = auth()->user()->id . "-" . "document-" . Carbon::now()->timestamp . "." . $request->file('dimg')->extension();
            $request->dimg->move(public_path('images'), $name);
            $driver->dimg = $name;
        }


        $driver->lexpiration = Carbon::create($request->lexpiration);
        $driver->lissue = Carbon::create($request->lissue);
        if (isset($request->limg)) {
            $request->validate([
                'limg' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
            ]);
            $name = auth()->user()->id . "-" . "license-" . Carbon::now()->timestamp . "." . $request->file('limg')->extension();
            $request->limg->move(public_path('images'), $name);
            $driver->limg = $name;
        }

        $driver->save();
        if ($driver) {
            return redirect()->route("drivers-index")->with("message", __("tran.Data updated successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();
        if ($driver) {
            return redirect()->route("drivers-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->forceDelete();
    }

    public function rate($id)
    {
        $driver = Driver::whereId($id)->first();
        if ($driver) {
            return json_encode(array("status" => "success", "rate" => $driver->rate));
        } else {
            return json_encode(array("status" => "error"));
        }
    }

    public function paymanagement_index()
    {
        $show_add_button = false;
        $add_button_link = "#";
        $title_tag = "Drivers Pay Management";
        $title_on_page = "Drivers Pay Management";

        $title = "Pay Managment";
        $icon_name = "fa-dollar";
        $table_headers = ["#", __("tran.Driver Name"),  __("tran.Pay Status")
            ,  __("tran.Status")
            , __("tran.Pickup Address"), __("tran.Last Delivery Address"), __("tran.Total Miles")
            , __("tran.Expenses"), __("tran.Profit"), __("tran.Invoice Status"), "Created At"
           
        ];
        $ajax_data_getting_url = route("drivers-paymanagement-index-ajax");
        $min_max_filter = ["value" => true, "min" =>10, "max" => 10, "type" => "date"];

        $multi_select = true;

        $from1 = new stdClass();
        $from1->route = route("driver-send-invoice-multiple");
        $from1->method = "post";
        $from1->btn_class = "btn btn-xs btn-success";
        $from1->button_txt = __("tran.Send Invoice");
        $multiselect_forms[0] = $from1;
        
        $from2 = new stdClass();
        $from2->route = route("loads-deduction");
        $from2->method = "post";
        $from2->btn_class = "btn btn-xs btn-danger";
        $from2->button_txt = __("tran.Add Deductions");
        $multiselect_forms[1] = $from2;

        $from3 = new stdClass();
        $from3->route = route("multi_pdf_show");
        $from3->method = "post";
        $from3->btn_class = "btn btn-xs btn-primary";
        $from3->button_txt = __("tran.Show PDF");
        $multiselect_forms[2] = $from3;
        

       

        return view("data_table", compact("multi_select", "multiselect_forms", "min_max_filter", "show_add_button", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    }

    public function paymanagement_index_ajax()
    {
        $table_data = Load::whereNotIn("status", ["Pending", "Needs Driver", "Dispatched"])
            // ->where('id', 9)
            ->whereHas('driver')->whereHas('consignee')->with("driver", "shipper", "consignee", "deductions", "accessories")
            // ->first();
            ->get();
        //  return $table_data;
        //                 dd($table_data);
        

        $data = [];
        $data["columns"] = [
            array("data" => "id"
            , "render" => "
            \"<a href='" . route("app_url") . "/loads-edit/\" + data + \"'>\"+ data +\"</a>\"
            "
            ),
            array("data" => "id"
            , "render" =>
                "row.driver.name;"
            ),
            // array("data" => "id"
            // , "render" => "
            // var month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            // var x=new Date(row.consignee[0].dropoff_time);
            // var y='AM';
            // var hr=x.getHours();
            // var min=x.getMinutes();
            // var sec=x.getSeconds();
            // var month=(x.getMonth()+1);
            // var date=x.getDate();
            
            // if(date<10){
            // date='0'+date;
            // }
            // if(month<10){
            // month='0'+month;
            // }
            // if(x.getHours()>12){
            //  y='PM';
            //  hr=hr-12;
            // }
            // if(hr<10){
            // hr='0'+hr;
            // }
            // if(min<10){
            // min='0'+min;
            // }
            // if(sec<10){
            // sec='0'+sec;
            // }
            // date + '/' + month + '/' + x.getFullYear() + ' ' +hr + ':' +min+':'+sec+' '+y;
            // "
            // ),
            array("data" => "driver_payment"),
            // array("data" => "id",
            //     "render" => "
            // if(row.driver_payment=='Paid'){
            // var month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            // var x=new Date(row.payment_time);
            // var y='AM';
            // var hr=x.getHours();
            // var min=x.getMinutes();
            // var sec=x.getSeconds();
            // var month=(x.getMonth()+1);
            // var date=x.getDate();
            
            // if(date<10){
            // date='0'+date;
            // }
            // if(month<10){
            // month='0'+month;
            // }
            // if(x.getHours()>12){
            //  y='PM';
            //  hr=hr-12;
            // }
            // if(hr<10){
            // hr='0'+hr;
            // }
            // if(min<10){
            // min='0'+min;
            // }
            // if(sec<10){
            // sec='0'+sec;
            // }
            // date + '/' + month + '/' + x.getFullYear() + ' ' +hr + ':' +min+':'+sec+' '+y;
            // }
            // else{
            // '-';
            // }
            // "
            // ),
            // array("data" => "id"),//amount
            array("data" => "status"),
            array("data" => "id",//pickup address
                "render" => "
                if(row.shipper != null&&row.shipper != \"\"&&row.shipper.length != 0 )
                {
                    row.shipper[0].pickup_address;    
                }else
                
                {
                    ''
                }
                
                "
            ),
            array("data" => "id",//last delivery address
                "render" => "
                row.consignee[row.consignee.length-1].dropoff_address;
                "),
            array("data" => "miles"),
      //      array("data" => "id", "render"=>"row.driver.rate"),
            array("data" => "id"//expenses
            , "render" => "
            var driver=0;
            var deductions=0;
            var totalexpense = 0;

            row.accessories.forEach(function (currentValue, index, arr) {    
                if(currentValue.value != null&&currentValue.type==\"expense\")
                    totalexpense=parseFloat(totalexpense)+parseFloat(currentValue.value);
                
            });
    
            row.deductions.forEach(function (currentValue, index, arr) {    
                
                deductions=parseFloat(deductions)+parseFloat(currentValue.value);
            
            });

            console.log(deductions) 
            driver=parseFloat((row.driver.rate)*row.miles);
            if(totalexpense != 0)
            deductions+ \" +  \" +parseFloat(totalexpense)+\"= $\"+ (deductions + parseFloat(totalexpense));
            else
            deductions+ \" +  \" +0+\"= $\"+ (deductions + 0);
            "),
            array("data"=>'id', 'render'=>'"$ "+row.profit'),
            // array("data" => "id"
            // , "render" => "
            // var driver=0;
            // var deductions=0;
            // var totalexpense = 0;

            // row.accessories.forEach(function (currentValue, index, arr) {    
            //     if(currentValue.value != null&&currentValue.type==\"expense\")
            //         totalexpense=parseFloat(totalexpense)+parseFloat(currentValue.value);
                
            // });
            // row.deductions.forEach(function (currentValue, index, arr) {     
            //  deductions=parseFloat(deductions)+parseFloat(currentValue.value);
            // });
            // driver=parseFloat((row.driver.rate)*row.miles);
            // if(totalexpense != 0){
            // console.log(totalexpense);
            // row.value - (deductions + driver+parseFloat(totalexpense));
            // }
            // else
            // row.value - (deductions + driver+0);
            
            // "),
            array("data" => "inv_status"),
            // array("data" => "inv_date",
            //     "render" => "
            // if(row.inv_status=='Sent'){
            // var month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            // var x=new Date(data);
            // var y='AM';
            // var hr=x.getHours();
            // var min=x.getMinutes();
            // var sec=x.getSeconds();
            // var month=(x.getMonth()+1);
            // var date=x.getDate();
            
            // if(date<10){
            // date='0'+date;
            // }
            // if(month<10){
            // month='0'+month;
            // }
            // if(x.getHours()>12){
            //  y='PM';
            //  hr=hr-12;
            // }
            // if(hr<10){
            // hr='0'+hr;
            // }
            // if(min<10){
            // min='0'+min;
            // }
            // if(sec<10){
            // sec='0'+sec;
            // }
            // date + '/' + month + '/' + x.getFullYear() + ' ' +hr + ':' +min+':'+sec+' '+y;
            // }
            // else{
            // '-';
            // }
            // "
            // ),
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
            array("data" => "created_at", "render"=> "
            var options = {
                year: \"numeric\",
                month: \"2-digit\",
                day: \"numeric\",
                hour: \"numeric\",
                minute: \"numeric\"
            };

            new Date(data).toLocaleDateString(\"en\", options);
            ")
//             array("data" => "created_at",
//                 "render" =>
//                     "var month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
//             var x=new Date(data);
//             var y='AM';
//             var hr=x.getHours();
//             var min=x.getMinutes();
//             var sec=x.getSeconds();
//             var month=(x.getMonth()+1);
//             var date=x.getDate();
            
//             if(date<10){
//             date='0'+date;
//             }
//             if(month<10){
//             month='0'+month;
//             }
//             if(x.getHours()>12){
//              y='PM';
//              hr=hr-12;
//             }
//             if(hr<10){
//             hr='0'+hr;
//             }
//             if(min<10){
//             min='0'+min;
//             }
//             if(sec<10){
//             sec='0'+sec;
//             }
// //            date + '/' + month + '/' + x.getFullYear() + ' ' +hr + ':' +min+':'+sec+' '+y;
//             x;
//            ")
            // array("data" => "id",
            //     "render" =>
            //         "\"<a href=\"+base_url+\"/driver/send/invoice/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Send Invoice") . "</button></a>" .
            //         "<a href=\"+base_url+\"/pdf/show/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>Show PDF</button></a>" .
            //         "<a href=\"+base_url+\"/loads-deduction-single/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>Add Deduction</button></a>" .
            //         //    "\"<a href=\"+base_url+\"/customer/call/\" + data + \"><button type='button' class='btn btn-block btn-success btn-xs'>Call</button></a>\""
            //         //                    "<a href=\"+base_url+\"/drivers-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\"
            //         "<a href=\"+base_url+\"/payment-status/\" + data + \" onClick='return confirm(\\\"Alert!Are you sure to change status?\\\")'><button type='button' class='btn btn-block \"+ (row.driver_payment=='Pending' ? 'btn-danger':'btn-success') +\"  btn-xs'>\"+ (row.driver_payment=='Pending' ? '" . __("tran.Paid") . "':'" . __("tran.Pending") . "') +\"</button></a>\""
            // )
        ];
        $data["columnDefs"] = [
            array("targets" => 9, "className" => "button_inline", "searchable" => false, "orderable" => false),
            //            array("targets" => 15, "className" => "d-none hide hidden"),
            //            array("targets" => 5,"searchable" => false)
        ];

        //        $data["createdRow"] = "
        //         if (data.category && data.category.reminder==\"true\")
        //        $(row).addClass('reminder');
        //        ";

        $data["buttons"] = [
            array(
                "text" => 'Select All',
                "className" => 'btn btn-xs btn-info',
                "action" => "table.rows({search: 'applied'}).select();"
            ),
            array("extend" => 'selectNone', "className" => 'btn btn-xs btn-danger')
        ];

        $data["data"] = $table_data;

        return json_encode($data);
    }

    

    public function send_invoice($id)
    {
        $pageTitle = "Send Invoice";
        $url = route("driver-invoice-process");
        return view("invoice.create", compact("id", "url", "pageTitle"));
    }

    public function send_invoice_multiple(Request $request)
    {
        $id = $request->id;
        $pageTitle = "Send Invoice";
        $url = route("driver-invoice-process");
        return view("invoice.create", compact("id", "url", "pageTitle"));
    }

    public function invoice_process(Request $request)
    {
        $request->validate([
            'id' => 'required|array|min:1',
        ]);
        $drivers = Load::select("driver_id")->whereIn("id", $request->id)->distinct()->get();
        foreach ($drivers as $driver) {
            $loads = Load::whereIn("id", $request->id)->where("driver_id", $driver->driver_id)->get();
            if (!$loads)
                return redirect()->route("drivers-paymanagement-index")->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
            $data = [
                "driver" => $loads[0]->driver,
                "message2" => $request->message
            ];

            Mail::send('invoice.mail_text', $data, function ($message) use ($data, $loads) {

                $message->to($data["driver"]->email, $data["driver"]->name)->subject('Invoice');

                $message->attach(HomeController::pdf_create($loads->pluck("id")));
                //            $message->sender('email@example.com', 'Mr. Example');
                //            $message->returnPath('email@example.com');
                //            $message->cc('email@example.com', 'Mr. Example');
                //            $message->bcc('email@example.com', 'Mr. Example');
                //            $message->replyTo('email@example.com', 'Mr. Example');
                //            $message->priority(2);

                $message->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
            });

            $response = Load::whereIn("id", $request->id)->where("driver_id", $driver->driver_id)->update([
                "inv_status" => "Sent",
                "inv_date" => Carbon::now()
            ]);
            //            dd(Load::whereIn("id", $request->id)->where("driver_id", $driver->driver_id)->get(),$request->id,$driver,$driver->driver_id);
        }
        return redirect()->route("drivers-paymanagement-index")->with("message", __("tran.Invoice sent successfully."));
    }

    public function license_medical_card_expiration_notification()
    {
        //dd(Carbon::now()->add(1, "days"), Carbon::now());
        $dexpirations = Driver::where("dexpiration", "<=", Carbon::now()->add(30, "days"))->where("dexpiration", ">", Carbon::now())->get();
        $lexpiration = Driver::where("lexpiration", "<=", Carbon::now()->add(30, "days"))->where("lexpiration", ">", Carbon::now())->get();

        $data = [

        ];

        Mail::send("invoice.expiration_notification", $data, function ($message) use ($data, $lexpiration) {

//            $message->to($data["driver"]->email, $data["driver"]->name)->subject('Driving License Expiration');

            $message->to("rananadeemsports@gmail.com", "name here")->subject('Driving License Expiration');

            //            $message->attach(HomeController::pdf_create($dexpirations->pluck("id")));
            //            $message->sender('email@example.com', 'Mr. Example');
            //            $message->returnPath('email@example.com');
            //            $message->cc('email@example.com', 'Mr. Example');
            //            $message->bcc('email@example.com', 'Mr. Example');
            //            $message->replyTo('email@example.com', 'Mr. Example');
            //            $message->priority(2);

            $message->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
        });


        dd($dexpirations, Carbon::now()->add(30, "days"), $lexpiration);
    }
    public function driverOwner($id)
    {
        $driver = Driver::where('id', $id)->first();
        if(!$driver)
            return response()->json(['status'=>'false']);
        return response()->json(['status'=>"true", 'data'=>$driver->ownership]);
    }

}
