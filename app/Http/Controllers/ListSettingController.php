<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Accessorial;

class ListSettingController extends Controller
{
    //
    private $groups =   ["Expense", "Income", "Deductions"];
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("listsettings-create");
        
        $title_tag = "List Settings";
        $title_on_page = "List Settings";
        $table_headers = ['#', __('tran.Value'), __('tran.Grouping'), __('tran.Action')];
        $ajax_data_getting_url = route("listsettings-ajax");
        $page="listsettings-index";
        $title = "List Management";
        $icon_name = "fa-file";
        $add_btn_txt = "Add New List";
        return view('data_table', compact("add_btn_txt","icon_name", "title","show_add_button","page", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));
    }

    public function listsettings_ajax()
    {
        $table_data = Accessorial::all();

        $data["columns"] = [
            array("data" => "id"),
            array("data" => "title"),           
            array("data" => "group"),
            array("data" => "id",
            "render" =>
                "\"<a href=\"+base_url+\"/listsettings-edit/\"" . '+ data + ' . "\"><button type='button' class='btn btn-block btn-primary btn-xs'>" . __("tran.Edit") . "</button></a>" .
                "<a href=\"+base_url+\"/listsettings-delete/\" + data + \" onClick='return confirm(\\\"Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\\")'><button type='button' class='btn btn-block btn-danger btn-xs'>" . __("tran.Delete") . "</button></a>\""
            )
        ];

        $data["columnDefs"] = [
            array("targets" => 3, "className" => "button_inline", "searchable" => false, "orderable" => false),
            //            array("targets" => 5,"searchable" => false)
        ];
        
        $data["data"] = $table_data;

        return json_encode($data);

    }

    public function create()
    {
        $pageTitle = "Create List Setting";
        $title = "Create List Setting";
        $icon_name = "fa-file";
        $url = route('listsettings-store');
        $groups =$this->groups;
        return view('listsettings.create', compact('pageTitle', 'url', 'groups', 'title', 'icon_name'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'group' => 'required',
        ]);
        
        $input = $request->all();
        $input['status'] = "active";
        $accssorial = Accessorial::create($input);

        if ($accssorial) {
            return redirect()->route("listsettings-index")->with("message", __("tran.Data saved successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }


    public function edit($id)
    {
        
        $data = Accessorial::findorfail($id);
        $pageTitle = "Edit List Setting";
        $url = route("listsettings-update", $id);
        $title = "Edit List Setting";
        $icon_name = "fa-file";
        $groups =$this->groups;
        return view("listsettings.create", compact("pageTitle", "url", "data", 'groups', 'title', 'icon_name'));
    }

    public function update(Request $request, $id)
    {
        if ($request->id != $id) {
            return redirect()->back()->with("error", "Invalid request");
        }
        $request->validate([
            'title' => 'required',
            'group' => 'required',
        ]);

      
        
        $accessorial = Accessorial::findorfail($id);
        $accessorial->title = $request->title;
        $accessorial->group = $request->group;
        $accessorial->save();
      

       
        if ($accessorial) {
            return redirect()->route("listsettings-index")->with("message", __("tran.Data updated successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function softDelete($id)
    {
        $tractor = Accessorial::findOrFail($id);
        $tractor->delete();
        if ($tractor) {
            return redirect()->route("listsettings-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function destroy($id)
    {
        $tractor = Accessorial::findOrFail($id);
        $tractor->forceDelete();
    }
}
