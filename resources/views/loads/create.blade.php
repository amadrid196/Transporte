@extends('layouts.main')

@push("header_top"){{--less priority css--}}
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/jquery.toast.css')}}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css')}}">
<style>
    .box-title2 {
        background: #a5c2eb;
        border-radius: 5px;
    }
    .remve_document{
        cursor: pointer;
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


    .form-control[readonly] {
        background-color:#fff !important;
    }
    .link-button {
        cursor: pointer;
        font-size:12px;
        font-weight:bold;
    }

    .float-right {
            float: right;
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
        var dead_head_miles = 0;
        var map="";
        var origin_lat = 0;
        var origin_lng =  0;
        var selected_accessorial_type = 9999;
        var selected_accessorial_index = 9999;
        var stop_type = 0;
        var selected_stop_type = 1;
        var selected_stop_index = 0; 
        @if(isset($data))
        origin_lat = {{$lat}}
        origin_lng = {{$long}}
        @endif
        @if(isset($data)&&!empty($data->driver))
                @if($data->driver && $data->driver->rate)
            driver_rate = {{$data->driver->rate}};
        @endif
        @if($data->miles)
        total_miles = {{ $data->miles}};
        dead_head_miles ={{$data->dead_head_miles}} ;
       
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
        options_get("Accessorial", "accessorial_category",null, false, 0);
        function options_get(model, select_id, selected = null, again = false, accessorial_type = 0) {

            $.ajax({
                url: '{{route("app_url")}}' + '/options/' + model + '/' + selected,
                method: 'get',
                data:{
                    accessorial_type: accessorial_type
                },
                success: function (result) {
                    document.getElementById(select_id).innerHTML = result;
                    
                    if(select_id=="broker")
                        brokerChange()
                    if(select_id=="drivers")
                        driverChange()
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
                <button type="button"  class="btn btn-info float-right invoice-btn" disabled onclick="generateInvoice()">{{__("tran.Generate Invoice")}}</button>
                <button type="button" class="btn btn-success float-right edit-sms" onclick="editSmspage()" disabled>{{__("tran.Preview and edit SMS message")}}</button>
                <button type="button"  onclick="submitSendSmsButton()" class="btn btn-muted float-right submit_sms_btn" disabled>{{__("tran.submit_sms")}}</button>
                <button type="submit"  class="btn btn-primary float-right submit_btn">{{__("tran.Submit")}}</button>
                
                
            </div>

            <form id="form1"  method="post" action="{{$url}}" enctype="multipart/form-data">
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
                            <option value="Billed" {{isset($data) ? ($data->status=="Billed" ? "selected":""):""}}>{{__("tran.Billed")}}</option>
                            <option value="Paid by Customer" {{isset($data) ? ($data->status=="Paid by Customer" ? "selected":""):""}}>{{__("tran.Paid by Customer")}}</option>
                            <option value="Cancelled" {{isset($data) ? ($data->status=="Cancelled" ? "selected":""):""}}>{{__("tran.Cancelled")}}</option>
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
                            <select id="broker" name="broker_id" class="form-control" onchange="brokerChange()">
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
                        <select id="drivers" name="driver_id" class="form-control" onChange="driverChange()">
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
                                <div id="shipper_spec_time_key_var">
                                    <label for="pickup_date_key_var">{{__("tran.Pickup Date")}}</label>
                                    <input type="datetime-local" name="pickup_date[]" class="form-control" id="pickup_date_key_var" placeholder="{{__("tran.Pickup Date")}}" value="" >
                                    <a class="link-button" onclick="setWindowTime(1,'_key_var')">{{__("Set a date/time window")}}</a>
                               </div>
                                <div class="hide" id="shipper_window_time_key_var">
                                    <label for="">{{__("tran.Start Time")}}</label>
                                
                                    <input type="datetime-local" name="shipper_start_periode[]"  class="form-control datetime" id="shipper_periode_start_key_var">
                                    <label for="">{{__("tran.End Time")}}</label>
                                    <input type="datetime-local" name="shipper_end_periode[]"  class="form-control datetime"  id="shipper_periode_end_key_var">
                                    <a class="link-button" onclick="setSpecificTime(1,'_key_var')">{{__("Set a specific date/time")}}</a>
                                </div>
                                <div class="shipper_assign_key_var">
                                <label>
                                <input type="checkbox" id="shipper_assign_checkbox_key_var" name="fake[]"  onchange="shipperAssign('_key_var')" >
                                To be assigned
                                <input type="hidden" id="shipper_assign_key_var" name="shipper_assign[]" value="false" >
                                </label>
                                </div>
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
                            <button type="button" class="btn btn-info col-md-3 col-md-offset-1" onclick="showCustomerModal(1)">{{__("tran.Add")}} {{__("tran.Customer")}}</button>
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
                                <div id="consignee_spec_time_key_var">
                                <label for="dropoff_date_key_var">{{__("tran.Dropoff Date")}}</label>
                                <input type="datetime-local" name="dropoff_date[]" class="form-control" id="dropoff_date_key_var" placeholder="{{__("tran.Dropoff Date")}}" value="">
                                <a class="link-button" onclick="setWindowTime(2,'_key_var')">{{__("Set a date/time window")}}</a>
                                </div>
                                <div class="hide" id="consignee_window_time_key_var">
                                <label for="">{{__("tran.Start Time")}}</label>
                                <input type="datetime-local" name="consignee_start_periode[]" class="form-control datetime" id="consignee_periode_start_key_var">
                                <label for="">{{__("tran.End Time")}}</label>
                                <input type="datetime-local" name="consignee_end_periode[]" class="form-control datetime" id="consignee_periode_end_key_var">
                                <a class="link-button" onclick="setSpecificTime(2,'_key_var')">{{__("Set a specific date/time")}}</a>
                                </div>
                                <div class="consignee_assign_key_var">
                                <label>
                                <input type="checkbox" id="consignee_assign_checkbox_key_var" name="fake[]"  onchange="consigneeAssign('_key_var')" >
                                To be assigned
                                <input type="hidden" id="consignee_assign_key_var" name="consignee_assign[]" value="false" >
                                </label>
                                </div>
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

                            <button type="button" class="btn btn-info col-md-3 col-md-offset-1"  onclick="showCustomerModal(2)">{{__("tran.Add")}} {{__("tran.Customer")}}</button>
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
                            <div class="col-md-6 col-sm-6 col-xs-6"><b><span class="total-miles">0</span> <i>miles</i></b> </div>
                        </div>

                        <div class="row margin-bottom">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                            <b>{{__('tran.Customer Mileage')}}</b><br>
                            <small><i>{{__('tran.Using calculated carrier mileage')}}</i></small>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6"><b><span class="total-miles">0</span> <i>miles</i></b></div>
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
                  
                  
                    <input type="hidden" name="value" class="form-control" id="value" value="{{isset($data) ? $data->value:""}}" onchange="set_revenue()">
                    <div class="col-md-12 box-title2 margin-bottom">
                        <h4>{{__("tran.Accessorial")}}</h4>
                    </div>

                    @include('loads.accessorial_partial')
                    <input type="hidden" name="miles"  id="miles"  value="{{isset($data) ? $data->miles:""}}">
                    <input type="hidden"  id="dead_head_miles" name="dead_head_miles"  value="{{isset($data) ? $data->dead_head_miles:0}}" >
                   
                    <div class="col-md-6" id="loading_summary" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="background: rgba(255, 255, 255, 0); display: block;" width="50px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                            <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#3c8dbc" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                            </circle>
                        </svg>
                    </div>
                </div>

                <div class="box-footer text-center">
                    <button type="button" class="btn btn-success float-right edit-sms loading-btn"  onclick="editSmspage()" disabled>{{__("tran.Preview and edit SMS message")}}</button>
                    <button type="button"  onclick="submitSendSmsButton()" class="btn btn-muted float-right submit_sms_btn loading-btn" disabled>{{__("tran.submit_sms")}}</button>
                    <button type="submit"  class="btn btn-primary float-right submit_btn loading-btn">{{__("tran.Submit")}}</button>
                
                    <!-- <button type="button" class="btn btn-success" onclick="update_summary()">{{__("tran.Update")}}</button> -->
                </div>
            </form>
            <form id="send_email_form" action="{{route('send_document_email')}}" method="post" enctype="multipart/form-data">
            @csrf
        
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
                                <input required  type="text" name="address" class="form-control" id="id3" placeholder="Enter address" 
                                autocomplete="off"
                               data-language="fr"
                                >
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
                                <input type="text" name="fname" class="form-control" id="fname" placeholder="Enter {{__("tran.Full Name")}}" value="">
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
                                <input type="text" name="address" class="form-control" id="id4" placeholder="Enter address">
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

    <div id="accessorail_model" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title category-title">{{__("tran.Income/budget")}}</h4>
                </div>

                    <div class="modal-body">
                      
                        <div class="box-body">
                           
                        <form>
                            <div class="form-group row ">
                                <div class="col-md-4">
                                <label for="accessorial_category">{{__("tran.Select a Category")}}</label>
                                </div>

                                <div class="col-md-8">
                                <select required id="accessorial_category" class="form-control text-capitalize accessorial-category">
                                    <option>Loading...</option>
                                </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="company_name">{{__("tran.Rate")}}</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        <input required  type="text" class="form-control" id="accessorial_rate" placeholder="" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="company_name">{{__("tran.Quantity")}}</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                                        <input required type="text" class="form-control" id="accessorial_quantity" onchange="calculatorTotal()" placeholder="" value="">
                                    </div>
                                </div>
                            </div>
                           
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="company_name">{{__("tran.Total")}}</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        <input required readonly type="text" class="form-control" id="accessorial_total" placeholder="" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="id3">{{__("tran.Note/Description")}}</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea required name="fake[]" class="form-control" id="accessorial_description" placeholder="" autocomplete="off" data-language="fr"></textarea>
                                </div>
                            </div>
                        </form>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                        <button type="button" class="btn btn-primary" onClick="AddAccessorailItem()"><i class="fa fa-file"></i> {{__("tran.Save Pay Item")}}</button>
                       
                    </div>
                
            </div>

        </div>
    </div>
        <!-- end accessorail model -->

    <div id="windowmodal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title category-title">{{__("tran.Date/time Window")}}</h4>
                </div>

                    <div class="modal-body">
                      
                        <div class="box-body">
                           
                        
                           
                            <div class="form-group row">
                                <label for="periode_start">{{__("tran.Start Time")}}</label>
                                <input  type="text" class="form-control datetime" id="periode_start" placeholder="{{__("tran.Start Time")}}" value="">
                            </div>
                            
                            <div class="form-group row">
                                <label for="periode_end">{{__("tran.End Time")}}</label>
                                <input  type="text" class="form-control datetime" id="periode_end" placeholder="{{__("tran.End Time")}}" value="">
                            </div>

                            
                        
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                        <button type="button" class="btn btn-primary" onClick="addPeriodetime()"><i class="fa fa-file"></i> {{__("tran.Set a date/time window")}}</button>
                       
                    </div>
                
            </div>

        </div>
    </div>
    {{--    broker create end--}}
@endsection

@push("footer")
    <!-- Select2 -->
    <script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{ asset('js/jquery.toast.js')}}"></script>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.2.6/jquery.inputmask.bundle.min.js"></script><script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API')}}&callback=initMap"></script> -->
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
        

        $(document).on('change', '#current_address', function(){
            var value = $(this).val();
            if(value == "")
            {
                $('#current_address_lat').val("") 
                $('#current_address_lng').val("")                  
                getRoadData()
            }
        })
        try {
            google.maps.event.addDomListener(window, 'load', function () {
                var current_address = new google.maps.places.Autocomplete(document.getElementById('current_address'));

                var places2 = new google.maps.places.Autocomplete(document.getElementById('shipper_address0'));
                var places3 = new google.maps.places.Autocomplete(document.getElementById('consignee_address0'));
                var id3 = new google.maps.places.Autocomplete((document.getElementById('id3')));
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
                     debugger;
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
                //debugger;
                Array.from(document.getElementsByClassName("consignee_locations")).forEach(function (currentValue, index, arr) {
                    let consignee = new google.maps.places.Autocomplete(currentValue);
                    // console.log("*** Consignee ***")
                    // console.log(consignee.getPlace())
                    if (currentValue.value == "")
                        return;
                    consignee_locations[index] = currentValue.value;
                });

                // set shippers
                Array.from(document.getElementsByClassName("shipper_locations")).forEach(function (currentValue, index, arr) {
                    let shipper = new google.maps.places.Autocomplete(currentValue);
                    // console.log("*** Shipper ***")
                    // debugger
                    // console.log(shipper.getPlace())
                    if (currentValue.value == "")
                        return;
                    shipper_locations[index] = currentValue.value;
                });

                var current_location = document.getElementById('current_address').value;
                // if (current_location == "")
                //     return;
                // console.log("shipper_locations\n", shipper_locations, "consignee_locations\n", consignee_locations);

                document.getElementById("loading_summary").style.display = "block";
                $('.submit_btn').prop('disabled', true)
               
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
                            total_miles = result.miles+result.dead_head_miles;
                            $('.expense_quantiy').get(0).value = total_miles
                            if(driver_rate == 99999)
                            {
                                $('#rate_a2ccesorial0').val("$"+0)
                            }else
                            {
                                $('#rate_a2ccesorial0').val(numberToString(driver_rate))
                            }
                            
                            rateChange(0,2)
                            update_summary();
                        }
                        document.getElementById("loading_summary").style.display = "none";
                        $('.submit_btn').prop('disabled', false)
                    }, error: function (result) {
                        document.getElementById("loading_summary").style.display = "none";
                        $('.submit_btn').prop('disabled', false)
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
            load_brokers();
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
                    @if(empty($accessory->accessorial_id))
                    options_get("Accessorial", "a1ccesorial{{$temp}}", 0, false, 0);
                    @else
                    options_get("Accessorial", "a1ccesorial{{$temp}}",{{$accessory->accessorial_id}}, false, 0);
                    @endif
                    @php $temp++; @endphp
                @endforeach

            @else
            options_get("Accessorial", "a1ccesorial0", 0,  false, 0);
            @endif

            @if(isset($data)&&!empty($data->accessories))
            // expense accessories
            
            
            @php $temp=1; @endphp

                @foreach($data->accessories->where("type","expense") as $key=>$accessory)
                    @if(empty($accessory->accessorial_id))
                    options_get("Accessorial", "a2ccesorial{{$temp}}", 0, false, 1);
                    @else
                    options_get("Accessorial", "a2ccesorial{{$temp}}",{{$accessory->accessorial_id}}, false, 1);
                    @endif
                @php $temp++; @endphp
            @endforeach
            @else
           
           
          
          
            // options_get("Accessorial", "a2ccesorial0", 0,  false, 1);
            @endif


            @if(isset($data)&&count($data->deductions)!=0)
            // deduction accessories
            @php $temp=0; @endphp
            @foreach($data->deductions as $key=>$deduction)
                options_get("Accessorial", "a3ccesorial{{$temp}}",{{$deduction->accessorial_id}}, false,2);
            @php $temp++; @endphp
            @endforeach

            @else
            options_get("Accessorial", "a3ccesorial0", 0, false,2);
            @endif
           
            @if(!isset($data))
                $('#company_a2ccesorial0').val();
                $('.expense_quantiy').get(0).value = total_miles
                if(driver_rate == 99999)
                {
                    $('#rate_a2ccesorial0').val("$"+0)
                }else
                {
                    $('#rate_a2ccesorial0').val(numberToString(driver_rate))
                }
               

                rateChange(0,2)
            @endif

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
            calculate_accessorail_deductions();
            update_summary();
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
                let shipper = new google.maps.places.Autocomplete(currentValue);
                // TODO Milan shipper
                shipper.addListener('place_changed', function(){
                    console.log("** shipper  Place Changed **")
                    console.log(shipper.getPlace())
                });
            });
        });

        document.getElementById("remove_shipper").addEventListener("click", function (e) {
            if (shipper > 0) {
                document.getElementById("shipper_num" + shipper).outerHTML = "";
                getRoadData();
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
                // TODO Milan consignee
                let consignee = new google.maps.places.Autocomplete(currentValue);
                consignee.addListener('place_changed', function(){
                    
                    console.log("** Consignee Place Changed **")
                    
                    console.log(consignee.getPlace())
                });
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
                        //accessorial_expense: accessorial_expense,
                        success: function (result) {
                            result = JSON.parse(result);
                            if (result.status == "success") {
                                driver_rate = result.rate;
                                if(driver_rate == 99999)
                                {
                                    $('#rate_a2ccesorial0').val(0)
                                }else
                                {
                                    $('#rate_a2ccesorial0').val(driver_rate)
                                }
                                rateChange(0, 2)
                                //update_summary();
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
                    //console.log("by =", by, "driver_rate", driver_rate, "miles value=", document.getElementById("miles").value, "accessorial_expense", accessorial_expense);
                    document.getElementById("cost").value = numberToString(Math.round((((driver_rate * document.getElementById("miles").value) + Number.EPSILON) + accessorial_expense) * 100) / 100);
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
                        //console.log(result);
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
                //console.log(parseFloat(document.getElementById("value").value), parseFloat(document.getElementById("cost").value));
                document.getElementById("profit").value = numberToString(Math.round((num + Number.EPSILON) * 100) / 100);
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
            // if (accesorials.value > 1) {
            //     //accessory_value.value = "";
            //     accessory_value.disabled = false;
            //     accessory_value.setAttribute("type", "text");
            //     accessory_div.classList.remove("hidden");
            // } else {
            //     accessory_value.value = "";
            //     accessory_value.disabled = true;
            //     accessory_value.setAttribute("type", "hidden");
            //     accessory_div.classList.add("hidden");
            // }
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
                
                var value = localStringToNumber(currentValue.value)
                if (parseFloat(value)) {
                    // if (!document.getElementsByClassName("payable_to_driver")[index].checked) {
                        accessorial_expense = accessorial_expense + parseFloat(value);
                    // }
                }
                $('.total-expense').val(numberToString(accessorial_expense));
            });
            //set_revenue();
            //update_summary();
            return accessorial_expense;
        }

        function calculate_accessorail_income() {
            accessorial_income = 0;
            Array.from(document.getElementsByClassName("accessory_value_income")).forEach(function (currentValue, index, arr) {
                //console.log("========rty=======")
                var value = localStringToNumber(currentValue.value)
                if (parseFloat(value)) {
                    accessorial_income = accessorial_income + parseFloat(value);
                }
            });

            $('#amount_income').val(numberToString(accessorial_income));
            $('.total-income').val(numberToString(accessorial_income));
            //console.log(accessorial_income+"accessorial_income")
            set_revenue();
            update_summary();
            return accessorial_income;
        }

        function calculate_accessorail_deductions() {
            let accessorial_deductions = 0;
          
            Array.from(document.getElementsByClassName("accessory_value_deduction")).forEach(function (currentValue, index, arr) {
                var value = localStringToNumber(currentValue.value)
                if (parseFloat(value)) {
                    accessorial_deductions = accessorial_deductions + parseFloat(value);
                }
            });

            $('.total-deductions').val(numberToString(accessorial_deductions));
           
            update_summary();
            return accessorial_deductions;
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
                   // console.log(result);
                  
                    if (result.status == "success") {
                        if(stop_type == 2)
                        {
                            var LastIndex =document.getElementsByClassName("consignees_select2").length-1 
                            Array.from(document.getElementsByClassName("consignees_select2")).forEach(function (currentValue, index, arr) {
                                //console.log(index,currentValue);
                                //console.log($($('.consignees_select2')[index]).attr('id'), $('.consignees_select2')[0].value);
                                if (index == LastIndex) {
                                    // console.log("updated");
                                    options_get("Customer", $($('.consignees_select2')[index]).attr('id'));

                                    setTimeout(function () {
                                        // document.getElementById($($('.consignees_select2')[index]).attr('id')).value = result.id;
                                        $('#'+$($('.consignees_select2')[index]).attr('id')).val(result.id).trigger('change')
                                    }, 2000);

                                }
                            });
                        }else
                        {
                            var LastIndex =document.getElementsByClassName("shippers_select2").length-1 
                            Array.from(document.getElementsByClassName("shippers_select2")).forEach(function (currentValue, index, arr) {
                                // console.log($($('.shippers_select2')[index]).attr('id'), $('.shippers_select2')[0].value);
                                if (index == LastIndex) {
                                    options_get("Customer", $($('.shippers_select2')[index]).attr('id'));
                                    setTimeout(function () {
                                        $('#'+$($('.shippers_select2')[index]).attr('id')).val(result.id).trigger('change');
                                    }, 2000);
                                }
                            });
                        }
                        

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
            //console.log("submitted broker");

            $.ajax({
                type: 'post',
                url: '{{route("brokers-store")}}',
                data: $('#broker_form').serialize(),
                success: function (result) {
                    //console.log(result);
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
                if (parseFloat(currentValue.value.replace("$", ""))) {
                    income = income + parseFloat(currentValue.value);
                }
            });

            Array.from(document.getElementsByClassName("accessory_value_expense")).forEach(function (currentValue, index, arr) {
                if (parseFloat(currentValue.value.replace("$", ""))) {
                   
                    expense = expense + parseFloat(currentValue.value);
                }

            });
            //console.log("=============set revneue====================")
            //console.log(document.getElementById("value").value)
            cost = Math.round((((driver_rate * total_miles) + Number.EPSILON) + accessorial_expense) * 100) / 100;
            total_revenue = Math.round(((parseFloat(document.getElementById("value").value.replace("$", "")) +income-cost) + Number.EPSILON) * 100) / 100;
            // document.getElementById("revenue").value = total_revenue;
            update_summary();
        }
        function update_summary() {
            
            total_cost =  accessorial_expense;
            // console.log(
            //     total_miles, "total_miles",
            //     total_cost, "total_cost",
            //     total_profit, "total_profit",
            //     total_revenue, "total_revenue",
            //     driver_rate, "driver_rate"
            // );
            // set cost value
            document.getElementById("cost").value = numberToString(total_cost);
            // set revenue
           // document.getElementById("revenue").value = total_revenue;
            // set miles
            document.getElementById("miles").value = total_miles;
            // set profit
            let total_profit_M =0;
            var accessory_total = 0;
            
            
            accessory_total = accessorial_income + accessorial_expense
            
            total_profit_M = Math.round(((parseFloat(localStringToNumber(document.getElementById("amount_income").value)) - accessorial_expense) + Number.EPSILON) * 100) / 100;
            //console.log(accessory_total, total_profit_M)
            document.getElementById("profit").value = numberToString(total_profit_M)
            document.getElementById("profit_rate").value = Math.round(total_profit_M/accessorial_income*100)+"%"
        }


        function submitSendSmsButton()
        {
            $('#send_sms').val(true);
            var storeUrl = "{{ route('loads-store') }}"
           
            $('#form1').attr('action', storeUrl).submit();
            $('.submit_btn').trigger('click');
        }
        
        $(document).on('change', '#drivers', function(){
            if($(this).val() == "")
            {
                $('.submit_sms_btn').prop('disabled', true)
                $('.submit_sms_btn').addClass('btn-muted')
                $('.submit_sms_btn').removeClass('btn-success')

                $('.edit-sms').prop('disabled', true)
                $('.edit-sms').addClass('btn-muted')
                $('.edit-sms').removeClass('btn-success')
            }else
            {
                $('.submit_sms_btn').prop('disabled', false)
                $('.submit_sms_btn').removeClass('btn-muted')
                $('.submit_sms_btn').addClass('btn-success')
                
                setTimeout(() => {
                    $('.edit-sms').prop('disabled', false)
                    $('.invoice-btn').prop('disabled', false)
                    $('.edit-sms').removeClass('btn-muted')
                    $('.edit-sms').addClass('btn-success')
                }, 3000);
                
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
            //if (current_location == "")
              //  return;

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
                    //console.log(result.success);
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
            map = new google.maps.Map(document.getElementById('map'), {
                  //  zoom:14,
                    center:{
                        lat: parseFloat(current_location_lat),
                        lng:parseFloat(current_location_lng)
                    },
                    
                    // disableDefaultUI: true,
                })
            var waypts = [];
            var dd = [];
            // map.setCenter({
            
            // })
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

                   // console.log(directions, status)
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
               //console.log(error.message)
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
            $('#send_sms').val(false);
            var id = $('#id').val();
            if(id !="")
            {
                url  =  '{{route("app_url")}}'+"/loads-update/"+id;
            }
            var buttonDisableStatus = $('.submit_btn').prop('disabled');
            
            if(!buttonDisableStatus)
            {
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
                        //console.log(result.success);
                        if (result.success) {
                            $('#id').val(result.id)
                            //console.log('success', result)
                        }
                    }, error: function (result) {
                    }
                });
            }
            
         
        }

        $(document).on('click','.submit_btn', function(e){
            e.preventDefault();
            
            var storeUrl = "{{ route('loads-store') }}"
            var updateUrl = '{{route("app_url")}}'+"/loads-update/";
            var id = $('#id').val();
            //console.log(id);
            if(id != "")
            {
                $('#form1').attr('action', updateUrl+id).submit();
            }else
            {
                $('#form1').attr('action', storeUrl).submit();
            }
        })
        

        $(document).ready(function(){
            $('.datetime').datetimepicker();
             setInterval(() => autoSaveFunction(), 3000);
           
            @if(isset($data)&&!empty($data->driver))
                $('.submit_sms_btn').prop('disabled', false)
                $('.submit_sms_btn').removeClass('btn-muted')
                $('.submit_sms_btn').addClass('btn-success')

                $('.invoice-btn').prop('disabled', false)
                $('.edit-sms').prop('disabled', false)
                $('.edit-sms').removeClass('btn-muted')
                $('.edit-sms').addClass('btn-success')
              //  $("#expense0-description").val({{$data->driver->ownership}})
            @endif
            @if(isset($data))

                $('.total-miles').text(total_miles-dead_head_miles);
                $('#deadhead-miles').text(dead_head_miles);
                document.getElementById('current_address_lat').value = origin_lat;
                document.getElementById('current_address_lng').value = origin_lng;
               setTimeout(() => getRoadData(), 2000); 
            @endif

            $('#send_document_email_btn').on('click',(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                if(id =="")
                {
                    alert('Please try again.')
                }else
                {
                    var form = $('#send_email_form')[0];
                    var loadReference = $('#reference').val();
                    var brokerId = $('#broker').val();
                    var totalIncome = accessorial_income;
                    var formData = new FormData(form);
                    formData.append('loadReference',loadReference);
                    formData.append('brokerId', brokerId);
                    formData.append('totalIncome', totalIncome);
                    formData.append('load_id', id);
                    $('#send_document_email_btn').button('loading')
                    $.ajax({
                        type:'POST',
                        url:$('#send_email_form').attr('action'),
                        data:formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success:function(data){
                        if(data.status == "success")
                        {
                          
                            $.toast({
                                heading: 'Success',
                                text: 'Email Send Succsfully',
                                showHideTransition: 'fade',
                                icon: 'success',
                                showHideTransition: 'slide',
                                position : 'top-right'  
                            })
                        }else
                        {
                            $.toast({
                                heading: 'Warning',
                                text: data.message,
                                showHideTransition: 'fade',
                                icon: 'warning',
                                position : 'top-right'  
                            })
                        }
                        $('#send_document_email_btn').button('reset')
                        },
                        error: function(data){
                            console.log("error");
                            console.log(data);
                            $('#send_document_email_btn').button('reset')
                        }
                    });
                }
               
            }));

            $('#save_document_btn').on('click',(function(e) {
                e.preventDefault();
                var id = $('#id').val();
                if(id =="")
                {
                    alert('Please try again.')
                }else
                {
                    var form = $('#send_email_form')[0];
                    var loadReference = $('#reference').val();
                    var brokerId = $('#broker').val();
                    var totalIncome = accessorial_income;
                    var formData = new FormData(form);
                    formData.append('loadReference',loadReference);
                    formData.append('brokerId', brokerId);
                    formData.append('totalIncome', totalIncome);
                    formData.append('load_id', id);
                    formData.append('disable_email',true);
                    $('#save_document_btn').button('loading')
                    $('#send_document_email_btn').button('disabled')
                    $.ajax({
                        type:'POST',
                        url:$('#send_email_form').attr('action'),
                        data:formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success:function(data){
                        if(data.status == "success")
                        {
                          
                            $.toast({
                                heading: 'Success',
                                text: data.message,
                                showHideTransition: 'fade',
                                icon: 'success',
                                showHideTransition: 'slide',
                                position : 'top-right'  
                            })
                        }else
                        {
                            $.toast({
                                heading: 'Warning',
                                text: data.message,
                                showHideTransition: 'fade',
                                icon: 'warning',
                                position : 'top-right'  
                            })
                        }
                        $('#save_document_btn').button('reset')
                        $('#send_document_email_btn').button('reset')
                        },
                        error: function(data){
                            console.log("error");
                            console.log(data);
                            $('#save_document_btn').button('reset')
                            $('#send_document_email_btn').button('reset')
                        }
                    });
                }
               
            }));
        });

        function driverChange()
        {
            let value = $('#drivers option:selected').text();
            $('.company_a2ccesorial').val(value);
            $('.company_a3ccesorial').val(value);
            
          //  driverOwner()
            set_amounts('driver')
            

        }

        function brokerChange()
        {
           
            let value = $('#broker option:selected').text();
            
            $('.company_a1ccesorial').val(value);
          
        }


        function rateChange(index, type)
        {
            initCurrencyMask();
            //console.log("===============")
            //console.log(index, type);
            if(index == 0&&type==2)
            {
                if(driver_rate != 99999)
                var value = numberToString(parseFloat(driver_rate*total_miles))
                else
                var value = "$"+0;
                $('#value_a'+type+'ccesorial'+index).val(value);
                calculate_accessorail_income()
                calculate_accessorial_expense()
                calculate_accessorail_deductions()
                update_summary()
            }else
            {
                var rate = $('#rate_a'+type+'ccesorial'+index).val();
                var quantity = $('#quantity_a'+type+'ccesorial'+index).val();
                
                if(!parseFloat(rate.replace("$", "")))
                    rate = 0
                
                if(!parseFloat(quantity))
                quantity = 0;

               
                var value = parseFloat(quantity)*localStringToNumber(rate);
                //console.log("quantity+++++++++++++---------=========="+value, localStringToNumber(rate), quantity)
                $('#value_a'+type+'ccesorial'+index).val(numberToString(value));
                calculate_accessorail_income()
                calculate_accessorial_expense()
                calculate_accessorail_deductions()
                update_summary()
            }
            
        }
       
        function selectRow(index, type)
        {
            //console.log(index,type)
            if(selected_accessorial_index != 9999)
            document.getElementById('div'+selected_accessorial_type+'_num_accesorials'+selected_accessorial_index).classList.remove("tr-active");
            
            if(index == selected_accessorial_index&&type == selected_accessorial_type)
            {
                selected_accessorial_type = 9999;
               selected_accessorial_index = 9999;
               document.getElementById('remove_accessorial_button').disabled = true;
               document.getElementById('div'+type+'_num_accesorials'+index).classList.remove("active");
            }else
            {
                document.getElementById('remove_accessorial_button').disabled = false;
                document.getElementById('div'+type+'_num_accesorials'+index).classList.add("tr-active");
                selected_accessorial_type = type;
                selected_accessorial_index = index;
            }

            
            // if(index == selected_accessorial_index&&index == selected_accessorial_type)
            // {
            //     selected_accessorial_type = 9999;
            //     selected_accessorial_index = 9999;
            
            //     document.getElementById('div'+type+'_num_accesorials'+index).classList.remove("active");
            // }else
            // {
            //                        selected_accessorial_type = type;
            //         selected_accessorial_index = index;
            //         document.getElementById('div'+type+'_num_accesorials'+index).classList.add("active");
               
               
            // }
        }

        function showCustomerModal(type)
        {
            stop_type = type;
            $('#customer_model').modal('show');
        }

        function initCurrencyMask()
        {
           
         
            // console.log(value);
            // document.getElementsByClassName('currency').value = '$'+value
        }
        
        
        $(document).on('click', '#remove_accessorial_button', function(){
            
            if(selected_accessorial_index == 0)
            alert('You can\'t remove this row.')
            else
            {
                document.getElementById('div'+selected_accessorial_type+'_num_accesorials'+selected_accessorial_index).outerHTML = "";
                calculate_accessorail_income()
                calculate_accessorial_expense()
                calculate_accessorail_deductions()
                update_summary()
                document.getElementById('remove_accessorial_button').disabled = true;
                selected_accessorial_index = 9999
                selected_accessorial_type = 9999
            }
            
        });
        // format inital value
       

        // bind event listeners
      
        
        $(document).on('change','.currency', function(){
            var value= $(this).val();
            var options = {
                maximumFractionDigits : 2,
                currency              : "USD",
                style                 : "currency",
                currencyDisplay       : "symbol"
            }
            value = localStringToNumber(value)
            if(parseFloat(value)){
            var value = value.toLocaleString(undefined, options)
            $(this).val(value);
            }
            else
            $(this).val("$"+0);
        });
        
        function localStringToNumber( s ){
            if(s == "")
            s = 0
            return Number(String(s).replace(/[^0-9.-]+/g,""))
        }

        function numberToString(s)
        {
            var options = {
                maximumFractionDigits : 2,
                currency              : "USD",
                style                 : "currency",
                currencyDisplay       : "symbol"
            }
            return localStringToNumber(s).toLocaleString(undefined, options)
        }

        function driverOwner()
        {
            // let value = $('#drivers').val();
            // let url = '/driver/driverowner/'+value
            // $.ajax({
            //     type: 'GET',
                
            //     url: url,
            //     success: function (result) {
            //         console.log(result.status);
            //         if (result.status == "true") {
            //             $("#expense0-description").val(result.data);
            //         }else
            //         {
            //             $("#expense0-description").val(0);
            //         }
            //     }, error: function (result) {
            //     }
            // });
           
        }
       
        function removeDocument(url, name)
        {
            console.log(url)
            $('#send_document_email_btn').button('loading')
            $.ajax({
                type: 'GET',
                
                url: url,
                success: function (result) {
                    $('.'+name+"_eye").hide();
                    $('.'+name+"_remove").hide();

                    $('#send_document_email_btn').button('reset')
                }, error: function (result) {
                    $('#send_document_email_btn').button('reset')
                }
            });
        }


        function editSmspage()
        {
            var id = $('#id').val();
            console.log(id);

            if(id != "")
            {
              var url ='{{route("app_url")}}'+ '/smstemplate/'+id

              window.location.replace(url);
            }else
            {
                alert('Please try again after couple seconds.')
            }
        }

        function setWindowTime(type, index)
        {
           
            if(type == 1)
            {
               document.getElementById('pickup_date'+index).value=""
               document.getElementById('shipper_window_time'+index).classList.remove('hide');
               document.getElementById('shipper_spec_time'+index).classList.add('hide');

            }
            else
            {

               document.getElementById('dropoff_date'+index).value=""
               document.getElementById('consignee_window_time'+index).classList.remove('hide');
               document.getElementById('consignee_spec_time'+index).classList.add('hide');
            }

        }

        function setSpecificTime(type, index)
        {
            if(type == 1)
            {
                document.getElementById('shipper_periode_start'+index).value = "";
                document.getElementById('shipper_periode_end'+index).value = "";
                document.getElementById('shipper_window_time'+index).classList.add('hide');
                document.getElementById('shipper_spec_time'+index).classList.remove('hide');
            }
            else
            {

               document.getElementById('consignee_periode_start'+index).value = "";
               document.getElementById('consignee_periode_end'+index).value = "";
               document.getElementById('consignee_window_time'+index).classList.add('hide');
               document.getElementById('consignee_spec_time'+index).classList.remove('hide');
            }

        }

       function calculatorTotal()
       {
        let rate = $('#accessorial_rate').val()
        if(!parseFloat(rate))
        {
            rate = 0
        }
        let quantity = $('#accessorial_quantity').val()
        if(!parseFloat(quantity))
        {
            quantity = 0
        }
        let total  = $('#accessorial_total').val(rate*quantity)
       }

       //Genreate Invoice
       function generateInvoice()
       {
           var id = $('#id').val();
           window.open("{{route('app_url')}}/home/load/invoice/"+id, '_blank');
           
       }

       function consigneeAssign(index)
       {
           if(document.getElementById('consignee_assign_checkbox'+index).checked)
           {
                document.getElementById('dropoff_date'+index).setAttribute("readonly", true)
                document.getElementById('consignee_periode_start'+index).setAttribute("readonly", true)
                document.getElementById('consignee_periode_end'+index).setAttribute("readonly", true)
                document.getElementById('consignee_assign'+index).value = "true";
           }else
           {
                document.getElementById('dropoff_date'+index).removeAttribute("readonly");
                document.getElementById('consignee_periode_start'+index).removeAttribute("readonly");
                document.getElementById('consignee_periode_end'+index).removeAttribute("readonly");
                document.getElementById('consignee_assign'+index).value = "false";
           }
           
       }

       function shipperAssign(index)
       {
           if(document.getElementById('shipper_assign_checkbox'+index).checked)
           {
                document.getElementById('pickup_date'+index).setAttribute("readonly", true)
                document.getElementById('shipper_periode_start'+index).setAttribute("readonly", true)
                document.getElementById('shipper_periode_end'+index).setAttribute("readonly", true)
                document.getElementById('shipper_assign'+index).value = "true";
           }else
           {
                document.getElementById('pickup_date'+index).removeAttribute("readonly");
                document.getElementById('shipper_periode_start'+index).removeAttribute("readonly");
                document.getElementById('shipper_periode_end'+index).removeAttribute("readonly");
                document.getElementById('shipper_assign'+index).value = "false";
           }
           
       }

       
    </script>
@endpush