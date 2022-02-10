<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Load;
use App\Models\Report;
use App\Models\Customer;
use App\Exports\LoadExport;
use App\Exports\LoadExportSummary;
use Excel;
use Carbon\Carbon;
class SearchLoadController extends Controller
{
    //
    private $state_list = array('AL'=>"Alabama",  
                                'AK'=>"Alaska",  
                                'AZ'=>"Arizona",  
                                'AR'=>"Arkansas",  
                                'CA'=>"California",  
                                'CO'=>"Colorado",  
                                'CT'=>"Connecticut",  
                                'DE'=>"Delaware",  
                                'DC'=>"District Of Columbia",  
                                'FL'=>"Florida",  
                                'GA'=>"Georgia",  
                                'HI'=>"Hawaii",  
                                'ID'=>"Idaho",  
                                'IL'=>"Illinois",  
                                'IN'=>"Indiana",  
                                'IA'=>"Iowa",  
                                'KS'=>"Kansas",  
                                'KY'=>"Kentucky",  
                                'LA'=>"Louisiana",  
                                'ME'=>"Maine",  
                                'MD'=>"Maryland",  
                                'MA'=>"Massachusetts",  
                                'MI'=>"Michigan",  
                                'MN'=>"Minnesota",  
                                'MS'=>"Mississippi",  
                                'MO'=>"Missouri",  
                                'MT'=>"Montana",
                                'NE'=>"Nebraska",
                                'NV'=>"Nevada",
                                'NH'=>"New Hampshire",
                                'NJ'=>"New Jersey",
                                'NM'=>"New Mexico",
                                'NY'=>"New York",
                                'NC'=>"North Carolina",
                                'ND'=>"North Dakota",
                                'OH'=>"Ohio",  
                                'OK'=>"Oklahoma",  
                                'OR'=>"Oregon",  
                                'PA'=>"Pennsylvania",  
                                'RI'=>"Rhode Island",  
                                'SC'=>"South Carolina",  
                                'SD'=>"South Dakota",
                                'TN'=>"Tennessee",  
                                'TX'=>"Texas",  
                                'UT'=>"Utah",  
                                'VT'=>"Vermont",  
                                'VA'=>"Virginia",  
                                'WA'=>"Washington",  
                                'WV'=>"West Virginia",  
                                'WI'=>"Wisconsin",  
                                'WY'=>"Wyoming");
    public function index($id=0)
    {
        $pageTitle = $title = "Search Loads";
        $icon_name = "fa fa-list-alt";
        $page = "search-load";

        $table_headers = [
            __("Load ID"), __("Load Status"), __('Last Contact'), __("Customers"),__("Picks"),__("Pick Date"),
            __("Drops"),  __("Drop Date"),  __("tran.Driver"),__("tran.Power Unit"),//tractor
            __("tran.Trailer"),__("Distance"),
             __("tran.Income"), __("tran.Expenses"), __("tran.Reference") 
        ];
        $reports = Report::all();

        $currentReport = Report::with('origin')
                                ->with('destination')
                                ->find($id);
       
        if($id != 0 &&!$currentReport)
        {
            return redirect()->back()->with("error", __("tran.Report Not Found."));
        }
       
        
        $origins = $destinations = $this->state_list;
        return view('searchload.index', 
                    compact(
                    'pageTitle', 'title', 'icon_name', 'page',
                    'reports',  'currentReport', 'id', 'origins', 'destinations', 'table_headers'
                    )
                );
    }

    public function searchLoadAjax(Request $request)
    {
        $loads_data = $this->searchLoad($request);
        
        $summary = [];
        $loadsCount = $loads_data->count();
        if($loadsCount != 0)
        {
            $total_miles = $loads_data->sum("miles");
            $miles = $loads_data->sum('miles') - $loads_data->sum("dead_head_miles");
            $dead_headmiles = $loads_data->sum("dead_head_miles");
            $customerCount = $loads_data->groupBy('customer_id')->count();    
            $total_expense = 0;
            $total_income = 0;
            $total_gross_profit = 0;
            $clientRPM = 0;
            $clientDhRPM = 0;
            foreach($loads_data as $load_data)
            {
                
                if($load_data->accessories&&$load_data->accessories->count())
                {                    
                    foreach($load_data->accessories as $val)
                    {
                        if(!empty($val->value)&&$val->type == "income")
                        $total_income = $total_income + $val->value;

                        if(!empty($val->value)&&$val->type == "expense")
                        $total_expense = $total_expense + $val->value;
                    }
                }  
                    
                    
            }
            $total_gross_profit = $total_income - $total_expense;
            if($miles != 0)
            {
                $clientRPM = round($total_income / $miles, 2);
            }

            if($total_miles != 0)
            {
                $clientDhRPM = round($total_income / $total_miles, 2);
            }

            $summary['miles'] =  $miles;
            $summary['total_miles'] = $total_miles;
            $summary['dead_headmiles'] = $dead_headmiles ;
            $summary['total_income'] = $total_income;
            $summary['total_expense'] = round($total_expense,2);
            $summary['total_gross_profit'] =  $total_gross_profit;
            $summary['clientRPM'] = $clientRPM;
            $summary['clientDhRPM'] = $clientDhRPM;
           
        }else
        {
            $summary['miles'] = 0;
            $summary['total_miles'] = 0;
            $summary['dead_headmiles'] = 0;
            $summary['total_income'] = 0;
            $summary['total_expense'] = 0;
            $summary['total_gross_profit'] = 0;
            $summary['clientRPM'] = 0;
            $summary['clientDhRPM'] = 0;
        }
        $table_data = $loads_data;
        
        $data = [];
        $data["columns"] = [
            array("data" => "id", 'render'=>"
            \"<a class='tooltip-container' href=\"+base_url+\"/loads-edit/\"" . '+ data + ' . "\"> \"+data+\"</a>\" " ),
            
            array("data" => "status"),
            
            array("data"=>"id", "render"=> "
            var options = {
                year: \"numeric\",
                month: \"2-digit\",
                day: \"2-digit\",
                hour: \"2-digit\",
                minute: \"2-digit\"
            };
            if(row.message_records.length != 0)
            {
              var legnth = row.message_records.length-1;
              var date = new Date(row.message_records[legnth].created_at).toLocaleDateString(\"en\", options);  

              date;
            }else
            ''
            "),
            array("data" => "id"
            , "render" =>
                "
                if(row.broker)
                \"<span class='tooltip-container'>\"+row.broker.company+\"</span>\";
                else
                ''
                "
            ),
            //            pickup adddress
            array("data" => "id"
            , "render" =>
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
                    \"<span class='tooltip-container'>\"+shippers+\"</span>\";
                 }
                 else
                  ''
                 "
            ),
            //            pickup_date
            array("data" => "id"
            , "render" =>
                "
                if(row.shipper){
                 var shippers=\"\";
                 var options = {
                    year: \"numeric\",
                    month: \"2-digit\",
                    day: \"2-digit\",
                    hour: \"2-digit\",
                    minute: \"2-digit\"
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

                    \"<span class='tooltip-container'>\"+date+\"</span>\";
                  }
                   
                  else
                    ''
                } else
                  ''
            "),

            //            dropoff_address
            array("data" => "id"
            , "render" =>
                "
                var originIndex = 1
                if(row.shipper&&row.shipper.length>0){
                    originIndex = row.shipper.length+1
                }
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
                                    consignee=consignee+(index+originIndex)+'. '+splited[splited.length-4]+' '+splited[splited.length-3]+' '+splited[splited.length-2]+' '+splited[splited.length-1]+'<br>'
                                else
                                    consignee=consignee+(index+originIndex)+'. '+splited[splited.length-3]+' '+splited[splited.length-2]+' '+splited[splited.length-1]+'<br>'
                            }
                           

                            
                        }else
                        {
                            ''
                        }
                        
                    });
                    \"<span class='tooltip-container'>\"+consignee+\"</span>\";
                }
                else
                ''
                "
            ),
            //            dropoff_time
            array("data" => "id"
            , "render" =>
                "
                if(row.consignee){
                 var consignee=\"\";
                 var options = {
                    year: \"numeric\",
                    month: \"2-digit\",
                    day: \"2-digit\",
                    hour: \"2-digit\",
                    minute: \"2-digit\"
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

                    \"<span class='tooltip-container'>\"+date+\"</span>\"
                  }
                   
                  else
                    ''
                } else
                  ''
                 "),
            array("data" => "id"
                 , "render" =>
                     "
                     if(row.driver)
                     \"<span class='tooltip-container'>\"+row.driver.name+\"</span>\";
                     else
                     ''
                     "
            ),    
          
         
            
            array("data" => "id"
            , "render" =>
                "
                if(row.tractor)
                row.tractor.pun;
                else
                ''
                "
            ),
            array("data" => "id"
            , "render" =>
                "
                if(row.trailer)
                row.trailer.number;
                else
                ''
                "
            ),
            
            array("data" => "miles", "render"=>"
            if(row.miles == null)
            0
            else
            {
                var miles =  row.miles.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                miles;
            }
            
            "),
            array("data" => "profit", "render"=> "
               
                var totalincome = 0;

                row.accessories.forEach(function (currentValue, index, arr) {    
                    if(currentValue.value != null&&currentValue.type==\"income\")
                        totalincome=parseFloat(totalincome)+parseFloat(currentValue.value);
                    
                });
        
                
                if(totalincome == null||totalincome == 0)
                '$'+0
                else
                {
                    var profit = '$'+ totalincome.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                    profit;
                }
            "),
            

            array("data" => "cost", "render"=> "
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

            array("targets" => 4, "createdCell"=> "","width"=> 150),
            array("width"=> 100, "targets"=> 5 ),
            array("width"=> 150, "targets"=> 6 ),
            array("width"=> 100, "targets"=> 7 ),
        ];

        $data["data"] = $table_data;
        
        $result["summary"] = $summary;

        $result["table_data"] = $data;

        return response()->json(['result'=>$result]);
        
    }

    public function store(Request $request)
    {
        if($request->id)
        {
            $report = Report::where('id', $request->id)
                            ->update([
                                'search_terms' => $request->search_terms,
                                'origin' => $request->origin,
                                'destination' => $request->destination,
                                'range_start' => $request->range_start,
                                'range_end' => $request->range_end,
                                'range_type' => $request->range_type,
                                'status' => $request->status,
                                'column_layout' => $request->column_layout,
                            ]);
        }else
        {
            $report = Report::create([
                'search_terms' => $request->search_terms,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'range_start' => $request->range_start,
                'range_end' => $request->range_end,
                'range_type' => $request->range_type,
                'status' => $request->status,
                'column_layout' => $request->column_layout,
            ]);
        }
        return response()->json(['status'=>'save', 'report'=>$report]);
    } 

    public function delete($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        if ($report) {
            return redirect()->route("search-load-index")->with("message", __("tran.Data Deleted successfully"));
        } else {
            return redirect()->back()->with("error", __("tran.An Error occurred in the system.Please Try Again!"));
        }
    }

    public function getReport(Request $request)
    {
        return response()->json(Report::all());
    }

    public function export(Request $request)
    {
        $loads_data = $this->searchLoad($request);
        
        return Excel::download(new LoadExport($loads_data, $request->column_layout), 'LoadReport.xlsx');
       
    }

    public function exportSummary(Request $request)
    {
        $loads_data = $this->searchLoad($request);
        $summary = collect();
        $result = collect();
        $loadsCount = $loads_data->count();
        
        if($loadsCount != 0)
        {
            $total_miles = $loads_data->sum("miles");
            $miles = $loads_data->sum('miles') - $loads_data->sum("dead_head_miles");
            $dead_headmiles = $loads_data->sum("dead_head_miles");
            $customerCount = $loads_data->groupBy('customer_id')->count();    
            $total_expense = 0;
            $total_income = 0;
            $total_gross_profit = 0;
            $clientRPM = 0;
            $clientDhRPM = 0;
            foreach($loads_data as $load_data)
            {
                
                if($load_data->accessories&&$load_data->accessories->count())
                {                    
                    foreach($load_data->accessories as $val)
                    {
                        if(!empty($val->value)&&$val->type == "income")
                        $total_income = $total_income + $val->value;

                        if(!empty($val->value)&&$val->type == "expense")
                        $total_expense = $total_expense + $val->value;
                    }
                }  
                    
                    
            }
            $total_gross_profit = $total_income - $total_expense;
            if($miles != 0)
            {
                $clientRPM = round($total_income / $miles, 2);
            }

            if($total_miles != 0)
            {
                $clientDhRPM = round($total_income / $total_miles, 2);
            }


            $result->push((string)$total_income); // to. revenue
            $result->push((string)$miles); // av. revenue
            $result->push((string)$clientRPM); // av revenu per mile

            $result->push((string)$dead_headmiles);
            $result->push((string)$total_miles);
            $result->push((string)$clientDhRPM); // to customer revenue

            $result->push((string)$total_income);
            $result->push((string)$total_expense);
            $result->push((string)$total_gross_profit); // to customer revenue
           
            
        }
        $summary->push($result);
        //$summary->downloadExcel('load_summary.xlsx');
        return Excel::download(new LoadExportSummary($summary), 'load_summary.xlsx');
    }

    private function searchLoad($request)
    {
        $loads =  Load::with("driver", "shipper", "tractor", "trailer", "consignee", 'broker', 'accessories', 'messageRecords');
        if($request->status != 'all')
        {
            $loads =$loads->where('status', $request->status);
        }

        $loads = $loads->get();
        // if($request->search_terms)
        // {
        //     dd($request->search_terms);
        //     $loads = $loads->where(function($query){
        //                 $query->where('reference', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('status', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('driver_payment', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('address', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('cost', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('profit', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('miles', 'LIKE',  '%'.$request->search_terms.'%')
        //                     ->orwhere('dead_head_miles', 'LIKE',  '%'.$request->search_terms.'%');
        //     });
        // }
        $loads_data = collect();

      
        if(empty($request->destination))
        {
            
            $loads_data = $loads;
            
        }else
        {
            $loads_data = $loads->filter(function($value) use($request){
                if($value->consignee&&$value->consignee->count()>0)
                {
                    
                    foreach($value->consignee as $val)
                    {
                        $address = $val->dropoff_address;
                        $addressList = explode(",", $address);
                        
                        if(count($addressList) > 3) 
                        {
                            $state = explode(" ", $addressList[2])[1];
                            return $state == $request->destination;
                        }
                    }
                }
            });
            
            
        }

        
        if(!empty($request->origin))
        {
            $loads_data = $loads_data->filter(function($value) use($request){
                if($value->shipper&&$value->shipper->count()>0)
                {
                    
                    foreach($value->shipper as $val)
                    {
                        $address = $val->pickup_address;
                        $addressList = explode(",", $address);
                        
                        if(count($addressList) > 3) 
                        {
                            $state = explode(" ", $addressList[2])[1];
                            return $state == $request->origin;
                        }
                    }
                }
            });
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

            case "ID":
                $filteredData =  $loads_data->where('completed_date', ">=", $range_start)
                            ->where('completed_date', "<=", $range_end); 
                
                foreach($filteredData as $val)
                {
                    $result->push($val);
                }   
            break;
            
            default:
                foreach($loads_data as $load)
                {
                    
                    if($load->shipper&&$load->shipper->count())
                    {
                        $shipper = $load->shipper[0];
                        if(!empty($shipper->start_period))
                        {
                            if($shipper->start_periode >= $range_start&&$shipper->start_periode <= $range_end)
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
        // dd(count($result));
        return $result;
    }
}
