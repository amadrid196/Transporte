@extends('layouts.main')

@push("header")
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('data_theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    {{--    select all &none buttons css--}}
    <link rel="stylesheet" href="{{ asset('data_theme/bower_components/datatables.net-bs/css/select.dataTables.min.css') }}">
    {{--for column search--}}
    <link rel="stylesheet" href="{{ asset('data_theme/bower_components/datatables.net-bs/css/fixedHeader.dataTables.min.css') }}">
    <style>
        .button_inline {
            display: inline-flex;
        }

        .float-right {
            float: right;
        }

        .no_anchor_style {
            color: inherit;
            text-decoration: none;
        }

        .size140 {
            font-size: 140%;
        }

        thead input {
            width: 100%;
        }

        .mr-2
        {
            margin-right:10px;
        }

        .color-red {
            color:red;
        }

        .sms-button {
            cursor:pointer;
        }

        .color-green {
            color:#33B63C;
        }
    </style>
@endpush

@section("title") {{__("tran.".$title_tag)}} @endsection

@section('content')
    <ul class="nav nav-tabs">
            <li class="active"><a href="#" category="0"><i><b>Actived Loads</b></i></a></li>

            <!--<li><a href="#" category="1"><i><b>{{ __("tran.Planning Loads") }}</b></i></a></li>-->
            <li><a href="#" category="2"><i><b>{{ __("tran.BILLED") }}</b></i></a></li>
            <li><a href="#" category="3"><i><b>{{ __("tran.All Loads") }}</b></i></a></li>
            <!--<li><a href="#" category="4"><i><b>{{ __("tran.My Loads") }}</b></i></a></li>-->
    </ul>
    <div class="box">

        <div class="box-header">
            <a class="mr-2" onclick="showColumnsModel()"><i class="fa fa-cog "></i></a>
            @if($show_add_button)
                <a href="{{$add_button_link}}" class=" mr-2 loading-btn"><i class="fa fa-fw fa-plus"></i>Build a new Load </a>
            @endif
            @if($title_tag == "Loads")
            <a href="{{url('loads-edit')}}"  class="mr-2 edit-load-button hidden loading-btn"><i class="fa fa-pencil"></i> {{__("tran.Edit Load")}}</a>
            <!-- <a href='/loads-deduction-single/' class="float-right  mr-2">Add Deduction</a> -->
            <a href='/smstemplate/' class="mr-2 edit-sms-button hidden loading-btn"><i class="fa fa-pencil-square-o"></i > Edit SMS</a>
            <a href="/loads-delete" class="mr-2 remove-button hidden" onClick='return confirm(\\Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?\\)'><i class="fa fa-times-circle color-red"></i> {{__("tran.Delete") }} </a>
            <a  class='sms-button mr-2 hidden' ><i class="fa fa-comment"></i> {{__("tran.Send SMS")}}</a>
            <a  class='complete-load-button mr-2 hidden' ><i class="fa fa-check color-green"></i> {{__("tran.Mark Billed")}}</a>
            <a  class='paid-load-button mr-2 hidden' ><i class="fa fa-check color-green"></i> {{__("tran.Mark Paid by Customer")}}</a>
            <a class="float-right mr-2 unselect-all-btn"><i class="fa fa-circle-o"></i> {{__('tran.Unselect All')}}</a>
                <a class="float-right mr-2 select-all-btn"><i class="fa fa-check-circle-o"></i> {{__('tran.Select All')}}</a>
            @endif
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            @if(isset($min_max_filter) && $min_max_filter["value"])
                <div class="row">
                    <div class="col-md-12 margin-bottom">
                        <input type="{{$min_max_filter["type"]}}" id="min" title="Minimum Value">
                        <input type="{{$min_max_filter["type"]}}" id="max" title="Maximum Value">
                        <input hidden type="number" id="min_col" value="{{$min_max_filter["min"]}}">
                        <input hidden type="number" id="max_col" value="{{$min_max_filter["max"]}}">
                        <input type="button" id="apply" value="apply" class="btn btn-xs btn-success">
                    </div>
                </div>
            @endif
            <div class="table-responsive">
                <table id="data_table_custom" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                    <tr>
                        @foreach($table_headers as $table_header)
                            <th>{{$table_header}}<span class="resize-handle"></span></th>
                        @endforeach
                    </tr>

                    </thead>
                    <tbody>
                    </tbody>
                    <!-- <tfoot>
                    <tr>
                        @foreach($table_headers as $table_header)
                            <th>{{$table_header}}</th>
                        @endforeach
                    </tr>
                    </tfoot> -->
                </table>
                @if($multi_select)
                    <script>
                        var selectStyle = "multi";
                        // var selectStyle="single";
                    </script>
                @else
                    <script>
                        var selectStyle = "api";
                    </script>
                @endif
                @if(isset($multiselect_forms) && $multi_select)

                    {{--multi select--}}
                    <div class="button_inline">
                        @foreach($multiselect_forms as $key=>$form)
                            <form id="form{{$key}}" action="{{$form->route}}" method="{{$form->method}}">
                                @csrf
                                <input type="submit" class="{{$form->btn_class}}" value="{{$form->button_txt}}">
                            </form>
                        @endforeach
                    </div>
                    {{--multi select end--}}


                @endif

            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <span id="total_income_wrapper"></span>
                </div>
            </div>
        </div>
    </div>




<div id="columnsmodal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title category-title">{{__("tran.Choose Details")}}</h4>
            </div>

                <div class="modal-body">

                    <div class="box-body">
                        <div class="row">
                            @foreach($table_headers as $key => $table_header)
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label><input type="checkbox" data-id="{{$key}}" class="column_set_item" value="" checked>{{$table_header}}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                    <button type="button" class="btn btn-primary" onClick="setColumns()"><i class="fa fa-cog"></i> {{__("tran.Set Columns")}}</button>

                </div>

        </div>

    </div>
</div>
@endsection

@push("footer")
    <script src="{{ asset('data_theme/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    {{--for column search--}}
    <script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.fixedHeader.min.js')}}"></script>
    {{--select all and select none buttons--}}
    <script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.select.min.js')}}"></script>
    <script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.buttons.min.js')}}"></script>
    {{--select all and select none buttons end--}}


    <script src="{{ asset('data_theme/datatables.js')}}"></script>
    <script>
        var activeCategory = 0;

        $(function () {
            getData("{{$ajax_data_getting_url}}", "{{route("app_url")}}", "data_table_custom", true, "{{$title_tag}}");
        });

        $(".content .nav a").on("click", function(){
            $(".nav").find(".active").removeClass("active");
            $(this).parent().addClass("active");
            activeCategory = $(this).attr('category');
            var category = "/"+$(this).attr('category');
            table.destroy();
            $('#data_table_custom tbody').empty();
            $('#data_table_custom thead tr:eq(1)').remove();
            getData("{{$ajax_data_getting_url}}"+category, "{{route("app_url")}}", "data_table_custom", true, "{{$title_tag}}");
            if(activeCategory != 2)
            {
                if(!$(".paid-load-button").hasClass("hidden"))
                {
                    $('.paid-load-button').addClass('hidden');
                }
            }else
            {
                if(!$(".complete-load-button").hasClass("hidden"))
                {
                    $('.complete-load-button').addClass('hidden');
                }
            }
            $('.edit-load-button').addClass('hidden');
            $('.edit-sms-button').addClass('hidden');
            $('.remove-button').addClass('hidden');
            $('.sms-button').addClass('hidden');

        });

        @if($multi_select&&isset($multiselect_forms))
        @foreach($multiselect_forms as $key=>$form)
        // Handle form submission event
        $('#form{{$key}}').on('submit', function (e) {
            if (!table.rows({selected: true}).count()) {
                alert("Error!\nNothing Selected.");
                e.preventDefault();
                return false;
            }

            $('input[name="id\[\]"]', '#form{{$key}}').remove();
            $.each(table.rows({selected: true}).data().toArray(), function (index, data) {

                // Create a hidden element
                $('#form{{$key}}').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(data.id)
            );
            });
            // Prevent actual form submission
            // e.preventDefault();
            // return false;
        });
        @endforeach
        @endif

        // $(document).on('click', '.sms-button', function(){
        //     var id = $(this).attr('value');
        //     table .rows( '.selected' ).nodes()
        //     .to$()
        //     .removeClass( 'selected' );
        //     // show_message("action_success")
        //     $.ajax({
        //         type: 'get',
        //         url: '{{route("send-sms")}}',
        //         data:{loadId:id},
        //         dataType: "Json",
        //         success: function (result) {

        //             if (result.status == "succeess") {

        //                 show_message("action_success")
        //             } else {
        //                 alert(result.message)
        //             }

        //         }
        //     });
        // });
        @if($title_tag == "Loads")
        $(document).on('click', 'tbody tr', function() {
            checkSelectedStatus()
        });

        $(document).on('click', '.edit-load-button', function(e){
            e.preventDefault();
            if(table.rows({selected: true}).data().toArray().length >1)
            {
                alert('Please Select 1 row.')

            }else
            {
                var url  = $(this).attr('href')
                    var id = table.rows({selected: true}).data().toArray()[0].id
                    location.href= url+"/"+id;
            }
            hideLoading()
        })
        $(document).on('click', '.edit-sms-button', function(e){
            e.preventDefault();
            if(table.rows({selected: true}).data().toArray().length >1)
            {
                alert('Please Select 1 row.')
            }else
            {
                var driverId = table.rows({selected: true}).data().toArray()[0].driver_id
                if(driverId !=null &&driverId !="")
                {
                    var url  = $(this).attr('href')
                    var id = table.rows({selected: true}).data().toArray()[0].id
                    location.href= url+id;
                }else
                {
                    alert('Driver does no selected')
                }

            }
            hideLoading()
        })
        $(document).on('click', '.remove-button', function(e){
            e.preventDefault();
            if(confirm("Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?"))
            {
                var url  = $(this).attr('href')+"?"
                var rows = table.rows({selected: true}).data().toArray()
                for(var i  = 0; i < rows.length; i++)
                {
                    url=  url+"ids[]="+rows[i].id+"&";
                }

                location.href=url
            }else
            {

            }
        })
        $(document).on('click', '.sms-button', function(e){
            e.preventDefault();
                showLoading()
                var rows = table.rows({selected: true}).data().toArray()
                var ids = [];
                for(var i  = 0; i < rows.length; i++)
                {
                    var driverId = rows[i].driver_id
                    if(driverId !=null &&driverId !="")
                    {
                        ids.push(rows[i].id)
                        table.rows( '.selected' ).nodes()
                        .to$()
                        .removeClass( 'selected' );
                            // show_message("action_success")

                    }
                }

                $.ajax({
                    type: 'get',
                    url: '{{route("sendbulksms")}}',
                    data:{loadIds:ids},
                    dataType: "Json",
                    success: function (result) {

                        if (result.status == "succeess") {

                            show_message("action_success")
                        } else {
                            alert(result.message)
                        }
                        hideLoading()
                    },
                    error: function() {
                        hideLoading()
                    }
                });
        })

        $(document).on('click', '.complete-load-button', function(e){
            e.preventDefault();

                var rows = table.rows({selected: true}).data().toArray()
                var ids = [];
                for(var i  = 0; i < rows.length; i++)
                {
                    ids.push(rows[i].id)
                }
                showLoading();
                $.ajax({
                    type: 'get',
                    url: '{{route("change_status_complete")}}',
                    data:{ids:ids},
                    dataType: "Json",
                    success: function (result) {

                        if (result.status == "success") {

                            show_message("action_success")
                            location.reload();
                        } else {
                            alert(result.message)
                        }
                        hideLoading()
                    },
                    error: function()
                    {
                        hideLoading()
                    }
                });
        })

        $(document).on('click', '.paid-load-button', function(e){
            e.preventDefault();

                var rows = table.rows({selected: true}).data().toArray()
                var ids = [];
                for(var i  = 0; i < rows.length; i++)
                { ids.push(rows[i].id)
                }
                showLoading();
                $.ajax({
                    type: 'get',
                    url: '{{route("change_status_paid")}}',
                    data:{ids:ids},
                    dataType: "Json",
                    success: function (result) {

                        if (result.status == "success") {

                            show_message("action_success")
                            location.reload();
                        } else {
                            alert(result.message)
                        }
                        hideLoading()
                    },
                    error: function()
                    {
                        hideLoading()
                    }
                });
        })
        @endif

        function showColumnsModel()
        {
            $('#columnsmodal').modal('show')
        }

        function setColumns()
        {
            $('.column_set_item').each(function(){
                var index = $(this).attr('data-id')

                if($(this).is(":checked")){

                    table.column(index).visible(true);
                }else
                {
                    table.column(index).visible(false);
                }

                $('#columnsmodal').modal('hide')
            })
        }

        function checkSelectedStatus()
        {
            $('.edit-load-button').removeClass('hidden');
            $('.edit-sms-button').removeClass('hidden');
            $('.remove-button').removeClass('hidden');
            $('.sms-button').removeClass('hidden');

            if(activeCategory == 2)
            {
                $('.paid-load-button').removeClass('hidden');
            }else
            {
                $('.complete-load-button').removeClass('hidden');
            }
            if (!table.rows({selected: true}).count()) {
                $('.edit-load-button').addClass('hidden');
                $('.edit-sms-button').addClass('hidden');
                $('.remove-button').addClass('hidden');
                $('.sms-button').addClass('hidden');

                if(activeCategory == 2)
                {
                    if(!$(".complete-load-button").hasClass("hidden"))
                    {
                        $('.complete-load-button').addClass('hidden');
                    }
                    $('.paid-load-button').addClass('hidden');

                }else
                {

                    if(!$(".paid-load-button").hasClass("hidden"))
                    {
                        $('.paid-load-button').addClass('hidden');
                    }
                    $('.complete-load-button').addClass('hidden');
                }
            }
        }
        $(document).on( 'click', '.unselect-all-btn', function(){
            table.rows().deselect();
            checkSelectedStatus()
        })
        $(document).on('click', '.select-all-btn', function(){
            table.rows({search: 'applied'}).select();
            checkSelectedStatus()
        })




    </script>
@endpush
