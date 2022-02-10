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

        #range_type
        {
            padding: 3px;
        }
    </style>
@endpush

@section("title") {{__("tran.".$title_tag)}} @endsection

@section('content')
    @if($title_tag == "Drivers Pay Management")    
    <ul class="nav nav-tabs pay-navs">
        <li class="active"><a href="#"  category="1"><i><b>Paid</b></i></a></li>
        <li><a href="#" category="2"><i><b>Pending</b></i></a></li>
    </ul>
    @endif
    <div class="box">
        <div class="box-header">
                @if($show_add_button)
                    <a href="{{$add_button_link}}" class="mr-2 loading-btn"><i class="fa fa-fw fa-plus"></i>{{ isset($add_btn_txt)?$add_btn_txt: "Build a new Load"}} </a>
                @endif
                
                @if($title_tag == "Drivers Pay Management")
                <a class="float-right mr-2 unselect-all-btn"><i class="fa fa-circle-o"></i> {{__('tran.Unselect All')}}</a>
                <a class="float-right mr-2 select-all-btn"><i class="fa fa-check-circle-o"></i> {{__('tran.Select All')}}</a>
               
                <a href="/payment-status/"  class="float-right mr-2 pay-status-button driver-button hidden"><i class="fa fa-check"></i> <span id="pay-status-text"> {{__("tran.Paid")}}</span></a>
                <a href="{{ route('loads-deduction')}}" class="float-right  mr-2 add-deduction-button driver-button  hidden"><i class="fa fa-plus"></i > {{__("tran.Add Deduction")}}</a>
                <a href="{{ route('multi_pdf_show')}}" class="float-right  mr-2 show-pdf-button driver-button hidden"><i class="fa fa-eye"></i> {{__("tran.Show PDF")}} </a>
                <a href="{{ route('driver-send-invoice-multiple')}}" class='send-invoice-button float-right  mr-2 driver-button  hidden'  > <i class="fa fa-send"></i> {{__("tran.Send Settlement")}}</a>
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
                        @if(isset($min_max_filter["range_type"])&& $min_max_filter["range_type"])
                        <select id="range_type" name="range_type">
                            <option value="FPD">{{ __('tran.Search by: Pickup Date')}}</option>
                            <option value="LDD">{{ __('tran.Search by: Delivery Date')}}</option>
                            
                        </select>
                        @endif
                        <input type="button" id="apply" value="apply" class="btn btn-xs btn-success">
                    </div>
                </div>
            @endif
            <div class="table-responsive">
                <table id="data_table_custom" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                    <tr>
                        @foreach($table_headers as $table_header)
                            <th>{{$table_header}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        @foreach($table_headers as $table_header)
                            <th>{{$table_header}}</th>
                        @endforeach
                    </tr>
                    </tfoot>
                </table>
                @if(isset($multi_select))
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
                    <script>
                        var selectStyle = "multi";
                        // var selectStyle="single";
                    </script>
                    {{--multi select--}}
                    <div class="button_inline">
                        @foreach($multiselect_forms as $key=>$form)
                            <form id="form{{$key}}" action="{{$form->route}}" method="{{$form->method}}" @if(isset($form->btn_attr))target="{{$form->btn_attr}}"@endif>
                                @csrf
                                <input type="submit" class="{{$form->btn_class}}" value="{{$form->button_txt}}" >
                            </form>
                        @endforeach
                    </div>
                    {{--multi select end--}}
               
                @endif

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
        @if($title_tag == "Drivers Pay Management")
        
        function setSelectedValue(selectObj, valueToSet) {
            for (var i = 0; i < selectObj.options.length; i++) {
                if (selectObj.options[i].value== valueToSet) {
                    selectObj.options[i].selected = true;
                    return;
                }
            }
        }
        const min = localStorage.getItem('min');
        const max = localStorage.getItem('max');
        const range_type = localStorage.getItem('range_type');
        document.getElementById('min').value = min;
        document.getElementById('max').value = max;
        document.getElementById('range_type').value;
        
        
        var objSelect = document.getElementById("range_type");

        //Set selected
        setSelectedValue(objSelect, range_type);
        var url = "{{ route('drivers_paymanagement_date_range_ajax') }}";
        getData(url, "{{route("app_url")}}", "data_table_custom", true, "{{$title_tag}}", min, max, 1, range_type);
        @else
        $(function () {
            getData("{{$ajax_data_getting_url}}", "{{route("app_url")}}", "data_table_custom", true, "{{$title_tag}}");
        });
        @endif
        @if(isset($multiselect_forms) && $multi_select)
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
        
       
        @if($title_tag == "Loads")
            $(document).on('click', 'tbody tr', function() {
                // if ($(this).hasClass('selected'))
                // {
                    $('.edit-load-button').removeClass('hidden');
                        $('.edit-sms-button').removeClass('hidden');
                        $('.remove-button').removeClass('hidden');
                        $('.sms-button').removeClass('hidden');
                    if (!table.rows({selected: true}).count()) {
                        $('.edit-load-button').addClass('hidden');
                        $('.edit-sms-button').addClass('hidden');
                        $('.remove-button').addClass('hidden');
                        $('.sms-button').addClass('hidden');
                    }
                    console.log(table.rows({selected: true}).data().toArray())
                
                // }
                
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
                    location.href= url+id;
                }
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
            })
            $(document).on('click', '.remove-button', function(e){
                e.preventDefault();
                if(table.rows({selected: true}).data().toArray().length >1)
                {
                    alert('Please Select 1 row.')
                }else
                {
                    if(confirm("Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?"))
                    {
                        var url  = $(this).attr('href')
                        var id = table.rows({selected: true}).data().toArray()[0].id

                        location.href= url+id;
                    }else
                    {

                    }
                    
                }
            })
            $(document).on('click', '.sms-button', function(e){
                e.preventDefault();
                if(table.rows({selected: true}).data().toArray().length >1)
                {
                    alert('Please Select 1 row.')
                }else
                {
                    var driverId = table.rows({selected: true}).data().toArray()[0].driver_id
                    if(driverId !=null &&driverId !="")
                    {   

                        var id = table.rows({selected: true}).data().toArray()[0].id
                            table .rows( '.selected' ).nodes()
                            .to$() 
                            .removeClass( 'selected' );
                            // show_message("action_success")
                            $.ajax({
                                type: 'get',
                                url: '{{route("send-sms")}}',
                                data:{loadId:id},
                                dataType: "Json",
                                success: function (result) {
                                    
                                    if (result.status == "succeess") {
                                        
                                        show_message("action_success")
                                    } else {
                                        alert(result.message)
                                    }

                                }
                            });
                    }else
                    {
                        alert('Driver does no selected')
                    }
                
                }
            })
        @endif

        
        @if($title_tag == "Drivers Pay Management")

            $(".content .nav a").on("click", function(){
                $(".nav").find(".active").removeClass("active");
                $(this).parent().addClass("active");
                var category = "/"+$(this).attr('category');
                table.destroy();
                var min = document.getElementById('min').value;
                var max = document.getElementById('max').value;
                var range_type = document.getElementById('range_type').value;
                var category = $('.pay-navs .active a').attr('category');
                $('#data_table_custom tbody').empty();
                $('#data_table_custom thead tr:eq(1)').remove();
                var url = "{{ route('drivers_paymanagement_date_range_ajax') }}";
                getData(url, "{{route("app_url")}}", "data_table_custom", true, "{{$title_tag}}", min, max, category, range_type);
                // document.getElementById("min").value = ""
                // document.getElementById("max").value = ""
            });

            $(document).on('click','#apply', function(){
                var category = $('.pay-navs .active a').attr('category');
                var min = document.getElementById('min').value;
                var max = document.getElementById('max').value;
                var range_type = document.getElementById('range_type').value;
                localStorage.setItem('min', min);
                localStorage.setItem('max', max);
                localStorage.setItem('range_type', range_type);
                table.destroy();
                $('#data_table_custom tbody').empty();
                $('#data_table_custom thead tr:eq(1)').remove();
                var url = "{{ route('drivers_paymanagement_date_range_ajax') }}";

                getData(url, "{{route("app_url")}}", "data_table_custom", true, "{{$title_tag}}", min, max, category, range_type);

            });

            $(document).on('click', 'tbody tr', function() {
                // if ($(this).hasClass('selected'))
                // {
                    $('.pay-status-button').removeClass('hidden');
                    $('.driver-button').removeClass('hidden');
                
                    if (!table.rows({selected: true}).count()) {
                        $('.pay-status-button').addClass('hidden');
                        $('.driver-button').addClass('hidden');
                    
                    }else
                    {
                        var row = table.rows({selected: true}).data().toArray()[0];
                    
                        $('#pay-status-text').text(row.driver_payment=='Pending'?" Paid":" Pending")
                    }
                
                console.log(table.rows({selected: true}).data().toArray()[0])
                // }
                
            });
            
            $(document).on('click', '.driver-button', function(e){
                e.preventDefault();
                
                if(table.rows({selected: true}).data().toArray().length == 0)
                {
                    alert('Please Select 1 row.')
                }else
                {
                    var url  = $(this).attr('href')
                    var selectedRows = table.rows({selected: true}).data().toArray()
                    var urlParameters = ""
                    for (var i=0;i< selectedRows.length;i++)
                    {
                        urlParameters+="&id[]="+selectedRows[i].id;
                    }
                    // location.href= url+"?"+urlParameters;
                    window.open(url+"?"+urlParameters, '_blank');
                }
            }) 

            $(document).on( 'click', '.unselect-all-btn', function(){
                console.log("select btn clicked")
                table.rows().deselect();
               
            })
            $(document).on('click', '.select-all-btn', function(){
                table.rows({search: 'applied'}).select();
            })
            // $(document).on('click', '.pay-status-button', function(e){
            //     e.preventDefault();
            //     if(table.rows({selected: true}).data().toArray().length >1)
            //     {
            //         alert('Please Select 1 row.')
            //     }else
            //     {
            //         if(confirm("Alert!Are you sure to change status?"))
            //         {
            //             var url  = $(this).attr('href')
            //             var id = table.rows({selected: true}).data().toArray()[0].id

            //             location.href= url+id;
            //         }else
            //         {

            //         }
                    
            //     }
            // })
        
        @endif
    </script>
@endpush