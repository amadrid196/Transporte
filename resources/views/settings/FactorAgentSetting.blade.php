@extends('layouts.main')

@push("header_top"){{--less priority css--}}

@endpush

@push("header")

@endpush

@section("title") {{__("tran.".$pageTitle)}} @endsection

@section('content')
    <div class="box-body">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{__($pageTitle)}}</h3>
            </div>

            <form role="form" method="post" action="{{$url}}">
                @csrf
                
                <div class="box-body">

                    <div class="form-group">
                        <label for="name">{{__("tran.Email")}}</label>
                        <input type="email" required name="email" class="form-control" value="{{!empty($data) ? $data->email:""}}">
                    </div>

                    <div class="form-group">
                        <label for="email">{{__("tran.POD")}}</label>
                        <input type="email" required name="pod" class="form-control" value="{{!empty($data) ? $data->pod:""}}">
                    </div>

                    <div class="form-group">
                        <label for="password">{{__("tran.CC")}}</label>
                        <input type="email" required name="cc" class="form-control" value="{{!empty($data) ? $data->cc:""}}">
                    </div>
 

                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">{{__("tran.Submit")}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push("footer")

@endpush