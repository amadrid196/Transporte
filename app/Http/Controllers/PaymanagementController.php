<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Utility\MilamUtility;
use Carbon\Carbon;
use stdClass;
use Auth;

class PaymanagementController extends Controller
{
    //
    private $utility;
    function __construct()
    {
        $this->utility = new MilamUtility();
    }
    public function index()
    {
        $show_add_button = false;
        $add_button_link = "#";
        $title_tag = "Drivers Pay Management";
        $title_on_page = "Drivers Pay Management";

        $title = "Pay Management";
        $icon_name = "fa-dollar";
        $table_headers = ["#", __("tran.Driver Name"),  __("tran.Pay Status")
            ,  __("tran.Status")
            , __("tran.Pickup Address"), __("tran.Last Delivery Address"), __("tran.Total Miles")
            , __("tran.Expenses"), __("tran.Profit"), __("tran.Invoice Status"), __("Pick Date"),__("Drop Date")
           
        ];
        $ajax_data_getting_url = route("drivers-paymanagement-index-ajax");
        $min_max_filter = ["value" => true, "min" =>10, "max" => 10, "type" => "date", "range_type"=>true];

        $multi_select = true;

        $from1 = new stdClass();
        $from1->route = route("driver-send-invoice-multiple");
        $from1->method = "post";
        $from1->btn_class = "btn btn-xs btn-success";
        $from1->button_txt = __("tran.Send Settlement");
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
        $from3->btn_attr = "_blank";
        $from3->button_txt = __("tran.Show PDF");
        $multiselect_forms[2] = $from3;
        

        
        $page="paymanagement_index";
        return view("drivers.paymanageindex", compact("title", "icon_name", "multi_select","page", "multiselect_forms", "min_max_filter", "show_add_button", "add_button_link", "title_tag", "title_on_page", "table_headers", "ajax_data_getting_url"));

    
    }


    public function dateRangePayAjax(Request $request, $category=1)
    {
        $PAIDPAYMENT = 1;
        $PENDINGPAYMENT = 2;
        $category = $request->category;
        $tableData = collect();
        $fieldNames = ["id",
        "driverName",
        "payStatus",
        "status",
        "pickupAddress",
        "lastDeliveryAddress",
        "totalMiles",
        "expenses",
        "profit",
        "invoiceStatus",
        "pickDate",
        "dropDate",
        "driver"];
        $pageLength = 100;
        $orderColumn = 0;
        if(isset($request->order))
        {
            $orderColumn = $request->order[0]["column"];
        }
        $orderAsc = true;
        
        if(isset($request->order)&&$request->order[0]["dir"] != "asc")
        {
            $orderAsc = true;
        }
        
        if($request->length)
        {
            $pageLength = $request->length;
        }
        if($category == $PENDINGPAYMENT)
        {
            $loads = Load::where("driver_payment", "Pending")
            ->whereHas('driver')
            ->whereHas('consignee')
            ->with("driver", "shipper", "consignee", "deductions", "accessories")
            ->get();
        }else
        {
            $loads = Load::where("driver_payment", '!=' ,"Pending")
            ->whereHas('driver')
            ->whereHas('consignee')
            ->with("driver", "shipper", "consignee", "deductions", "accessories")
            ->get();
        }

        $range_start = "2000-01-01";
        $range_end = "2099-12-31 23:59:59";

        
        
        $result = collect();
        if(!empty($request->range_start))
        {
            $range_start = $request->range_start;
        }

        if(!empty($request->range_end))
        {
            $range_end = $request->range_end." 23:59:59";
        }
        
        switch($request->range_type)
        {
            case "FPD":
                foreach($loads as $load)
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
                                if(empty($request->range_start)&&empty($reqeust->range_end))
                                {
                                    $result->push($load);
                                }
                            }
                            
                        }
                    }

                }
            break;

            case "LDD":
                foreach($loads as $load)
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
                                if(empty($request->range_start)&&empty($reqeust->range_end))
                                {
                                    $result->push($load);
                                }
                            }
                            
                        }
                        
                    }
                }
            break;
            
            
            default:
                foreach($loads as $load)
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
                                if(empty($request->range_start)&&empty($reqeust->range_end))
                                {
                                    $result->push($load);
                                }
                            }
                            
                        }
                    }

                }
            break;
        }
        $totalRecords = count($result);
        
        $tableData = $this->getPaymanagementDatatableReponse($result);

        if(!empty($request->range_start))
        {
            $data["range_start"] = $request->range_start;
        }

        if(!empty($request->range_end))
        {
            $data["range_end"] = $request->range_end;
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
                (stripos("".$value["driverName"], "".$filter) !== false)||
                (stripos("".$value["payStatus"], "".$filter) !== false)||
                (stripos("".$value["status"], "".$filter) !== false)||
                (stripos("".$value["pickupAddress"], "".$filter) !== false)||
                (stripos("".$value["lastDeliveryAddress"], "".$filter) !== false)||
                (stripos("".$value["totalMiles"], "".$filter) !== false)||
                (stripos("".$value["expenses"], "".$filter) !== false)||
                (stripos("".$value["profit"], "".$filter) !== false)||
                (stripos("".$value["invoiceStatus"], "".$filter) !== false)||
                (stripos("".$value["pickDate"], "".$filter) !== false)||
                (stripos("".$value["dropDate"], "".$filter) !== false);
                
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

    private function getPaymanagementDatatableReponse($loads)
    {
        $tableData = collect();
        foreach($loads as $load)
        {
            $expense = 0;
            $deductions = 0;
            $pickDate = "";
            $dropDate = "";
            foreach($load->accessories as $val)
            {
                if(!empty($val->value)&&$val->type=="expense")
                {
                    $expense += (float)$val->value;
                }
            }

            foreach($load->deductions as $val)
            {
                if(!empty($val->value))
                {
                    $deductions += (float)$val->value;
                }
                
            }

            if(!empty($load->shipper)&&count($load->shipper) >0)
            {
                foreach($load->shipper as $shipper)
                {
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
            $totalExpense = $this->utility->convertNumber($deductions + $expense);
            $expense = $this->utility->convertNumber($expense);
            $deductions = $this->utility->convertNumber($deductions);

            $paymanagementDataTable = [];
            $paymanagementDataTable["id"] = $load->id;
            $paymanagementDataTable["driverName"] = $load->driver?$load->driver->name:"";
            $paymanagementDataTable["payStatus"] = $load->driver_payment;
            $paymanagementDataTable["status"] = $load->status;
            $paymanagementDataTable["pickupAddress"]  = "";
            $paymanagementDataTable["lastDeliveryAddress"] =  "";
            if(!empty($load->shipper)&&count($load->shipper))
            {
                $paymanagementDataTable["pickupAddress"] = $load->shipper[0]->pickup_address;
            }
            if(!empty($load->consignee)&&count($load->consignee))
            {
                $paymanagementDataTable["pickupAddress"] = $load->consignee[count($load->consignee)-1]->dropoff_address;
            }
            
            $paymanagementDataTable["totalMiles"] = 
            $this->utility->convertNumber($load->miles);
            $paymanagementDataTable["expenses"] = $deductions." + ".$expense." = $".$totalExpense;
            $paymanagementDataTable["profit"] = "$".
            $this->utility->convertNumber(
                $load->profit
            );
            $paymanagementDataTable["invoiceStatus"] = $load->inv_status;
            $paymanagementDataTable["pickDate"] = $pickDate;
            $paymanagementDataTable["dropDate"] = $dropDate;
            $paymanagementDataTable["driver"] = $load->driver;
            $tableData->push($paymanagementDataTable);
        }

        return $tableData;
    }
}
