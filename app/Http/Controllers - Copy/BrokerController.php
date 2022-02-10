<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use Illuminate\Http\Request;

class BrokerController extends Controller
{
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("brokers-create");
        $title_tag = "Brokers";
        $title_on_page = "Brokers";

        $title = "Brokers";
        $icon_name = "fa-users";
        $add_btn_txt = "Add New Broker";
        $table_headers = ["#", __("tran.Name"),  __("tran.Address"),__("tran.Primary Phone")];
        $ajax_data_getting_url = route("brokers-ajax");
        $multi_select = true;
        //        $min_max_filter = ["value" => true, "min" => 8, "max" => 8, "type" => "date"];
        return view("data_table", compact('multi_select','add_btn_txt','title', 'icon_name', "show_add_button", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    }

    public function brokers_ajax()
    {
        $table_data = Broker::all();

        $data = [];
        $data["columns"] = [
            array("data" => "id"),
            array("data" => "fname"
                //            ,
                //                "render" =>
                //                    "
                //                    row.fname+\" \"+row.lname;"

            ),
            //            array("data" => "email"),
            array("data" => "contact"),
            //            array("data" => "company",),
            array("data" => "address"),
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
            // array("data" => "id",
            //     "render" =>
            //         "\"<a href=\"+base_url+\"/brokers-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
            //         //    "\"<a href=\"+base_url+\"/customer/view/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>View</button></a>" .
            //         //    "\"<a href=\"+base_url+\"/customer/call/\" + data + \"><button type='button' class='btn btn-block btn-success btn-xs'>Call</button></a>\""
            //         "<a href=\"+base_url+\"/brokers-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\""
            // )
        ];
        $data["columnDefs"] = [
        // array("targets" => 4, "className" => "button_inline", "searchable" => false, "orderable" => false),
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
        $pageTitle = "Broker Create";
        $url = route("brokers-store");
        return view("brokers.create", compact("pageTitle", "url"));
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'fname' => 'required|string|min:3',
        //     'number' => 'required|string|min:3',
        //     'address' => 'required|string|min:3',
        // ]);
        
        $broker = new Broker();
        $broker->fname = $request->fname;
        $broker->company = $request->company;
        $broker->address = $request->address;
        $broker->contact = $request->number;
        $broker->mc_number = $request->mc_number;
        $broker->save();

        if ($broker && isset($request->ajax)) {
            return response([
                "status" => "success",
                "id"=>$broker->id
            ]);
        }

        if ($broker) {
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
        $data = Broker::findorfail($id);
        $pageTitle = "Broker Edit";
        $url = route("brokers-update", $id);
        return view("brokers.create", compact("pageTitle", "url", "data"));
    }

    public function update(Request $request, $id)
    {
        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }

        $broker = Broker::findorfail($id);

        $request->validate([
            'fname' => 'required|string|min:3',
            'number' => 'required|string|min:3',
            'address' => 'required|string|min:3',
        ]);

        $broker->fname = $request->fname;
        $broker->company = $request->company;
        $broker->address = $request->address;
        $broker->contact = $request->number;
        $broker->save();
        if ($broker) {
            return redirect()->route("brokers-index")->with("message", __("tran.Data updated successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete(Request $request)
    {
        $broker = Broker::whereIn('id', $request->ids)->delete();
        // $broker->delete();
        if ($broker) {
            return redirect()->route("brokers-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $broker = Broker::findOrFail($id);
        $broker->forceDelete();
    }

}
