@extends('layouts.main')
@section("title") {{__("tran.".$pageTitle)}} @endsection
@push("header")
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
{{--    select all &none buttons css--}}
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/datatables.net-bs/css/select.dataTables.min.css') }}">
{{--for column search--}}
<link rel="stylesheet" href="{{ asset('data_theme/bower_components/datatables.net-bs/css/fixedHeader.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jquery.toast.css')}}">
<style>
    .mr-2   
    {
        margin-right:10px;
    }

    .mb-2   
    {
        margin-bottom:20px;
    }


    .flex-container {
        display: flex;
        /* background-color: DodgerBlue; */
    }

    .flex-container > div {
        background-color: #f1f1f1;
        /* margin: 10px; */
        padding: 20px;
        /* font-size: 30px; */
    }
    .flex-container > button {
        
        /* margin: 10px; */
        margin-right: 2px;
        /* font-size: 30px; */
    }
    .flex-container > span 
    {
        padding:5px 10px;
        background: #F3F0F6;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: bold;
    }

    .w-90 {
        width:100%;
    }

    .w-50 {
        width:50%;
    }

    .w-30 {
        width: 35%;
    }

    .bg-br {
        background: #34495E !important;
    }
    
</style>
@endpush
@section('content')
<!-- Start  Search Input Box -->
<div class="box mb-2">
    <div class="box-header">
        <a class="mr-2" ><i class="fa fa-search"></i></a> 
    </div>
    <div class="box-body">
        <form action="{{ url('search-load-export')}}" method="POST" class="form-horizontal" id="search-form">
        @csrf
        <input type="hidden" name="id" value="{{$id}}">
        <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="reports">Saved Reports:</label>
                    <div class="col-sm-9">
                        <select  class="form-control" id="reports" onchange="selectReport()">
                            <option value="0">Choose a Saved Reports</option>
                            @foreach($reports as $val)
                                <option value="{{$val->id}}" @if($val->id == $id) selected @endif>{{$val->range_start}}  {{$val->range_end}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
            @if($id == 0)
                <button class="btn btn-success w-90"  type="button" onclick="saveReport()"><i class="fa fa-file"></i> Save Report</button>
            @else
            <div class="flex-container"> 
                <button class="btn btn-success"  type="button" onclick="saveReport()"><i class="fa fa-file"></i> Save Report</button>
                <a href="{{route('search-load-delete', $id)}}" class="btn btn-danger" onclick="return confirm('Alert!This will be deleted Permanently and will not Recoverable.Are you sure to delete this item?')"><i class="fa fa-trash color-red"></i> {{__("tran.Delete Report") }} </a>
            </div>
            @endif
            </div>
        </div>

       
        <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="search_terms">{{ __('tran.Search Terms') }}:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="search_terms" name="search_terms" value="{{$currentReport&&$currentReport->search_terms?$currentReport->search_terms:""}}" placeholder="{{__('tran.Search Terms')}}">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="search_terms">{{__('tran.Origin / Destination State')}}:</label>
                    <div class="col-sm-9 flex-container">
                        <select  class="form-control" id="origin" name="origin">
                                <option value="0">{{__('tran.All Origins')}}</option>
                                @foreach($origins as $key => $origin)
                                    <option value="{{$key}}" @if($currentReport&&$key == $currentReport->origin) selected @endif >{{ $origin }}</option>
                                @endforeach
                        </select> 
                        <span >to</span>
                        <select  class="form-control" id="destination" name="destination">
                                <option value="0">{{__('tran.All Destinations')}}</option>
                                @foreach($destinations as $key =>  $destination)
                                    <option value="{{$key}}" @if($currentReport&&$key == $currentReport->destination) selected @endif>{{$destination}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="date_range">{{__('tran.Date Range')}}:</label>
                    <div class="col-sm-9  flex-container">
                        <input type="date" class="form-control" id="range_start" name="range_start" value="{{ !empty($currentReport)?$currentReport->range_start:"" }}" placeholder="">
                        <span >to</span>
                        <input type="date" class="form-control" id="range_end" name="range_end" value="{{!empty($currentReport)?$currentReport->range_end:""}}">
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control" id="range_type" name="range_type" >
                    <option value="FPD"  @if($currentReport&&$currentReport->range_type=="FPD") selected @endif>{{__('tran.Search by: First Pickup Date')}}</option>
                    <option value="LDD" @if($currentReport&&$currentReport->range_type=="LDD") selected @endif>{{__('tran.Search by: Last Delivery Date')}}</option>
                    <option value="ID" @if($currentReport&&$currentReport->range_type=="ID") selected @endif>{{__('tran.Search by: Billed Date')}}</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="status">{{__('tran.Load Status')}}:</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="status" id="status">
                            <option value="all">{{__('tran.Leave empty to serach all load statuses')}}</option>
                            <option value="Pending" @if($currentReport&&$currentReport->status == "Pending") selecetd @endif}}>{{__('tran.Pending')}}</option>
                            <option value="Needs Driver" @if($currentReport&&$currentReport->status == "Needs Driver") selecetd @endif}}>{{__('tran.Needs Driver')}}</option>
                            <option value="In Transit" @if($currentReport&&$currentReport->status == "In Transit") selecetd @endif}}>{{__('tran.In Transit')}}</option>
                            <option value="Dispatched" @if($currentReport&&$currentReport->status == "Dispatched") selecetd @endif}}>{{__('tran.Dispatched')}}</option>
                            <option value="Delivered" @if($currentReport&&$currentReport->status == "Delivered") selecetd @endif}}>{{__('tran.Delivered')}}</option>
                            <option value="Billed" @if($currentReport&&$currentReport->status == "Billed") selecetd @endif}}>{{__('tran.Billed')}}</option>
                            <option value="Cancelled" @if($currentReport&&$currentReport->status == "Cancelled") selecetd @endif}}>{{__('tran.Cancelled')}}</option>
                            
                        </select>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="conlumn_layout">{{__('tran.Column Layout')}}:</label>
                    <div class="col-sm-9">
                    <select class="form-control" name="column_layout">
                        <option value="standard"  @if($currentReport&&$currentReport->column_layout == "standard") selecetd @endif>{{__('standard')}}</option>
                        <option value="extend"  @if($currentReport&&$currentReport->column_layout == "extend") selected @endif>{{__('tran.extend')}}</option>
                    </select>
                    </div>
                </div>
            </div>
        </div>

         <div class="row">
            <div class="col-md-9 mb-2">
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                    <button class="btn btn-danger w-30"  type="button" onclick="initfunction()"><i class="fa fa-ban"></i> {{__('tran.Reset Cateria') }}</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div class="box-footer form-horizontal">
        <div class="row">
            <div class="col-md-9 mb-2"> 
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button class="btn btn-success bg-br w-30" onclick="exportLoad()"><i class="fa fa-file"></i> {{__('tran.Export Report') }} </button> 
                        <button class="btn btn-success w-30" onclick="search()"><i class="fa fa-search"></i> {{__('tran.Search Loads / Preview Report')}}</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2"></div>
        </div>    

    </div>

</div>
<!-- End of Search Input box -->

<!-- Start Report Summary Box -->
<div class="box mb-2">
    
    <div class="box-header">
        <span class="load-summary-text">{{__('Showing Report Summary for')}} <span class='load-count'>0</span> {{__('loads')}}</span> |
        <a onclick="exportLoadSummary()"><i class="fa fa-download"></i> Export Loads Summary to Excel</a>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4 card">
                    <div class="summary-item">
                        <span>{{ __('tran.Total Income')}}</span>
                        <span class="total-income">0</span>
                    </div>
                    <div class="summary-item">
                        <span>{{__('tran.Total Client Mileage')}}</span>
                        <span class="miles">0</span>
                    </div>
                    <div class="summary-item">
                        <span>{{__('tran.Client RPM')}}</span>
                        <span class="client-rpm">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-item">
                        <span>{{__('tran.Total Deadhead')}}</span>
                        <span class="deadhead-miles">0</span>
                    </div>
                    <div class="summary-item">
                        <span>{{__('tran.Total Mileage + DH')}}</span>
                        <span class="total-miles">0</span>
                    </div>
                
                
                    <div class="summary-item">
                        <span>{{__('tran.Client + DH RPM')}}</span>
                        <span class="client-dh-rpm">0</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-item">
                        <span>{{__('tran.Total Income')}}</span>
                        <span class="total-income">0</span>
                    </div>
                    <div class="summary-item">
                        <span>{{__('tran.Total Expenses')}}</span>
                        <span class="total-expense">0</span>
                    </div>
                    <div class="summary-item">
                        <span>{{__('tran.Gross Profit')}}</span>
                        <span class="gross-profit">0</span>
                    </div>
                </div>
                    
            </div>
        </div>

    </div>
</div>
<!-- End of Report Summary Box -->

<!-- start of table -->
<div class="box">
<div class="box-header">
        <a class="mr-2" ><i class="fa fa-list"></i></a> 
</div>
<div class="box-body">
<div class="row">
<div class="col-md-12">
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
        
        </table>
   

</div>
</div>
</div>
</div>
</div>
<!-- End of table -->
@endsection

@push("footer")
<script src="{{ asset('data_theme/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

<script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.fixedHeader.min.js')}}"></script>

<script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.select.min.js')}}"></script>
<script src="{{ asset('data_theme/bower_components/datatables.net-bs/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('data_theme/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ asset('js/jquery.toast.js')}}"></script>
<script>


$(document).ready(function(){
    $('.select2').select2()
    @if($id != 0)
    search()
    @endif
})
var table;
function search()
{
    clearSummary()
    showLoading()
    if(table != null)
    {
        table.destroy();
        $('#data_table_custom tbody').empty();
        if($('#data_table_custom thead tr:eq(1)').length >0)
            $('#data_table_custom thead tr:eq(1)').remove();
    }
   
    var form =  $('#search-form')[0];
    var formData = new FormData(form);
    var base_url = "{{ url('')}}";
    $.ajax({
        url: "{{ url('search-load-ajax') }}",
        method:"POST",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success:function(res)
        {
           
            var result = res.result.table_data;
            var columns = result.columns;
            columns.forEach(function (currentValue, index, arr) {
                if (currentValue.render) {

                    columns[index] = {
                        render: function (data, type, row) {
                            return eval(currentValue.render);
                        },
                        data: currentValue.data,
                        // orderable: false,
                    };
                }
            });

            var createdRow = "";
            if (result && result.createdRow) 
            {
                createdRow = function (row, data, dataIndex) {
                    eval(JSON.parse(result).createdRow);
                };
            }

            var new_row = $("<tr class='search-header'/>");
            $('#data_table_custom thead th').each(function(i) {
                var title = $(this).text();
                var new_th = $('<th style="' + $(this).attr('style') + '" />');
                $(new_th).append('<input type="text" placeholder="' + title + '" data-index="'+i+'"/>');
                $(new_row).append(new_th);
            });
            $('#data_table_custom thead').append(new_row);
            var selectStyle = "api";
            table = $('#data_table_custom').DataTable({
                                                columns: columns,
                                                data: result.data,
                                                columnDefs: result.columnDefs,
                                                createdRow: createdRow,
                                                order: [[0, "desc"]],
                                                // scrollY: 550,
                                                paging: false,
                                                scrollX: true,
                                                orderCellsTop: true,
                                                buttons:[],
                                                dom: '<"top"f>t',
                                                "oLanguage": {
                                                    "sSearch": "<i class='fa fa-question-circle'></i> <b>Search the Loads:</b>"
                                                },
                                                language: {
                                                    buttons: {
                                                        selectNone: "Select None"
                                                    },

                                                },
       
                                            })

            setTimeout(function () {
                $('.dataTables_scrollHead').css({
                    'overflow-x':'scroll'
                }).on('scroll', function(e){
                    var scrollBody = $(this).parent().find('.dataTables_scrollBody').get(0);
                    scrollBody.scrollLeft = this.scrollLeft;
                    $(scrollBody).trigger('scroll');
                });
                $(table.table().container() ).on( 'keyup', 'thead input', function () {
                    table
                        .column( $(this).data('index') )
                        .search( this.value )
                        .draw();
                    });
                
            }, 100);

            setSummary(res.result.summary)
            hideLoading()
        },
        error:function()
        {
            hideLoading()
        }
    })
}

function setSummary(summary)
{
    $('.total-income').text(numberToString(summary['total_income']))
    $('.miles').text(numberTonumberString(summary['miles']))
    $('.client-rpm').text(numberToString(summary['clientRPM']))

    $('.deadhead-miles').text(numberTonumberString(summary['dead_headmiles']))
    $('.total-miles').text(numberTonumberString(summary['total_miles']))
    $('.client-dh-rpm').text(numberToString(summary['clientDhRPM']))
   
    $('.total-income').text(numberToString(summary['total_income']))
    $('.total-expense').text(numberToString(summary['total_expense']))
    $('.gross-profit').text(numberToString(summary['total_gross_profit']))
    
}

function clearSummary()
{
    $('.total-income').text(0)
    $('.miles').text(0)
    $('.client-rpm').text(0)
    $('.deadhead-miles').text(0)
    $('.total-miles').text(0)
    $('.total-expense').text(0)
    $('.client-dh-rpm').text(0)
    $('.total-income').text(0)
    $('.total-miles').text(0)
    $('.total-expense').text(0)
    $('.gross-profit').text(0)
}

function numberToString(s)
{
    var options = {
        maximumFractionDigits : 2,
        currency              : "USD",
        style                 : "currency",
        currencyDisplay       : "symbol"
    }
    return localStringToNumber(s).toLocaleString(undefined, options)
}

function numberTonumberString(s)
{
    var options = {
        maximumFractionDigits : 2,
        
    }
    return localStringToNumber(s).toLocaleString(undefined, options)
}

function localStringToNumber( s ){
    if(s == "")
    s = 0
    return Number(String(s).replace(/[^0-9.-]+/g,""))
}

function saveReport(){
    var form =  $('#search-form')[0];
    var formData = new FormData(form);
    
    $.ajax({
        url: "{{ url('search-load-store') }}",
        method:"POST",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success:function(res)
        {
            @if($id == 0)
            
                setReportOption(res.report)
            @endif
            
            $.toast({
                heading: 'Success',
                text: 'Report Saved Successfully.',
                showHideTransition: 'fade',
                icon: 'success',
                showHideTransition: 'slide',
                position : 'top-right'  
            })
        }
    })
}

function setReportOption(report)
{
    var range_start = "" 
    var range_end = ""
    if(report["range_start"] != null)
        range_start =report["range_start"]

    if(report["range_start"] != null)
        range_end =report["range_end"]
    $('#reports').append('<option value=\"'+report["id"]+'\">'+range_start+" "+range_end+'</option>');
}

function selectReport()
{
    var id = $('#reports').val();
    location.href = "{{ url('') }}/search-load/"+id
}

function initfunction()
{
    
    clearSummary();
    $('#origin').val(0);
    $('#destination').val(0);
    $('#range_start').val('')
        .attr('type', 'text')
        .attr('type', 'date');
    $('#range_end').val('')
        .attr('type', 'text')
        .attr('type', 'date');

    $('#range_type').val('FPD')

    $('#status').val('all')

    $('#column_layout').val("standard")
    $('#search_terms').val('');
    if(table != null)
    {
        
        $('#data_table_custom tbody').empty();
        if($('#data_table_custom thead tr:eq(1)').length >0)
            $('#data_table_custom thead tr:eq(1)').remove();
    }


}

function exportLoad()
{
     $('#search-form').attr('action', "{{ url('search-load-export') }}");
     $('#search-form').submit();
   
}

function exportLoadSummary()
{
    $('#search-form').attr('action', "{{ url('search-load-export-summary') }}");
    $('#search-form').submit();
}
</script>
@endpush