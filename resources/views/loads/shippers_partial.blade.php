@if($type=="simple")
    <div id="shipper_num0">
        <div class="col-md-12 box-title2">
            <h4>{{__("tran.Shipper")}} 1</h4>
        </div>

        <div class="form-group col-md-6">
            <label for="shippers0">{{__("tran.Select") ." ".__("tran.Shipper")}}</label>
            <select id="shippers0" name="shipper_id[]" class="form-control shippers_select2" onchange="set_contact_hash_address(0,'shipper')">
                <option>Loading...</option>
            </select>
        </div>

        <script>
            options_get("Customer", "shippers0");
        </script>
        @php 
            $specHide = "";
            $windowHide = "hide";
            if(isset($data)&&!empty($data->start_periode))
            {
                $specHide = "hide";
                $windowHide = "";
            }

            if(isset($data)&&!empty($data->end_periode))
            {
                $specHide = "hide";
                $windowHide = "";
            }
        @endphp
        <div class="form-group col-md-6">
            <div id="shipper_spec_time0" class="{{$specHide}}">
                <label for="pickup_date0">{{__("tran.Pickup Date")}}</label>
                <input type="datetime-local" name="pickup_date[]" class="form-control" id="pickup_date0" placeholder="{{__("tran.Pickup Date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->pickup_date)->format("Y-m-d"):""}}"
                @if(isset($data)&&$data->assign) readonly @endif
                >
                <a class="link-button" onclick="setWindowTime (1,0)">{{__("Set a date/time window")}}</a>
            </div>
            <div class="{{$windowHide}}" id="shipper_window_time0">
                <label for="">{{__("tran.Start Time")}}</label>
                <input type="datetime-local" name="shipper_start_periode[]" class="form-control" id="shipper_periode_start0" value="{{isset($data)?$data->start_periode:""}}"
                @if(isset($data)&&$data->assign) readonly @endif
                >
                <label for="">{{__("tran.End Time")}}</label>
                <input type="datetime-local" name="shipper_end_periode[]" class="form-control" id="shipper_periode_end0" value="{{isset($data)? $data->end_periode:""}}"
                @if(isset($data)&&$data->assign) readonly @endif
                >
                <a class="link-button" onclick="setSpecificTime(1,0)">{{__("Set a specific date/time")}}</a>
            </div>
            <div class="shipper_assign0">
            <label>
                <input type="checkbox" id="shipper_assign_checkbox0" name="fake[]"  onchange="shipperAssign('0')" @if(isset($data)&&$data->assign) checked @endif>
                To be assigned
                <input type="hidden" id="shipper_assign0" name="shipper_assign[]"  value="{{isset($data)?$data->assign:"false"}}" >
            </label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="shipper_number0">{{__("tran.Shipper")}} {{__("tran.Contact")}}</label>
            <input type="text" name="shipper_number[]" class="form-control" id="shipper_number0" placeholder="{{__("tran.Shipper")}} {{__("tran.Contact")}}" value="{{isset($data) ? $data->vehicle_id:""}}">
        </div>

        <div class="form-group col-md-6">
            <label for="shipper_hash0">{{__("tran.Shipper")}} #</label>
            <input type="text" name="shipper_hash[]" class="form-control" id="shipper_hash0" placeholder="{{__("tran.Shipper")}} #" value="{{isset($data) ? $data->vehicle_id:""}}">
        </div>

        <div class="form-group col-md-12">
            <label for="shipper_address0">{{__("tran.Pickup Address")}}</label>
            <textarea autocomplete="off" name="shipper_address[]" class="form-control shipper_locations maps_class" id="shipper_address0" placeholder="{{__("tran.Pickup Address")}}" onfocusout="calculate_distance()">{{isset($data) ? $data->current_address:""}}</textarea>
        </div>

        <div class="form-group col-md-12">
            <label for="shipper_notes0">{{__("tran.Shipper")}} {{__("tran.Notes")}}</label>
            <textarea name="shipper_notes[]" class="form-control" id="shipper_notes0" placeholder="{{__("tran.Shipper")}} {{__("tran.Notes")}}">{{isset($data) ? $data->current_address:""}}</textarea>
        </div>
    </div>
@else
     @php 
        $specHide = "";
        $windowHide = "hide";
        if(isset($shipper)&&!empty($shipper->start_periode))
        {
            $specHide = "hide";
            $windowHide = "";
        }

        if(isset($shipper)&&!empty($shipper->end_periode))
        {
            $specHide = "hide";
            $windowHide = "";
        }

    @endphp
    <div id="shipper_num{{$key}}">
        <div class="col-md-12 box-title2">
            <h4>{{__("tran.Shipper")}} {{$key+1}}</h4>
        </div>

        <div class="form-group col-md-6">
            <label for="shippers{{$key}}">{{__("tran.Select") ." ".__("tran.Shipper")}}</label>
            <select id="shippers{{$key}}" name="shipper_id[]" class="form-control shippers_select2" onchange="set_contact_hash_address({{$key}},'shipper')">
                <option>Loading...</option>
            </select>
        </div>

        <script>
            shipper ={{$key}};
            options_get("Customer", "shippers{{$key}}",{{$shipper->customer_id}});
        </script>

        <div class="form-group col-md-6">
            <div id="shipper_spec_time{{$key}}" class="{{$specHide}}">
                <label for="pickup_date{{$key}}">{{__("tran.Pickup Date")}}</label>
                <input type="datetime-local" name="pickup_date[]" class="form-control" id="pickup_date{{$key}}" placeholder="{{__("tran.Pickup Date")}}" value="{{isset($shipper) ? (\Carbon\Carbon::create($shipper->pickup_date)->format("Y-m-d")."T".\Carbon\Carbon::create($shipper->pickup_date)->format("H:i")):""}}">
                <a class="link-button" onclick="setWindowTime(1,'{{$key}}')">{{__("Set a date/time window")}}</a>
            </div>
            <div class="{{$windowHide}}" id="shipper_window_time{{$key}}">
                <label for="">{{__("tran.Start Time")}}</label>
                <input type="datetime-local" name="shipper_start_periode[]" class="form-control" id="shipper_periode_start{{$key}}" value="{{isset($shipper)?$shipper->start_periode:""}}"
                @if(isset($shipper)&&$shipper->assign) readonly @endif
                >
                <label for="">{{__("tran.End Time")}}</label>        
                <input type="datetime-local" name="shipper_end_periode[]" class="form-control" id="shipper_periode_end{{$key}}" value="{{isset($shipper)? $shipper->end_periode:""}}"
                @if(isset($shipper)&&$shipper->assign) readonly @endif
                >
                <a class="link-button" onclick="setSpecificTime(1,'{{$key}}')">{{__("Set a specific date/time")}}</a>
            </div>
            <div class="shipper_assign0">
            <label>
                <input type="checkbox" id="shipper_assign_checkbox{{$key}}" name="fake[]"  onchange="shipperAssign({{$key}})" @if(isset($shipper)&&$shipper->assign) checked @endif>
                To be assigned
                <input type="hidden" id="shipper_assign{{$key}}" name="shipper_assign[]"  value="{{isset($shipper)?$shipper->assign:"false"}}" >
            </label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="shipper_number{{$key}}">{{__("tran.Shipper")}} {{__("tran.Contact")}}</label>
            <input type="text" name="shipper_number[]" class="form-control" id="shipper_number{{$key}}" placeholder="{{__("tran.Shipper")}} {{__("tran.Contact")}}" value="{{isset($shipper) ? $shipper->contact_number:""}}">
        </div>

        <div class="form-group col-md-6">
            <label for="shipper_hash{{$key}}">{{__("tran.Shipper")}} #</label>
            <input type="text" name="shipper_hash[]" class="form-control" id="shipper_hash{{$key}}" placeholder="{{__("tran.Shipper")}} #" value="{{isset($shipper) ? $shipper->shipper_hash:""}}">
        </div>

        <div class="form-group col-md-12">
            <label for="shipper_address{{$key}}">{{__("tran.Pickup Address")}}</label>
            <textarea name="shipper_address[]" class="form-control shipper_locations maps_class" id="shipper_address{{$key}}" placeholder="{{__("tran.Pickup Address")}}" onfocusout="calculate_distance()">{{isset($shipper) ? $shipper->pickup_address:""}}</textarea>
        </div>

        <div class="form-group col-md-12">
            <label for="shipper_notes{{$key}}">{{__("tran.Shipper")}} {{__("tran.Notes")}}</label>
            <textarea name="shipper_notes[]" class="form-control" id="shipper_notes{{$key}}" placeholder="{{__("tran.Shipper")}} {{__("tran.Notes")}}">{{isset($shipper) ? $shipper->note:""}}</textarea>
        </div>
    </div>

@endif