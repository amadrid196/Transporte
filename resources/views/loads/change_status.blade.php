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

            <form id="form1" role="form" method="post" action="{{$url}}">
                @csrf
                @if(isset($id))
                    @foreach($id as $load_id)
                        <input type="hidden" name="id[]" class="form-control" value="{{$load_id}}">
                    @endforeach
                @endif

                <div class="box-body">

                    <div class="form-group col-md-12">
                        <label for="status">{{__("tran.Load") ." ".__("tran.Status")}}</label>
                        <select id="status" name="status" class="form-control text-capitalize" required>
                            <option value="Pending">{{__("tran.Pending")}}</option>
                            <option value="Needs Driver">{{__("tran.Needs Driver")}}</option>
                            <option value="Dispatched">{{__("tran.Dispatched")}}</option>
                            <option value="In Transit">{{__("tran.In Transit")}}</option>
                            <option value="Delivered">{{__("tran.Delivered")}}</option>
                            <option value="Billed">{{__("tran.Billed")}}</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <p>{{__("tran.Changing") . " ". __("tran.Status")." ".__("tran.of")." ".count($id)." ".__("tran.loads")}}.</p>
                    </div>

                    <div class="box-footer text-center">
                        <button type="submit" class="btn btn-primary">{{__("tran.Submit")}}</button>
                    </div>
            </form>
        </div>

    </div>
@endsection

@push("footer")

@endpush