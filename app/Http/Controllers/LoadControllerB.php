<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Utility\MilamUtility;
use stdClass;
use Auth;
class LoadControllerB extends Controller
{
    //
    private $utility;
    function __construct()
    {
        $this->utility = new MilamUtility();
    }
    public function index()
    {
        $show_add_button = true;
        $add_button_link = route("loads-create");
        
        $title_tag = "Loads";
       
        $title = "Load Management";
        $icon_name = "fa-list-alt";

        $table_headers = [
            __("Load ID"), __("Load Status"), __('Last Contact'), __("Customers"),__("Picks"),__("Pick Date"),
            __("Drops"),  __("Drop Date"),  __("tran.Driver"),__("tran.Power Unit"),//tractor
            __("tran.Trailer"),__("Distance"),
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
        $page="loads-index";
        return view("loads.indexB", compact("multi_select","page", "show_add_button", "add_button_link", "title_tag", "table_headers", "ajax_data_getting_url", "title", "icon_name"));
    }

    public function loads_ajax(Request $request, $categoryId=0)
    {
        $ACTIVE_LOADS = 0;
        $PLANNING_LOADS = 1;
        $READY_LOADS = 2;
        $ALL_LOADS = 3;
        $MY_LOADS = 4;

        $pageLength = 100;
        $loads = Load::with("driver", "shipper", "tractor", "trailer", "consignee", 'broker','messageRecords', 'accessories');
        switch($categoryId)
        {
            case $ACTIVE_LOADS:
                $loads = $loads->where('status','!=', 'Billed')
                ->where('status', '!=', 'Paid by Customer')
                ->where('status', '!=', 'Cancelled');
                break;
            case $PLANNING_LOADS:
                $loads = $loads->where('status', 'Pending')
                ->orWhere('status', 'Paid by Customer');
                break;
            case $READY_LOADS:
                $loads = $loads->where('status', 'Billed');
                break;
            case $ALL_LOADS:
                $loads = $loads;
                break;
            case $MY_LOADS:
                $loads = $loads->where('user_id', Auth::user()->id)
                ->where('status', '!=', 'Cancelled');
                break;

            default:
                $loads = $loads->where('status', '!=', 'Compeleted');
                break;
        }
        $totalRecords = $loads->count();
      
        $tableData = collect();
        
        $fieldNames = ["id",
                        "status",
                        "lastContract",
                        "customers",
                        "picks",
                        "pickDate",
                        "drops",
                        "dropDate",
                        "driver",
                        "powerUnit",
                        "trailer",
                        "distance",
                        "income",
                        "expense",
                        "reference",];
        

        $orderColumn = $request->order[0]["column"];
        $orderAsc = false;
        if($request->order[0]["dir"] == "asc")
        {
            $orderAsc = true;
        }
        
        if($request->length)
        {
            $pageLength = $request->length;
        }
       
        $loads = $loads->get();
       
        foreach($loads as $load)
        {
            $picks = "";
            $pickDate = "";
            $drops = "";
            $dropDate = "";
            $stopIndex = 0;
            $lastContract = "";
            $driver = "";
            $powerUnit = "";
            $trailer = "";
            $reference = "";
            $totalIncome  = 0;
            $cost = 0;

            $loadDataTable =[];
            
            if(!empty($load->message_records)&&count($load->message_records) != 0)
            {
                $lastContract = 
                $this->utility->convertDate(
                    $load["message_records"][count($load["message_records"])]->created_at
                );
            }

            if(!empty($load->shipper)&&count($load->shipper) >0)
            {
                foreach($load->shipper as $shipper)
                {
                    $currentAddress = $shipper->pickup_address;
                    if($currentAddress != null && $currentAddress != "")
                    {
                        $pickAddress = $this->utility->getAddress($currentAddress);
                        if($pickAddress != "")
                        {
                            $picks = $picks.(++$stopIndex).
                            ".".$pickAddress."\n";
                        } 
                    }

                    if($shipper->assign)
                    {
                        $pickDate .= "To be assigned \n";

                    }else
                    {
                        if($shipper->start_periode)
                        {
                            $pickDate .= 
                            $this->utility->convertDate(
                                $shipper->start_periode
                            )."\n";
                        }else
                        {
                            $pickDate .=
                            $this->utility->convertDate(
                                $shipper->pickup_date
                            )."\n";
                        }
                    }
                }
            }
            
            
            if(!empty($load->consignee)&&count($load->consignee) >0)
            {
                foreach($load->consignee as $consignee)
                {
                    $currentAddress = $consignee->dropoff_address;
                    if($currentAddress != null && $currentAddress != "")
                    {
                        $dropoffAddress = $this->utility->getAddress($currentAddress);
                        if($dropoffAddress != "")
                        {
                            $drops .= (++$stopIndex).
                            ".".$dropoffAddress."\n";
                        } 
                    }

                    if($consignee->assign)
                    {
                        $dropDate .= "To be assigned \n";

                    }else
                    {
                        if($consignee->start_periode)
                        {
                            $dropDate .= 
                            $this->utility->convertDate(
                                $consignee->start_periode
                            )."\n";
                        }else
                        {
                            $dropDate .=
                            $this->utility->convertDate(
                                $consignee->dropoff_time
                            )."\n";
                        }
                    }
                }
            }
            

            if(!empty($load->accessories)&&count($load->accessories)>0)
            {
                foreach($load->accessories as $val)
                {
                    if(!empty($val->value))
                    {
                        if($val->type == "income")
                        {
                            $totalIncome += (float)$val->value;
                        }else
                        {
                            // $cost += (float)$val->value;
                        }   
                    }
                }
            }
            
            if(!empty($load->cost))
            {
                $cost = $load->cost;
            }

            $loadDataTable["id"] = $load->id;
            $loadDataTable["status"] = $load->status;
            $loadDataTable["lastContract"] = $lastContract;
            $loadDataTable["customers"] = $load->broker?$load->broker->company:"";
            $loadDataTable["picks"] = $picks;
            $loadDataTable["pickDate"] = $pickDate;
            $loadDataTable["drops"] = $drops;
            $loadDataTable["dropDate"] = $dropDate;
            $loadDataTable["driver"] = $load->driver?$load->driver->name:"";
            $loadDataTable["powerUnit"] = $load->tractor?$load->tractor->pun:"";
            $loadDataTable["trailer"] = $load->trailer?$load->trailer->number:"";
            $loadDataTable["distance"] =
            $this->utility->convertNumber($load->miles); 
            $loadDataTable["income"] = "$".
            $this->utility->convertNumber(
                $totalIncome
            );
            $loadDataTable["expense"] = "$".
            $this->utility->convertNumber(
                $cost
            );
            $loadDataTable["reference"] = $load->reference;
            $loadDataTable["broker"] = $load->broker;
            $loadDataTable["shipper"] = $load->shipper;
            $loadDataTable["consignee"] = $load->consignee;
            $loadDataTable["driverObj"] = $load->driver;
            $tableData->push($loadDataTable);
        }

        foreach($request->columns as $key => $val)
        {
            if(!empty($val["search"]["value"]))
            {
                $columnFilter = $val["search"]["value"];
                
                $tableData = $tableData->filter(function($value) use($columnFilter, $fieldNames, $key){
                    return stripos("".$value[$fieldNames[$key]], "".$columnFilter) !== false;
                });
            }
        }

        if(!empty($request->search["value"]))
        {
            $filter = $request->search["value"];
            
            $tableData = $tableData->filter(function($value) use($filter){
                
                return (stripos("".$value["id"], "".$filter) !== false)||
                (stripos("".$value["status"], "".$filter) !== false)||
                (stripos("".$value["lastContract"], "".$filter) !== false)||
                (stripos("".$value["customers"], "".$filter) !== false)||
                (stripos("".$value["picks"], "".$filter) !== false)||
                (stripos("".$value["pickDate"], "".$filter) !== false)||
                (stripos("".$value["drops"], "".$filter) !== false)||
                (stripos("".$value["dropDate"], "".$filter) !== false)||
                (stripos("".$value["driver"], "".$filter) !== false)||
                (stripos("".$value["powerUnit"], "".$filter) !== false)||
                (stripos("".$value["trailer"], "".$filter) !== false)||
                (stripos("".$value["distance"], "".$filter) !== false)||
                (stripos("".$value["income"], "".$filter) !== false)||
                (stripos("".$value["expense"], "".$filter)!==false);
                
            });
        }
        
        $filterCount = count($tableData);
        $tableData = $tableData->slice($request->start)->take($pageLength);
        if($orderAsc)
        {
            $tableData = $tableData->sortBy($fieldNames[$orderColumn])->values()->all();
        }else
        {
            $tableData = $tableData->sortByDesc($fieldNames[$orderColumn])->values()->all();
        }
       
        return response()->json(["data"=>$tableData, "recordsTotal"=>$totalRecords, "recordsFiltered"=>$filterCount]);
    }


}
