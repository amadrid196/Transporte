<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
class GoogleApiController extends Controller
{
    public function calculateDistance(Request $request)
    {

        //        https://maps.googleapis.com/maps/api/distancematrix/json?origins=Vancouver+BC|Seattle&destinations=San+Francisco|Victoria+BC&mode=bicycling&language=fr-FR&key=YOUR_API_KEY
        //        https://maps.googleapis.com/maps/api/distancematrix/json?origins=Karachi,%20Pakistan|Burewala,%20Pakistan&destinations=Lahore,%20Pakistan&mode=driving&language=fr-FR&key=AIzaSyAs59dPfauDX-PJk5omzSvy0gMMrG5eG_Q

        // Google API key
        $apiKey = env("MAP_API");


        if (isset($request->addressFrom) && isset($request->addressTo)) {


            // Change address format
            $formattedAddrFrom = str_replace(' ', '+', $request->addressFrom);
            $formattedAddrTo = str_replace(' ', '+', $request->addressTo);

            // Geocoding API request with start address
            $geocodeFrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddrFrom . '&sensor=false&key=' . $apiKey);
            $outputFrom = json_decode($geocodeFrom);
            if (!empty($outputFrom->error_message)) {
                return $outputFrom->error_message;
            }

            // Geocoding API request with end address
            $geocodeTo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddrTo . '&sensor=false&key=' . $apiKey);
            $outputTo = json_decode($geocodeTo);
            if (!empty($outputTo->error_message)) {
                return $outputTo->error_message;
            }

            // Get latitude and longitude from the geodata
            $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
            $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
            $latitudeTo = $outputTo->results[0]->geometry->location->lat;
            $longitudeTo = $outputTo->results[0]->geometry->location->lng;

            // Calculate distance between latitude and longitude
            $theta = $longitudeFrom - $longitudeTo;
            $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;

            return response()->json([
                'status' => 'ok',
                'miles' => round($miles, 0),
                'adr1' => $request->addressFrom,
                'adr2' => $request->addressTo,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'miles' => 0,
                'adr1' => $request->addressFrom,
                'adr2' => $request->addressTo,
            ]);
        }
    }

    public function distance_between_multiple_points(Request $request)
    {
        // Google API key
        $apiKey = env("MAP_API");

        $shippers = "";
        $consignees = "";
        //Change address format and add starting points
        $placeList = [];

        if($request->has('shipper_locations'))
        foreach($request->shipper_locations as $key => $location)
        {
            array_push($placeList, $location);
        }
        if($request->has('consignee_locations'))
        foreach($request->consignee_locations as $key=>$location)
        {
            array_push($placeList, $location);
        }

        //return response()->json(['placelist'=> $placeList]);
        for($i = 0; $i < count($placeList)-1; $i++)
        {
            $location = $placeList[$i];
            $destination  = $placeList[$i+1];
            if($shippers == "")
                $shippers = $shippers . str_replace(' ', '%20', $location);
            else
                $shippers = $shippers. "|".str_replace(' ', '%20', $location);
            
            if($consignees == "")
                $consignees = $consignees.str_replace(' ', '%20', $destination);
            else
                $consignees = $consignees. "|".str_replace(' ', '%20', $destination);
        }
        // foreach ($request->shipper_locations as $key => $location) {
        //     if ($shippers == "")
        //         $shippers = $shippers . str_replace(' ', '%20', $location);
        //     else
        //         if ($consignees == "")
        //             $consignees = $consignees . str_replace(' ', '%20', $location);
        //         else
        //             $consignees = $consignees . "|" . str_replace(' ', '%20', $location);
        // }

        //        add current location in shippers
        //        if ($shippers=="")
        //            return response()->json([
        //                'status' => 'error',
        //                'message' => "Shippers are not defined."
        //            ]);
        //        else
        //            $shippers = str_replace(' ', '%20', $request->current_location)."|".$shippers;

        //        set destinations

        // foreach ($request->consignee_locations as $key => $location) {
        //     if ($consignees == "")
        //         $consignees = $consignees . str_replace(' ', '%20', $location);
        //     else
        //         $consignees = $consignees . "|" . str_replace(' ', '%20', $location);
        // }

        

        //$geocodeForm = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=Karachi,%20Pakistan|Burewala,%20Pakistan&destinations=Lahore,%20Pakistan&mode=driving&language=fr-FR&key=AIzaSyAs59dPfauDX-PJk5omzSvy0gMMrG5eG_Q");
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$shippers&destinations=$consignees&mode=driving&language=fr-FR&key=$apiKey";
    

        $distance = file_get_contents($url);
        $distance = json_decode($distance);

       
        $miles=0;
        foreach($distance->rows as $index=>$row)
        {
            $miles = $miles+round(($row->elements[$index]->distance->value/1609), 0);
        }
        // foreach ($distance->rows[0]->elements as $key => $value) {
        //     $miles=$miles+round(($value->distance->value/1609), 0);
        // }
        $miles=$miles;

        $deadhead_miles = 0;
        if($request->current_location != ""&&isset($request->shipper_locations[0]))
        {
            $deadhead_origin=str_replace(' ', '%20', $request->current_location);
            $deadhead_destinations=str_replace(' ', '%20', $request->shipper_locations[0]);
            $dead_head_url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$deadhead_origin&destinations=$deadhead_destinations&mode=driving&language=fr-FR&key=$apiKey";
       
            $deadhead_distance = file_get_contents($dead_head_url);
            $deadhead_distance = json_decode($deadhead_distance);
            $deadhead_miles = round(($deadhead_distance->rows[0]->elements[0]->distance->value/1609), 0);
        }

        return response()->json([
            'status' => 'success',
            'miles' => $miles,
            'km' => round($miles*1.609, 0),
            'dead_head_miles'=>$deadhead_miles,
            'response'=>$distance,
            "url"=>$url
        ]);
    }


    public function roadData(Request $request)
    {
        $shipperIds = [];
        
        $consigneeIds = [];
        if($request->has('consignee_locations'))
        $consigneeIds = $request->consignee_locations;
        if($request->has('shipper_locations'))
        $shipperIds = $request->shipper_locations;
        $currentLocation = $request->current_location;
        $currentLat = $request->current_location_lat;
        $currentLng = $request->current_location_lng;

        $shippers = [];
        $consignees = [];
        
        $placeLegnths = count($shipperIds);
        
        
        $roadInfos = [];
        if($request->current_location != "")
        $roadInfos[0] = [$currentLat, $currentLng];

        foreach($shipperIds as $val)
        {
            $shipper = Customer::where('id', $val)->first();
            
            $roadInfo = [];
            $roadInfo[0] = $shipper->lat;
            $roadInfo[1] = $shipper->lng;
            array_push($roadInfos, $roadInfo);
          
        }

        foreach($consigneeIds as $val)
        {
            $consignee = Customer::where('id', $val)->first();
          
            $roadInfo = [];
            $roadInfo[0] = $consignee->lat;
            $roadInfo[1] = $consignee->lng;
            array_push($roadInfos, $roadInfo);
        }

        
        
        return response()->json(['success'=>true, 'data'=>$roadInfos]);
    }
}
