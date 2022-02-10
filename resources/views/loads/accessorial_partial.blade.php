
<style>
  .accessorial-card {
      /* border:1px solid #ddd; */
      font-size:12px;
  }
  .mr-2{
    margin-right: 10px;
  }

  .mt-6 {
      margin-top: 6px;
  }
  .br{
      border-right:1px  solid #ddd;
  }

  .accessorai_item_header {
      border: 1px solid #ddd;
      padding:5px;
  }

  .accessorai_item_header .icon {
    padding:6px;
    padding-right:11px;
  }
  .accessorai_item_header .title {
    padding:5px;

    margin-left:20px;
  }
  .accessorai_item_header .add-button {
    margin-left:40px;
    color:#4481BA !important;
    cursor:pointer;
  }

  .accessorial-card table th {
    border:1px solid #ddd;
    text-align:center;
    padding:6px !important;
  }

  .accessorial-card table  td {
    border:1px solid #ddd;
    /* text-align:center; */
    padding:0px !important;
  }

  
  .accessorial-card input,.accessorial-card select{
    border:0px !important;
  }

  .total-box {
      font-size:12px !important;
  }

  .total-box input{
    border:0px !important;
  }

  .total-box td {
      vertical-align:middle !important;
  }

  table {
    border-collapse: collapse;
  }

  /* tbody .active input,tbody .active select{
      background:#f4e224 !important;
  } */
 .tr-active{
      border:2px solid fuchsia  !important;
  }

  /* .hidden{
      display:none;
  } */

.flex-container {
    display: flex;
    /* background-color: DodgerBlue; */
}
</style>
<!-- New part  -->
<div class="col-md-12 margin-bottom text-right">
<button type="button" class="btn btn-danger" id="remove_accessorial_button" disabled><i class='fa fa-trash'></i> Remove</button>
</div>
<div class="col-md-12 margin-bottom">
<!--  start of Income section -->

    <div class="row accessorial-card">
        <div class="accessorai_item_header">
            
            <span class=" br text-center icon"><i class="fa fa-th-list"></i></span>
            <span class="title"> <b>{{__('tran.Income/budget')}}</b></span>
            <span class="add-button"><a   onClick="showAccessorialModel(1)"><i class="fa fa-pencil"></i> &nbsp;<i><b>{{__('tran.Add Line Item')}}</b></i></a></span>
        </div>
        <table class="table table-borderd">
        <script>var accesorial_count1 = 0;</script>
                <tr >
                    <th class="col-xs-3">{{__('tran.Company')}}</th>
                    <th class="col-xs-3">{{__('tran.Description')}}</th>
                    <th class="col-xs-3">{{__('tran.Notes')}}</th>
                    <th class="col-xs-1">{{__('tran.Rate')}}</th>
                    <th class="col-xs-1">{{__('tran.Quantity')}}</th>
                    <th class="col-xs-1">{{__('tran.Total')}}</th>

                </tr>
                <tbody  id="main_accesorials_div1">
                @if(isset($data)&&$data->accessories->where('type', 'income'))
                    @php $income_count=0; @endphp
                    @foreach($data->accessories->where("type","income") as $key=>$accessory)
                    <tr id="div1_num_accesorials{{$income_count}}"  onclick="selectRow('{{$income_count}}','1')">
                        <td><input type="text" class="form-control company_a1ccesorial" name="fake[]" id="company_a1ccesorial{{$income_count}}"></td>
                        <td><select type="text"  id="a1ccesorial{{$income_count}}" name="accesorials[]" class="form-control text-capitalize" onchange="accessory_change('{{$income_count}}','1')"> <option>Loading...</option></select></td>
                        <td><input type="text" class="form-control" id="note_a1ccesorial{{$income_count}}" name="note_a1ccesorial[]" value="{{$accessory->note}}"></td>
                        <td><input type="text" class="form-control text-right currency" id="rate_a1ccesorial{{$income_count}}" name="rate_a1ccesorial[]" value="${{$accessory->rate?$accessory->rate:'0'}}" step="0.01" onchange="rateChange('{{$income_count}}','1')"></td>
                        <td ><input type="number" class="form-control text-right" name="quantity_a1ccesorial[]" id="quantity_a1ccesorial{{$income_count}}" onchange="rateChange('{{$income_count}}','1')"  value="{{$accessory->quantity?$accessory->quantity:'0'}}"></td>
                        <td id="a1ccessory_div{{$income_count}}"><input id="value_a1ccesorial{{$income_count}}" type="text" name="accessory_value[]" value="${{$accessory->value?$accessory->value:'0'}}"class="form-control accessory_value accessory_value_income text-right"readonly onchange="calculate_accessorail_income()"></td>
                    </tr>
                        <script>
                            accesorial_count1 ={{$income_count}};
                        </script>
                        @php $income_count++; @endphp
                    @endforeach
                @else
                    <tr id="div1_num_accesorials0" onclick="selectRow('{{0}}','1')">
                        <td><input type="text" class="form-control company_a1ccesorial" id="company_a1ccesorial0" name="fake[]"></td>
                        <td><select type="text"  id="a1ccesorial0" name="accesorials[]" class="form-control text-capitalize" onchange="accessory_change('{{0}}','1')"> <option>Loading...</option></select></td>
                        <td><input type="text" class="form-control" id="note_a1ccesorial0" name="note_a1ccesorial[]"></td>
                        <td><input type="text" class="form-control text-right currency" id="rate_a1ccesorial0" name="rate_a1ccesorial[]"  step="0.01" onchange="rateChange('{{0}}','1')"></td>
                        <td ><input type="number" class="form-control text-right" name="quantity_a1ccesorial[]" id="quantity_a1ccesorial0" onchange="rateChange('{{0}}','1')"></td>
                        <td id="a1ccessory_div0"><input id="value_a1ccesorial0" type="text" name="accessory_value[]" class="form-control accessory_value accessory_value_income text-right" value="" readonly onchange="calculate_accessorail_income()"></td>
                    </tr>
                @endif
                </tbody>
                <tfooter>
                <tr>
                <td class="col-xs-3"><input type="text" class="form-control text-center" name="fake[]" value="{{__('tran.Total Income')}}" readonly></td>
                <td class="col-xs-3"></td>
                <td class="col-xs-3"></td>
                <td class="col-xs-1"></td>
                <td class="col-xs-1"></td>
                <td class="col-xs-1"><input type="text" class="form-control total-income   text-right" name="fake[]"  readonly></td>
                </tr>
                </tfooter>
        </table> 

    </div>
<!-- End of Income Section -->


<!-- Start of Expense section -->
<div class="row accessorial-card">
    <div class="accessorai_item_header">
        
        <span class=" br text-center icon"><i class="fa fa-th-list"></i></span>
        <span class="title"> <b>{{__('tran.Expense')}}</b></span>
        <span class="add-button"><a  onClick="showAccessorialModel(2)"><i class="fa fa-pencil"></i> &nbsp;<i><b>{{__('tran.Add Line Item')}}</b></i></a></span>
    </div>
    <table  class="table table-borderd">
    <script>var accesorial_count2 = 0;</script>
        <tr >
            <th class="col-xs-3">{{__('tran.Company')}}</th>
            <th class="col-xs-3">{{__('tran.Description')}}</th>
            <th class="col-xs-3">{{__('tran.Notes')}}</th>
            <th class="col-xs-1">{{__('tran.Rate')}}</th>
            <th class="col-xs-1">{{__('tran.Quantity')}}</th>
            <th class="col-xs-1">{{__('tran.Total')}}</th>

        </tr>
        <tbody id="main_accesorials_div2">
        @if(isset($data)&&$data->accessories->where('type', 'expense')->count() != 0)
                @php $expenses_count=0; @endphp
                @foreach($data->accessories->where("type","expense") as $key=>$accessory)
                    
                    <tr id="div2_num_accesorials{{$expenses_count}}" onclick="selectRow('{{$expenses_count}}','2')">
                        <td><input type="text" class="form-control company_a2ccesorial" name="fake[]" id="company_a2ccesorial{{$expenses_count}}"></td>

                        @if($expenses_count == 0)
                        <td id="expense0">
                            <input type="text"  id="expense0-description" name="fake[]" readonly  value="Company Driver" class="form-control text-capitalize">
                            <input type="hidden"  id="a2ccesorial0" name="accesorials2[]" value="0" class="form-control text-capitalize">
                        </td>
                        @else
                        <td><select type="text"  id="a2ccesorial{{$expenses_count}}" name="accesorials2[]" class="form-control text-capitalize" onchange="accessory_change('{{$expenses_count}}','2')"> <option>Loading...</option></select></td>
                        @endif
                        <td><input type="text" class="form-control"  id="note_a2ccesorial{{$expenses_count}}"  value="{{$accessory->note}}" name="note_a2ccesorial[]"></td>
                        <td><input type="text" class="form-control text-right currency" id="rate_a2ccesorial{{$expenses_count}}"  name="rate_a2ccesorial[]"  value="${{$accessory->rate}}"  step="0.01" onchange="rateChange('{{$expenses_count}}','2')"></td>
                        @if($expenses_count == 0)
                        <td ><input type="number" class="form-control text-right  expense_quantiy" value="{{$accessory->quantity}}" name="quantity_a2ccesorial[]" id="quantity_a2ccesorial{{$expenses_count}}" readonly></td>
                        @else
                        <td ><input type="number" class="form-control text-right" value="{{$accessory->quantity}}" id="quantity_a2ccesorial{{$expenses_count}}" name="quantity_a2ccesorial[]" onchange="rateChange('{{$expenses_count}}','2')" ></td>
                        @endif
                        <td id="a2ccessory_div{{$expenses_count}}"><input id="value_a2ccesorial{{$expenses_count}}"  value="${{$accessory->value}}" readonly type="text" name="accessory_value2[]" class="form-control text-right accessory_value accessory_value_expense" value="" onchange="calculate_accessorial_expense()"></td>
                    </tr>
                    <script>
                        accesorial_count2 ={{$expenses_count}};
                    </script>
                @php $expenses_count++; @endphp
                @endforeach
        @else
        <tr id="div2_num_accesorials0" onclick="selectRow('{{0}}','2')">
            <td><input type="text" class="form-control company_a2ccesorial" id="company_a2ccesorial0" name="fake[]"></td>
            <!-- <td><select type="text"  id="a2ccesorial0" name="accesorials2[]" class="form-control text-capitalize" onchange="accessory_change('{{0}}','2')"> <option>Loading...</option></select></td> -->
            <td id="expense0">
                <input type="text"  id="expense0-description" name="fake[]" readonly value="Company Driver" class="form-control text-capitalize">
                <input type="hidden"  id="a2ccesorial0" name="accesorials2[]" value="0" class="form-control text-capitalize">
            </td>
            
            <td><input type="text" class="form-control"  id="note_a2ccesorial0" name="note_a2ccesorial[]"></td>
            <td><input type="text" class="form-control text-right currency" id="rate_a2ccesorial0" name="rate_a2ccesorial[]"  step="0.01"  onchange="rateChange('{{0}}','2')"></td>
            <td><input type="number" class="form-control text-right  expense_quantiy" name="quantity_a2ccesorial[]" id="quantity_a2ccesorial0" readonly></td>
            <td id="a2ccessory_div0"><input id="value_a2ccesorial0" readonly type="text" name="accessory_value2[]" class="form-control text-right accessory_value accessory_value_expense" value="" onchange="calculate_accessorial_expense()"></td>
        </tr>
        @endif
        </tbody>
        <tfooter>
        <tr>
                <td class="col-xs-3"><input type="text" class="form-control text-center" name="fake[]" readonly value="{{__('tran.Total Expenditures')}}"></td>
                <td class="col-xs-3"></td>
                <td class="col-xs-3"></td>
                <td class="col-xs-1"></td>
                <td class="col-xs-1"></td>
                <td class="col-xs-1"><input type="text" class="form-control total-expense  text-right" name="fake[]"  readonly></td>
        </tr>
        </tfooter>
    </table> 

</div>
<!-- End of Expense section -->


<!--  Start of Deduction -->
<div class="row accessorial-card">
    <div class="accessorai_item_header">
        
        <span class=" br text-center icon"><i class="fa fa-th-list"></i></span>
        <span class="title"> <b>{{__('tran.Driver Deduction')}}</b></span>
        <span class="add-button"><a  onClick="showAccessorialModel(3)"><i class="fa fa-pencil"></i> &nbsp;<i><b>{{__('tran.Add Line Item')}}</b></i></a></span>
    </div>
    
    <table  class="table table-borderd">
    <script>var accesorial_count3 = 0;</script>
            <tr>
                <th class="col-xs-3">{{__('tran.Company')}}</th>
                <th class="col-xs-3">{{__('tran.Description')}}</th>
                <th class="col-xs-3">{{__('tran.Notes')}}</th>
                <th class="col-xs-1">{{__('tran.Rate')}}</th>
                <th class="col-xs-1">{{__('tran.Quantity')}}</th>
                <th class="col-xs-1">{{__('tran.Total')}}</th>

            </tr>
            <tbody id="main_accesorials_div3">
            @if(isset($data)&&count($data->deductions) != 0)
                @php $deductions_count=0; @endphp
                @foreach($data->deductions as $key=>$val)
                    <tr id="div3_num_accesorials{{$deductions_count}}" onclick="selectRow('{{$deductions_count}}','3')">
                    <td><input type="text" class="form-control company_a3ccesorial" name="fake[]" id="company_a3ccesorial{{$deductions_count}}"></td>
                    <td><select type="text"  id="a3ccesorial{{$deductions_count}}" name="accesorials3[]" class="form-control text-capitalize" onchange="accessory_change('{{$deductions_count}}','3')"> <option>Loading...</option></select></td>
                    <td><input type="text" class="form-control"   id="note_a3ccesorial{{$deductions_count}}" name="note_a3ccesorial[]" value="{{$val->title}}"></td>
                    <!-- <td><input type="text" class="form-control text-right currency"  name="rate_a3ccesorial[]" id="rate_a3ccesorial{{$deductions_count}}" value="${{$val->rate?$val->rate:'$0'}}" step="0.01" onchange="rateChange('{{$deductions_count}}','3')"></td> -->
                    <td><input type="text" class="form-control text-right currency"  name="rate_a3ccesorial[]" id="rate_a3ccesorial{{$deductions_count}}" 
                    value="@if($val->rate&&$val->rate[0] == '-'){{'-$'.str_replace('-', '', $val->rate)}} @elseif($val->rate&&$val->rate[0] != '-'){{'$'.$val->rate}} @else{{'$0'}} @endif"
                   
                    step="0.01" onchange="rateChange('{{$deductions_count}}','3')"></td>
                    <td ><input type="text" class="form-control quantity"  name="quantity_a3ccesorial[]" id="quantity_a3ccesorial{{$deductions_count}}" value="{{$val->quantity}}" onchange="rateChange('{{0}}','3')"></td>
                    <td id="a3ccessory_div{{$deductions_count}}"><input id="value_a3ccesorial{{$deductions_count}}" 
                    value="@if($val->value&&$val->value[0] == '-'){{'-$'.str_replace('-', '', $val->value)}}@elseif($val->value&&$val->value[0] != '-'){{'$'.$val->value}} @else {{'$0'}} @endif"
                 
                    type="text" name="accessory_value3[]" class="currency form-control accessory_value text-right  accessory_value_deduction" value="" onchange="calculate_accessorail_deductions()"></td>
                    <!-- <td id="a3ccessory_div{{$deductions_count}}"><input id="value_a3ccesorial{{$deductions_count}}" value="${{$val->value?$val->value:'$0'}}" type="text" name="accessory_value3[]" class="currency form-control accessory_value text-right  accessory_value_deduction" value="" onchange="calculate_accessorail_deductions()"></td> -->
                    </tr>
                    <script>
                        accesorial_count3 ={{$deductions_count}};
                    </script>
                    @php $deductions_count++; @endphp
                @endforeach
            @else
                <tr id="div3_num_accesorials0" onclick="selectRow('{{0}}','3')">
                    <td><input type="text" class="form-control company_a3ccesorial" name="fake[]" id="company_a3ccesorial0"></td>
                    <td><select type="text"  id="a3ccesorial0" name="accesorials3[]" class="form-control text-capitalize" onchange="accessory_change('{{0}}','3')"> <option>Loading...</option></select></td>
                    <td><input type="text" class="form-control"   id="note_a3ccesorial0" name="note_a3ccesorial[]" value=""></td>
                    <td><input type="text" class="form-control text-right currency" name="rate_a3ccesorial[]"  id="rate_a3ccesorial0" value="$0"  step="0.01" onchange="rateChange('{{0}}','3')"></td>
                    <td ><input type="number" class="form-control quantity" name="quantity_a3ccesorial[]" id="quantity_a3ccesorial0" onchange="rateChange('{{0}}','3')"></td>
                    <td id="a3ccessory_div0"><input id="value_a3ccesorial0" type="text" name="accessory_value3[]" value="$0" class="form-control accessory_value text-right  accessory_value_deduction" value="" onchange="calculate_accessorail_deductions()"></td>
                </tr>
               
            @endif
            </tbody>
            <tfooter>
            <tr>
                <td class="col-xs-3"><input type="text" class="form-control text-center" name="fake[]" readonly value="{{__('tran.Total Deductions')}}"></td>
                <td class="col-xs-3"></td>
                <td class="col-xs-3"></td>
                <td class="col-xs-1"></td>
                <td class="col-xs-1"></td>
                <td class="col-xs-1"><input type="text" class="form-control total-deductions text-right" name="fake[]"  readonly></td>
            </tr>
            </tfooter>
    </table> 
    
</div>
<!-- End of Deduction -->

<!-- total Part -->
<div class="row">
<div class="col-md-6">
<div class="col-md-10">
<div class="row">
    <div class="col-md-12 box-title2 margin-bottom">
        <h4>{{__("tran.Load")}} {{__("tran.Documents")}}</h4>
    </div>
</div>

<div class="row">
 
        
        <div class="form-group col-md-6">
            <label for="rate_con">{{__("tran.Rate Con")}} </label> 
            @if(isset($data)&&!empty($data->document)&&!empty($data->document->rate_con))
           
                <span class="text-right rate_con_eye" ><a href="{{ url($data->document->rate_con) }}" target="_blank"><i class="fa fa-eye"></i></a></span>
                <span class="text-right rate_con_remove remve_document" onclick="removeDocument('{{route('remove_document', [$data->document->id, 'rate_con'])}}', 'rate_con')"><a><i class="fa fa-trash  text-danger"></a></i></span>
            @endif
            <input type="file" name="rate_con" class="" id="rate_con" value="" form="send_email_form">
           
        </div>

        <div class="form-group col-md-6">
            <label for="bill_of_loading">{{__("tran.Bill of Loading")}}</label>
            @if(isset($data)&&!empty($data->document)&&!empty($data->document->bill_of_loading))
           
           <span class="text-right bill_of_loading_eye" ><a href="{{ url($data->document->bill_of_loading) }}"><i class="fa fa-eye"></i></a></span>
           <span class="text-right bill_of_loading_remove remve_document" onclick="removeDocument('{{route('remove_document', [$data->document->id, 'bill_of_loading'])}}', 'bill_of_loading')"><a><i class="fa fa-trash  text-danger"></a></i></span>
            @endif
            <input type="file" name="bill_of_loading" class="" id="bill_of_loading" form="send_email_form">
        </div>

        <div class="form-group col-md-6">
            <label for="proof_delivery">{{__("tran.Proof Of Delivery")}} </label>
            @if(isset($data)&&!empty($data->document)&&!empty($data->document->proof_delivery))
           
           <span class="text-right proof_delivery_eye" ><a href="{{ url($data->document->proof_delivery) }}"><i class="fa fa-eye"></i></a></span>
           <span class="text-right proof_delivery_remove remve_document" onclick="removeDocument('{{route('remove_document', [$data->document->id, 'proof_delivery'])}}', 'proof_delivery')"><a><i class="fa fa-trash  text-danger"></a></i></span>
            @endif
            <input type="file" name="proof_delivery" class="" id="proof_delivery" form="send_email_form">
        </div>

        <div class="form-group col-md-6">
            <label for="lumper_recepit">{{__("tran.Lumper Recepit")}} </label>
            @if(isset($data)&&!empty($data->document)&&!empty($data->document->lumper_recepit))
           
            <span class="text-right lumper_recepit_eye" ><a href="{{ url($data->document->lumper_recepit) }}"><i class="fa fa-eye"></i></a></span>
            <span class="text-right lumper_recepit_remove remve_document" onclick="removeDocument('{{route('remove_document', [$data->document->id, 'lumper_recepit'])}}', 'lumper_recepit')"><a><i class="fa fa-trash  text-danger"></a></i></span>
             @endif
            <input type="file" name="lumper_recepit" class="" id="lumper_recepit"form="send_email_form" >
        </div>

        <div class="form-group col-md-6">
            <label for="scale_ticket">{{__("tran.Scale Ticket")}} </label>
            @if(isset($data)&&!empty($data->document)&&!empty($data->document->scale_ticket))
           
                <span class="text-right scale_ticket_eye" ><a href="{{ url($data->document->scale_ticket) }}"><i class="fa fa-eye"></i></a></span>
                <span class="text-right scale_ticket_remove remve_document" onclick="removeDocument('{{route('remove_document', [$data->document->id, 'scale_ticket'])}}', 'scale_ticket')"><a><i class="fa fa-trash  text-danger"></a></i></span>
            @endif
            <input type="file" name="scale_ticket" class="" id="scale_ticket" form="send_email_form">
        </div>

        <div class="form-group col-md-6">
            <label for="other">{{__("tran.Other")}} </label>
            @if(isset($data)&&!empty($data->document)&&!empty($data->document->other))
                
                <span class="text-right other_eye" ><a href="{{ url($data->document->other) }}"><i class="fa fa-eye"></i></a></span>
                <span class="text-right other_remove remve_document" onclick="removeDocument('{{route('remove_document', [$data->document->id, 'other'])}}', 'other')"><a><i class="fa fa-trash  text-danger"></a></i></span>
            @endif
            <input type="file" name="other" class="" id="other" form="send_email_form" >
        </div>
        <div class="form-group col-md-12">
        <h6><b>Email to Factoring</b><h6>
        </div>
        <div class="form-group col-md-8">
            <label for="other">{{__("tran.Email")}} </label>
            
            <!-- <input type="email" name="email_to_factoring" form="send_email_form" value="phil@carrierfs.com" class="form-control" placeholder="" id="email_to_factoring" > -->
            <input type="email" name="email_to_factoring" form="send_email_form" value="{{ !empty($factor_agent_settings)?$factor_agent_settings->email:'' }}" class="form-control" placeholder="" id="email_to_factoring" >
        </div>
        <div class="form-group col-md-4"></div>
        <div class="form-group col-md-8">
            <label for="other">{{__("tran.POD")}} </label>
            <!-- <input type="email" name="POD" form="send_email_form" class="form-control" value="pod@carrierfs.com" placeholder="" id="POD" > -->
            <input type="email" name="POD" form="send_email_form" value="{{ !empty($factor_agent_settings)?$factor_agent_settings->pod:'' }}" class="form-control" placeholder="" id="email_to_factoring" >
        </div>
        <div class="form-group col-md-4"></div>
        <div class="form-group col-md-8">
            <label for="other">{{__("tran.CC")}} </label>
            <!-- <input type="email" name="CC" form="send_email_form" class="form-control" placeholder="" value="dispatch@milamtrans.com" id="CC" > -->
            <input type="email" name="CC" form="send_email_form" value="{{ !empty($factor_agent_settings)?$factor_agent_settings->cc:'' }}" class="form-control" placeholder="" id="email_to_factoring" >
        </div>
        <div class="form-group col-md-4"></div>
        <div class="form-group col-md-5">
            <div class="row">
                <div class="col-md-6">
                <button loading-text="<i class='fa fa-spinner fa-spin'></i> Processing Order" type="button" id="send_document_email_btn" form="send_email_form" class="btn btn-primary" value="">{{__('tran.Send Email')}}</button>
                </div>

                <div class="col-md-6">
                <button loading-text="<i class='fa fa-spinner fa-spin'></i> Processing Order" type="button" id="save_document_btn" form="send_email_form" class="btn btn-success" value="">{{__('tran.Save Document')}}</button>
                </div>
            </div>
        </div>
        <div class="col-md-12 flex-container">
            <label class="mr-2 mt-6">{{__('tran.Billed Date')}} : </label>
            <input type="date" name="completed_date" value="{{isset($data)&&!empty($data)?$data->completed_date:""}}">
        </div>
    
</div>
</div>
</div>
<div class="col-md-6 total-box">
<table id="total-box" class="table table-borderd">
    <tr>
        <td class="col-xs-6">{{__('tran.Total Income')}}</td>
        <td class="col-xs-6"><input type="text" class="form-control text-right" id="amount_income" name="total_income" readonly></td>
    </tr>      
    <tr>
        <td>{{__('tran.Total Expenditures')}}</td>
        <td><input type="text" class="form-control text-right" id="cost" name="cost" value="{{isset($data) ? $data->cost:''}}" readonly></td>
    </tr>      
    <tr>
        <td>{{__('tran.Gross Profit/Loss')}}</td>
        <td><input type="text" class="form-control text-right" id="profit" name="profit" value="{{isset($data) ? $data->profit:''}}" readonly></td>
    </tr>      
    <tr>
        <td>{{__('tran.Gross Profit/Loss Percentage')}}</td>
        <td><input type="text" class="form-control text-right" id="profit_rate" name="profit_rate" readonly></td>
    </tr>      
</table> 
</div>
</div>
<!-- end of total Part -->
</div>
<!-- End of New part -->



<script>
    var accessorial_modal_value = 0;
    
    function showAccessorialModel(id)
    {
        accessorial_modal_value = id;
        options_get("Accessorial", "accessorial_category",null, false, id-1);
        
        if(id == 1)
        {
            $('.category-title').text("{{__('tran.Income/budget')}}");
           
        }
            
        
        if(id == 2)
        {   
            $('.category-title').text("{{__('tran.Expense')}}");
           
        }
            
        
        if(id == 3)
        {   
            $('.category-title').text("{{__('tran.Driver Deduction')}}");
          
        }
            

        $('#accessorail_model').modal('show');
    }

    function AddAccessorailItem()
    {
        let total = $('#accessorial_total').val();
        let rate= $('#accessorial_rate').val();
        let descripton = $('#accessorial_description').val();
        let category = $('#accessorial_category').val();
        let type = accessorial_modal_value
        let driver =  $('#drivers option:selected').text();
        let broker =  $('#broker option:selected').text();
        let quantity = $('#accessorial_quantity').val();


        var accesorial_count = 0;

        
        var accesorial_sample = document.getElementById("div" + type + "_num_accesorials0").outerHTML;
        eval("accesorial_count" + type + "++;");
        eval("accesorial_count=accesorial_count" + type);
        //accessorail expense 
        if(type == 2)
        {
            var replaceString = "<td><select type=\"text\"  id=\"a2ccesorial0\" name=\"accesorials2[]\" class=\"form-control text-capitalize\" onchange=\"accessory_change('{{0}}','2')\"> <option>Loading...</option></select></td>"
            var queryString = document.getElementById("expense0").outerHTML;

            accesorial_sample =  accesorial_sample.replace(queryString, replaceString);

            var queryString2 = document.getElementById("quantity_a2ccesorial0").outerHTML;
            var replaceString2 = "<input type=\"number\" class=\"form-control text-right\" name=\"quantity_a2ccesorial[]\" id=\"quantity_a"+type+"ccesorial"+accesorial_count+"\" onchange=\"rateChange("+accesorial_count+","+type+")\">"

            accesorial_sample =  accesorial_sample.replace(queryString2, replaceString2);


        }
       
        var new_accesorial = accesorial_sample.replaceAll("a" + type + "ccesorial0", "a" + type + "ccesorial" + accesorial_count);
        new_accesorial = new_accesorial.replaceAll("div" + type + "_num_accesorials0", "div" + type + "_num_accesorials" + accesorial_count);
        new_accesorial = new_accesorial.replaceAll("value_a" + type + "ccesorial0", "value_a" + type + "ccesorial" + accesorial_count);
        
        new_accesorial = new_accesorial.replaceAll("selectRow('0','" + type + "')", "selectRow('" + accesorial_count + "','" + type + "')");
        new_accesorial = new_accesorial.replaceAll("accessory_change('0','" + type + "')", "accessory_change('" + accesorial_count + "','" + type + "')");
        
        new_accesorial = new_accesorial.replaceAll("rate_a" + type + "ccesorial0", "rate_a" + type + "ccesorial" + accesorial_count);
        
        new_accesorial = new_accesorial.replaceAll("rateChange('0','" + type + "')", "rateChange('" + accesorial_count + "','" + type + "')");
        
        new_accesorial = new_accesorial.replaceAll("note_a" + type + "ccesorial0", "note_a" + type + "ccesorial" + accesorial_count);

       
        new_accesorial = new_accesorial.replaceAll("rate_a" + type + "ccesorial0", "rate_a" + type + "ccesorial" + accesorial_count);
       
        new_accesorial = new_accesorial.replaceAll("quantity_a" + type + "ccesorial0", "quantity_a" + type + "ccesorial" + accesorial_count);

        new_accesorial = new_accesorial.replaceAll("company_a" + type + "ccesorial0", "company_a" + type + "ccesorial" + accesorial_count);
        
        new_accesorial = new_accesorial.replaceAll("a" + type + "ccessory_div0", "a" + type + "ccessory_div" + accesorial_count);
        new_accesorial = new_accesorial.replaceAll("payable0", "payable" + accesorial_count);
        new_accesorial = new_accesorial.replace("tr-active", "")
        document.getElementById("main_accesorials_div" + type).insertAdjacentHTML('beforeend', new_accesorial);
        if(type ==2)
            options_get("Accessorial", "a2ccesorial"+accesorial_count, category,  false, 1);
        //debugger;
        $("#company_a" + type + "ccesorial" + accesorial_count).val(driver);
        if(type==1)
        $("#company_a" + type + "ccesorial" + accesorial_count).val(broker);
        $("#a" + type + "ccesorial" + accesorial_count).val(category);
        $("#note_a" + type + "ccesorial" + accesorial_count).val(descripton);


        if(parseFloat(rate))
        $("#rate_a" + type + "ccesorial" + accesorial_count).val(numberToString(parseFloat(rate)));
        else
        $("#rate_a" + type + "ccesorial" + accesorial_count).val("$"+parseFloat(0));
        console.log("========================"+quantity)
        if(parseFloat(quantity))
            $("#quantity_a" + type + "ccesorial" + accesorial_count).val(parseFloat(quantity));
        else
            $("#quantity_a" + type + "ccesorial" + accesorial_count).val(parseFloat(0));
                
        
        rateChange(accesorial_count, type)
            // document.getElementById("consignee_num" + consignee).style.display = "block";
            // if (accesorial_count > 0)
            //     document.getElementById("remove_accesrial" + type).classList.remove("hidden");
            //calculate_accessorial_expense();
        $('#accessorial_total').val(0);
        $('#accessorial_rate').val(0);
        $('#accessorial_quantity').val(0);
        $('#accessorial_description').val("");
        $('#accessorial_category').val(1);
        $('#accessorail_model').modal('hide');

    }
    

    
    
</script>

