@if($type=="simple")
    <div id="consignee_num0">
        <div class="col-md-12 box-title2">
            <h4>{{__("tran.Consignee")}} 1</h4>
        </div>

        <div class="form-group col-md-6">
            <label for="consignees0">{{__("tran.Select") ." ".__("tran.Consignee")}}</label>
            <select id="consignees0" name="consignee_id[]" class="form-control consignees_select2" onchange="set_contact_hash_address(0,'consignee')">
                <option>Loading...</option>
            </select>
        </div>
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
        <script>
            options_get("Customer", "consignees0");
        </script>

        <div class="form-group col-md-6">
            <div id="consignee_spec_time0" class="{{$specHide}}">
                <label for="dropoff_date0">{{__("tran.Dropoff Date")}}</label>
                <input type="datetime-local" name="dropoff_date[]" class="form-control" id="dropoff_date0" placeholder="{{__("tran.Dropoff Date")}}" value=""
                @if(isset($consignee)&&$consignee->assign) readonly @endif
                >
                <a class="link-button" onclick="setWindowTime(2,0)">{{__("Set a date/time window")}}</a>
            </div>
            
            <div id="consignee_window_time0" class="{{$windowHide}}">
                <label for="">{{__("tran.Start Time")}}</label>
                <input type="datetime-local" name="consignee_start_periode[]" class="form-control" id="consignee_periode_start0" value="{{isset($data)?$data->start_periode:""}}"
                @if(isset($data)&&$data->assign) readonly @endif
                >
                <label for="">{{__("tran.End Time")}}</label>
                <input type="datetime-local" name="consignee_end_periode[]" class="form-control" id="consignee_periode_end0" value="{{isset($data)? $data->end_periode:""}}"
                @if(isset($data)&&$data->assign) readonly @endif
                >
                <a class="link-button" onclick="setSpecificTime(2,0)">{{__("Set a specific date/time")}}</a>            
            </div>
            <div class="consignee_assign0">
                <label>
                    <input type="checkbox" id="consignee_assign_checkbox0" name="fake[]"  onchange="consigneeAssign(0)" @if(isset($data)&&$data->assign) checked @endif>
                    To be assigned
                    <input type="hidden" id="consignee_assign0" name="consignee_assign[]" value="{{isset($data)?$data->assign:"false"}}" >
                </label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="consignee_number0">{{__("tran.Consignee")}} {{__("tran.Contact")}}</label>
            <input type="text" name="consignee_number[]" class="form-control" id="consignee_number0" placeholder="{{__("tran.Consignee")}} {{__("tran.Contact")}}" value="">
        </div>

        <div class="form-group col-md-6">
            <label for="consignee_hash0">{{__("tran.Consignee")}} #</label>
            <input type="text" name="consignee_hash[]" class="form-control" id="consignee_hash0" placeholder="{{__("tran.Consignee")}} #" value="">
        </div>

        <div class="form-group col-md-12">
            <label for="consignee_address0">{{__("tran.Dropoff Address")}}</label>
            <textarea name="consignee_address[]" class="form-control consignee_locations maps_class" id="consignee_address0" placeholder="{{__("tran.Current Address")}}" onfocusout="calculate_distance()"></textarea>
        </div>

        <div class="form-group col-md-12">
            <label for="consignee_notes0">{{__("tran.Consignee")}} {{__("tran.Notes")}}</label>
            <textarea name="consignee_notes[]" class="form-control" id="consignee_notes0" placeholder="{{__("tran.Consignee")}} {{__("tran.Notes")}}"></textarea>
        </div>
    </div>
@else
    @php 
        $specHide = "";
        $windowHide = "hide";
        if(isset($consignee)&&!empty($consignee->start_periode))
        {
            $specHide = "hide";
            $windowHide = "";
        }

        if(isset($consignee)&&!empty($consignee->end_periode))
        {
            $specHide = "hide";
            $windowHide = "";
        }

    @endphp
    <div id="consignee_num{{$key}}">
        <div class="col-md-12 box-title2">
            <h4>{{__("tran.Consignee")}} {{$key+1}}</h4>
        </div>

        <div class="form-group col-md-6">
            <label for="consignees{{$key}}">{{__("tran.Select") ." ".__("tran.Consignee")}}</label>
            <select id="consignees{{$key}}" name="consignee_id[]" class="form-control consignees_select2" onchange="set_contact_hash_address({{$key}},'consignee')">
                <option>Loading...</option>
            </select>
        </div>

        <script>
            consignee={{$key}};
            options_get("Customer", "consignees{{$key}}",{{$consignee->customer_id}});
        </script>

        <div class="form-group col-md-6">
            <div id="consignee_spec_time{{$key}}" class="{{$specHide}}">
                <label for="dropoff_date{{$key}}">{{__("tran.Dropoff Date")}}</label>
                <input type="datetime-local" name="dropoff_date[]" class="form-control" id="dropoff_date{{$key}}" placeholder="{{__("tran.Dropoff Date")}}" value="{{isset($consignee) ? (\Carbon\Carbon::create($consignee->dropoff_time)->format("Y-m-d")."T".\Carbon\Carbon::create($consignee->dropoff_time)->format("H:i")):""}}" @if(isset($consignee)&&$consignee->assign) readonly @endif>
                <a class="link-button" onclick="setWindowTime(2,'{{$key}}')">{{__("Set a date/time window")}}</a>
            </div>
            <div class="{{$windowHide}}"  id="consignee_window_time{{$key}}">
                <label for="">{{__("tran.Start Time")}}</label>
                <input type="datetime-local" class="form-control"  name="consignee_start_periode[]" id="consignee_periode_start{{$key}}" value="{{isset($consignee)?$consignee->start_periode:""}}"  @if(isset($consignee)&&$consignee->assign) readonly @endif>
                <label for="">{{__("tran.End Time")}}</label>
                <input type="datetime-local" class="form-control" name="consignee_end_periode[]" id="consignee_periode_end{{$key}}" value="{{isset($consignee)? $consignee->end_periode:""}}"  @if(isset($consignee)&&$consignee->assign) readonly @endif>
                <a class="link-button" onclick="setSpecificTime(2,'{{$key}}')">{{__("Set a specific date/time")}}</a>
            </div>
            <div class="consignee_assign{{$key}}">
            <label>
            <input type="checkbox" id="consignee_assign_checkbox{{$key}}" name="fake[]"  onchange="consigneeAssign({{$key}})" @if(isset($consignee)&&$consignee->assign) checked @endif>
            To be assigned
            <input type="hidden" id="consignee_assign{{$key}}" name="consignee_assign[]" value="{{isset($consignee) ? $consignee->assign:"false"}}" >
            </label>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="consignee_number{{$key}}">{{__("tran.Consignee")}} {{__("tran.Contact")}}</label>
            <input type="text" name="consignee_number[]" class="form-control" id="consignee_number{{$key}}" placeholder="{{__("tran.Consignee")}} {{__("tran.Contact")}}" value="{{isset($consignee) ? $consignee->contact_number:""}}">
        </div>

        <div class="form-group col-md-6">
            <label for="consignee_hash{{$key}}">{{__("tran.Consignee")}} #</label>
            <input type="text" name="consignee_hash[]" class="form-control" id="consignee_hash{{$key}}" placeholder="{{__("tran.Consignee")}} #" value="{{isset($consignee) ? $consignee->hash:""}}">
        </div>

        <div class="form-group col-md-12">
            <label for="consignee_address{{$key}}">{{__("tran.Dropoff Address")}}</label>
            <textarea name="consignee_address[]" class="form-control consignee_locations maps_class" id="consignee_address{{$key}}" placeholder="{{__("tran.Dropoff Address")}}" onfocusout="calculate_distance()">{{isset($consignee) ? $consignee->dropoff_address:""}}</textarea>
        </div>

        <div class="form-group col-md-12">
            <label for="consignee_notes{{$key}}">{{__("tran.Consignee")}} {{__("tran.Notes")}}</label>
            <textarea name="consignee_notes[]" class="form-control" id="consignee_notes{{$key}}" placeholder="{{__("tran.Consignee")}} {{__("tran.Notes")}}">{{isset($consignee) ? $consignee->note:""}}</textarea>
        </div>
    </div>
@endif