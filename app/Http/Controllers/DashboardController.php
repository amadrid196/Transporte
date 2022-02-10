<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use Carbon\Carbon;
use App\Models\Shipper;
use App\Models\Consignee;
use App\Models\UsCity;
class DashboardController extends Controller
{
    //
    public function loadSummary(Request $request)
    {
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        
        $prevStartDate = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');  
        $prevEndDate = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
     
                
        $load_ids = Shipper::where(function($q) use($weekStartDate, $weekEndDate){
            $q->whereBetween('start_periode', [$weekStartDate, $weekEndDate]);
        })->orWhere(function($q) use($weekStartDate, $weekEndDate) {
            $q->whereBetween('pickup_date', [$weekStartDate, $weekEndDate]);
        })->select("load_id")->groupBy("load_id")->pluck('load_id');
        $load_ids = $this->filterLoadIds($load_ids, $weekStartDate, $weekEndDate);

        $loads = Load::whereIn('id', $load_ids);

        $total_miles = round($loads->sum('miles'), 2);
        $total_dead_head_miles = round($loads->sum('dead_head_miles'), 2);
        $total_profit = round($loads->sum('profit'), 2);

        if($total_miles == 0)
        {   
            $rate_per_miles = 0;
        }else
        {
            $rate_per_miles =  round($total_profit / $total_miles, 2);
        }

        $prev_load_ids = Shipper::where(function($q) use($prevStartDate, $prevEndDate){
            $q->whereBetween('start_periode', [$prevStartDate, $prevEndDate]);
        })->orWhere(function($q) use($prevStartDate, $prevEndDate) {
            $q->whereBetween('pickup_date', [$prevStartDate, $prevEndDate]);
        })->select("load_id")->groupBy("load_id")->pluck('load_id');
        $prev_load_ids = $this->filterLoadIds($prev_load_ids, $prevStartDate, $prevEndDate);

        $prev_loads = Load::whereIn('id', $prev_load_ids);

        $prev_total_miles = round($prev_loads->sum('miles'), 2);
        $prev_total_dead_head_miles = round($prev_loads->sum('dead_head_miles'), 2);
        $prev_total_profit = round($prev_loads->sum('profit'), 2);
        if($prev_total_miles == 0)
        {   
            $prev_rate_per_miles = 0;
        }else
        {
            $prev_rate_per_miles =  round($prev_total_profit / $prev_total_miles, 2);
        }
       
        if($prev_total_miles != 0)
        {
            $diff_total_miles = round(($total_miles-$prev_total_miles)/$prev_total_miles, 2);
        }else
        {
            $diff_total_miles = 0;
        }
      

        if($prev_total_dead_head_miles != 0)
        {
            $diff_total_dead_head_miles = round(($total_dead_head_miles-$prev_total_dead_head_miles)/$prev_total_dead_head_miles, 2);
        }else
        {
            $diff_total_dead_head_miles = 0;
        }
        // $diff_total_dead_head_miles = round($total_dead_head_miles/$prev_total_dead_head_miles, 2);

        if($prev_total_profit != 0)
        {
            $diff_total_profit = round(($total_profit-$prev_total_profit)/$prev_total_profit, 2);
        }else
        {
            $diff_total_profit = 0;
        }


        #diff_total_profit = round($total_profit/$prev_total_profit, 2);
        if($prev_rate_per_miles != 0)
        {
            $diff_rate_per_miles = round(($rate_per_miles-$prev_rate_per_miles)/$prev_rate_per_miles, 2);
        }else
        {
            $diff_rate_per_miles = 0;
        }

        #$diff_rate_per_miles = round($rate_per_miles/$prev_rate_per_miles, 2);
        $total_miles = number_format($total_miles);
        $total_dead_head_miles = number_format($total_dead_head_miles);
        $total_profit = number_format($total_profit, 2);
        $rate_per_miles = number_format($rate_per_miles, 2);
        $prev_total_miles = number_format($prev_total_miles, 2);
        $prev_total_dead_head_miles = number_format($prev_total_dead_head_miles, 2);
        $prev_total_profit = number_format($prev_total_profit, 2);
        $prev_rate_per_miles= number_format($prev_rate_per_miles, 2);
        $diff_total_miles = number_format($diff_total_miles*100, 2);
        $diff_total_dead_head_miles = number_format($diff_total_dead_head_miles*100, 2);
        $diff_total_profit= number_format($diff_total_profit*100, 2);
        $diff_rate_per_miles = number_format($diff_rate_per_miles*100, 2);

        
        return response()->json([
            'total_miles' => $total_miles,
            'total_dead_head_miles' => $total_dead_head_miles,
            'total_profit' => $total_profit,
            'rate_per_miles' => $rate_per_miles,
            'prev_total_miles' => $prev_total_miles,
            'prev_total_dead_head_miles' => $prev_total_dead_head_miles,
            'prev_total_profit' => $prev_total_profit,
            'prev_rate_per_miles' => $prev_rate_per_miles,
            'diff_total_miles' => $diff_total_miles,
            'diff_total_dead_head_miles' => $diff_total_dead_head_miles,
            'diff_total_profit' => $diff_total_profit,
            'diff_rate_per_miles' => $diff_rate_per_miles,
        ]);
    }

    public function loadSearch(Request $request)
    {

    }

    public function daliyGrossChart(Request $request)
    {
        $index = $request->index;
        
        $daysAgo = -10*($index+1);
        $today = Carbon::now();

        $startDate = Carbon::now()->add($daysAgo, 'day');
        $dataList = [];

        $todayLoadIds= Shipper::whereDate("pickup_date", $today->format('Y-m-d'))
                                ->orwhereDate('start_periode', $today->format('Y-m-d'))
                                ->select("load_id")
                                ->groupBy("load_id")
                                ->pluck('load_id');
        
        $todayLoadIds = $this->filterLoadIdsByDate($todayLoadIds, $today->format('Y-m-d'));
       
        $todayProfits = round(Load::whereIn('id', $todayLoadIds)->sum('profit'), 2);
        $lastDate = $today->add(-1, 'day')->format('Y-m-d');
        $lastLoadIds= Shipper::whereDate("pickup_date", $today->format('Y-m-d'))
                                ->orwhereDate('start_periode', $today->format('Y-m-d'))
                                ->select("load_id")
                                ->groupBy("load_id")
                                ->pluck('load_id');
        
        

        $lastProfits = round(Load::whereIn('id', $lastLoadIds)->sum('profit'), 2);
        if($todayProfits != 0)
        {
            $diff = number_format(round(($todayProfits-$lastProfits)*100/$lastProfits, 2), 2);
        }else
        {
            $diff = 0;
        }
       

        for($i = 0; $i < 10; $i++)
        {
            $currentDate = $startDate->add(1, 'day')->format('Y-m-d');  
            $load_ids = Shipper::whereDate("pickup_date", $currentDate)
                                ->orwhereDate('start_periode', $currentDate)
                                ->select("load_id")
                                ->groupBy("load_id")
                                ->pluck('load_id');
            $profits = round(Load::whereIn('id', $load_ids)->sum('profit'), 2);
            $data = [
                "year" => $currentDate,
                "value" => $profits,
            ];
            array_push($dataList, $data);
        }

        return response()->json([
            "data"=>$dataList,
            "todayProfits"=>number_format($todayProfits, 2),
            "lastProfits"=>number_format($lastProfits, 2),
            "diff"=>number_format($diff, 2)
        ]);
    }

    public function pickUpCities(Request $request)
    {
        $today = Carbon::now();

        $startDate = $today->firstOfYear()->format('Y-m-d');
        $lastDate = $today->endOfMonth()->format('Y-m-d');  
        $shippers = Shipper::with("customer")->where(function($q) use($startDate, $lastDate){
            $q->whereBetween('start_periode', [$startDate, $lastDate]);
        })->orWhere(function($q) use($startDate, $lastDate) {
            $q->whereBetween('pickup_date', [$startDate, $lastDate]);
        })->whereHas('customer')->groupBy('pickup_address')
        ->selectRaw('count(*) as total, pickup_address')
        ->orderByDesc("total")
        ->get();
       
        $consignees = Consignee::with("customer")->where(function($q) use($startDate, $lastDate){
            $q->whereBetween('start_periode', [$startDate, $lastDate]);
        })->orWhere(function($q) use($startDate, $lastDate) {
            $q->whereBetween('dropoff_time', [$startDate, $lastDate]);
        })->groupBy('dropoff_address')
        ->selectRaw('count(*) as total, dropoff_address')
        ->get();
        $address = [];
        $visited = [];
        $addressComponents = [];
        $cities = [];

        foreach($shippers as $shipper)
        {
            if($shipper->pickup_address)
            {
                $addressComponent = explode(", ", $shipper->pickup_address);
                $usCity = UsCity::whereIn('city', $addressComponent)->first();
                $cityName = "";
                if(!empty($usCity))
                {
                    $cityName = $usCity->CITY;
                    $index = array_search($cityName, $address);
                    if($index)
                    {
                        $visited[$index] += $shipper->total; 
                        
                    }else
                    {
                        array_push($address, $cityName);
                        array_push($visited, $shipper->total);
                    }
                }
            }
        }

        foreach($consignees as $consignee)
        {
            if($consignee->dropoff_address)
            {
                $addressComponent = explode(", ", $consignee->dropoff_address);
                $usCity = UsCity::whereIn('city', $addressComponent)->first();
                $cityName = "";
                if(!empty($usCity))
                {
                    $cityName = $usCity->CITY;
                    $index = array_search($cityName, $address);
                    if($index)
                    {
                        $visited[$index] += $consignee->total;
                    }else
                    {
                        array_push($address, $cityName);
                        array_push($visited, $consignee->total);
                    }
                }
            }
           
        }
        for($i=0;$i<count($visited);$i++){
            $val = $visited[$i];
            $addressVal = $address[$i];
            $j = $i-1;
            while($j>=0 && $visited[$j] < $val){
                $visited[$j+1] = $visited[$j];
                $address[$j+1] = $address[$j];
                $j--;
            }
            $visited[$j+1] = $val;
            $address[$j+1] = $addressVal;
        }

        
        $results = [];
        foreach($address as $key=>$val)
        {
            if($key == 5)
            {
                break;
            }
            // $val= explode(" ", $val);
            // if(count($val) > 2)
            // {
                $result = [
                    "label" => $val,
                    "value" => $visited[$key],
                ];
                array_push($results, $result);
            // }
            

            
        }
        
        return response()->json([
            "data"=>$results
        ]);
    }

    public function filterLoadIds($loadIds, $startDate, $endDate)
    {
        $results = [];

        $loads = Load::with('shipper')->whereIn('id', $loadIds)->get();
        $startDate = Carbon::parse($startDate);
       
        $endDate = Carbon::parse($endDate);
        foreach($loads as $load)
        {
            if(count($load->shipper)>0)
            {
                $shipper = $load->shipper[0];
                $date = null;
                if(!empty($shipper->start_periode))
                {
                    $date = Carbon::parse($shipper->start_periode);
                }else
                {
                    $date = Carbon::parse($shipper->pickup_date);
                }
                
                if(!empty($date))
                {
                    if($startDate <= $date && $endDate >= $date)
                    {
                        array_push($results, $load->id);
                    }
                }
               
                
            }
        }

        return $results;
    }


    public function filterLoadIdsByDate($loadIds, $currentDate)
    {
        $results = [];

        $loads = Load::with('shipper')->whereIn('id', $loadIds)->get();
        $currentDate = Carbon::parse($currentDate)->format("Y-m-d");
     
        foreach($loads as $load)
        {
            if(count($load->shipper)>0)
            {
                $shipper = $load->shipper[0];
                $date = null;
                if(!empty($shipper->start_periode))
                {
                    $date = Carbon::parse($shipper->start_periode)->format("Y-m-d");
                }else
                {
                    $date = Carbon::parse($shipper->pickup_date)->format("Y-m-d");
                }
                if(!empty($date))
                {
                    if($currentDate == $date)
                    {
                        array_push($results, $load->id);
                    }
                }
               
                
            }
        }

        return $results;
    }
}
