@extends('layouts.main')

@push("header")
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('data_theme/dataTables.scroller.css') }}">
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
<script src="{{ asset('data_theme/dataTables.scroller.js')}}"></script>

<!-- <script src="{{ asset('data_theme/datatables.js')}}"></script> -->
<script>
    function setSelectedValue(selectObj, valueToSet) {
        for (var i = 0; i < selectObj.options.length; i++) {
            if (selectObj.options[i].value== valueToSet) {
                selectObj.options[i].selected = true;
                return;
            }
        }
    }
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
       
        var url = "{{ route("app_url")}}/testpayment-daterange-ajax/"+category;
        initTable(url, min, max, category, range_type);
       
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
        var url = "{{ route("app_url")}}/testpayment-daterange-ajax/"+category;
        initTable(url, min, max, category, range_type);

    });
    const min = localStorage.getItem('min');
    const max = localStorage.getItem('max');
    const range_type = localStorage.getItem('range_type');
    document.getElementById('min').value = min;
    document.getElementById('max').value = max;
    var objSelect = document.getElementById("range_type");
    
    //Set selected
    setSelectedValue(objSelect, range_type);
    var url = "{{ route("app_url")}}/testpayment-daterange-ajax/1";
    initTable(url, min, max, 1, range_type);
    // new task
    function initTable(url, range_start, range_end, category, range_type){
        var new_row = $("<tr class='search-header'/>");
        $('#data_table_custom thead th').each(function(i) {
        var title = $(this).text();
        var new_th = $('<th style="' + $(this).attr('style') + '" />');
        $(new_th).append('<input type="text" placeholder="' + title + '" data-index="'+i+'"/>');
            $(new_row).append(new_th);
        });
        $('#data_table_custom thead').append(new_row);
   
        table = $('#data_table_custom').DataTable({
            "searchable": true,
            "bDestroy": true,
            "ordering": true,
            "serverSide": true,
            responsive: true,
            processing: true,
            sortable: true,
            scrollY: 550,
            pageLength: 100,
            orderCellsTop: true,
            //paging: false,
            select: {
                style: selectStyle
            },
            // dom: "<'top'f>tiS",
            dom: "<'top'f>rtiS",
            ajax: {
                "url": url,
                "type": "GET",
                "data": {
                    range_start : range_start,
                    range_end : range_end,
                    category :category,
                    range_type : range_type
                }
            },
            scroller: {
                loadingIndicator: true
            },
            columns: [
                {
                    name:'#',
                    data: 'id',
                    title: '#',
                    sortable: true,
                    "render":function(data,type, full){
                        
                        return '<a href="{{ route("app_url")}}/loads-edit/'+full.id+'">'+full.id+'</a>'
                    }
                },
                {
                    name:'Driver Name',
                    data: 'driverName',
                    title: 'Driver Names',
                    sortable: true,
                    
                },
                {
                    name:'Pay Status',
                    data: 'payStatus',
                    title: 'Pay Status',
                    sortable: true,
                },
                {
                    name:'Status',
                    data: 'status',
                    title: 'Status',
                    sortable: true,
                },
                {
                    name:'Pickup Address',
                    data: 'pickupAddress',
                    title: 'Pickup Address',
                    sortable: true,
                },
                {
                    name:'Last Delivery Address',
                    data: 'lastDeliveryAddress',
                    title: 'Last Delivery Address',
                    sortable: true,
                },
                {
                    name:'Total Miles',
                    data: 'totalMiles',
                    title: 'Total Miles',
                    sortable: true,
                },
                {
                    name:'Expenses',
                    data: 'expenses',
                    title: 'Expenses',
                    sortable: true,
                },
                {
                    name:'Profit',
                    data: 'profit',
                    title: 'Profit',
                    sortable: true,
                },
                {
                    name:'Invoice Status',
                    data: 'invoiceStatus',
                    title: 'Invoice Status',
                    sortable: true,
                },
                {
                    name:'Pick Date',
                    data: 'pickDate',
                    title: 'Pick Date',
                    sortable: true,
                },
                {
                    name:'Drop Date',
                    data: 'dropDate',
                    title: 'Drop Date',
                    sortable: true,
                },
                
            ],
        });

        setTimeout(function () {
        $(table.table().container()).on( 'keyup', 'thead input', function () {
                table
                .column( $(this).data('index') )
                .search( this.value )
                .draw();
            });

        }, 300);
    }
    // End of Todo new Task
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
            location.href= url+"?"+urlParameters;
        }
    }) 

    $(document).on( 'click', '.unselect-all-btn', function(){
        console.log("select btn clicked")
        table.rows().deselect();
        
    })

    $(document).on('click', '.select-all-btn', function(){
        table.rows({search: 'applied'}).select();
    })
        
    
    
</script>
@endpush