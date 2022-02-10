@extends('layouts.main')

@push("header_top"){{--less priority css--}}
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">
<style>
    .box-title2 {
        background: #a5c2eb;
        border-radius: 5px;
    }

    .hover_green:hover {
        color: green;
    }

    .hover_red:hover {
        color: red;
    }

    .modal {
        z-index: 100 !important;
    }

    .modal-backdrop {
        z-index: 99 !important;
    }

    #map {
        position: relative;
        height:400px;
    }

    .edit-icon {
        color: #8A6D3B  !important;

        margin-left:20px;
    }

    #distance-part{
        color: #515A64 !important; 
    }
</style>
<script src="https://maps.googleapis.com/maps/api/js?key={{env("MAP_API")}}&libraries=places"></script>

@endpush

@push("header")
    <script>
        var consignee_sample_loaded = false;
        var shipper_sample_loaded = false;
        var accessorial_expense = 0;
        var accessorial_income = 0;
        var accesorials = 0;
        var total_miles = 0;
        var total_cost = 0;
        var total_profit = 0;
        var total_revenue = 0;
        var driver_rate = 99999;
        
        var map="";
        
        @if(isset($data)&&!empty($data->driver))
                @if($data->driver && $data->driver->rate)
            driver_rate = {{$data->driver->rate}};
        @endif
        @if($data->miles)
        total_miles = {{$data->dead_head_miles + $data->miles}};
        @endif

        @if($data->cost)
        total_cost ={{$data->cost}};
        @endif
        @if($data->profit)
        total_profit ={{$data->profit}};
        @endif
        @if($data->value)
        total_revenue ={{$data->value}};
        @endif
        @endif

        function options_get(model, select_id, selected = null, again = false) {

            $.ajax({
                url: '{{route("app_url")}}' + '/options/' + model + '/' + selected,
                method: 'get',
                success: function (result) {
                    document.getElementById(select_id).innerHTML = result;
                    if (select_id == "consignees_key_var")
                        consignee_sample_loaded = true;
                    if (select_id == "shippers_key_var")
                        shipper_sample_loaded = true;
                },
                error: function (xhr, status, error) {
                    //function to run if the request fails
                    if (again)
                        alert("Try again! Unable to load page properly.");
                    else
                        setTimeout(function () {
                            options_get(model, select_id, selected, true);
                        }, 1000);
                }
            });
        }
    </script>
@endpush

@section("title") {{__("tran.".$pageTitle)}} @endsection

@section('content')

    <div class="box-body">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{__("tran.".$pageTitle)}}</h3>
            </div>

            <form id="form1" role="form" method="post" action="{{$url}}" enctype="multipart/form-data">
                @csrf
               
                <input type="hidden" name="id" class="form-control" id="id" value="{{isset($data)?$data->id:''}}">
                <input type="hidden" name="send_sms" class="form-control" id="send_sms" value="false">
                <div class="box-body">

                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Load Basics")}}</h4>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="status">{{__("tran.Load") ." ".__("tran.Status")}}</label>
                        <select id="status" name="status" class="form-control text-capitalize">
                            <option value="Pending" {{isset($data) ? ($data->status=="Pending" ? "selected":""):""}}>{{__("tran.Pending")}}</option>
                            <option value="Needs Driver" {{isset($data) ? ($data->status=="Needs Driver" ? "selected":""):""}}>{{__("tran.Needs Driver")}}</option>
                            <option value="Dispatched" {{isset($data) ? ($data->status=="Dispatched" ? "selected":""):""}}>{{__("tran.Dispatched")}}</option>
                            <option value="In Transit" {{isset($data) ? ($data->status=="In Transit" ? "selected":""):""}}>{{__("tran.In Transit")}}</option>
                            <option value="Delivered" {{isset($data) ? ($data->status=="Delivered" ? "selected":""):""}}>{{__("tran.Delivered")}}</option>
                            <option value="Completed" {{isset($data) ? ($data->status=="Completed" ? "selected":""):""}}>{{__("tran.Completed")}}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="reference">{{__("tran.Load Reference ID / Numbers")}}</label>
                        <input type="text" name="reference" class="form-control" id="reference" placeholder="{{__("tran.Load Reference ID / Numbers")}}" value="{{isset($data) ? $data->reference:""}}">
                    </div>

                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Broker")}} {{__("tran.Information")}}</h4>
                    </div>

                    <div class="row no-margin">
                        <div class="form-group col-md-6">
                            <label for="broker">{{__("tran.Select") ." ".__("tran.Broker")}}</label>
                            <select id="broker" name="broker_id" class="form-control" >
                                <option>Loading...</option>
                            </select>
                            <button type="button" class="btn btn-info btn-sm margin" data-toggle="modal" data-target="#broker_model" href="#">{{__("tran.Add Broker")}}</button>
                        </div>
                    </div>

                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Carrier")}}/{{__("tran.Asset")}} {{__("tran.Info")}}</h4>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="driver_id">{{__("tran.Select") ." ".__("tran.Driver")}}</label>
                        <select id="drivers" name="driver_id" class="form-control">
                            <option>Loading...</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="trailers">{{__("tran.Select") ." ".__("tran.Trailer")}}</label>
                        <select id="trailers" name="trailer_id" class="form-control">
                            <option>Loading...</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="tractor">{{__("tran.Select") ." ".__("tran.Tractor")}}</label>
                        <select id="tractor" name="tractor_id" class="form-control" >
                            <option>Loading...</option>
                        </select>
                    </div>

                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Edit")}} {{__("tran.Stops")}}</h4>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="current_address">{{__("tran.Deadhead Origin")}}</label>
                        <input name="current_address" id="current_address" class="form-control"
                               placeholder="{{__("tran.Deadhead Origin")}}"
                               value="{{isset($data) ? $data->address:""}}"
                               type="text"
                               autocomplete="off"
                               data-language="fr"
                             
                               >
                               <!-- onfocusout="calculate_distance()"  -->
                        <input type="hidden" name="current_address_lat" id="current_address_lat">
                        <input type="hidden" name="current_address_lng" id="current_address_lng">

                    </div>
                    <script>
                        var consignee = 0;
                        var shipper = 0;
                    </script>
                    {{--shipper--}}
                    <div class="col-md-6">
                        <div id="shipper_div">
                            @if(isset($data) && isset($data->shipper)&&count($data->shipper) != 0)
                                @foreach($data->shipper as $key=>$shipper)
                                    @include('loads.shippers_partial', ['type' => 'data'])
                                @endforeach
                            @else
                                @include('loads.shippers_partial', ['type' => 'simple'])
                            @endif
                        </div>

                        {{--shipper sample for js--}}
                        <div id="shipper_num_key_var" style="display: none;">
                            <div class="col-md-12 box-title2">
                                <h4>{{__("tran.Shipper")}} _key_plus</h4>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="shippers_key_var">{{__("tran.Select") ." ".__("tran.Shipper")}}</label>
                                <select id="shippers_key_var" name="shipper_id[]" class="form-control shippers_select2" onchange="set_contact_hash_address('_key_var','shipper')">
                                    <option>Loading...</option>
                                </select>
                            </div>

                            <script>
                                options_get("Customer", "shippers_key_var");
                            </script>

                            <div class="form-group col-md-6">
                                <label for="pickup_date_key_var">{{__("tran.Pickup Date")}}</label>
                                <input type="datetime-local" name="pickup_date[]" class="form-control" id="pickup_date_key_var" placeholder="{{__("tran.Pickup Date")}}" value="" >
                            </div>

                            <div class="form-group col-md-6">
                                <label for="shipper_number_key_var">{{__("tran.Shipper")}} {{__("tran.Contact")}}</label>
                                <input type="text" name="shipper_number[]" class="form-control" id="shipper_number_key_var" placeholder="{{__("tran.Shipper")}} {{__("tran.Contact")}}" value="">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="shipper_hash_key_var">{{__("tran.Shipper")}} #</label>
                                <input type="text" name="shipper_hash[]" class="form-control" id="shipper_hash_key_var" placeholder="{{__("tran.Shipper")}} #" value="">
                            </div>

                            <div class="form-group col-md-12">
                                <label for="shipper_address_key_var">{{__("tran.Pickup Address")}}</label>
                                <input name="shipper_address[]" id="shipper_address_key_var" class="form-control shipper_locations maps_class"
                                       placeholder="{{__("tran.Pickup Address")}}"
                                       value=""
                                       type="text" autocomplete="off"
                                       onfocusout="calculate_distance()">
                            </div>

                            <div class="form-group col-md-12">
                                <label for="shipper_notes_key_var">{{__("tran.Shipper")}} {{__("tran.Notes")}}</label>
                                <textarea name="shipper_notes[]" class="form-control" id="shipper_notes_key_var" placeholder="{{__("tran.Shipper")}} {{__("tran.Notes")}}">{{isset($data)&&!empty($data->current_address) ? $data->current_address:""}}</textarea>
                            </div>
                        </div>
                        {{--shipper sample end--}}

                        <div class="row margin-bottom">
                            <input type="button" id="add_shipper" value="{{__("tran.Add")}} {{__("tran.Pickup")}}" class="btn btn-success col-md-3 col-md-offset-1">
                            <input type="button" id="remove_shipper" value="Remove" class="btn btn-danger col-md-3 col-md-offset-1">
                            <button type="button" class="btn btn-info col-md-3 col-md-offset-1" data-toggle="modal" data-target="#customer_model">{{__("tran.Add")}} {{__("tran.Customer")}}</button>
                        </div>
                        {{--                        <div class="row margin-bottom">--}}
                        {{--                            <a href="{{route("customers-create")}}" target="_blank"><input type="button" value="Add Customer" class="btn btn-info col-md-4 col-md-offset-4"></a>--}}
                        {{--                        </div>--}}

                    </div>
                    {{--shipper end--}}

                    {{--consignee--}}
                    <div class="col-md-6">
                        <div id="consignee_div">
                          @if(isset($data) && isset($data->consignee)&& count($data->consignee) != 0)
                                @foreach($data->consignee as $key=>$consignee)
                                    @include('loads.consignee_partial', ['type' => 'data'])
                                @endforeach
                            @else
                                @include('loads.consignee_partial', ['type' => 'simple'])
                            @endif
                        </div>
                        {{--consignee sample--}}
                        <div id="consignee_num_key_var" style="display: none;">
                            <div class="col-md-12 box-title2">
                                <h4>{{__("tran.Consignee")}} _key_plus</h4>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="consignees_key_var">{{__("tran.Select") ." ".__("tran.Consignee")}}</label>
                                <select id="consignees_key_var" name="consignee_id[]" class="form-control consignees_select2" onchange="set_contact_hash_address('_key_var','consignee')">
                                    <option>Loading...</option>
                                </select>
                            </div>

                            <script>
                                options_get("Customer", "consignees_key_var");
                            </script>

                            <div class="form-group col-md-6">
                                <label for="dropoff_date_key_var">{{__("tran.Dropoff Date")}}</label>
                                <input type="datetime-local" name="dropoff_date[]" class="form-control" id="dropoff_date_key_var" placeholder="{{__("tran.Dropoff Date")}}" value="">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="consignee_number_key_var">{{__("tran.Consignee")}} {{__("tran.Contact")}}</label>
                                <input type="text" name="consignee_number[]" class="form-control" id="consignee_number_key_var" placeholder="{{__("tran.Consignee")}} {{__("tran.Contact")}}" value="">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="consignee_hash_key_var">{{__("tran.Consignee")}} #</label>
                                <input type="text" name="consignee_hash[]" class="form-control" id="consignee_hash_key_var" placeholder="{{__("tran.Consignee")}} #" value="">
                            </div>

                            <div class="form-group col-md-12">
                                <label for="consignee_address_key_var">{{__("tran.Dropoff Address")}}</label>
                                <textarea autocomplete="off" name="consignee_address[]" class="form-control consignee_locations maps_class" id="consignee_address_key_var" placeholder="{{__("tran.Current Address")}}" onfocusout="calculate_distance()" ></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="consignee_notes_key_var">{{__("tran.Consignee")}} {{__("tran.Notes")}}</label>
                                <textarea name="consignee_notes[]" class="form-control" id="consignee_notes_key_var" placeholder="{{__("tran.Consignee")}} {{__("tran.Notes")}}"></textarea>
                            </div>
                        </div>
                        {{--consignee sample end--}}
                        <div class="row margin-bottom">
                            <input type="button" id="add_consignee" value="{{__("tran.Add")}} {{__("tran.Delivery")}}" class="btn btn-success col-md-3 col-md-offset-1">
                            <input type="button" id="remove_consignee" value="Remove" class="btn btn-danger col-md-3 col-md-offset-1">

                            <button type="button" class="btn btn-info col-md-3 col-md-offset-1" data-toggle="modal" data-target="#customer_model">{{__("tran.Add")}} {{__("tran.Customer")}}</button>
                        </div>
                    </div>
                    {{--consignee end--}}

                    <!-- start the google map -->
                    
                    <div class="col-md-12 margin-bottom">
                        <div id="map"></div>
                    </div>
                    <!-- end of the google map -->
                    <!-- Distance Information Part -->
                    <div class="col-md-12" id="disance-part">
                        <p><b><i>{{__('tran.Map Info Description text')}}</i></b></p>
                        <div class="row margin-bottom">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                            <b>{{__('tran.Carrier Mileage')}}</b><br>
                            <small><i>{{__('tran.Using calculated carrier mileage')}}</i></small>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6"><b><span class="total-miles">0</span> <i>miles</i></b> &nbsp;<i class="fa fa-pencil edit-icon"></i></div>
                        </div>

                        <div class="row margin-bottom">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                            <b>{{__('tran.Customer Mileage')}}</b><br>
                            <small><i>{{__('tran.Using calculated carrier mileage')}}</i></small>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6"><b><span class="total-miles">0</span> <i>miles</i></b> &nbsp;<i class="fa fa-pencil edit-icon"></i></div>
                        </div>

                        <div class="row margin-bottom">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                            <b>{{__('tran.Deadhead Mileage')}}</b><br>
                            <small><i>{{__('tran.(If applicable)')}}</i></small>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6"><b><span id="deadhead-miles">0</span> <i>miles</i></b></div>
                        </div>
                    </div>
                   
                    <div class="row margin-bottom">
                        <div class="col-md-12">
                            <!-- <button type="button" class="btn btn-info col-md-4 col-md-offset-4" data-toggle="modal" data-target="#customer_model">{{__("tran.Add")}} {{__("tran.Customer")}}</button> -->
                        </div>
                    </div>
                  
                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Financials")}}</h4>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="value">{{__("Flat Rate")}}</label>
                        <input type="number" step="0.1" name="value" class="form-control" id="value" placeholder="{{__("Flat Rate")}}" value="{{isset($data) ? $data->value:""}}" onchange="set_revenue()">
                    </div>

                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Accessorial")}}</h4>
                    </div>

                    @include('loads.accessorial_partial')

                    <div class="col-md-12 box-title2">
                        <h4>{{__("tran.Load Summary")}}</h4>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="miles">{{__("tran.Total Miles")}}</label>
                        <input type="number" step="1" name="miles" class="form-control" id="miles" placeholder="{{__("tran.Total Miles")}}" value="{{isset($data) ? $data->miles:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="cost">{{__("tran.Load Cost")}}</label>
                        <input type="text" readonly name="cost" class="form-control" id="cost" placeholder="{{__("tran.Load Cost")}}" value="{{isset($data) ? $data->cost:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="profit">{{__("tran.Load Profit")}}</label>
                        <input type="text" readonly name="profit" class="form-control" id="profit" placeholder="{{__("tran.Load Profit")}}" value="{{isset($data) ? $data->profit:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="revenue">{{__("tran.Total Revenue")}}</label>
                        <input type="text" readonly name="revenue" class="form-control" id="revenue" placeholder="{{__("tran.Total Revenue")}}" value="{{isset($data) ? $data->value:""}}" >
                    </div>

                    <div class="form-group col-md-6">
                        <label for="revenue">{{__("tran.Deadhead Miles")}}</label>
                        <input type="text" readonly id="dead_head_miles" placeholder="{{__("tran.Deadhead Miles")}}" name="dead_head_miles" class="form-control" value="{{isset($data) ? $data->dead_head_miles:0}}" >
                    </div>
                    <div class="col-md-6" id="loading_summary" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="background: rgba(255, 255, 255, 0); display: block;" width="50px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                            <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#3c8dbc" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                            </circle>
                        </svg>
                    </div>
                </div>

                <div class="box-footer text-center">
                    <button type="submit" id="submit_btn" class="btn btn-primary">{{__("tran.Submit")}}</button>
                    <button type="button" id="submit_sms_btn" onclick="submitSendSmsButton()" class="btn btn-muted" disabled>{{__("tran.submit_sms")}}</button>
                    <button type="button" class="btn btn-success" onclick="update_summary()">{{__("tran.Update")}}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Create -->
    <div id="customer_model" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{__("tran.Customer Create")}}</h4>
                </div>
                <form role="form" method="post" id="customer_form" action="">

                    <div class="modal-body">
                        @csrf
                        <div class="box-body">
                            <input type="hidden" name="ajax" value="true">
                            <input type="hidden" name="lat" id="lat" value="">
                            <input type="hidden" name="lng" id="lng" value="">
                            <div class="form-group">
                                <label for="number">{{__("tran.Contact Number")}}</label>
                                <input  type="text" name="number" class="form-control" id="id1" placeholder="Enter number" value="">
                            </div>
                            <div class="form-group">
                                <label for="company_name">{{__("tran.Company Name")}}</label>
                                <input required type="text" name="company" class="form-control" id="id2" placeholder="{{__("tran.Company Name")}}" value="">
                            </div>

                            <div class="form-group">
                                <label for="id3">{{__("tran.Address")}}</label>
                                <textarea required name="address" class="form-control" id="id3" placeholder="Enter address" autocomplete="off" data-language="fr"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    {{--    customer create end--}}
    {{--    broker create--}}
    <div id="broker_model" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{__("tran.Broker Create")}}</h4>
                </div>
                <form role="form" method="post" id="broker_form" action="">

                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="ajax" value="true">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="fname">{{__("tran.Contact Name")}}</label>
                                <input required type="text" name="fname" class="form-control" id="fname" placeholder="Enter {{__("tran.Full Name")}}" value="">
                            </div>

                            <div class="form-group">
                                <label for="company">{{__("tran.Company Name")}}</label>
                                <input  type="text" name="company" class="form-control" id="company" placeholder="{{__("tran.Company Name")}}" value="">
                            </div>

                            <div class="form-group">
                                <label for="number">{{__("tran.Contact Number")}}</label>
                                <input  type="text" name="number" class="form-control" id="number" placeholder="Enter number" value="">
                            </div>
                            <div class="form-group">
                                <label for="number">{{__("tran.Mc Number")}}</label>
                                <input  type="text" name="mc_number" class="form-control" id="mc_number" placeholder="Enter {{__("tran.Mc Number")}}" value="">
                            </div>
                            <div class="form-group">
                                <label for="id4">{{__("tran.Address")}}</label>
                                <textarea name="address" class="form-control" id="id4" placeholder="Enter address"></textarea>
                            </div>
        
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    {{--    broker create end--}}
@endsection

@push("footer")
    <!-- Select2 -->
    <script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API')}}&callback=initMap"></script> -->
    <!-- <script src="{{ asset('js/googlemap.js')}}"></script> -->
    <script>
        
        function set_select2() {
            $('.shippers_select2').select2();
            $('.consignees_select2').select2();
            $('#broker').select2();
        }
        {{--    </script>--}}
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> --}}
        // <script type="text/javascript">
        //init google maps
       
        // Autocomplete google maps
      
        try {
            google.maps.event.addDomListener(window, 'load', function () {
                var current_address = new google.maps.places.Autocomplete(document.getElementById('current_address'));

                var places2 = new google.maps.places.Autocomplete(document.getElementById('shipper_address0'));
                var places3 = new google.maps.places.Autocomplete(document.getElementById('consignee_address0'));
                var id3 = new google.maps.places.Autocomplete((document.getElementById('id3')), {types:['geocode']});
                var id4 = new google.maps.places.Autocomplete(document.getElementById('id4'));
                id3.addListener('place_changed', function(){
                    var place = id3.getPlace();
                    if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                    }

                    console.log(place.geometry.location.lat(), place.geometry.location.lng())
                     $('#lat').val(place.geometry.location.lat()) 
                     $('#lng').val(place.geometry.location.lng())                  

                })

                current_address.addListener('place_changed', function(){
                    var place = current_address.getPlace();
                    if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                    }

                    console.log(place.geometry.location.lat(), place.geometry.location.lng())
                     $('#current_address_lat').val(place.geometry.location.lat()) 
                     $('#current_address_lng').val(place.geometry.location.lng())                  
                     getRoadData()
                })


                 map = new google.maps.Map(document.getElementById('map'), {
                    zoom:14,
                    center:{
                        lat:38.543672,
                        lng:-95.855207
                    },
                    
                    // disableDefaultUI: true,
                })



                {{--places3.addListener("place_changed", function () {--}}
                {{--    $.ajaxSetup({--}}
                {{--        headers: {--}}
                {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                {{--        }--}}
                {{--    });--}}
                {{--    var addressFrom = document.getElementById('shipper_address0').value;--}}
                {{--    var addressTo = places3.getPlace().formatted_address;--}}
                {{--    var urlForCalculateDistance = '{{ route('loads-calculateDistance') }}';--}}
                {{--    $.ajax({--}}
                {{--        type: 'POST',--}}
                {{--        data: {--}}
                {{--            addressFrom: addressFrom,--}}
                {{--            addressTo: addressTo--}}
                {{--        },--}}
                {{--        url: urlForCalculateDistance,--}}
                {{--        dataType: "Json",--}}
                {{--        success: function (result) {--}}
                {{--            document.getElementById('miles').value = result.miles;--}}
                {{--            set_amounts("miles");--}}
                {{--        }--}}
                {{--    });--}}
                {{--});--}}
                {{--places2.addListener("place_changed", function () {--}}
                {{--    var addressFrom = places2.getPlace().formatted_address;--}}
                {{--    var addressTo = document.getElementById('consignee_address0').value;--}}
                {{--    $.ajaxSetup({--}}
                {{--        headers: {--}}
                {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                {{--        }--}}
                {{--    });--}}
                {{--    var urlForCalculateDistance = '{{ route('loads-calculateDistance') }}';--}}
                {{--    $.ajax({--}}
                {{--        type: 'POST',--}}
                {{--        data: {--}}
                {{--            addressFrom: addressFrom,--}}
                {{--            addressTo: addressTo--}}
                {{--        },--}}
                {{--        url: urlForCalculateDistance,--}}
                {{--        dataType: "Json",--}}
                {{--        success: function (result) {--}}
                {{--            document.getElementById('miles').value = result.miles;--}}
                {{--            set_amounts("miles");--}}
                {{--        }--}}
                {{--    });--}}
                {{--});--}}

            });
        } catch (e) {
            console.log(e)
        }
        var temp;

        function calculate_distance() {
            setTimeout(function () {
                var consignee_locations = [];
                var shipper_locations = [];
                // set consignees
                Array.from(document.getElementsByClassName("consignee_locations")).forEach(function (currentValue, index, arr) {
                    new google.maps.places.Autocomplete(currentValue);
                    if (currentValue.value == "")
                        return;
                    consignee_locations[index] = currentValue.value;
                });

                // set shippers
                Array.from(document.getElementsByClassName("shipper_locations")).forEach(function (currentValue, index, arr) {
                    new google.maps.places.Autocomplete(currentValue);
                    if (currentValue.value == "")
                        return;
                    shipper_locations[index] = currentValue.value;
                });

                var current_location = document.getElementById('current_address').value;
                if (current_location == "")
                    return;
                // console.log("shipper_locations\n", shipper_locations, "consignee_locations\n", consignee_locations);

                document.getElementById("loading_summary").style.display = "block";
                document.getElementById("submit_btn").disabled = true;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    data: {
                        shipper_locations: shipper_locations,
                        consignee_locations: consignee_locations,
                        current_location: current_location
                    },
                    url: '{{ route('distance-between-multiple-points') }}',
                    dataType: "Json",
                    success: function (result) {
                        console.log(result);
                        if (result.status == "success") {
                            document.getElementById('miles').value = result.miles;
                            document.getElementById('dead_head_miles').value = result.dead_head_miles;
                            $('#deadhead-miles').html(result.dead_head_miles) 
                            $('.total-miles').html(result.miles) 
                            total_miles = result.miles + result.dead_head_miles;
                            
                            update_summary();
                        }
                        document.getElementById("loading_summary").style.display = "none";
                        document.getElementById("submit_btn").disabled = false;
                    }, error: function (result) {
                        document.getElementById("loading_summary").style.display = "none";
                        document.getElementById("submit_btn").disabled = false;
                    }
                });

            }, 1000);
        }

        function load_brokers() {
            
            @if(isset($data)&&!empty($data->broker_id))
            options_get("Broker", "broker",{{$data->broker_id}});
            @else
            options_get("Broker", "broker");
            @endif
        }

        function load_data() {
            // console.log("load data");
            @if(isset($data)&&!empty($data->driver_id))
            options_get("Driver", "drivers",{{$data->driver_id}});
            @else
            options_get("Driver", "drivers");
            @endif

            @if(isset($data)&&!empty($data->trailer_id))
            options_get("Trailer", "trailers",{{$data->trailer_id}});
            @else
            options_get("Trailer", "trailers");
            @endif
            
            @if(isset($data)&&!empty($data->tractor_id))
            options_get("Tractor", "tractor",{{$data->tractor_id}});
            @else
            options_get("Tractor", "tractor");
            @endif
            
            @if(isset($data)&&!empty($data->accessories))
            // income accessories
            @php $temp=0; @endphp
            @foreach($data->accessories->where("type","income") as $key=>$accessory)
            options_get("Accessorial", "a1ccesorial{{$temp}}",{{$accessory->accessorial_id}});
            @php $temp++; @endphp
            @endforeach

            @else
            options_get("Accessorial", "a1ccesorial0",1);
            @endif

            @if(isset($data)&&!empty($data->accessories))
            // expense accessories
            @php $temp=0; @endphp
            @foreach($data->accessories->where("type","expense") as $key=>$accessory)
            options_get("Accessorial", "a2ccesorial{{$temp}}",{{$accessory->accessorial_id}});
            @php $temp++; @endphp
            @endforeach
            @else
           
           
          
          
            options_get("Accessorial", "a2ccesorial0",1);
            @endif
            load_brokers();

            // set_select2();
            if (shipper < 1)
                document.getElementById("remove_shipper").style.display = "none";
            if (consignee < 1)
                document.getElementById("remove_consignee").style.display = "none";
        }

        window.addEventListener('load', function () {
            load_data();
            calculate_accessorail_income();
            calculate_accessorial_expense();
        });

        document.getElementById("drivers").addEventListener("change", function (e) {
            set_amounts("driver");
        });
        // shippers
        var shipper_sample = null;

        document.getElementById("add_shipper").addEventListener("click", function (e) {
            if (shipper_sample == null) {
                shipper_sample = document.getElementById("shipper_num_key_var").outerHTML;
                document.getElementById("shipper_num_key_var").outerHTML = "";
            }
            shipper++;
            var new_shipper = shipper_sample.replace(/_key_var/gi, shipper);
            new_shipper = new_shipper.replace(/_key_plus/gi, shipper + 1);
            // document.getElementById("shipper_div").innerHTML = document.getElementById("shipper_div").innerHTML + new_shipper;
            document.getElementById("shipper_div").insertAdjacentHTML('beforeend', new_shipper);
            document.getElementById("shipper_num" + shipper).style.display = "block";
            set_select2();
            if (shipper > 0)
                document.getElementById("remove_shipper").style.display = "block";

            Array.from(document.getElementsByClassName("shipper_locations")).forEach(function (currentValue, index, arr) {
                new google.maps.places.Autocomplete(currentValue);
            });
        });

        document.getElementById("remove_shipper").addEventListener("click", function (e) {
            if (shipper > 0) {
                document.getElementById("shipper_num" + shipper).outerHTML = "";
                shipper--;
            } else
                alert("One shipper is Required");
            if (shipper < 1) {
                document.getElementById("remove_shipper").style.display = "none";
            }
        });
        // consignee
        var consignee_sample = null;

        document.getElementById("add_consignee").addEventListener("click", function (e) {
            if (consignee_sample == null) {
                consignee_sample = document.getElementById("consignee_num_key_var").outerHTML;
                document.getElementById("consignee_num_key_var").outerHTML = "";
            }
            consignee++;
            var new_consignee = consignee_sample.replace(/_key_var/gi, consignee);
            new_consignee = new_consignee.replace(/_key_plus/gi, consignee + 1);
            // document.getElementById("consignee_div").innerHTML = document.getElementById("consignee_div").innerHTML + new_consignee;
            document.getElementById("consignee_div").insertAdjacentHTML('beforeend', new_consignee);
            document.getElementById("consignee_num" + consignee).style.display = "block";
            set_select2();
            if (consignee > 0)
                document.getElementById("remove_consignee").style.display = "block";

            Array.from(document.getElementsByClassName("consignee_locations")).forEach(function (currentValue, index, arr) {
                new google.maps.places.Autocomplete(currentValue);
            });

        });

        document.getElementById("remove_consignee").addEventListener("click", function (e) {
            if (consignee > 0) {
                document.getElementById("consignee_num" + consignee).outerHTML = "";
                consignee--;
            } else
                alert("One consignee is Required");
            if (consignee < 1)
                document.getElementById("remove_consignee").style.display = "none";
            calculate_distance();
        });
        // consignee end
        var interval123 = setInterval(function () {
            if (shipper_sample_loaded && !shipper_sample) {
                shipper_sample = document.getElementById("shipper_num_key_var").outerHTML;
                document.getElementById("shipper_num_key_var").outerHTML = "";
            }
            if (consignee_sample_loaded && !consignee_sample) {
                consignee_sample = document.getElementById("consignee_num_key_var").outerHTML;
                document.getElementById("consignee_num_key_var").outerHTML = "";
            }
            if (consignee_sample_loaded && shipper_sample_loaded) {
                clearInterval(interval123);
                set_select2();
            }
        }, 1000);

        document.getElementById("miles").addEventListener("change", function (e) {
            set_amounts("miles");
        });

        var num = 0;

        function set_amounts(by) {
            if (driver_rate == 99999 && by == "miles")
                set_amounts("driver");

            // if (document.getElementById("drivers").value && document.getElementById("miles").value > 0) {
            if (by == "driver") {
                if (document.getElementById("drivers").value) {
                    $.ajax({
                        url: '{{route("app_url")}}' + '/drivers-rate/' + document.getElementById("drivers").value,
                        method: 'get',
                        // driver_rate: driver_rate,
                        accessorial_expense: accessorial_expense,
                        success: function (result) {
                            result = JSON.parse(result);
                            if (result.status == "success") {
                                driver_rate = result.rate;
                                update_summary();
                            } else
                                alert("Try Again! Unable to get required data.");
                        },
                        error: function (xhr, status, error) {
                            //function to run if the request fails
                            alert("Try Again! Unable to get required data.");
                        }
                    });
                } else {
                    driver_rate = 0;
                }
            } else if (by == "miles")
                if (document.getElementById("drivers").value && document.getElementById("miles").value) {
                    console.log("by =", by, "driver_rate", driver_rate, "miles value=", document.getElementById("miles").value, "accessorial_expense", accessorial_expense);
                    document.getElementById("cost").value = Math.round((((driver_rate * document.getElementById("miles").value) + Number.EPSILON) + accessorial_expense) * 100) / 100;
                    // document.getElementById("cost").value = Math.round((num + Number.EPSILON) * 100) / 100;
                    update_summary();
                } else {

                    console.log("else by miles", document.getElementById("drivers").value && document.getElementById("miles").value);
                }
            // console.log(driver_rate,document.getElementById("miles").value,driver_rate * document.getElementById("miles").value)
            // }
        }

        function set_contact_hash_address(field_id, shipper_consignee, again = false) {
            var customer_id, contact, address;
            if (shipper_consignee == "consignee") {
                customer_id = document.getElementById("consignees" + field_id).value;
                contact = document.getElementById("consignee_number" + field_id);
                address = document.getElementById("consignee_address" + field_id);
            } else {
                customer_id = document.getElementById("shippers" + field_id).value;
                contact = document.getElementById("shipper_number" + field_id);
                address = document.getElementById("shipper_address" + field_id);
            }


            $.ajax({
                url: '{{route("app_url")}}' + '/customers-set-contact-address-ajax/' + customer_id,
                method: 'get',
                success: function (result) {
                    result = JSON.parse(result);
                    if (result.status == "success") {
                        address.value = result.data.address;
                        contact.value = result.data.contact;
                        console.log(result);
                        //set a change replace change map function.//MB_TOP
                        getRoadData();
                    } else {
                        if (again)
                            alert("Try again! Unable to load page properly.");
                        else
                            setTimeout(function () {
                                set_contact_hash_address(field_id, shipper_consignee, true);
                            }, 1000);
                    }

                },
                error: function (xhr, status, error) {
                    //function to run if the request fails
                    if (again)
                        alert("Try again! Unable to load page properly.");
                    else
                        setTimeout(function () {
                            set_contact_hash_address(field_id, shipper_consignee, true);
                        }, 1000);
                }
            });
        }

      

        function set_profit() {
            if (parseFloat(document.getElementById("value").value) && document.getElementById("cost").value) {
                num = parseFloat(document.getElementById("value").value) - parseFloat(document.getElementById("cost").value);
                console.log(parseFloat(document.getElementById("value").value), parseFloat(document.getElementById("cost").value));
                document.getElementById("profit").value = Math.round((num + Number.EPSILON) * 100) / 100;
            } else {
                console.log("set profilt else");
                console.log(parseFloat(document.getElementById("value").value), parseFloat(document.getElementById("cost").value));
            }

        }

        //accesorials
        function accessory_change(id, type) {
            // console.log("value_a" + type + "ccesorial" + id);
            var accesorials = document.getElementById("a" + type + "ccesorial" + id);
            var accessory_value = document.getElementById("value_a" + type + "ccesorial" + id);
            var accessory_div = document.getElementById("a" + type + "ccessory_div" + id);
            if (accesorials.value > 1) {
                // accessory_value.value = "";
                accessory_value.disabled = false;
                accessory_value.setAttribute("type", "text");
                accessory_div.classList.remove("hidden");
            } else {
                accessory_value.value = "";
                accessory_value.disabled = true;
                accessory_value.setAttribute("type", "hidden");
                accessory_div.classList.add("hidden");
            }
            calculate_accessorial_expense();
        }

        function add_accesrial(type) {
            var accesorial_count = 0;

            var accesorial_sample = document.getElementById("div" + type + "_num_accesorials0").outerHTML;
            eval("accesorial_count" + type + "++;");
            eval("accesorial_count=accesorial_count" + type);
            var new_accesorial = accesorial_sample.replaceAll("a" + type + "ccesorial0", "a" + type + "ccesorial" + accesorial_count);
            new_accesorial = new_accesorial.replaceAll("div" + type + "_num_accesorials0", "div" + type + "_num_accesorials" + accesorial_count);
            new_accesorial = new_accesorial.replaceAll("value_a" + type + "ccesorial0", "value_a" + type + "ccesorial" + accesorial_count);
            new_accesorial = new_accesorial.replaceAll("accessory_change('0','" + type + "')", "accessory_change('" + accesorial_count + "','" + type + "')");
            new_accesorial = new_accesorial.replaceAll("a" + type + "ccessory_div0", "a" + type + "ccessory_div" + accesorial_count);
            new_accesorial = new_accesorial.replaceAll("payable0", "payable" + accesorial_count);
            document.getElementById("main_accesorials_div" + type).insertAdjacentHTML('beforeend', new_accesorial);
            // document.getElementById("consignee_num" + consignee).style.display = "block";
            if (accesorial_count > 0)
                document.getElementById("remove_accesrial" + type).classList.remove("hidden");
            calculate_accessorial_expense();
        }

        function calculate_accessorial_expense() {
            accessorial_expense = 0;
            Array.from(document.getElementsByClassName("accessory_value_expense")).forEach(function (currentValue, index, arr) {
                if (parseFloat(currentValue.value)) {
                    if (!document.getElementsByClassName("payable_to_driver")[index].checked) {
                        accessorial_expense = accessorial_expense + parseFloat(currentValue.value);
                    }
                }

            });
            set_revenue();
            update_summary();
            return accessorial_expense;
        }

        function calculate_accessorail_income() {
            accessorial_income = 0;
           
            Array.from(document.getElementsByClassName("accessory_value_income")).forEach(function (currentValue, index, arr) {
                if (parseFloat(currentValue.value)) {
                    accessorial_income = accessorial_income + parseFloat(currentValue.value);
                }
            });
            console.log(accessorial_income+"accessorial_income")
            set_revenue();
            update_summary();
            return accessorial_income;
        }

        function remove_accesrial(type) {
            var accesorial_count;

            eval("accesorial_count=accesorial_count" + type);
            if (accesorial_count > 0) {
                document.getElementById("div" + type + "_num_accesorials" + accesorial_count).outerHTML = "";
                eval("accesorial_count" + type + "--;");
                eval("accesorial_count=accesorial_count" + type);
                if (!accesorial_count > 0) {
                    document.getElementById("remove_accesrial" + type).classList.add("hidden");
                }
            }
            calculate_accessorial_expense();
        }

        // Customer Create
        document.getElementById("customer_form").addEventListener("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: '{{route("customers-store")}}',
                data: $('#customer_form').serialize(),
                success: function (result) {
                    console.log(result);
                    if (result.status == "success") {
                        Array.from(document.getElementsByClassName("consignees_select2")).forEach(function (currentValue, index, arr) {
                            console.log(index,currentValue);
                            console.log($($('.consignees_select2')[index]).attr('id'), $('.consignees_select2')[0].value);
                            if (currentValue.value == "") {
                                // console.log("updated");
                                options_get("Customer", $($('.consignees_select2')[index]).attr('id'));

                                setTimeout(function () {
                                    // document.getElementById($($('.consignees_select2')[index]).attr('id')).value = result.id;
                                    $('#'+$($('.consignees_select2')[index]).attr('id')).val(result.id).trigger('change')
                                }, 2000);

                            }
                        });

                        Array.from(document.getElementsByClassName("shippers_select2")).forEach(function (currentValue, index, arr) {
                            // console.log($($('.shippers_select2')[index]).attr('id'), $('.shippers_select2')[0].value);
                            if (currentValue.value == "") {
                                options_get("Customer", $($('.shippers_select2')[index]).attr('id'));
                                setTimeout(function () {
                                    $('#'+$($('.shippers_select2')[index]).attr('id')).val(result.id).trigger('change');
                                }, 2000);
                            }
                        });

                        $('#customer_model').modal('toggle');
                    } else {
                        alert("Data is not stored.Try again!");
                    }

                }
            });
        });
        // Broker Create
        document.getElementById("broker_form").addEventListener("submit", function (e) {
            e.preventDefault();
            console.log("submitted broker");

            $.ajax({
                type: 'post',
                url: '{{route("brokers-store")}}',
                data: $('#broker_form').serialize(),
                success: function (result) {
                    console.log(result);
                    if (result.status == "success") {
                        options_get("Broker", "broker", result.id);
                        $('#broker_model').modal('toggle');
                    } else {
                        alert("Data is not stored.Try again!");
                    }

                }
            });
        });
        function set_revenue() {
            var income = 0;
            var expense = 0;
            var cost = 0;
            Array.from(document.getElementsByClassName("accessory_value_income")).forEach(function (currentValue, index, arr) {
                if (parseFloat(currentValue.value)) {
                    income = income + parseFloat(currentValue.value);
                }
            });

            Array.from(document.getElementsByClassName("accessory_value_expense")).forEach(function (currentValue, index, arr) {
                if (parseFloat(currentValue.value)) {
                   
                    expense = expense + parseFloat(currentValue.value);
                }

            });
            console.log("=============set revneue====================")
            console.log(document.getElementById("value").value)
            cost = Math.round((((driver_rate * total_miles) + Number.EPSILON) + accessorial_expense) * 100) / 100;
            total_revenue = Math.round(((parseFloat(document.getElementById("value").value) +income-cost) + Number.EPSILON) * 100) / 100;
            document.getElementById("revenue").value = total_revenue;
            update_summary();
        }
        function update_summary() {
            
            total_cost = Math.round((((driver_rate * total_miles) + Number.EPSILON) + accessorial_expense) * 100) / 100;
            console.log(
                total_miles, "total_miles",
                total_cost, "total_cost",
                total_profit, "total_profit",
                total_revenue, "total_revenue",
                driver_rate, "driver_rate"
            );
            // set cost value
            document.getElementById("cost").value = total_cost;
            // set revenue
            document.getElementById("revenue").value = total_revenue;
            // set miles
            document.getElementById("miles").value = total_miles;
            // set profit
            let total_profit_M =0;
            var accessory_total = 0;
            
            Array.from(document.getElementsByClassName("accessory_value")).forEach(function (currentValue, index, arr) {
                if (parseFloat(currentValue.value)) {
                  
                    accessory_total = accessory_total + parseFloat(currentValue.value);
                   
                }
            });
            accessory_total = accessorial_income + accessorial_expense
            total_profit_M = Math.round(((parseFloat(document.getElementById("value").value) + accessory_total) + Number.EPSILON) * 100) / 100;
            document.getElementById("profit").value = Math.round(((total_profit_M - accessorial_expense - (driver_rate * total_miles)) + Number.EPSILON) * 100) / 100;
        }


        function submitSendSmsButton()
        {
            $('#send_sms').val(true);
            $('#submit_btn').trigger('click');
        }
        
        $(document).on('change', '#drivers', function(){
            if($(this).val() == "")
            {
                $('#submit_sms_btn').prop('disabled', true)
                $('#submit_sms_btn').addClass('btn-muted')
                $('#submit_sms_btn').removeClass('btn-success')
            }else
            {
                $('#submit_sms_btn').prop('disabled', false)
                $('#submit_sms_btn').removeClass('btn-muted')
                $('#submit_sms_btn').addClass('btn-success')
            }
        })
        /**
         * Get Road Data
         * 
        */
        function getRoadData(){
            calculate_distance()
            var consignee_locations = [];
            var shipper_locations = [];
          
            Array.from(document.getElementsByClassName("shippers_select2")).forEach(function (currentValue, index, arr) {
                new google.maps.places.Autocomplete(currentValue);
                if (currentValue.value == "")
                    return;
                    shipper_locations[index] = currentValue.value;
               
            });

            Array.from(document.getElementsByClassName("consignees_select2")).forEach(function (currentValue, index, arr) {
                new google.maps.places.Autocomplete(currentValue);
                if (currentValue.value == "")
                    return;
                    consignee_locations[index] = currentValue.value;
                   
            });

            var current_location = document.getElementById('current_address').value;
            if (current_location == "")
                return;

            var current_location_lat = document.getElementById('current_address_lat').value;
            var current_location_lng = document.getElementById('current_address_lng').value;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                data: {
                    shipper_locations: shipper_locations,
                    consignee_locations: consignee_locations,
                    current_location: current_location,
                    current_location_lng:current_location_lng,
                    current_location_lat:current_location_lat,
                },
                url: '{{route("road_data")}}',
                dataType: "Json",
                success: function (result) {
                    console.log(result.success);
                    if (result.success) {
                        updateMap(result.data)
                    }
                }, error: function (result) {
                }
            });
        }

        /**
         * update the map
         * @ roadData array[].
         * 
         * */ 
        function updateMap(roadData)
        {
            var current_location_lat = document.getElementById('current_address_lat').value;
            var current_location_lng = document.getElementById('current_address_lng').value;
           
            var waypts = [];
            var dd = [];
            map.setCenter({
                lat: parseFloat(current_location_lat),
                lng:parseFloat(current_location_lng)
            })
            let markers = [];
            
           
            ds = new google.maps.DirectionsService;
            markers.push([roadData[0][0], roadData[0][1]]);
            for (var j = 1; j < roadData.length - 1; j++) {
                waypts.push({
                location: roadData[j][0] + ',' + roadData[j][1],
                stopover: true
                });
                //var myLatlng = new google.maps.LatLng(roadData[j][0],roadData[j][1]);
                markers.push([roadData[j][0], roadData[j][1]]);
    
            }
            markers.push([roadData[roadData.length - 1][0], roadData[roadData.length - 1][1]]);
           
           try{
                ds.route({
                    'origin': roadData[0][0] + ',' + roadData[0][1],
                    'destination': roadData[roadData.length - 1][0] + ',' + roadData[roadData.length - 1][1],
                    'waypoints': waypts,
                    'travelMode': 'DRIVING'
                },
                function(directions, status) {

                    console.log(directions, status)
                    dd.push(new google.maps.DirectionsRenderer({
                    suppressInfoWindows: true,
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: "#82B6CB"
                    },
                    map: map
                    }));

                    if (status == google.maps.DirectionsStatus.OK) {
                    dd[dd.length - 1].setDirections(directions);
                    }
                }
                );
           }catch(error)
           {
               console.log(error.message)
           }
            
            
            for (var h = 0; h < markers.length; h++) {
                if(roadData.length ==1)
                {
                    createMapMarker(map, new google.maps.LatLng(parseFloat(markers[h][0]), parseFloat(markers[h][1])), String(1), "", "");
                }else
                {
                    createMapMarker(map, new google.maps.LatLng(parseFloat(markers[h][0]), parseFloat(markers[h][1])), String(h+1), "", "");
                }
                
            }
        }

        /**
         * Show the map places. 
         *
         */
        function createMapMarker(map, latlng, label, html, sign) {
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                icon:getMarkerIcon(label),
                title:  label
            });

            marker.myname = label;


            return marker;
        }

        /**/ 
        function getMarkerIcon(number) {
            // inline your SVG image with number variable
            var svg ='<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="40" >'+ 

                        '<circle id="path-1" cx="20" cy="20" r="15" stroke="black" stroke-width="1" fill="#F6F6F6" />'+ 
                        '<text id="1" fill="#20539F" font-size="14" font-weight="600" letter-spacing=".104" text-anchor="middle" x="50%" y="25">'+number+'</text>'+
                        '</svg>'
                                                            // use SVG without base64 see: https://css-tricks.com/probably-dont-base64-svg/
            return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
        }

        /**
         * 
         */
        function autoSaveFunction()
        {
            var url = "{{ route('loads-store') }}"

            var id = $('#id').val();
            if(id !="")
            {
                url  = "/loads-update/"+id;
            }
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                data:  $('#form1').serialize(),
                url: url,
                dataType: "Json",
                success: function (result) {
                    console.log(result.success);
                    if (result.success) {
                        $('#id').val(result.id)
                        console.log('success', result)
                    }
                }, error: function (result) {
                }
            });
         
        }


        document.getElementById("form1").addEventListener("submit", function (e) {
            e.preventDefault();
            var storeUrl = "{{ route('loads-store') }}"
            var updateUrl = "/loads-update/";
            var id = $('#id').val();

            if(id != "")
            {
                $('#form1').attr('action', updateUrl+id).submit();
            }else
            {
                $('#form1').attr('action', storeUrl).submit();
            }
            

        })


        $(document).ready(function(){
           
            setInterval(() => autoSaveFunction(), 4000);
            @if(isset($data)&&!empty($data->driver))
                $('#submit_sms_btn').prop('disabled', false)
                $('#submit_sms_btn').removeClass('btn-muted')
                $('#submit_sms_btn').addClass('btn-success')
            @endif
            
        });
    </script>

@endpush