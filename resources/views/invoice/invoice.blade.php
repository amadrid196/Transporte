<!DOCTYPE html>
<html>
<head>

</head>
<style>
.text-center {
    text-align:center;
}

.load-table tr, .load-table td {
    border:1px !important;
}
  .load-table {
  
    border-top: 0.00em solid;
    border-collapse: collapse;
}  
.load-table tbody td{
    border-left:  0.01em solid;
    border-right: 0.01em solid;
    border-top:  0.01em solid;
    border-bottom: 0.01em solid;
    padding:3px;
}


.page-break {
    page-break-after: always;
}
p {margin:3px;}
</style>
<body>
<table style="border-collapse: collapse; width: 100%;" border="0">
    <tbody>
    <tr>
        <td style="width: 52.0668%;"><img src="{{asset("data_theme/img/logo.png")}}" alt="" width="306" height="81"/></td>
        <td style="width: 47.9332%;">
            <p style="text-align: center;"><strong>SETTLEMENT REPORT</strong></p>
            <p style="text-align: center;"><strong>Driver </strong> &nbsp;&nbsp;{{$loads[0]->driver->name}}</p>
            <p style="text-align: center;"><strong>Date </strong> &nbsp;&nbsp;{{Date('m/d/Y')}} </p>
        </td>
    </tr>
    <tr>
        <td style="width: 52.0668%;" >
            <p style="text-align: center;"><strong>MILAM TRANSPORT, LLC</strong></p>
            <p style="text-align: center;">PO BOX 40783</p>
            <p style="text-align: center;">TAMPA, FL 33646</p>
            <p style="text-align: center;"><strong>Phone:</strong> 1-888-433-0331</p>
            <p style="text-align: center;"><strong>Fax:</strong> 813-315-6260</p>
        </td>
        <td style="width: 47.9332%;">
            <p>&nbsp;</p>
        </td>
    </tr>
    </tbody>
</table>
<p style="border-bottom:2px solid;"><strong>Settlement Items:</strong></p>

<table class="load-table"  >  
    <tbody>
    <tr style="border:0px !important; font-size:14px; padding-bottom:6px;">
        <th style="width:17%;">Source</th>
        <th style="width:11%;">Payroll Date</th>
        <th style="width:17%;">Pick</th>
        <th style="width:17%;">Drop</th>
        <th style="width:11%;">Description</th>
        <th style="width:8%;">Rate</th>
        <th style="width:8%;">Quantity</th>
        <th style="width:11%;">Amount</th>
    </tr>
    @php $total_miles=0;$driver_rate=0; @endphp
    @foreach($loads as $load)
        <tr style=" font-size:13px;">
            <td style="width:17%;">Load #{{$load->id}}<br>
                @if($load->reference !="")
                    LoadRef: <br>Load# {{$load->reference}}
                @endif 
            </td>
            <td style="width:11%;">{{Date("m/d/Y")}}</td>
            <td style="width:17%; padding: 7px;">
                @foreach($load->shipper as $shipper)
                    
                    @if(empty($shipper->start_periode))
                    {{Date("m/d/Y h:i", strtotime($shipper->pickup_date))}}
                    @else
                    {{Date("m/d/Y h:i", strtotime($shipper->start_periode))}}
                    @endif
                    <hr>
                    {{$shipper->pickup_address}}
                @endforeach
            </td>
            <td style="width:17%; padding:7px;">
                @foreach($load->consignee as $consignee)
                    
                    @if(empty($consignee->start_periode))
                        {{Date("m/d/Y h:i", strtotime($consignee->dropoff_time))}}
                    @else
                        {{Date("m/d/Y h:i", strtotime($consignee->start_periode))}}
                    @endif
                    <hr>
                    {{$consignee->dropoff_address}}
                @endforeach
            </td>
            <td style="width:11%;">
                Company Driver  
                @foreach($load->accessories as $val)
                   
                    @if($val->type == "expense")
                    {{$val->accessorial&&isset($val->accessorial->title)? $val->accessorial->title." ":" "}} 
                    @endif
                @endforeach
            </td>
            <td style="width:8%; text-align:center;">{{"$".number_format($load->driver->rate, 2)}}</td>
            <td style="width:8%;">{{number_format($load->miles)}}</td>
            <td style="width:11%;text-align:right">${{number_format(($load->miles*$load->driver->rate),2)}}</td>
        </tr>
        @php
            $total_miles=$total_miles+$load->miles;
            $driver_rate=$load->driver->rate;
        @endphp
    @endforeach
    </tbody>
</table>
<p><strong>Deductions:</strong></p>
<table style="border-collapse: collapse; width: 100%;" border="1">
    <tbody>
    @php
        $deduction_total=0;
    @endphp
    @foreach($loads as $load)
        @foreach($load->deductions as $deduction)
            <tr>
                <td style="width: 80%;"> {{$deduction->title}}</td>
                <td style="width: 20%; text-align: right;">${{$deduction->value}}</td>
            </tr>
            @php
                $deduction_total=$deduction_total+$deduction->value;
            @endphp
        @endforeach
    @endforeach

    @if($deduction_total==0)
        <tr>
            <td style="text-align: center" colspan="2">No Deductions</td>
        </tr>
    @endif

    </tbody>
</table>

<p><strong>Accessorial:</strong></p>
<table style="border-collapse: collapse; width: 100%;" border="1">
    <tbody>
    @php
        $accessorial_total=0;
       
    @endphp
    @foreach($loads as $load)
        @php  $key = 0; @endphp
        @foreach($load->accessories->where("type","expense") as $load_accessory)
            @if($key != 0)
                <tr>
                    <td style="width: 80%;">{{$val->accessorial&&isset($val->accessorial->title)? $val->accessorial->title." ":" "}} {{$val->note}}</td>
                    <td style="width: 20%; text-align: right;">${{$load_accessory->value}}</td>
                </tr>
                
                @php
                    $deduction_total=$deduction_total+$load_accessory->value;
                    $accessorial_total=$accessorial_total+$load_accessory->value;
                @endphp
            @endif
            @php
            $key++; 
            @endphp    
        @endforeach
    @endforeach

    @if($accessorial_total==0)
        <tr>
            <td style="text-align: center" colspan="2">No Accessorial</td>
        </tr>
    @endif

    </tbody>
</table>

<p>&nbsp;</p>
<table style="border-collapse: collapse; width: 100%;" >
    <tbody>
    <tr>
        <td style="width: 80%;"><b>Total</b></td>
        <td style="width: 20%;text-align: right;"><b>${{number_format(round($total_miles*$driver_rate,2)+$deduction_total,2)}}</b></td>
    </tr>
    </tbody>
</table>
</body>
</html>