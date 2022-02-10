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
    width:100%;
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
            <p style="text-align: center;"><strong>INVOICE</strong></p>
            <p style="text-align: center;"><strong>Invoice # </strong> &nbsp;&nbsp;{{$load->id}}</p>
            <p style="text-align: center;"><strong>Date </strong> &nbsp;&nbsp;{{Date('m/d/Y')}} </p>
            <p style="text-align: center;"><strong>Terms </strong> &nbsp;&nbsp;NET 30 Days </p>
            <p style="text-align: center;"><strong>Reference </strong> &nbsp;&nbsp;Load# {{$load->reference}} </p>
        </td>
    </tr>
    <tr>
        <td style="width: 52.0668%;" >
            <p style="text-align: center;">PO BOX 40783</p>
            <p style="text-align: center;">TAMPA, FL 33646</p>
            <p style="text-align: center;"><strong>Docket:</strong> MC051238</p>
            <p style="text-align: center;"><strong>Phone:</strong> 1-888-433-0331</p>
            <p style="text-align: center;"><strong>Fax:</strong> 813-315-6260</p>
        </td>
        <td style="width: 47.9332%;">
            <p>&nbsp;</p>
        </td>
    </tr>
    </tbody>
</table>
<p style="border-bottom:2px solid;"><strong>Customer Information:</strong></p>
@if(!empty($load->customer))
<table style="border-collapse: collapse; width: 100%;" border="0">
    <tbody>
    <tr>
        <td style="width: 40%;">
            <p style="text-align: center;"><strong>{{$load->customer->company}}</strong></p>
            <p style="text-align: center;">{{$load->customer->address}}</p>
            <p style="text-align: center;">{{$load->customer->contact}}</p>
            
        </td>
        <td style="width: 47.9332%;">
        </td>
    </tr>
    
    </tbody>
</table>
@endif


<p style="border-bottom:2px solid;"><strong>Pay Items:</strong></p>
<table class="load-table" >  
    <tbody>
    <tr style="border:0px !important; font-size:14px; padding-bottom:6px;">
        <th style="width:25%;">Description</th>
        <th style="width:25%;">Notes</th>
        <th style="width:16.7%;">Quantity</th>
        <th style="width:16.7%;">Rate</th>
        <th style="width:16.6%;">Amount</th>
    </tr>
    @php
        $accessorial_total=0;
    @endphp
    @foreach($load->accessories as $val)

        @if($val->type == "income")
            <tr>
                <td style="width:25%;">{{$val->accessorial&&isset($val->accessorial->title)? $val->accessorial->title." ":" "}}
                    
                </td>
                <td style="width:25%;">{{$val->note}}</td>
                <td style="width:16.7%;">{{"$".number_format($val->rate, 2)}}</td>
                <td style="width:16.7%;">{{number_format($val->quantity)}}</td>
                <td style="width:16.6%;">${{number_format(($val->rate*$val->quantity),2)}}</td>
            </tr>
            @php
            $accessorial_total = $accessorial_total+$val->rate*$val->quantity;
            @endphp
        @endif
    @endforeach
    </tbody>
</table>
<table style="border-collapse: collapse; width: 100%;" >
    <tbody>
    <tr>
        <td style="width: 20%;"><b>Total</b></td>
        <td style="width: 80%;text-align: right;"><b>${{number_format($accessorial_total,2)}}</b></td>
    </tr>
    </tbody>
</table>
<p style="border-bottom:2px solid;"><strong>Stops / Actions:</strong></p>
@php $i = 1; @endphp
<table class="load-table" >  
    <tbody>
    <tr style="border:0px !important; font-size:14px; padding-bottom:6px;">
        <th style="width:10%;">#</th>
        <th style="width:18%;">Action</th>
        <th style="width:26%;">Date/Time</th>
        <th style="width:26%;">Location</th>
    </tr>
        @foreach($load->shipper as $shipper)
        <tr style=" font-size:13px;">
            <td style="width:10%;">{{$i}}</td>
            <td style="width:18%;">Pickup</td> 
            <td style="width:26%;">
                @if(empty($shipper->start_periode))
                    {{Date("m/d/Y h:i", strtotime($shipper->pickup_date))}}
                @else
                    {{Date("m/d/Y h:i", strtotime($shipper->start_periode))}}
                @endif
            </td>
            <td style="width:26%;">
                {{$shipper->pickup_address}}
            </td>
        </tr>
        @php $i++; @endphp
        @endforeach

        @foreach($load->consignee as $consignee)
        <tr style=" font-size:13px;">
            <td style="width:10%;">{{$i}}</td>
            <td style="width:18%;">Delivery</td> 
            <td style="width:26%;">
                @if(empty($consignee->start_periode))
                    {{Date("m/d/Y h:i", strtotime($consignee->dropoff_time))}}
                @else
                    {{Date("m/d/Y h:i", strtotime($consignee->start_periode))}}
                @endif
            </td>
            <td style="width:26%;">
                {{$consignee->dropoff_address}}
            </td>  
        </tr>
        @php $i++; @endphp
        @endforeach
    </tbody>
</table>
<p><strong>Please mail payments to:</strong></p>
<p><strong>Attention: Accounting</strong></p>
<p><strong>PO Box 47083</strong></p>
<p><strong>Tampa, FL 33646</strong></p>
<p><strong>MILAM Transport, LLC</strong></p>
</body>
</html>