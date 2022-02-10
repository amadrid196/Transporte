<?php

namespace App\Http\Controllers;

use App\Models\Tractor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TractorController extends Controller
{
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("tractors-create");
        $title_tag = "Tractors";
        $title_on_page = "Tractors";
        $table_headers = ["#", __("tran.Model"), __("tran.Power Unit No."), __("tran.Engine Type"), __("tran.Fuel Type"), __("tran.License Plate"), __("tran.Status"), __("tran.Action")];
        $ajax_data_getting_url = route("tractors-ajax");
        //        $min_max_filter = ["value" => true, "min" => 8, "max" => 8, "type" => "date"];
        $title = "Tractors";
        $icon_name = "fa-barcode";
        $add_btn_txt = "Add New Tractor";
        $page="tractors-index";
        return view("data_table", compact("add_btn_txt","title", "icon_name", "show_add_button", "page", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    }

    public function tractors_ajax()
    {
        $table_data = Tractor::all();

        $data = [];
        $data["columns"] = [
            array("data" => "id"),
            array("data" => "model"
                //            , "render" =>
                //                    "
                //                    row.fname+\" \"+row.lname;"

            ),
            array("data" => "pun"),
            array("data" => "eng_type"),
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
            array("data" => "fuel_type"),
            array("data" => "license_plate"),
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
                    "\"<a href=\"+base_url+\"/tractors-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/view/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>View</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/call/\" + data + \"><button type='button' class='btn btn-block btn-success btn-xs'>Call</button></a>\""
                    "<a href=\"+base_url+\"/tractors-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\""
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
        $pageTitle = "Create Tractor";
        $title = "Create Tractor";
        $icon_name = "fa-barcode";
        $url = route("tractors-store");
        return view("tractors.create", compact("pageTitle", "url", 'title', 'icon_name'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'model' => 'required|string|min:3',
            'pun' => 'required|string|min:1',
            'status' => 'required|string|min:3',
        ]);

        $tractor = new Tractor();

        $tractor->model = $request->model;
        $tractor->pun = $request->pun;
        $tractor->status = $request->status;

        if (isset($request->eng_type)) {
            $tractor->eng_type = $request->eng_type;
        }

        if (isset($request->service_mileage)) {
            $tractor->service_mileage = $request->service_mileage;
        }

        if (isset($request->pur_leas_from)) {
            $tractor->pur_leas_from = $request->pur_leas_from;
        }
        if (isset($request->transmission)) {
            $tractor->transmission = $request->transmission;
        }
        if (isset($request->fuel_type)) {
            $tractor->fuel_type = $request->fuel_type;
        }
        if (isset($request->horsepower)) {
            $tractor->horsepower = $request->horsepower;
        }
        if (isset($request->license_plate)) {
            $tractor->license_plate = $request->license_plate;
        }
        if (isset($request->model_yr)) {
            $tractor->model_yr = $request->model_yr;
        }
        if (isset($request->vehicle_id)) {
            $tractor->vehicle_id = $request->vehicle_id;
        }
        if (isset($request->insurance_info)) {
            $tractor->insurance_info = $request->insurance_info;
        }
        if (isset($request->reg_states)) {
            $tractor->reg_states = $request->reg_states;
        }
        if (isset($request->length)) {
            $tractor->length = $request->length;
        }
        if (isset($request->width)) {
            $tractor->width = $request->width;
        }
        if (isset($request->height)) {
            $tractor->height = $request->height;
        }
        if (isset($request->axles)) {
            $tractor->axles = $request->axles;
        }
        if (isset($request->unloaded_weight)) {
            $tractor->unloaded_weight = $request->unloaded_weight;
        }
        if (isset($request->gross_weight)) {
            $tractor->gross_weight = $request->gross_weight;
        }
        if (isset($request->notes)) {
            $tractor->notes = $request->notes;
        }
        if (isset($request->ownership)) {
            $tractor->ownership = $request->ownership;
        }
        if (isset($request->pur_or_leas)) {
            $tractor->pur_or_leas = $request->pur_or_leas;
        }

        if (isset($request->sold_to)) {
            $tractor->sold_to = $request->sold_to;
        }

        if (isset($request->pur_leas_amount)) {
            $tractor->pur_leas_amount = $request->pur_leas_amount;
        }

        if (isset($request->sold_amount)) {
            $tractor->sold_amount = $request->sold_amount;
        }

        if (isset($request->factory_price)) {
            $tractor->factory_price = $request->factory_price;
        }

        if (isset($request->current_value)) {
            $tractor->current_value = $request->current_value;
        }

        if (isset($request->odometer)) {
            $tractor->odometer = $request->odometer;
        }

        if (isset($request->oil_change_mileage)) {
            $tractor->oil_change_mileage = $request->oil_change_mileage;
        }

        if (isset($request->tune_up_mileage)) {
            $tractor->tune_up_mileage = $request->tune_up_mileage;
        }

        if (isset($request->pur_leas_date)) {
            $tractor->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->pur_leas_date)) {
            $tractor->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->sold_leas_end_date)) {
            $tractor->sold_leas_end_date = Carbon::create($request->sold_leas_end_date);
        }

        if (isset($request->license_exp)) {
            $tractor->license_exp = Carbon::create($request->license_exp);
        }

        if (isset($request->insp_exp)) {
            $tractor->insp_exp = Carbon::create($request->insp_exp);
        }

        if (isset($request->dot_exp)) {
            $tractor->dot_exp = Carbon::create($request->dot_exp);
        }

        if (isset($request->insurance_exp)) {
            $tractor->insurance_exp = Carbon::create($request->insurance_exp);
        }

        if (isset($request->reg_exp)) {
            $tractor->reg_exp = Carbon::create($request->reg_exp);
        }

        if (isset($request->oil_change)) {
            $tractor->oil_change = Carbon::create($request->oil_change);
        }

        if (isset($request->tune_up)) {
            $tractor->tune_up = Carbon::create($request->tune_up);
        }

        if (isset($request->service)) {
            $tractor->service = Carbon::create($request->service);
        }

        $tractor->save();

        if ($tractor) {
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
        $data = Tractor::findorfail($id);
        $pageTitle = "Edit Tractor";
        $title = "Edit Tractor";
        $icon_name = "fa-barcode";
        $url = route("tractors-update", $id);
        return view("tractors.create", compact("pageTitle", "url", "data", 'title', 'icon_name'));
    }

    public function update(Request $request, $id)
    {
        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }

        $tractor = Tractor::findorfail($id);

        $request->validate([
            'model' => 'required|string|min:3',
            'pun' => 'required|string|min:1',
            'status' => 'required|string|min:3',
        ]);

        $tractor->model = $request->model;
        $tractor->pun = $request->pun;
        $tractor->status = $request->status;

        if (isset($request->eng_type)) {
            $tractor->eng_type = $request->eng_type;
        }

        if (isset($request->service_mileage)) {
            $tractor->service_mileage = $request->service_mileage;
        }

        if (isset($request->pur_leas_from)) {
            $tractor->pur_leas_from = $request->pur_leas_from;
        }
        if (isset($request->transmission)) {
            $tractor->transmission = $request->transmission;
        }
        if (isset($request->fuel_type)) {
            $tractor->fuel_type = $request->fuel_type;
        }
        if (isset($request->horsepower)) {
            $tractor->horsepower = $request->horsepower;
        }
        if (isset($request->license_plate)) {
            $tractor->license_plate = $request->license_plate;
        }
        if (isset($request->model_yr)) {
            $tractor->model_yr = $request->model_yr;
        }
        if (isset($request->vehicle_id)) {
            $tractor->vehicle_id = $request->vehicle_id;
        }
        if (isset($request->insurance_info)) {
            $tractor->insurance_info = $request->insurance_info;
        }
        if (isset($request->reg_states)) {
            $tractor->reg_states = $request->reg_states;
        }
        if (isset($request->length)) {
            $tractor->length = $request->length;
        }
        if (isset($request->width)) {
            $tractor->width = $request->width;
        }
        if (isset($request->height)) {
            $tractor->height = $request->height;
        }
        if (isset($request->axles)) {
            $tractor->axles = $request->axles;
        }
        if (isset($request->unloaded_weight)) {
            $tractor->unloaded_weight = $request->unloaded_weight;
        }
        if (isset($request->gross_weight)) {
            $tractor->gross_weight = $request->gross_weight;
        }
        if (isset($request->notes)) {
            $tractor->notes = $request->notes;
        }
        if (isset($request->ownership)) {
            $tractor->ownership = $request->ownership;
        }
        if (isset($request->pur_or_leas)) {
            $tractor->pur_or_leas = $request->pur_or_leas;
        }

        if (isset($request->sold_to)) {
            $tractor->sold_to = $request->sold_to;
        }

        if (isset($request->pur_leas_amount)) {
            $tractor->pur_leas_amount = $request->pur_leas_amount;
        }

        if (isset($request->sold_amount)) {
            $tractor->sold_amount = $request->sold_amount;
        }

        if (isset($request->factory_price)) {
            $tractor->factory_price = $request->factory_price;
        }

        if (isset($request->current_value)) {
            $tractor->current_value = $request->current_value;
        }

        if (isset($request->odometer)) {
            $tractor->odometer = $request->odometer;
        }

        if (isset($request->oil_change_mileage)) {
            $tractor->oil_change_mileage = $request->oil_change_mileage;
        }

        if (isset($request->tune_up_mileage)) {
            $tractor->tune_up_mileage = $request->tune_up_mileage;
        }

        if (isset($request->pur_leas_date)) {
            $tractor->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->pur_leas_date)) {
            $tractor->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->sold_leas_end_date)) {
            $tractor->sold_leas_end_date = Carbon::create($request->sold_leas_end_date);
        }

        if (isset($request->license_exp)) {
            $tractor->license_exp = Carbon::create($request->license_exp);
        }

        if (isset($request->insp_exp)) {
            $tractor->insp_exp = Carbon::create($request->insp_exp);
        }

        if (isset($request->dot_exp)) {
            $tractor->dot_exp = Carbon::create($request->dot_exp);
        }

        if (isset($request->insurance_exp)) {
            $tractor->insurance_exp = Carbon::create($request->insurance_exp);
        }

        if (isset($request->reg_exp)) {
            $tractor->reg_exp = Carbon::create($request->reg_exp);
        }

        if (isset($request->oil_change)) {
            $tractor->oil_change = Carbon::create($request->oil_change);
        }

        if (isset($request->tune_up)) {
            $tractor->tune_up = Carbon::create($request->tune_up);
        }

        if (isset($request->service)) {
            $tractor->service = Carbon::create($request->service);
        }

        $tractor->save();
        if ($tractor) {
            return redirect()->route("tractors-index")->with("message", __("tran.Data updated successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete($id)
    {
        $tractor = Tractor::findOrFail($id);
        $tractor->delete();
        if ($tractor) {
            return redirect()->route("tractors-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $tractor = Tractor::findOrFail($id);
        $tractor->forceDelete();
    }
}
