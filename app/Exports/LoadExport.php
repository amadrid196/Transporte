<?php
namespace App\Exports;

use App\Models\Load;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
class LoadExport implements FromCollection, withHeadings
{
    use Exportable;
    

    protected $loads_data;
    private $column_type;
    public function __construct($loads_data, $column_type)
    {
        
        $this->loads_data = $loads_data;
        $this->column_type = $column_type;
    }
    

    public function collection()
    {

       
        $exportData = collect();
        foreach($this->loads_data as $val)
        {
            
            
            $row = collect();
            

            $pickups = "";
            $firstPickName = "";
            $firstPickCity = "";
            $firstPickState = "";
            $firstPickPostal = "";
            $firstPickPostal = "";
            $firstPickCountry = "";
            $firstPickDate = "";
            $firstPickupAddress = "";
            
            $addressIndex =1;
            if($val->shipper&&$val->shipper->count())
            {
                $firstPickup = $val->shipper[0];
                $firstPickName = (isset($firstPickup->customer->name))?$firstPickup->customer->name:'';
           
                if(!empty($firstPickup->pickup_address))
                {
                    $firstPickupAddress = $firstPickup->pickup_address;
                    $addressList = explode(",", $firstPickupAddress);
                    if(count($addressList) > 3) 
                    {
                        $firstPickupAddress = $addressList[0];
                        $firstPickCity = $addressList[1];
                        if(isset(explode(" ", $addressList[2])[1]))
                        $firstPickState = explode(" ", $addressList[2])[1];
                        if(isset(explode(" ", $addressList[2])[2]))
                            $firstPickPostal = explode(" ", $addressList[2])[2];
                        
                        $firstPickCountry = $addressList[3];;
                    }
                }
                $firstPickDate = date("m/d/Y h:i.a", strtotime($firstPickup->pickup_date));
                if($firstPickup->start_periode)
                {
                    $firstPickDate =  date("m/d/Y h:i.a", strtotime($firstPickup->start_periode));
                }
                //1171 Vaughn Pkwy, Portland, TN 37148, USA
                
                foreach($val->shipper as $shipper)
                {
                    $pickups = $pickups.$addressIndex.". ".$shipper->pickup_address. " : ";

                    if($shipper->start_periode&&$shipper->end_periode)
                    {
                        $pickups = $pickups.date("m/d/Y h:i.a", strtotime($shipper->start_periode)) ." - ". date("m/d/Y h:i.a", strtotime($shipper->end_periode)). "\n";
                    }else
                    {
                        if($shipper->pickup_date)
                        {
                            $pick_date = date("m/d/Y h:i.a", strtotime($shipper->pickup_date)); 
                            $pickups = $pickups.$pick_date."\n";
                        }
                        
                    }

                    $addressIndex++;
                }
            }
           
            $drops = "";
            $lastDropName = "";
            $lastDropAddress = "";
            $lastDropCity = "";
            $lastDropState = "";
            $lastDropPostal = "";
            $lastDropCountry = "";
            $lastDropDate = "";
            if($val->consignee&&$val->consignee->count())
            {
                
                $lastdrop = $val->consignee->sortDesc()[0];
                 $lastDropName = (isset($lastdrop->customer->name))?$lastdrop->customer->name:'';
                if(!empty($lastdrop->dropoff_address))
                {
                    $lastDropAddress = $lastdrop->dropoff_address;
                    $addressList = explode(",", $lastdrop->dropoff_address);
                    if(count($addressList) > 3) 
                    {
                        $lastDropAddress = $addressList[0];
                        $lastDropCity = $addressList[1];
                        if(isset(explode(" ", $addressList[2])[2]))
                        $lastDropState = explode(" ", $addressList[2])[1];
                        if(isset(explode(" ", $addressList[2])[2]))
                            $lastDropPostal = explode(" ", $addressList[2])[2];
                        
                        $lastDropCountry = $addressList[3];;
                    }
                    
                }
                
                $lastDropDate = date("m/d/Y h:i.a", strtotime($lastdrop->dropoff_time));
                if($lastdrop->start_periode)
                {
                    $lastDropDate = date("m/d/Y h:i.a", strtotime($lastdrop->start_periode));
                }
                foreach($val->consignee as $drop)
                {
                    $drops = $drops.$addressIndex.". ".$drop->dropoff_address. " : ";

                    if($drop->start_periode&&$drop->end_periode)
                    {
                        $drops = $drops.date("m/d/Y h:i.a", strtotime($drop->start_periode)) ." - ". date("m/d/Y h:i.a", strtotime($drop->end_periode))."\n";
                    }else
                    {
                        if($drop->dropoff_time)
                        {
                            $drop_date = date("m/d/Y h:i.a", strtotime($drop->dropoff_time))."\n"; 
                            $drops = $drops.$drop_date;
                        }
                        
                    }

                    $addressIndex++;
                }
            }
           
            
           
            
            $customer = "";
            if($val->broker)
            {
                $customer =$val->broker->name;
            }

            $driver = "";
            if($val->driver)
            {
                $driver = $val->driver->name;
            }
            
            $powerunit = "";
            if($val->tractor)
            {
                $powerunit = $val->tractor->pun;
            }
           
            $trailer = "";
            if($val->trailer)
            {
                $trailer = $val->trailer->number;
            }

            $invoice_date = "Unsent";
            if($val->inv_status!= "Unsent"&&$val->inv_date)
            {
                $invoice_date = $val->inv_date;
            }

            $totalIncome = 0;
            
            if($val->accessories&&$val->accessories->count())
            {
                foreach($val->accessories as $accessory)
                {
                    if($accessory->value != null && $accessory->type == "income")
                    {
                        $totalIncome = $totalIncome + $accessory->value;
                    }
                }
            }

            $grossPofit = $totalIncome;
            if(!empty($val->cost))
            {
                $grossPofit = $totalIncome - $val->cost;
            }

            $grossProfitRate = 0;
            
            if($totalIncome)
            {
                $grossPofitRate = round($grossPofit/$totalIncome, 2);
            }

            $truckStatus = "";
            if($val->truck)
            {
                $truckStatus = $val->truck->status;
            }

            $usdotnumber = "";
            if($val->trailer)
            {
                $usdotnumber = $val->trailer->number;
            }
            $row->push((string)$val->id);
            $row->push((string)$pickups);
            $row->push((string)$drops);
            $row->push((string)$pickups.$drops);
            $row->push((string)$customer);
            $row->push((string)"MILAM TRANSPORT, LLC");
            $row->push((string)$driver);
            $row->push((string)$powerunit);
            $row->push((string)$trailer);
            $row->push((string)$val->reference);
            // $row->push((string)""); //Note
            // $row->push((string)""); //Private Note
            $row->push((string)$val->status);
            // $row->push((string)"Shared");//shared
            $row->push((string)$invoice_date);//inv date
            $row->push((string)$val->compeleted_date);//Bill Date
            //$row->push((string)"");//User Roles
            $row->push((string)$totalIncome);// Total Income
            $row->push((string)$val->cost);// Total Expense
            $row->push((string)$grossPofit);
            $row->push((string)$grossPofitRate);
            
            $row->push((string)$firstPickName);
            $row->push((string)$firstPickupAddress);
            $row->push((string)$firstPickCity);
            $row->push((string)$firstPickState);
            $row->push((string)$firstPickPostal);
            $row->push((string)$firstPickCountry);
            $row->push((string)$firstPickDate);
            $row->push((string)$lastDropName);
            $row->push((string)$lastDropAddress);
            $row->push((string)$lastDropCity);
            $row->push((string)$lastDropDate);
            $row->push((string)$val->miles);
            $row->push((string)$val->miles);
            $row->push((string)$val->dead_head_miles);
            $row->push((string)$truckStatus);
            //$row->push((string)""); //MC Number
            $row->push((string)$usdotnumber);
            //$row->push((string)"");//weigth
            $row->push((string)$val->created_at);
           
            if($this->column_type == "extend")
            {
                $row->push($driver);//COMPANY DRIVER e
                $row->push($totalIncome);//Flat Rate i
               // $row->push("");//Lumper i
            }
            $exportData->push($row);
        }
        return  $exportData;
    }

    public function headings(): array
    {
        $header = [
            __('Load ID'),
            __('Pickups'),
            __('Deliveries'),
            __('All Stops & Actions'),
            __('Customer'),
            __('Carrier'),
            __('Drivers'),
            __('Power Unit'),
            __('Trailer'),
            __('References'),
          //   __('Notes'),
          //__('Private Notes'),
            __('Load Status'),
          //     __('Branch'),
            __('Invoice Dates'),
            __('Bill Dates'),
          //    __('Users & Roles'),
            __('Total Income'),
            __('Total Expenses'),
            __('Gross Profit/Loss'), 
            __('Gross Profit/Loss %'),
            __('First Pick Name'),
            __('First Pick Address'),
            __('First Pick City'),
            __('First Pick State'),
            __('First Pick Postal'),
            __('First Pick Country'),
            __('First Pick Date'),
            __('Last Drop Name'),
            __('Last Drop Address'),
            __('Last Drop City'),
            __('Last Drop Date'),
            __('Client Mileage'),
            __('Carrier Mileage'),
            __('Deadhead Mileage'),
            __('Truck Status'),
          //  __('Carrier MC Number'),
            __('Carrier USDOT Number'),
          //  __('Weight'),
            __('Load Created Date'),
           

        ];

        if($this->column_type == "extend")
        {
            array_push($header, __('COMPANY DRIVER e'));
            array_push($header, __('Flat Rate i'));
            //array_push($header, __('Lumper i'));
        }
        return $header;
    }
    
}
?>