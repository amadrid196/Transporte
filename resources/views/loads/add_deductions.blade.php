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
            <script>
                var deductions_count = 0;
            </script>
            <form id="form1" role="form" method="post" action="{{$url}}">
                @csrf
                @if(isset($id) && is_array($id))
                    @foreach($id as $load_id)
                        <input type="hidden" name="id[]" class="form-control" id="id" value="{{$load_id}}">
                    @endforeach
                @endif

                <div class="box-body" id="main_form">

                    @if(isset($data) && $data)
                        @foreach($data as $key=>$value)
                        <div id="deduction{{$key}}">
                            <div class="form-group col-md-6">
                                <label for="title{{$key}}">{{__("tran.Title")}}</label>
                                <input type="text" name="title[]" id="title{{$key}}" value="{{$value->title}}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="value{{$key}}">{{__("tran.Value")}}</label>
                                <input type="number" name="value[]" id="value{{$key}}" value="{{$value->value}}" step="0.01" class="form-control">
                            </div>
                        </div>
                            <script>
                                deductions_count = {{$key}};
                            </script>
                        @endforeach
                    @else
                        <div id="deduction0">
                            <div class="form-group col-md-6">
                                <label for="title0">{{__("tran.Title")}}</label>
                                <input type="text" name="title[]" id="title0" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="value0">{{__("tran.Value")}}</label>
                                <input type="number" name="value[]" id="value0" step="0.01" class="form-control">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="box-footer text-center">
                    <div class="col-md-12 margin-bottom">
                        <input type="button" id="add" value="Add" class="btn btn-success col-md-1 col-md-offset-5">
                        <input type="button" id="remove" value="Remove" class="btn btn-danger col-md-1 hidden">
                    </div>
                    <button type="submit" class="btn btn-primary">{{__("tran.Submit")}}</button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push("footer")
    <script>
        var sample = document.getElementById("deduction0").outerHTML;
        // add
        document.getElementById("add").addEventListener("click", function () {
            if (deductions_count == 0)
                document.getElementById("remove").classList.remove("hidden");
            deductions_count++;
            var new_deduction = sample.replace(/deduction0/gi, "deduction" + deductions_count);
            new_deduction = new_deduction.replace(/title0/gi, "title" + deductions_count);
            new_deduction = new_deduction.replace(/value0/gi, "value" + deductions_count);
            document.getElementById("main_form").insertAdjacentHTML('beforeend', new_deduction);
        });
        // remove
        document.getElementById("remove").addEventListener("click", function () {
            if (deductions_count > 0) {
                document.getElementById("deduction" + deductions_count).outerHTML = "";
                deductions_count--;
                if (deductions_count == 0)
                    document.getElementById("remove").classList.add("hidden");
            }
        });
        if (deductions_count>0)
            document.getElementById("remove").classList.remove("hidden");
    </script>
@endpush