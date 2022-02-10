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
                <h3 class="box-title">{{__("tran.".$pageTitle)}}</h3>
            </div>

            <form role="form" method="post" action="{{$url}}">
                @csrf
                @if(isset($data))
                    <input type="hidden" name="id" class="form-control" id="id" value="{{$data->id}}">
                @endif
                <div class="box-body">

                    <div class="form-group">
                        <label for="name">{{__("tran.Name")}}</label>
                        <input type="text" required name="name" class="form-control" id="name" placeholder="Enter {{__("tran.Name")}}" value="{{isset($data) ? $data->name:""}}">
                    </div>

                    <div class="form-group">
                        <label for="email">{{__("tran.Email")}}</label>
                        <input type="email" {{(auth()->user()->role=="admin") ? "required":"disabled"}} name="email" class="form-control" id="email" placeholder="Enter {{__("tran.Email")}}" value="{{isset($data) ? $data->email:""}}">
                    </div>

                    <div class="form-group">
                        <label for="password">{{__("tran.Password")}}</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="********" value="">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{__("tran.Confirm")}} {{__("tran.Password")}}</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="********" value="">
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