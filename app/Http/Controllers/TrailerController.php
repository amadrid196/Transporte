<?php

namespace App\Http\Controllers;

use App\Models\Trailer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrailerController extends Controller
{
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("trailers-create");
        $title_tag = "Trailers";
        $title_on_page = "Trailers";
        $table_headers = ["#", __("tran.Make/Model"), __("tran.Number"), __("tran.Model Year"), __("tran.Ownership"), __("tran.License Plate"), __("tran.Status"), __("tran.Action")];
        $ajax_data_getting_url = route("trailers-ajax");
        $title = "Trailers";
        $icon_name = "fa-barcode";
        $add_btn_txt = "Add New Trailer";
        //        $min_max_filter = ["value" => true, "min" => 8, "max" => 8, "type" => "date"];
        $page="trailers-index";
        return view("data_table", compact("title",'add_btn_txt', "icon_name","show_add_button", "page", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    }

    public function trailers_ajax()
    {
        $table_data = Trailer::all();

        $data = [];
        $data["columns"] = [
            array("data" => "id"),
            array("data" => "model"
                //            , "render" =>
                //                    "
                //                    row.fname+\" \"+row.lname;"

            ),
            array("data" => "number"),
            array("data" => "model_yr"),
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
            array("data" => "ownership"),
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
                    "\"<a href=\"+base_url+\"/trailers-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/view/\" + data + \"><button type='button' class='btn btn-block btn-info btn-xs'>View</button></a>" .
                    //    "\"<a href=\"+base_url+\"/customer/call/\" + data + \"><button type='button' class='btn btn-block btn-success btn-xs'>Call</button></a>\""
                    "<a href=\"+base_url+\"/trailers-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\""
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
        $pageTitle = "Create Trailer";
        $url = route("trailers-store");
        $title = "Create Trailer";
        $icon_name = "fa-barcode";
        return view("trailers.create", compact("title", 'icon_name',"pageTitle", "url"));
    }

    public function store(Request $request)
    {

        $request->validate([
            'model' => 'required|string|min:3',
            'number' => 'required|string|min:1',
            'status' => 'required|string|min:3',
        ]);

        $trailer = new Trailer();

        $trailer->model = $request->model;
        $trailer->number = $request->number;
        $trailer->status = $request->status;

        if (isset($request->type)) {
            $trailer->type = $request->type;
        }

        if (isset($request->generator_info)) {
            $trailer->generator_info = $request->generator_info;
        }

        if (isset($request->service_mileage)) {
            $trailer->service_mileage = $request->service_mileage;
        }

        if (isset($request->pur_leas_from)) {
            $trailer->pur_leas_from = $request->pur_leas_from;
        }
        if (isset($request->fuel_type)) {
            $trailer->fuel_type = $request->fuel_type;
        }
        if (isset($request->horsepower)) {
            $trailer->horsepower = $request->horsepower;
        }
        if (isset($request->license_plate)) {
            $trailer->license_plate = $request->license_plate;
        }
        if (isset($request->model_yr)) {
            $trailer->model_yr = $request->model_yr;
        }
        if (isset($request->vehicle_id)) {
            $trailer->vehicle_id = $request->vehicle_id;
        }
        if (isset($request->insurance_info)) {
            $trailer->insurance_info = $request->insurance_info;
        }
        if (isset($request->reg_states)) {
            $trailer->reg_states = $request->reg_states;
        }
        if (isset($request->length)) {
            $trailer->length = $request->length;
        }
        if (isset($request->width)) {
            $trailer->width = $request->width;
        }
        if (isset($request->height)) {
            $trailer->height = $request->height;
        }
        if (isset($request->axles)) {
            $trailer->axles = $request->axles;
        }
        if (isset($request->unloaded_weight)) {
            $trailer->unloaded_weight = $request->unloaded_weight;
        }
        if (isset($request->gross_weight)) {
            $trailer->gross_weight = $request->gross_weight;
        }
        if (isset($request->notes)) {
            $trailer->notes = $request->notes;
        }
        if (isset($request->ownership)) {
            $trailer->ownership = $request->ownership;
        }
        if (isset($request->pur_or_leas)) {
            $trailer->pur_or_leas = $request->pur_or_leas;
        }

        if (isset($request->sold_to)) {
            $trailer->sold_to = $request->sold_to;
        }

        if (isset($request->pur_leas_amount)) {
            $trailer->pur_leas_amount = $request->pur_leas_amount;
        }

        if (isset($request->sold_amount)) {
            $trailer->sold_amount = $request->sold_amount;
        }

        if (isset($request->factory_price)) {
            $trailer->factory_price = $request->factory_price;
        }

        if (isset($request->current_value)) {
            $trailer->current_value = $request->current_value;
        }

        if (isset($request->odometer)) {
            $trailer->odometer = $request->odometer;
        }

        if (isset($request->pur_leas_date)) {
            $trailer->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->pur_leas_date)) {
            $trailer->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->sold_leas_end_date)) {
            $trailer->sold_leas_end_date = Carbon::create($request->sold_leas_end_date);
        }

        if (isset($request->license_exp)) {
            $trailer->license_exp = Carbon::create($request->license_exp);
        }

        if (isset($request->insp_exp)) {
            $trailer->insp_exp = Carbon::create($request->insp_exp);
        }

        if (isset($request->dot_exp)) {
            $trailer->dot_exp = Carbon::create($request->dot_exp);
        }

        if (isset($request->insurance_exp)) {
            $trailer->insurance_exp = Carbon::create($request->insurance_exp);
        }

        if (isset($request->reg_exp)) {
            $trailer->reg_exp = Carbon::create($request->reg_exp);
        }


        if (isset($request->service)) {
            $trailer->service = Carbon::create($request->service);
        }

        $trailer->save();

        if ($trailer) {
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
        $data = Trailer::findorfail($id);
        $pageTitle = "Edit Trailer";
        $url = route("trailers-update", $id);
        $title = "Edit Trailer";
        $icon_name = "fa-barcode";
        return view("trailers.create", compact("pageTitle",'title', 'icon_name', "url", "data"));
    }

    public function update(Request $request, $id)
    {
        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }

        $trailer = Trailer::findorfail($id);
        $request->validate([
            'model' => 'required|string|min:3',
            'number' => 'required|string|min:1',
            'status' => 'required|string|min:3',
        ]);

        $trailer->model = $request->model;
        $trailer->number = $request->number;
        $trailer->status = $request->status;

        if (isset($request->type)) {
            $trailer->type = $request->type;
        }

        if (isset($request->generator_info)) {
            $trailer->generator_info = $request->generator_info;
        }

        if (isset($request->service_mileage)) {
            $trailer->service_mileage = $request->service_mileage;
        }

        if (isset($request->pur_leas_from)) {
            $trailer->pur_leas_from = $request->pur_leas_from;
        }
        if (isset($request->fuel_type)) {
            $trailer->fuel_type = $request->fuel_type;
        }
        if (isset($request->horsepower)) {
            $trailer->horsepower = $request->horsepower;
        }
        if (isset($request->license_plate)) {
            $trailer->license_plate = $request->license_plate;
        }
        if (isset($request->model_yr)) {
            $trailer->model_yr = $request->model_yr;
        }
        if (isset($request->vehicle_id)) {
            $trailer->vehicle_id = $request->vehicle_id;
        }
        if (isset($request->insurance_info)) {
            $trailer->insurance_info = $request->insurance_info;
        }
        if (isset($request->reg_states)) {
            $trailer->reg_states = $request->reg_states;
        }
        if (isset($request->length)) {
            $trailer->length = $request->length;
        }
        if (isset($request->width)) {
            $trailer->width = $request->width;
        }
        if (isset($request->height)) {
            $trailer->height = $request->height;
        }
        if (isset($request->axles)) {
            $trailer->axles = $request->axles;
        }
        if (isset($request->unloaded_weight)) {
            $trailer->unloaded_weight = $request->unloaded_weight;
        }
        if (isset($request->gross_weight)) {
            $trailer->gross_weight = $request->gross_weight;
        }
        if (isset($request->notes)) {
            $trailer->notes = $request->notes;
        }
        if (isset($request->ownership)) {
            $trailer->ownership = $request->ownership;
        }
        if (isset($request->pur_or_leas)) {
            $trailer->pur_or_leas = $request->pur_or_leas;
        }

        if (isset($request->sold_to)) {
            $trailer->sold_to = $request->sold_to;
        }

        if (isset($request->pur_leas_amount)) {
            $trailer->pur_leas_amount = $request->pur_leas_amount;
        }

        if (isset($request->sold_amount)) {
            $trailer->sold_amount = $request->sold_amount;
        }

        if (isset($request->factory_price)) {
            $trailer->factory_price = $request->factory_price;
        }

        if (isset($request->current_value)) {
            $trailer->current_value = $request->current_value;
        }

        if (isset($request->odometer)) {
            $trailer->odometer = $request->odometer;
        }

        if (isset($request->pur_leas_date)) {
            $trailer->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->pur_leas_date)) {
            $trailer->pur_leas_date = Carbon::create($request->pur_leas_date);
        }

        if (isset($request->sold_leas_end_date)) {
            $trailer->sold_leas_end_date = Carbon::create($request->sold_leas_end_date);
        }

        if (isset($request->license_exp)) {
            $trailer->license_exp = Carbon::create($request->license_exp);
        }

        if (isset($request->insp_exp)) {
            $trailer->insp_exp = Carbon::create($request->insp_exp);
        }

        if (isset($request->dot_exp)) {
            $trailer->dot_exp = Carbon::create($request->dot_exp);
        }

        if (isset($request->insurance_exp)) {
            $trailer->insurance_exp = Carbon::create($request->insurance_exp);
        }

        if (isset($request->reg_exp)) {
            $trailer->reg_exp = Carbon::create($request->reg_exp);
        }


        if (isset($request->service)) {
            $trailer->service = Carbon::create($request->service);
        }

        $trailer->save();
        if ($trailer) {
            return redirect()->route("trailers-index")->with("message", __("tran.Data updated successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete($id)
    {
        $trailer = Trailer::findOrFail($id);
        $trailer->delete();
        if ($trailer) {
            return redirect()->route("trailers-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $trailer = Trailer::findOrFail($id);
        $trailer->forceDelete();
    }
}
