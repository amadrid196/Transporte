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

        .color-green {
            color:#33B63C;
        }
    </style>
@endpush

@section("title") {{__("tran.".$title_tag)}} @endsection

@section('content')
<ul class="nav nav-tabs">
            <li class="active"><a href="#" category="0"><i><b>Active Loads</b></i></a></li>
            <li><a href="#" category="2"><i><b>{{ __("tran.BILLED") }}</b></i></a></li>
            <li><a href="#" category="3"><i><b>{{ __("tran.All Loads") }}</b></i></a></li>

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
            <a  class='complete-load-button mr-2 hidden' ><i class="fa fa-check color-green"></i> {{__("tran.Complete Load")}}</a>
            <a  class='paid-load-button mr-2 hidden' ><i class="fa fa-check color-green"></i> {{__("tran.Mark Piad by Customer")}}</a>
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
<script src="{{ asset('data_theme/dataTables.scroller.js')}}"></script>



<script>
var table;
const PENDING = "Pending";
const NEEDS_DRIVER ="Needs Driver";
const DISPATCHED ="Dispatched";
const IN_TRANSIT ="In Transit";
const DELIVERIED ="Delivered";
const  COMPLETED = "Billed";
const PADIBYCUSTOMER = "Paid by Customer";
const CANCELLED  = "Cancelled";
var activeCategory = 0;
$(function () {
    initTable("{{ route("app_url")}}/testload-ajax/"+activeCategory);
});
// function generateActionButtons(full) {

//     let btns =
//         '<div class="dropdown">' +
//         '<button class="btn btn-success btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
//         'Actions' +
//         '</button>' +
//         '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
//         '<a class="dropdown-item" href="/dashboard/user_select/'+full.id+'">Edit</a>' +

//         '<a class="dropdown-item" onclick="remove(\'/dashboard/user_delete/'+full.id+'\')" >Remove</a>' +
//         '</div>' +
//         '</div>';
//     return btns;
// }
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
$(".content .nav a").on("click", function(){
    $(".nav").find(".active").removeClass("active");
    $(this).parent().addClass("active");
    activeCategory = $(this).attr('category');
    var category = "/"+$(this).attr('category');
    table.destroy();
    $('#data_table_custom tbody').empty();
    $('#data_table_custom thead tr:eq(1)').remove();
    initTable("{{ route("app_url")}}/testload-ajax"+category);
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
function initTable(url){
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
        },
        scroller: {
            loadingIndicator: true
        },
        columns: [
            {
                name:'Load ID',
                data: 'id',
                title: 'Load ID',
                sortable: true,
                "render":function(data,type, full){

                    return '<a href="{{ route("app_url")}}/loads-edit/'+full.id+'">'+full.id+'</a>'
                }
            },
            {
                name:'Load Status',
                data: 'status',
                title: 'Load Status',
                sortable: true,
                "render":function(data,type, full){

                    return "<span class='tooltip-container'>"+full.status+"</span>"
                }
            },
            {
                name:'Last Contact',
                data: 'lastContract',
                title: 'Last Contact',
                sortable: true,
                "render":function(data,type, full){

                    return "<span class='tooltip-container'>"+full.lastContract+"</span>"
                }
            },
            {
                name:'Customers',
                data: 'customers',
                title: 'Customers',
                sortable: true,
                "render":function(data,type, full){

                    return "<span class='tooltip-container'>"+full.customers+"</span>"
                }
            },
            {
                name:'Picks',
                data: 'picks',
                title: 'Picks',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.picks+"</span>"
                }
            },
            {
                name:'Pick Date',
                data: 'pickDate',
                title: 'Pick Date',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.pickDate+"</span>"
                }
            },
            {
                name:'Drops',
                data: 'drops',
                title: 'Drops',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.drops+"</span>"
                }
            },
            {
                name:'Drop Date',
                data: 'dropDate',
                title: 'Drop Date',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.dropDate+"</span>"
                }
            },
            {
                name:'Driver',
                data: 'driver',
                title: 'Driver',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.driver+"</span>"
                }
            },
            {
                name:'Power Unit',
                data: 'powerUnit',
                title: 'Power Unit',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.powerUnit+"</span>"
                }
            },
            {
                name:'Trailer',
                data: 'trailer',
                title: 'Trailer',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.trailer+"</span>"
                }
            },
            {
                name:'Picks',
                data: 'picks',
                title: 'Picks',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.picks+"</span>"
                }
            },
            {
                name:'Distance',
                data: 'distance',
                title: 'Distance',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.distance+"</span>"
                }
            },
            {
                name:'Income',
                data: 'income',
                title: 'Income',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.income+"</span>"
                }
            },
            {
                name:'Expenses',
                data: 'expense',
                title: 'Expenses',
                sortable: true,
                "render":function(data,type, full){
                    return "<span class='tooltip-container'>"+full.expense+"</span>"
                }
            },

            // {
            //     name: 'Action',
            //     data: null,
            //     targets: -1,
            //     sortable: false,
            //     title: 'Action',
            //     searchable: false,
            //     "render": function (data, type, full, meta) {
            //         return generateActionButtons(full);
            //     }
            // }
        ],
        rowCallback: function(row, data, index){
            color = ""

            switch(data.status)
            {
                case PENDING:
                    color = "#FFF69A"
                    break;

                case DISPATCHED:
                    color = "#FECCCB"
                    break;

                case IN_TRANSIT:
                    color = "#C7FFC5"
                    break;

                case DELIVERIED:
                    color = "#CCFFFF"
                    break;
                case COMPLETED:
                    color = "#FECCFF"
                    break;
                case PADIBYCUSTOMER:
                    color = "#3c763d"
                    break;
                case CANCELLED:
                    color = "#CD5C5C"
                    break;
                default:
                    color  = ""
            }

            var lastColumnIndex =14
            $(row).find('td:eq(1)').css('background-color', color);
            $(row).find('td:eq(0)').css('borderLeft', '5px solid '+color)
            $(row).find('td:eq('+lastColumnIndex+')').css('borderRight', '5px solid '+color)

            $(row).find('td').css('whiteSpace', 'nowrap')
            $(row).find('td').css('overflow', 'hidden')
            $(row).find('td').css('textOverflow', 'ellipsis')


            // Edit Load
            $(row).find('td:eq(0) .tooltip-container').on({"mouseenter":function(){
                        $(this).tooltip({ items:$(this), title: "Edit Load", html:true, placement:"right", sanitize: true});

                        $(this).tooltip('show');}
            })
            // Customer
            $(row).find('td:eq(3) .tooltip-container').on({"click":function(){
                    title = "None"

                    if(data.broker)
                    {
                        title = "<i>"+data.broker.company+"</i>" + "<br> "+ "<i class='fa fa-phone'></i>"
                        if(data.broker.contact!= null)
                        title = title+ "&nbsp; <a> + "+data.broker.contact+"</a>"
                        else
                        title = title+"None"

                        title = title + "<br><br>" + "Contact"+"<br>"+ "<i class='fa fa-user'></i> <i>Priamary Contact</i>"+"<br>"+ "<i class='fa fa-phone'></i> "
                        if(data.broker.contact)
                        title = title + " &nbsp; <a> + "+data.broker.contact+"</a>"
                        else
                        title = title+ "None"
                    }
                    // $(this).popover({
                    //     container: 'body'
                    // })
                    // console.log("fire the Cutomeres")

                    $(this).tooltip({ items:$(this), title: title, html:true});
                    $(this).tooltip('enable');
                    $(this).tooltip('show');
                },


            })
            //Reference Load
            $(row).find('td:eq(14)').on({
                        "click":function(){
                                title = "None"

                                if(data.reference)
                                {
                                    title = "<i>Load# "+data.reference+"</i>"

                                }

                                $(this).tooltip({ items:$(this), title: title, html:true});
                                $(this).tooltip('enable');
                                $(this).tooltip('show');
                        },


            })
            // Driver
            $(row).find('td:eq(8) .tooltip-container').on({
                "click":function(){
                        title = "None"

                        if(data.driverObj)
                        {
                            title = "Driver<br> <i class='fa fa-user'></i>  <i> "+data.driverObj.name+"</i>"+"<br>"
                                    + "<i class='fa fa-phone'></i> "+ "<a>"+data.driverObj.contact+"</a>"

                        }

                        $(this).tooltip({ items:$(this), title: title, html:true});
                        $(this).tooltip('enable');
                        $(this).tooltip('show');
                },


            })

            //Pick Up Address
            $(row).find('td:eq(4) .tooltip-container').on({
                "click":function(){
                    var title = "None"

                    if(data.shipper&&data.shipper.length>0)
                    {
                        title =""
                        if(data.shipper[0].customer)
                        {
                            title = title + data.shipper[0].customer.company+"<br>"
                        }

                        if(data.shipper[0].pickup_address)
                        {
                            title = title + data.shipper[0].pickup_address+"<br>"
                        }


                        if(data.shipper[0].contact_number)
                        {
                            title = title + "<i class='fa fa-phone'></i> "+"<a>+"+data.shipper[0].contact_number+"</a>"+"<br>"
                            title = title + "Contact <br> <i class='fa fa-user'></i> Primary Contact <br>" +"<i class='fa fa-phone'></i> "+"<a>+"+data.shipper[0].contact_number+"</a>"
                        }else
                        {
                            title = title + "<i class='fa fa-phone'></i> None<br>"
                            title = title + "Contact <br> <i class='fa fa-user'></i> Primary Contact <br>" +"<i class='fa fa-phone'></i>  None"
                        }

                        $(this).tooltip({ items:$(this), title: title, html:true});
                        $(this).tooltip('enable');
                        $(this).tooltip('show');
                    }
                }
            })
            //Drop Off Address
            $(row).find('td:eq(6) .tooltip-container').on({
                "click":function(){
                    var title = "None"

                    if(data.consignee&&data.consignee.length>0)
                    {
                        title =""
                        if(data.consignee[0].customer)
                        {
                            title = title + data.consignee[0].customer.company+"<br>"
                        }

                        if(data.consignee[0].dropoff_address)
                        {
                            title = title + data.consignee[0].dropoff_address+"<br>"
                        }


                        if(data.consignee[0].contact_number)
                        {
                            title = title + "<i class='fa fa-phone'></i> "+"<a>+"+data.consignee[0].contact_number+"</a>"+"<br>"
                            title = title + "Contact <br> <i class='fa fa-user'></i> Primary Contact <br>" +"<i class='fa fa-phone'></i> "+"<a>+"+data.consignee[0].contact_number+"</a>"
                        }else
                        {
                            title = title + "<i class='fa fa-phone'></i> None<br>"
                            title = title + "Contact <br> <i class='fa fa-user'></i> Primary Contact <br>" +"<i class='fa fa-phone'></i>  None"
                        }

                        $(this).tooltip({ items:$(this), title: title, html:true});
                        $(this).tooltip('enable');
                        $(this).tooltip('show');
                    }
                }
            })

            //pick up Date
            $(row).find('td:eq(5) .tooltip-container').on({
                "click":function(){
                    var options = {
                        year: "numeric",
                        month: "2-digit",
                        day: "numeric",
                        hour: "numeric",
                        minute: "numeric"
                    };
                    var title = "None"
                    if(data.shipper&&data.shipper.length>0)
                    {
                        title =""
                        if(data.shipper[0].start_periode&&data.shipper[0].end_periode)
                        {
                            title =  new Date(data.shipper[0].start_periode).toLocaleDateString("en", options)+" - "+ new Date(data.shipper[0].end_periode).toLocaleDateString("en", options);
                        }else
                        {
                            title =  new Date(data.shipper[0].pickup_date).toLocaleDateString("en", options);
                        }
                    }

                    $(this).tooltip({ items:$(this), title: title, html:true});
                    $(this).tooltip('enable');
                    $(this).tooltip('show');
                }
            })

            //Drop off Date
            $(row).find('td:eq(7) .tooltip-container').on({
                "click":function(){
                    var options = {
                        year: "numeric",
                        month: "2-digit",
                        day: "numeric",
                        hour: "numeric",
                        minute: "numeric"
                    };

                    var title = "None"
                    if(data.consignee&&data.consignee.length>0)
                    {
                        title =""
                        if(data.consignee[0].start_periode&&data.consignee[0].end_periode)
                        {
                            title =  new Date(data.consignee[0].start_periode).toLocaleDateString("en", options)+" - "+ new Date(data.consignee[0].end_periode).toLocaleDateString("en", options);
                        }else
                        {
                            title =  new Date(data.consignee[0].dropoff_time).toLocaleDateString("en", options);
                        }
                    }

                    $(this).tooltip({ items:$(this), title: title, html:true});
                    $(this).tooltip('enable');
                    $(this).tooltip('show');
                }
            })
        }
    });

    //Todo not working
setTimeout(function () {
    $(table.table().container()).on( 'keyup', 'thead input', function () {
        table
        .column( $(this).data('index') )
        .search( this.value )
        .draw();
    });

}, 300);
}



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
        var driverObj = table.rows({selected: true}).data().toArray()[0].driverObj
        if(driverObj !=null &&driverObj.id !="")
        {
            var url  = $(this).attr('href')
            var id = table.rows({selected: true}).data().toArray()[0].driverObj.id
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
            if(rows[i].driverObj !=null &&rows[i].driverObj.id !="")
            {
                ids.push(rows[i].driverObj.id)
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
