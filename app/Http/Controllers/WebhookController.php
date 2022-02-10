<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebHook;
use App\Models\KeepTruck;
use App\Models\KeeepLoad;
use App\Models\KeepDriver;
use App\Events\SendLocation;
use App\Events\DriverLocation;
use Illuminate\Support\Facades\Http;
use App\Models\Load;
use App\Models\Tractor;


class WebhookController extends Controller
{
    //
    public function index(Request $request)
    {
        $input = $request->all();
        WebHook::create([
            "content"=> json_encode($input)
        ]);
        return "ab81c5b5d17d4c22976ddc46a59126c2";
    }
    // Web hook

    public function indexB(Request $request)
    {
        $actionType = $request->input("action");
        
        $locateData = null;
        if($actionType == "vehicle_location_received" || $actionType == "vehicle_location_updated")
        {
            $vehicle_id = $request->input('vehicle_id');

            $keepTruck = keepTruck::where("vehicle_id", $vehicle_id)->first();
            
            if(empty($keepTruck))
            {
                /// ToDp tp call the vechile inofmration from thid party api.
                $keepTruck = $this->getViechle($vehicle_id);
                if(empty($keepTruck))
                {
                    return $locateData;
                }
            }
            $current_driver_id = 0;
            $current_driver_id = $this->getCurrentDriverid($request->input("vehicle_id"));
           
            $keepTruck->current_driver_id = $current_driver_id;
            $keepTruck->save();
           
            $currentWebhook = WebHook::updateOrCreate(
                ["vehicle_id"=>$vehicle_id],
                [
                    "action" => $request->input("action"),
                    "bearing" => $request->input("bearing"),
                    "description" => $request->input("description"),
                    "engine_hours" => $request->input("engine_hours"),
                    "fuel" => $request->input("fuel"),
                    "lat" => $request->input("lat"),
                    "located_at" => $request->input("located_at"),
                    "lon" => $request->input("lon"),
                    "odometer" => $request->input("odometer"),
                    "speed" => $request->input("speed"),
                    "trigger" => $request->input("trigger"),
                    "type" => $request->input("type"),
                    "vehicle_id" => $request->input("vehicle_id"),
                    "current_driver_id" => $current_driver_id,
                ]
            );
            $currentDriver = KeepDriver::where("id", $current_driver_id)->first();

            if(empty($currentDriver))
            {
                $currentDriver = $this->getDriver($current_driver_id);
            }
            $currentTruck = KeepTruck::where('vehicle_id', $currentWebhook->vehicle_id)->first();
            $currentLocateData['id'] = $currentWebhook->id;
            $currentLocateData['drvier_name'] = $currentDriver->first_name." ". $currentDriver->last_name;
            $currentLocateData["phone"] = $currentDriver->phone;
            $currentLocateData["lat"] = $currentWebhook->lat;
            $currentLocateData["lng"] = $currentWebhook->lon;
            $currentLocateData["vehicle_id"] = $currentWebhook->vehicle_id;
            $currentLocateData["license_plate_number"] = $currentTruck->license_plate_number;
            $currentLocateData["lincense_plate_state"] = $currentTruck->lincense_plate_state;
            $currentLocateData["speed"] = $currentWebhook->speed;
            $currentLocateData["bearing"] = $currentWebhook->bearing;
            $currentLocateData["description"] = $currentWebhook->description;
            $currentLocateData["load_id"] = null;
            $currentLocateData["reference"] = "";
            $tractor = Tractor::where("license_plate", $currentTruck->license_plate_number)->first();

            if(!empty($tractor))
            {
                $load = Load::where("tractor_id", $tractor->id)
                ->whereIn("status", ["Dispatched", "In Transit"])
                ->orderBy("created_at", "desc")->first();
                if(!empty($load))
                {
                    $currentLocateData["load_id"] = $load->id;
                    $currentLocateData["reference"] = $load->reference;
                }
            }else
            {
                Tractor::create([
                    "model"=>$currentTruck->model,
                    "pun"=>$currentTruck->number,
                    "status"=>$currentTruck->status,
                    "fuel_type"=>$currentTruck->fuel_type,
                    "license_plate"=>$currentTruck->license_plate_number,
                    "vehicle_id"=>$currentTruck->vin,
                ]);
            }
            $results = [];
            $webHooks = WebHook::get();

            foreach($webHooks as $webHook)
            {
                $locateData = [];
                $keepTruck = keepTruck::where("vehicle_id", $webHook->vehicle_id)->first();
                $currentDriver = KeepDriver::where("id", $webHook->current_driver_id)->first();

                $locateData['id'] = $webHook->id;
                $locateData['drvier_name'] = $currentDriver->first_name." ". $currentDriver->last_name;
                $locateData["phone"] = $currentDriver->phone;
                $locateData["lat"] = $webHook->lat;
                $locateData["lng"] = $webHook->lon;
                $locateData["vehicle_id"] = $webHook->vehicle_id;
                $locateData["license_plate_number"] = $keepTruck->license_plate_number;
                $locateData["lincense_plate_state"] = $keepTruck->lincense_plate_state;
                $locateData["speed"] = $webHook->speed;
                $locateData["bearing"] = $webHook->bearing;
                $locateData["description"] = $webHook->description;
                $locateData["load_id"] = null;
                $locateData["reference"] = "";
                $tractor = Tractor::where("license_plate", $keepTruck->license_plate_number)->first();

                if(!empty($tractor))
                {
                    $load = Load::where("tractor_id", $tractor->id)
                    ->whereIn("status", ["Dispatched", "In Transit"])
                    ->orderBy("created_at", "desc")->first();
                    if(!empty($load))
                    {
                        $locateData["load_id"] = $load->id;
                        $locateData["reference"] = $load->reference;
                    }
                }else
                {
                    Tractor::create([
                        "model"=>$keepTruck->model,
                        "pun"=>$keepTruck->number,
                        "status"=>$keepTruck->status,
                        "fuel_type"=>$keepTruck->fuel_type,
                        "license_plate"=>$keepTruck->license_plate_number,
                        "vehicle_id"=>$keepTruck->vin,
                    ]);
                }
                array_push($results, $locateData);
            }

            $data = [
                'currentLocateData' => $currentLocateData,
                'results' => $results,
            ];
            event(new SendLocation($data));
        }

        return "ab81c5b5d17d4c22976ddc46a59126c2";
    }

    public function getViechle($id)
    {
        
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://api.keeptruckin.com/v1/vehicles/'.$id, [
            'headers' => [
                'Accept' => 'application/json',
                "X-Api-Key" => env('KEEP_API_KEY'),
            ],
        ]);

        $keepTruck = null;
      
        if($response->getStatusCode() == "200")
        {
            $result = json_decode($response->getBody());
            
            $result = $result->vehicle;
            $company_id = $result->company_id;
            $currentdriver_id  =  null;
            
            if(!empty($result->current_driver));
            {
                $currentdriver_id = $result->current_driver->id;
            }
            
            $keepTruck = KeepTruck::create([
                "company_id"=> $result->company_id,
                "current_driver_id" => $result->current_driver->id,
                "fuel_type" => $result->fuel_type,
                "vehicle_id" => $result->id,
                "ifta" => $result->ifta,
                "license_plate_number" => $result->license_plate_number,
                "lincense_plate_state" => $result->license_plate_state,
                "make" => $result->make,
                "metric_untis" => $result->metric_units,
                "model" => $result->model,
                "number" => $result->number,
                "prevent_auto_odometer_entry" => $result->prevent_auto_odometer_entry,
                "status" => $result->status,
                "vin" => $result->vin,
                "year" => $result->year,
            ]);
        }
      
        return $keepTruck;
        
    }

    public function getDriver($id)
    {
        
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://api.keeptruckin.com/v1/users/'.$id, [
            'headers' => [
                'Accept' => 'application/json',
                "X-Api-Key" => env('KEEP_API_KEY'),
            ],
        ]);
        $keepDriver = null;

        
        if($response->getStatusCode() == "200")
        {
            $result = json_decode($response->getBody());
            $result = $result->user;
            
            $keepDriver = KeepDriver::create([
                "id" => $result->id,
                "carrier_city" => $result->carrier_city,
                "carrier_name" => $result->carrier_name,
                "carrier_state" => $result->carrier_state,
                "carrier_street" => $result->carrier_street,
                "carrier_zip" => $result->carrier_zip,
                "cycle" => $result->cycle,
                "cycle2" => $result->cycle2,
                "driver_company_id" => $result->driver_company_id,
                "drivers_license_state" =>$result->drivers_license_state,
                "duty_status" => $result->duty_status,
                "eld_mode" => $result->eld_mode,
                "email" => $result->email,
                "first_name" => $result->first_name,
                "last_name" => $result->last_name,
                "mobile_current_sign_in_at" => $result->mobile_current_sign_in_at,
                "mobile_last_active_at" => $result->mobile_last_active_at,
                "phone" => $result->phone,
                "role" => $result->role,
                "time_zone" => $result->time_zone,
                "username" => $result->username,
                "violation_alerts" => $result->violation_alerts,
            ]);

            
        }
        
        return $keepDriver;
    }

    public function getCurrentDriverid($id)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.keeptruckin.com/v1/vehicles/'.$id, [
            'headers' => [
                'Accept' => 'application/json',
                "X-Api-Key" => env('KEEP_API_KEY'),
            ],
        ]);

        $currentdriver_id  =  null;

        if($response->getStatusCode() == "200")
        {
            $result = json_decode($response->getBody());
            $result = $result->vehicle;
           
            
            if(!empty($result->current_driver));
            {
                $currentdriver_id = $result->current_driver->id;
            }
        }
        return $currentdriver_id;
    }

    public function eventTest()
    {
        $location = [
            "lat" => "30",
            "lon" => "20"
        ];
        event(new SendLocation($location));
        return True;
    }

    public function webHookList()
    {
        $webHooks = WebHook::get();
        $results = [];
        foreach($webHooks as $webHook)
        {
            $locateData = [];
            $keepTruck = keepTruck::where("vehicle_id", $webHook->vehicle_id)->first();
            $currentDriver = KeepDriver::where("id", $webHook->current_driver_id)->first();
            $locateData['id'] = $webHook->id;
            $locateData['drvier_name'] = $currentDriver->first_name." ". $currentDriver->last_name;
            $locateData["phone"] = $currentDriver->phone;
            $locateData["lat"] = $webHook->lat;
            $locateData["lng"] = $webHook->lon;
            $locateData["vehicle_id"] = $webHook->vehicle_id;
            $locateData["license_plate_number"] = $keepTruck->license_plate_number;
            $locateData["lincense_plate_state"] = $keepTruck->lincense_plate_state;
            $locateData["speed"] = $webHook->speed;
            $locateData["bearing"] = $webHook->bearing;
            $locateData["description"] = $webHook->description;
            $locateData["load_id"] = null;
            $locateData["reference"] = "";
            $tractor = Tractor::where("license_plate", $keepTruck->license_plate_number)->first();
            
            if(!empty($tractor))
            {
                $load = Load::where("tractor_id", $tractor->id)
                ->whereIn("status", ["Dispatched", "In Transit"])
                ->orderBy("created_at", "desc")->first();
                
                if(!empty($load))
                {
                    $locateData["load_id"] = $load->id;
                    $locateData["reference"] = $load->reference;
                }
            }else
            {
                 Tractor::create([
                    "model"=>$keepTruck->model,
                    "pun"=>$keepTruck->number,
                    "status"=>$keepTruck->status,
                    "fuel_type"=>$keepTruck->fuel_type,
                    "license_plate"=>$keepTruck->license_plate_number,
                    "vehicle_id"=>$keepTruck->vin,
                ]);
            }
           
            
            
            array_push($results, $locateData);
        }
        $data = [
            'currentLocateData' => null,
            'results' => $results,
        ];
        return response()->json(["location"=>$data]);
    }
}
