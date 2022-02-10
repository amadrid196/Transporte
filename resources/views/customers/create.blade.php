@extends('layouts.main')

@push("header_top"){{--less priority css--}}
<!-- Select2 -->
{{--<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">--}}
@endpush

@push("header")
    <script src="https://maps.googleapis.com/maps/api/js?key={{env("MAP_API")}}&libraries=places"></script>
@endpush

@section("title") {{__("tran.".$pageTitle)}} @endsection

@section('content')
    <div class="box-body">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{__("tran.".$pageTitle)}}</h3>
            </div>

            <form role="form" method="post" action="{{$url}}">
                @csrf
                @if(isset($data))
                    <input type="hidden" name="id" class="form-control" id="id" value="{{$data->id}}">
                    <input type="hidden" name="lat" id="lat"   value="{{$data->lat}}">
                    <input type="hidden" name="lng" id="lng"   value="{{$data->lng}}">
                @else
                    <input type="hidden" name="lat" id="lat" value="">
                    <input type="hidden" name="lng" id="lng" value="">
                @endif
                
                <div class="box-body">

                    {{--                    <div class="form-group">--}}
                    {{--                        <label for="fname">{{__("tran.First Name")}}</label>--}}
                    {{--                        <input required type="text" name="fname" class="form-control" id="fname" placeholder="Enter {{__("tran.First Name")}}" value="{{isset($data) ? $data->fname:""}}">--}}
                    {{--                    </div>--}}

                    {{--                    <div class="form-group">--}}
                    {{--                        <label for="lname">{{__("tran.Last Name")}}</label>--}}
                    {{--                        <input required type="text" name="lname" class="form-control" id="lname" placeholder="Enter {{__("tran.Last Name")}}" value="{{isset($data) ? $data->lname:""}}">--}}
                    {{--                    </div>--}}

                    <div class="form-group">
                        <label for="number">{{__("tran.Contact Number")}}</label>
                        <input required type="text" name="number" class="form-control" id="number" placeholder="Enter number" value="{{isset($data) ? $data->contact:""}}">
                    </div>

                    {{--                    <div class="form-group">--}}
                    {{--                        <label for="number">{{__("tran.Email")}}</label>--}}
                    {{--                        <input required type="email" name="email" class="form-control" id="email" placeholder="{{__("tran.Email")}}..." value="{{isset($data) ? $data->email:""}}">--}}
                    {{--                    </div>--}}

                    <div class="form-group">
                        <label for="company_name">{{__("tran.Company Name")}}</label>
                        <input required type="text" name="company" class="form-control" id="company_name" placeholder="{{__("tran.Company Name")}}" value="{{isset($data) ? $data->company:""}}">
                    </div>

                    <div class="form-group">
                        <label for="address">{{__("tran.Address")}}</label>
                        <textarea name="address" class="form-control" id="address" placeholder="Enter address" autocomplete="off" data-language="fr">{{isset($data) ? $data->address:""}}</textarea>
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
   
    <script>
        // Autocomplete google maps
        google.maps.event.addDomListener(window, 'load', function () {
            var address = new google.maps.places.Autocomplete(document.getElementById('address'));
            address.addListener('place_changed', function(){
                    var place = address.getPlace();
                    if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                    }

                    console.log(place.geometry.location.lat(), place.geometry.location.lng())
                     $('#lat').val(place.geometry.location.lat()) 
                     $('#lng').val(place.geometry.location.lng())                  

            })
        });
    </script>

@endpush