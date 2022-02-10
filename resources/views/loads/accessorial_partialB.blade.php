<div class="col-md-12">

    {{--Accessorial income--}}
    <div class="col-md-6">
        <div class="col-md-12 margin box-title2">
            <h4>{{__("tran.Income")}}</h4>
        </div>
        <div id="main_accesorials_div1">
            <script>var accesorial_count1 = 0;</script>

            @if(isset($data))
                @php $income_count=0; @endphp
                @foreach($data->accessories->where("type","income") as $key=>$accessory)
                    <div id="div1_num_accesorials{{$income_count}}">
                        <div class="form-group col-md-12">
                            <label for="a1ccesorial{{$income_count}}">{{__("tran.Select") ." ".__("tran.Accessorial")}}</label>
                            <select required id="a1ccesorial{{$income_count}}" name="accesorials[]" class="form-control text-capitalize" onchange="accessory_change('{{$income_count}}',1)">
                                <option>Loading...</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12 {{isset($data) ? (($accessory->accessorial_id>1) ? "":"hidden"):"hidden"}}" id="a1ccessory_div{{$income_count}}">
                            <label for="value_a1ccesorial{{$income_count}}">{{__("tran.Accessorial Value")}}</label>
                            <input id="value_a1ccesorial{{$income_count}}" type="{{isset($data) ? (($accessory->accessorial_id>1)? "text":"hidden"):"hidden"}}" name="accessory_value[]" class="form-control accessory_value accessory_value_income" placeholder="{{__("tran.Accessorial Value")}}" value="{{isset($data) ? $accessory->value:""}}" onchange="calculate_accessorail_income()">
                        </div>
                    </div>
                    <script>
                        accesorial_count1 ={{$income_count}};
                    </script>
                    @php $income_count++; @endphp
                @endforeach
            @else
                <div id="div1_num_accesorials0">
                    <div class="form-group col-md-12">
                        <label for="a1ccesorial0">{{__("tran.Select") ." ".__("tran.Accessorial")}}</label>
                        <select required id="a1ccesorial0" name="accesorials[]" class="form-control text-capitalize" onchange="accessory_change('{{0}}','1')">
                            <option>Loading...</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12 hidden" id="a1ccessory_div0">
                        <label for="value_a1ccesorial0">{{__("tran.Accessorial Value")}}</label>
                        <input id="value_a1ccesorial0" type="text" name="accessory_value[]" class="form-control accessory_value accessory_value_income" placeholder="{{__("tran.Accessorial Value")}}" value="" onchange="calculate_accessorail_income('miles')">
                    </div>
                </div>
            @endif
        </div>
        <div class="form-group col-md-12" style="display: inline-flex;font-size: x-large;">
            <div title="Add Accessorial" id="add_accesrial1" class="hover_green"><i class="fa fa-fw fa-plus-circle float-right size140" onclick="add_accesrial(1)"></i></div>
            <div title="Remove Accessorial" id="remove_accesrial1" class="hover_red {{(isset($data)) ? ((count($data->accessories)>1 ? "":"hidden")):"hidden"}}"><i class="fa fa-fw fa-times-circle float-right size140" onclick="remove_accesrial(1)"></i></div>
        </div>
    </div>
    {{--Accessorial income end--}}

    {{--Accessorial expense--}}
    <div class="col-md-6">
        <div class="col-md-12 margin box-title2">
            <h4>{{__("tran.Expense")}}</h4>
        </div>
        <div id="main_accesorials_div2">
            <script>var accesorial_count2 = 0;</script>

            @if(isset($data))
                @php $expenses_count=0; @endphp
                @foreach($data->accessories->where("type","expense") as $key=>$accessory)
                    <div id="div2_num_accesorials{{$expenses_count}}">
                        <div class="form-group col-md-12">
                            <label for="a2ccesorial{{$expenses_count}}">{{__("tran.Select") ." ".__("tran.Accessorial")}}</label>
                            <select required id="a2ccesorial{{$expenses_count}}" name="accesorials2[]" class="form-control text-capitalize" onchange="accessory_change('{{$expenses_count}}','2')">
                                <option>Loading...</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12 {{isset($data) ? (($accessory->accessorial_id>1)? "":"hidden"):"hidden"}}" id="a2ccessory_div{{$expenses_count}}">
                            <label for="value_a2ccesorial{{$expenses_count}}">{{__("tran.Accessorial Value")}}</label>
                            <input id="value_a2ccesorial{{$expenses_count}}" type="{{isset($data) ? (($accessory->accessorial_id>1)? "text":"hidden"):"hidden"}}" name="accessory_value2[]" class="form-control accessory_value accessory_value_expense" placeholder="{{__("tran.Accessorial Value")}}" value="{{isset($data) ? $accessory->value:""}}" onchange="calculate_accessorial_expense()">
                        </div>

                        <div class="form-group col-md-12">
                            <input id="payable{{$expenses_count}}" type="checkbox" name="payable[]" class="payable_to_driver" value="payable" onchange="calculate_accessorial_expense()" {{$accessory->payable_to_driver ? "checked":""}}>
                            <label for="payable{{$expenses_count}}">{{__("tran.Payable to Driver")}}</label>
                        </div>

                    </div>
                    <script>
                        accesorial_count2 ={{$expenses_count}};
                    </script>
                    @php $expenses_count++; @endphp
                @endforeach
            @else
                <div id="div2_num_accesorials0">
                    <div class="form-group col-md-12">
                        <label for="a2ccesorial0">{{__("tran.Select") ." ".__("tran.Accessorial")}}</label>
                        <select required id="a2ccesorial0" name="accesorials2[]" class="form-control text-capitalize" onchange="accessory_change('{{0}}','2')">
                            <option>Loading...</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12 hidden" id="a2ccessory_div0">
                        <label for="value_a2ccesorial0">{{__("tran.Accessorial Value")}}</label>
                        <input id="value_a2ccesorial0" type="text" name="accessory_value2[]" class="form-control accessory_value accessory_value_expense" placeholder="{{__("tran.Accessorial Value")}}" value="" onchange="calculate_accessorial_expense()">
                    </div>

                    <div class="form-group col-md-12">
                        <input id="payable0" type="checkbox" name="payable[]" class="payable_to_driver" value="payable" onchange="calculate_accessorial_expense()">
                        <label for="payable0">{{__("tran.Payable to Driver")}}</label>
                    </div>
                </div>
            @endif
        </div>
        <div class="form-group col-md-12" style="display: inline-flex;font-size: x-large;">
            <div title="Add Accessorial" id="add_accesrial2" class="hover_green"><i class="fa fa-fw fa-plus-circle float-right size140" onclick="add_accesrial(2)"></i></div>
            <div title="Remove Accessorial" id="remove_accesrial2" class="hover_red {{(isset($data)) ? ((count($data->accessories)>1 ? "":"hidden")):"hidden"}}"><i class="fa fa-fw fa-times-circle float-right size140" onclick="remove_accesrial(2)"></i></div>
        </div>
    </div>

</div>