<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("customers-create");
        $title_tag = "Locations";
        $title_on_page = "Locations";
        $table_headers = ["#",/* __("tran.Name"), __("tran.Email"),*/
            __("tran.Contact"), __("tran.Company"), __("tran.Address"), __("tran.Action")];
        $ajax_data_getting_url = route("customers-ajax");
        //        $min_max_filter = ["value" => true, "min" => 8, "max" => 8, "type" => "date"];
        return view("data_table", compact("show_add_button", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    }

    public function customers_ajax()
    {
        $table_data = Customer::all();

        $data = [];
        $data["columns"] = [
            array("data" => "id"),
            //            array("data" => "null",
            //                "render" =>
            //                    "
            //                    row.fname+\" \"+row.lname;"
            //
            //            ),
            //            array("data" => "email"),
            array("data" => "contact"),
            array("data" => "company",),
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
            array("data" => "id",
                "render" =>
                    "\"<a href=\"+base_url+\"/customers-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/view/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>View</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/call/\" + data + \"><button type='button' class='btn btn-block btn-success btn-xs'>Call</button></a>\""
                    "<a href=\"+base_url+\"/customers-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\""
            )
        ];
        $data["columnDefs"] = [
            array("targets" => 4, "className" => "button_inline", "searchable" => false, "orderable" => false),
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
        $pageTitle = "Location Create";
        $url = route("customers-store");
        return view("customers.create", compact("pageTitle", "url"));
    }

    public function store(Request $request)
    {
        $request->validate([
            //            'fname' => 'required|string|min:3',
            //            'lname' => 'required|string|min:3',
            //            'number' => 'required|string|min:3',
            //            'email' => 'required|string|min:3',
            'address' => 'required|string|min:3',
            'company' => 'required|string|min:1',
        ]);

        $customer = new Customer();
        //        $customer->fname = $request->fname;
        //        $customer->lname = $request->lname;
        //        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->contact = isset($request->number) ? $request->number : "";
        $customer->company = $request->company;
        $customer->lat = $request->lat;
        $customer->lng = $request->lng;
        $customer->save();
        if ($customer && isset($request->ajax)) {
            return response([
                "status" => "success",
                "id"=>$customer->id
            ]);
        }

        if ($customer) {
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
        $data = Customer::findorfail($id);
        $pageTitle = "Locations Edit";
        $url = route("customers-update", $id);
        return view("customers.create", compact("pageTitle", "url", "data"));
    }

    public function update(Request $request, $id)
    {
        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }

        $customer = Customer::findorfail($id);

        $request->validate([
            //            'fname' => 'required|string|min:3',
            //            'lname' => 'required|string|min:3',
            //            'number' => 'required|string|min:3',
            //            'email' => 'required|string|min:3',
            'address' => 'required|string|min:3',
            'company' => 'required|string|min:1',
        ]);

        //        $customer->fname = $request->fname;
        //        $customer->lname = $request->lname;
        //        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->contact = isset($request->number) ? $request->number : "";
        $customer->company = $request->company;
        $customer->save();
        if ($customer) {
            return redirect()->route("customers-index")->with("message", __("tran.Data updated successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        if ($customer) {
            return redirect()->route("customers-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->forceDelete();
    }

    public function set_contact_address_ajax($id)
    {
        $customer = Customer::select("contact", "address")->whereId($id)->first();
        if ($customer)
            return json_encode(array("status" => "success", "data" => $customer));
        else
            return json_encode(array("status" => "error", "data" => ""));
    }
}
