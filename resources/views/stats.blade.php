@extends('layouts.main')

@push("header_top"){{--less priority css--}}
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">
@endpush

@push("header")
<style>
#range_type {
    padding: 3px;
}    
</style>
@endpush

@section("title") {{__("tran.".$title_tag)}} @endsection

@section('content')

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{__("tran.".$title_on_page)}}</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <div class="row">
                <form action="{{$form_url}}" method="post">
                    @csrf
                    <div class="col-md-12 margin-bottom">
                        <input required type="datetime-local" name="min" id="min" title="Minimum Value" value="{{isset($min) ? (\Carbon\Carbon::create($min)->format("Y-m-d")."T".\Carbon\Carbon::create($min)->format("H:i")):""}}">
                        <input required type="datetime-local" name="max" id="max" title="Maximum Value" value="{{isset($max) ? (\Carbon\Carbon::create($max)->format("Y-m-d")."T".\Carbon\Carbon::create($max)->format("H:i")):""}}">
                        <select id="range_type" name="range_type">
                            <option value="FPD" @if(isset($range_type)&&$range_type=="FPD") selected @endif>{{ __('tran.Search by: Pickup Date')}}</option>
                            <option value="LDD" @if(isset($range_type)&&$range_type=="LDD") selected @endif>{{ __('tran.Search by: Delivery Date')}}</option>
                        </select>
                        <select id="broker" name="broker_id" class="col-md-3">
                            <option value="">{{__("tran.Please Select")}} {{__("tran.Broker")}}</option>
                            @foreach($brokers as $broker)
                                <option value="{{$broker->id}}" {{(!isset($broker_selected)) ? "":(($broker_selected==$broker->id)?"selected":"")}}>{{$broker->company}}</option>
                            @endforeach
                        </select>
                        <div class="form-group col-md-6">
                            {{--                            <label for="broker">{{__("tran.Select") ." ".__("tran.Broker")}}</label>--}}

                        </div>
                        <input type="submit" id="apply" value="apply" class="btn btn-xs btn-success">
                    </div>
                </form>
            </div>

            @if(isset($loads))
                <div class="row">

                    <div class="col-md-12">
                        <p class="">{{__("tran.Stats of")}} {{$loads}} {{__("tran.Records")}}</p>
                    </div>


                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua">
{{--                                <i class="ion ion-ios-gear-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Total Revenue")}}</span>
                                <span class="info-box-number">${{ number_format($total_revenue, 2)}}{{--<small>%</small>--}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red">
{{--                                <i class="fa fa-google-plus"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Average Revenue")}}</span>
                                <span class="info-box-number">${{ number_format($avg_revenue, 2)}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <!-- fix for small devices only -->
                    <div class="clearfix visible-sm-block"></div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green">
{{--                                <i class="ion ion-ios-cart-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Avg. Rev. Per Mile")}}</span>
                                <span class="info-box-number">${{ number_format($avg_revenue_per_mile, 2)}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow">
{{--                                <i class="ion ion-ios-people-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Total Miles")}}</span>
                                <span class="info-box-number">{{ number_format($miles,2)}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue">
{{--                                <i class="ion ion-ios-people-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Avg. Miles")}}</span>
                                <span class="info-box-number">{{ number_format($avg_miles,2)}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue">
{{--                                <i class="ion ion-ios-people-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Deadhead Miles")}}</span>
                                <span class="info-box-number">{{ number_format($dead_head_miles, 2) }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-cyan">
{{--                                <i class="ion ion-ios-people-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Total Cost")}}</span>
                                <span class="info-box-number">${{ number_format($total_cost,2)}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-maroon">
{{--                                <i class="ion ion-ios-people-outline"></i>--}}
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">{{__("tran.Avg. Cost")}}</span>
                                <span class="info-box-number">${{ number_format($avg_cost,2)}}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>
            @endif


        </div>
    </div>
@endsection

@push("footer")
    <!-- Select2 -->
    <script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        window.addEventListener('load', function () {
            $('#broker').select2();
        });

    </script>

@endpush