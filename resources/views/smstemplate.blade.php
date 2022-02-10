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
         
           <div class="row">
               <div class="col-md-12 mt-10">
                    <form id="form1" role="form" method="post" action="{{$url}}" >
                        @csrf
                        <input type="hidden" name="load_id" id="load_id" value="{{$id}}">
                        
                        <div class="box-body">


                            <div class="form-group col-md-12">
                                <label for="model">{{__("tran.Content")}}</label>
                                <textarea rows="20" id="conent" name="content" class="form-control" placeholder="" value="">{{!empty($data) ? $data->content:$msg}}</textarea>
                            </div>
                           
                        </div>

                        <div class="box-footer text-center">
                        @if(route('loads-create') == url()->previous())
                        
                        <a type="button" href="{{ route('loads-edit', $id) }}"  class="btn btn-danger">{{__("tran.Go to Back")}}</a>
                        @else
                        <a type="button" href="{{ url()->previous() }}"  class="btn btn-danger">{{__("tran.Go to Back")}}</a>
                        @endif    
                        <button type="submit" class="btn btn-primary loading-btn">{{__("tran.Submit")}}</button>
                           
                        </div>
                    </form>         
                </div>
            </div>
        </div>

       


    </div>
@endsection

@push("footer")

    <!-- Select2 -->
    {{--    <script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>--}}
    {{--    <script>--}}
    {{--        $('.select2').select2();--}}
    {{--    </script>--}}

@endpush