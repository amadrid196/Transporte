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
                @if(isset($id) && !is_array($id))
                    <input type="hidden" name="id[]" class="form-control" id="id" value="{{$id}}">
                @endif

                @if(isset($id) && is_array($id))
                    @foreach($id as $load_id)
                    <input type="hidden" name="id[]" class="form-control" id="id" value="{{$load_id}}">
                    @endforeach
                @endif

                <div class="box-body">

                    <div class="form-group col-md-12">
                        <label for="message">{{__("tran.Additional Message")}}</label>
                        <textarea name="message" class="form-control" id="message" placeholder="{{__("tran.Additional Message")}}"></textarea>
                    </div>

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