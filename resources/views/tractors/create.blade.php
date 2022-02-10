@extends('layouts.main')

@push("header_top"){{--less priority css--}}
<!-- Select2 -->
{{--<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">--}}
@endpush

@push("header")

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
                @if(isset($data))
                    <input type="hidden" name="id" class="form-control" id="id" value="{{$data->id}}">
                @endif
                <div class="box-body">


                    <div class="form-group col-md-6">
                        <label for="model">{{__("tran.Make/Model")}}</label>
                        <input type="text" name="model" class="form-control" id="model" placeholder="{{__("tran.Make/Model")}}" value="{{isset($data) ? $data->model:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="pun">{{__("tran.Power Unit No.")}}</label>
                        <input type="text" name="pun" class="form-control" id="pun" placeholder="{{__("tran.Power Unit No.")}}" value="{{isset($data) ? $data->pun:""}}">
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="model_yr">{{__("tran.Model Year")}}</label>
                        <input type="text" name="model_yr" class="form-control" id="model_yr" placeholder="{{__("tran.Model Year")}}" value="{{isset($data) ? $data->model_yr:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="vehicle_id">{{__("tran.Vehicle ID Number")}}</label>
                        <input type="text" name="vehicle_id" class="form-control" id="vehicle_id" placeholder="{{__("tran.Vehicle ID Number")}}" value="{{isset($data) ? $data->vehicle_id:""}}">
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="license_plate">{{__("tran.License Plate")}}</label>
                        <input type="text" name="license_plate" class="form-control" id="license_plate" placeholder="{{__("tran.License Plate")}}" value="{{isset($data) ? $data->license_plate:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="reg_exp">{{__("tran.Registration Expiration")}}</label>
                        <input type="date" class="form-control" name="reg_exp" id="reg_exp" placeholder="{{__("tran.Registration Expiration")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->reg_exp)->format("Y-m-d"):""}}">
                    </div>

                    <div class="form-group col-md-12">
                        <label for="notes">{{__("tran.Notes")}}</label>
                        <textarea name="notes" class="form-control" id="notes" placeholder="{{__("tran.Notes")}}">{{isset($data) ? $data->notes:""}}</textarea>
                    </div>

{{--status--}}
                    <div class="form-group col-md-6">
                        <label>{{__("tran.Status")}}</label>

                        <div class="custom-control custom-radio inline">
                            <input class="custom-control-input" type="radio" id="customRadio1" name="status" value="active" {{isset($data) ? (($data->status=="active") ?"checked":""):"checked"}}>
                            <label for="customRadio1" class="custom-control-label">{{__("tran.Active")}}</label>
                        </div>
                        <div class="custom-control custom-radio inline">
                            <input class="custom-control-input" type="radio" id="customRadio2" name="status" value="inactive" {{isset($data) ? (($data->status=="inactive") ?"checked":""):""}}>
                            <label for="customRadio2" class="custom-control-label">{{__("tran.Inactive")}}</label>
                        </div>
                    </div>


                    {{--                    <div class="form-group">--}}
                    {{--                        <label for="group_ids">{{__("tran.Groups")}}</label>--}}
                    {{--                        <select name="group_ids[]" id="group_ids" class="form-control select2" multiple="multiple" data-placeholder="{{__("tran.Select")}} {{__("tran.Groups")}}" style="width: 100%;">--}}
                    {{--                            <option value="">{{__("tran.Select")}} {{__("tran.Groups")}}</option>--}}
                    {{--                            @foreach($groups as $key=>$group)--}}
                    {{--                                <option value="{{$key}}" {{isset($data) ? (in_array($group, $data->groups->pluck("title")->toArray()) ? "selected":""):""}}>{{$group}}</option>--}}
                    {{--                            @endforeach--}}
                    {{--                        </select>--}}
                    {{--                    </div>--}}
                    {{--                    @if(auth()->user()->role=="admin")--}}
                    {{--                        <div class="form-group">--}}
                    {{--                            <label for="status">{{__("tran.Status")}}</label>--}}
                    {{--                            <select name="status" required id="status" class="form-control select2" data-placeholder="{{__("tran.Select")}} {{__("tran.Status")}}" style="width: 100%;">--}}
                    {{--                                <option value="">{{__("tran.Select")}} {{__("tran.Status")}}</option>--}}
                    {{--                                @foreach($statuses as $key=>$status)--}}
                    {{--                                    <option value="{{$status}}" {{(isset($data)) ? (($data->status==$status) ? "selected":""):""}}>{{$status}}</option>--}}
                    {{--                                @endforeach--}}
                    {{--                            </select>--}}
                    {{--                        </div>--}}
                    {{--                    @endif--}}
                    {{--                    <div class="form-group">--}}
                    {{--                        <label for="address">{{__("tran.Address")}}</label>--}}
                    {{--                        <textarea name="info1" class="form-control" id="address" placeholder="{{__("tran.Address")}}...">{{isset($data) ? $data->address:""}}</textarea>--}}
                    {{--                    </div>--}}

                </div>

                <div class="box-footer text-center">
                    <button type="submit" class="btn btn-primary">{{__("tran.Submit")}}</button>
                </div>
            </form>
        </div>

        {{--        @if(Request::route()->getName() == "customer_create" && auth()->user()->role=="admin")--}}
        {{--            <div class="box box-primary">--}}
        {{--                <div class="box-header with-border">--}}
        {{--                    <h3 class="box-title">{{__("tran.Upload")}} {{__("tran.Excel File")}}</h3>--}}
        {{--                </div>--}}
        {{--                <form role="form" method="post" action="{{route("import_customer")}}" enctype="multipart/form-data">--}}
        {{--                    @csrf--}}
        {{--                    <div class="box-body">--}}
        {{--                        <div class="form-group">--}}
        {{--                            <label for="file">{{__("tran.File")}}</label>--}}
        {{--                            <input type="file" name="file" id="file" required>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="box-footer">--}}
        {{--                        <button type="submit" class="btn btn-primary">{{__("tran.Upload")}}</button>--}}
        {{--                    </div>--}}
        {{--                </form>--}}
        {{--            </div>--}}
        {{--        @endif--}}

    </div>
@endsection

@push("footer")

    <!-- Select2 -->
    {{--    <script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>--}}
    {{--    <script>--}}
    {{--        $('.select2').select2();--}}
    {{--    </script>--}}

@endpush