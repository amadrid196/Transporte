@extends('layouts.main')

@push("header_top")
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">
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


                    <div class="form-group col-md-12">
                        <label for="title">{{__("tran.Value")}}</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="{{__("tran.Value")}}" value="{{isset($data) ? $data->title:""}}">
                    </div>

                    
                    <div class="form-group col-md-12">
                        <label for="group">{{__("tran.Grouping")}}</label>
                        <select name="group" id="group" class="form-control select2"  data-placeholder="{{__("tran.Select")}} {{__("tran.Group")}}" style="width: 100%;">
                            <option value="">{{__("tran.Select")}} {{__("tran.Grouping")}}</option>
                            @foreach($groups as $key=>$group)
                                <option value="{{$group}}" {{isset($data)&&$data->group == $group? "selected" :"" }}>{{$group}}</option>
                            @endforeach
                        </select>
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

    <!-- Select2 -->
        <script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
        <script>
            $('.select2').select2();
        </script>

@endpush