@extends('layouts.main')
@section("title")
    {{ __('tran.Dashboard') }}
@endsection

@push("header_top"){{--less priority css--}}

@endpush

@push("header")
<style>
.custom-infobox {
    padding:10px;
    margin-right: 1px;
}

.custom-infobox-icon {
    /* border-radius: 50%;
    width: 60px;
    height: 60px;
    line-height: 58px;
    margin-top: 15px; */
    border-radius: 50%;
    line-height: 65px;
    width: 80px;
    height: 80px;
    margin-top: 9px;
    margin-left: 5px;
    background-color: #00c0ef !important;
    border: 6px solid #f7f9fc;
}

.custom-widget {
    padding: 10px 20px;
    vertical-align: middle;

}

.carsoule {
    position: absolute;
    top:110px;
    padding: 0px 15px;
    border-radius:50%;
    font-size: 30px;
    background: white;
    z-index: 200;
    box-shadow: 0 3px 5px rgb(0 0 0 / 20%);
}
.carsoule.next {
    cursor: pointer;
    position: absolute;
    color:black;
    top: 112px;
    right: 45px;
}
.carsoule.prev {
    cursor: pointer;
    position: absolute;
    color:black;
    top: 112px;
    left: 95px;
}

.morris-hover {
    position: absolute;
    font-size: 15px;
    position: absolute;
    /* padding: 6px ; */
    background: #10183F;
    font-size: 15px;
    color: white;
    border-radius: 5px;
    text-align: left;
}
.overlay {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: absolute;
    background: #222;
    z-index: 500;
    display: none;
}

.overlay__inner {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: absolute;
    z-index: 501;
}

.overlay__content {
    left: 50%;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    z-index: 502;
}

.spinner {
    width: 75px;
    height: 75px;
    display: inline-block;
    border-width: 2px;
    border-color: rgba(100, 100, 100, 0.05);
    border-top-color: black;
    animation: spin 1s infinite linear;
    border-radius: 100%;
    border-style: solid;
    z-index: 503;
}

.infomation-box {
    text-align: center;
    padding: 10px 50px;
}
.infomation-box span{
    color:#474d67 !important;
}

.infomation-box b {
    font-size:20px;

}

.infomation-header {
    color:#474d67 !important;
    font-weight:bold;
    padding: 10px 15px;
    background: #192148;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    border-bottom: 1px solid #262E55;
}

.info-box-number {
    color: #111B54;
}
/* tspan {
    font-size: 25px !important;
} */
@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}


.custom-btn {
    background: #00c0ef !important;
}

.info-box-icon {
    font-size: 20px;
}
.info-box-number {
    font-size: 30px;
}

#today-profit {
    font-size: 45px;
    font-weight:bold;
    color:#111B54;
}
.today-profit-currency {
    font-size: 45px;

    color:#00c0ef;
}

#city-list {
    padding-top:60px;
}

table tbody {
  display: block;
  max-height: 300px;
  overflow-y: scroll;
}

table thead, table tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}
.hyper {
    text-decoration: underline !important;
    color: #337ab7 !important;
}
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-md-12"><h2>Good Morning, {{ auth()->user()->name }}</h2></div>
</div>
<div class="row">
<div class="col-md-12">
    <div class="box box-success">
        <div class="box-header">
            <h3 class="box-title">Active driver's location</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-5">
                <div id="div_map" style="height: 400px; width: 100%;"></div>
                </div>
                <div class="col-md-7">

                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Current Load #</th>
                            <th>Driver</th>
                            <th>Current Location</th>
                        </tr>
                    </thead>
                    <tbody id="location_row">
                    </tbody>
                    <tfooter>
                    </tfooter>
                </table>

                <div class="flex">
                    <a href="{{ route('loads-create') }}" class="btn btn-success  custom-btn">Add New load</a>
                    <a  href="{{ route('loads-index') }}" class="btn btn-default ">view all loads</a>
                </div>
                </div>
            </div>


        </div>
    </div>
</div>
</div>
<div class="row mt:36">
    <div class="col-md-12 text-right">
        <span class="box custom-widget">By week &nbsp;<img src="{{ asset('/data_theme/img/Calendar_Icon.svg') }}" width="15px"></span>
    </div>

</div>

<div class="row mt:36">
    <div class="col-md-12">
        <div class="flex flex-wrap">
            <!-- <div class="col-md-3 col-sm-6 col-xs-12"> -->
                <div class="info-box custom-infobox flex-1">
                    <span class="info-box-icon custom-infobox-icon bg-aqua"><img src="{{ asset('/data_theme/img/Money_Icon.svg') }}" width="25px"></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL WEEKLY GROSS SALES</span>
                        <span class="info-box-number" id="total_profit">$ 0</span>
                        <span class="description-percentage" id="diff_total_profit">0%</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            <!-- /.info-box -->
            <!-- </div> -->
            <!-- <div class="col-md-3 col-sm-6 col-xs-12"> -->
                <div class="info-box custom-infobox flex-1">
                    <span class="info-box-icon custom-infobox-icon bg-aqua"><img src="{{ asset('/data_theme/img/Total_Billed_Miles_Icon.svg') }}" width="25px"></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL BILLED MILES</span>
                        <span class="info-box-number" id="total_miles">0 miles</span>
                        <span class="description-percentage" id="diff_total_miles">0%</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            <!-- /.info-box -->
            <!-- </div> -->
            <!-- <div class="col-md-3 col-sm-6 col-xs-12"> -->
                <div class="info-box custom-infobox flex-1">
                    <span class="info-box-icon custom-infobox-icon bg-aqua"><img src="{{ asset('/data_theme/img/Total_Deadhead_Miles_Icon.svg') }}" width="25px"></span>

                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL DEADHEAD</span>
                        <span class="info-box-number" id="total_dead_head">0 miles</span>
                        <span class="description-percentage" id="diff_total_dead_head">0%</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            <!-- /.info-box -->
            <!-- </div> -->
            <!-- <div class="col-md-3 col-sm-6 col-xs-12"> -->
                <div class="info-box custom-infobox flex-1">
                    <span class="info-box-icon custom-infobox-icon bg-aqua"><img src="{{ asset('/data_theme/img/RPM_Icon.svg') }}" width="25px"></span>

                    <div class="info-box-content">
                        <span class="info-box-text">RPM</span>
                        <span class="info-box-number" id="rpm">$ 0</span>
                        <span class="description-percentage" id="diff_rpm">0<small>%</small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            <!-- /.info-box -->
            <!-- </div> -->
        </div>
    </div>
</div>

<div class="row mt:36">
    <div class="col-md-7">
        <div class="box">
            <div class="box-header">
            <span>Your Daily Gross</span>
            </div>
            <div class="box-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <br><br>
                    <h3><span class="today-profit-currency">$</span><span id="today-profit">0</span></h3>
                    <div><h5><span id="diff-profit">0%</span> than last day</h5></div>
                    <br><br>
                    <div><a href="{{ route('stats-index') }}" class='btn btn-default'>Open Report</a></div>
                </div>
                <div class="col-md-9">
                <div class="overlay">
                <div class="overlay__inner">
                    <div class="overlay__content"><span class="spinner"></span></div>
                </div>
                </div>
                <span class="carsoule prev">&#8249;</span>

                <div class="chart" id="revenue-chart" style="position: relative; height: 300px; padding:20px 5px;">

                </div>
                <span class="carsoule next">&#8250;</span>

                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
    <div class="box">
    <div class="box-header">
       <span>PICKUP CITIES RPM RATIO</span>
    </div>
    <div class="box-body">
    <div class="row">
        <div class="col-md-8">
            <div class="chart" id="sales-chart" style="position: relative; height: 300px;"></div>
        </div>
        <div class="col-md-4">

        <div id="city-list">

        </div>
        </div>
    </div>
    </div>
    </div>
</div>
@endsection

@push("footer")

<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="{{ asset('data_theme/plugins/morris/morris.min.js') }}"></script>

<script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    getLoadSummaryData();
})
var dailyIndex = 0;

$('.box ul.nav a').on('shown.bs.tab', function () {
    area.redraw();
    donut.redraw();
});

function getLoadSummaryData()
{
    $.ajax({
        url: "{{ route('load-summary') }}",
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            var color = "green";
            if(res.diff_total_profit >= 0)
            {
                color = "green";
            }else
            {
                color = "red";
            }
            document.querySelector("#total_profit").innerHTML = `$ ${res.total_profit}`;
            document.querySelector("#diff_total_profit").innerHTML = `<span class="text-${color}">${res.diff_total_profit} <small>%</small></span> than last week`;
            if(res.diff_total_miles >= 0)
            {
                color = "green";
            }else
            {
                color = "red";
            }
            document.querySelector("#total_miles").innerHTML = `${res.total_miles} miles`;
            document.querySelector("#diff_total_miles").innerHTML = `<span class="text-${color}">${res.diff_total_miles} <small>%</small></span> than last week`;
            if(res.diff_total_dead_head_miles >= 0)
            {
                color = "green";
            }else
            {
                color = "red";
            }
            document.querySelector("#total_dead_head").innerHTML = `${res.total_dead_head_miles} miles`;
            document.querySelector("#diff_total_dead_head").innerHTML = `<span class="text-${color}">${res.diff_total_dead_head_miles} <small>%</small></span> than last week` ;
            if(res.diff_rate_per_miles >= 0)
            {
                color = "green";
            }else
            {
                color = "red";
            }
            document.querySelector("#rpm").innerHTML = `$ ${res.rate_per_miles}`;
            document.querySelector("#diff_rpm").innerHTML = `<span class="text-${color}">${res.diff_rate_per_miles} <small>%</small></span> than last week`;
        }
    });
}

var nextBtn = document.querySelector(".next");
var prevBtn = document.querySelector(".prev");



nextBtn.addEventListener('click', function(){
    dailyIndex--;
    daliyGross(dailyIndex);
});

prevBtn.addEventListener('click', function(){
    dailyIndex++;
    daliyGross(dailyIndex);
})
function drawDailyGross(data)
{
    document.querySelector("#revenue-chart").innerHTML = "";
    var area = new Morris.Area({
        element   : 'revenue-chart',
        resize    : true,
        data      : data,
        xkey      : 'year',
        ykeys     : ['value'],
        xLabels: "day",
        lineColors: ['#a0d0e0'],
        labels:["value"],
        hideHover:"auto",
        hoverCallback: function (index, options, content, row) {
            return `
            <div class="infomation-header"> In Total</div>
            <div class="infomation-box">
            <b>${numberToString(row.value)}</b> <br> <span>${new Date(row.year).toLocaleDateString()}</span>
            </div>`;
        },
        xLabelFormat:function (x) { return new Date(x).toLocaleDateString(); }
    });
}

function drawMostVisitedCities(data)
{
    var donut = new Morris.Donut({
        element  : 'sales-chart',
        resize   : true,
        colors   : ['#13CBE7', '#ADECF3', '#73DDED'],
        data     : data,
        hideHover: 'auto',
        hoverCallback: function (index, options, content, row) {
            return `<b style="font-size:20px">${row.label}</b> <br> ${row.value}`;
        },
    });

}
getdrawMostVisitedCities();
function getdrawMostVisitedCities()
{
    var colors = ['#13CBE7', '#ADECF3', '#73DDED'];
    document.querySelector("#sales-chart").innerHTML = "";
    $.ajax({
        url: "{{ route('pick-up-cities') }}",
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            drawMostVisitedCities(res.data);
            var innerhtml = "";

            res.data.forEach((element, key) => {
                var color = colors[key%3];
                innerhtml += `<i class="fa fa-circle" style="color:${color}"><b></i> &nbsp;&nbsp;${element.label}</b><br><br>`
            });
            document.querySelector("#city-list").innerHTML = innerhtml;
        }
    })
}

daliyGross(0);
function daliyGross(index)
{
    showOverlay();
    //daily-gross-chart
    $.ajax({
        url: "{{ route('daily-gross-chart') }}",
        method: 'GET',
        data:{
            index:index
        },
        dataType: 'json',
        success: function (res) {
            drawDailyGross(res.data);
            var color = "green"
            if(res.diff_total_dead_head_miles < 0)
            {
                color = "red";
            }
            document.querySelector("#diff-profit").innerHTML = `<span class='text-${color}'>${res.diff}%</span>`;
            document.querySelector("#today-profit").innerHTML = `${res.todayProfits}`;
            hideOverlay();
        }
    })
}


function showOverlay()
{
    document.querySelector(".overlay").style.display = "block";
}

function hideOverlay()
{
    document.querySelector(".overlay").style.display = "none";
}


</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env("MAP_API")}}"></script>
<!-- <script src="{{ asset('data_theme/dist/js/pages/dashboard.js') }}"></script> -->
<script>

var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
  cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
});

var channel = pusher.subscribe("location");

channel.bind("SendLocation", (data) => {
  // Method to be dispatched on trigger.
  console.log(data);
  updateMap(data)

});


var lat  = 37.0902;
var lng  = 255;
var nigeria= {lat: lat, lng: lng};
let markers = [];
let infoWindows = [];

var map = new google.maps.Map(document.getElementById('div_map'), {
      zoom: 5,
      center: nigeria
});




var lineCoordinates  = [];

var trucks = [];
var locations = [];
function drawTable(currentLocateData)
{
    var location_rows = document.querySelector("#location_row");
    var rows = location_rows.rows;

    locations.forEach(element => {
        var status = false
        for(let i = 0; i < rows.length; i++)
        {
            var row = rows[i];
            var id =  row.cells[0].children[0].value;
            if(element.id == id)
            {
                if(element.load_id != null)
                {
                row.cells[0].children[3].innerHTML = `
                    <input type="hidden" name="id[]" value='${element.id}'>
                    <input type="hidden" name="lat[]" value='${element.lat}'>
                    <input type="hidden" name="lat[]" value='${element.lng}'>
                    <a class="hyper" href="{{url('loads-edit')}}/${element.load_id}">${element.reference}</a>
                `;
                }else
                {
                    row.cells[0].innerHTML  = `
                        <input type="hidden" name="id[]" value='${element.id}'>
                        <input type="hidden" name="lat[]" value='${element.lat}'>
                        <input type="hidden" name="lat[]" value='${element.lng}'>
                        <a></a>
                    `;
                }
                var cell = row.cells[0],
                clone = row.cloneNode(true);

                row.parentNode.replaceChild(clone, row);
                clone.addEventListener('click', function(){
                    setCenter(element.lat, element.lng);
                })
                row.cells[1].innerHTML = `${element.drvier_name}`;
                row.cells[2].children[0].innerHTML = `<span class="btn btn-success btn-xs">${element.description}</span>`;
                // row.cells[3].children[0].innerHTML = ` <a>${element.lat} ${element.lng}</a>`;
                status = true;
            }
        }

        if(status == false)
        {
            var newRow = location_rows.insertRow();
            var cell0 = newRow.insertCell();
            var cell1 = newRow.insertCell();
            var cell2 = newRow.insertCell();
            // var cell3 = newRow.insertCell();
            if(element.load_id != null)
            {
                cell0.innerHTML  = `
                    <input type="hidden" name="id[]" value='${element.id}'>
                    <input type="hidden" name="lat[]" value='${element.lat}'>
                    <input type="hidden" name="lat[]" value='${element.lng}'>
                    <a class="hyper" href="{{url('loads-edit')}}/${element.load_id}">${element.reference} </a>
                `;
            }else
            {
                cell0.innerHTML  = `
                    <input type="hidden" name="id[]" value='${element.id}'>
                    <input type="hidden" name="lat[]" value='${element.lat}'>
                    <input type="hidden" name="lat[]" value='${element.lng}'>
                    <a></a>
                `;
            }
            newRow.addEventListener('click', function(){
                setCenter(element.lat, element.lng);
            })
            cell1.innerHTML = `${element.drvier_name}`;
            cell2.innerHTML = `<span class="btn btn-success btn-xs">${element.description}</span>`;
            // cell3.innerHTML = ` <a>${element.lat} ${element.lng}</a>`;

        }
    });

}

function updateMap(data){

    var currentLocateData = data.location.currentLocateData;
    var currentLoacateDataIndex = null;
    console.log("Update Data Push Event")
    console.log(data)
    locations.forEach((item, key) => {
        if(item.id == currentLocateData.id)
        {
            currentLoacateDataIndex = key;
        }
    });
    var lat = parseFloat(currentLocateData.lat);
    var lng = Number(currentLocateData.lng);
    var bearing = Number(currentLocateData.bearing)
    var speed = Number(currentLocateData.speed);
    if(currentLoacateDataIndex !== null)
    {
        if(speed == 0 || speed == null)
        {
            setPoint(lat, lng, bearing, "inactive", currentLocateData, currentLoacateDataIndex);
        }else
        {
            setPoint(lat, lng, bearing, "active", currentLocateData, currentLoacateDataIndex);
        }
    }else
    {
        locations.push(currentLocateData);
        if(speed == 0 || speed == null)
        {
            addPoint(lat, lng, bearing, "inactive", currentLocateData);
        }else
        {
            addPoint(lat, lng, bearing, "active", currentLocateData);
        }
    }


    drawTable(currentLocateData);
}
// google.maps.event.addListener(map, 'click', function(event) {
// console.log(event)
// addPoint(event);
// });
function setPoint(lat, lng, bearing, status="active", item, index) {
    let path = `M256,0C114.51,0,0,114.497,0,256c0,141.49,114.497,256,256,256c141.488,0,256-114.497,256-256
			C512,114.512,397.503,0,256,0z M416.124,281.445l-102.455,32.229l-32.224,102.45c-4.717,14.994-25.624,16.213-31.935,1.676
			L137.629,160.081c-6.157-14.182,8.294-28.598,22.451-22.452L417.799,249.51C432.255,255.787,431.151,276.718,416.124,281.445z`;
    let rotation = 45 + bearing;
    if(status == "active")
    {
        fillColor = "green";
    }else
    {
        fillColor = "gray";
    }
    var icon = {
        path: path,
        fillColor: fillColor,
        backGroundColor:"white",
        fillOpacity: 1,
        //anchor: new google.maps.Point(12,-290),
        strokeWeight: 0,
        scale: .05,
        rotation: rotation
    }
    var contentString = `<b>${item.drvier_name}</b>
    <br><span><i class="fa fa-map-marker" style="color:#498ECB"></i> ${item.description}</span>
    <br><span style="color:#498ECB"><i class="fa fa-phone"></i> ${item.phone}</span>
    <br><span style="color:#498ECB"> # ${item.reference} </span>
    <br><span style="color:#498ECB"> ${item.speed} km/hr</span>`;

    markers[index].setPosition({lat:lat, lng:lng, alt:0});
    markers[index].setIcon(icon);
    infoWindows[index].setContent(contentString);

}
function addPoint(lat, lng, bearing, status="active", item) {
    console.log(lat)
    console.log(lng)
    console.log(bearing)
    rotation = 45 + bearing;

    path = `M256,0C114.51,0,0,114.497,0,256c0,141.49,114.497,256,256,256c141.488,0,256-114.497,256-256
			C512,114.512,397.503,0,256,0z M416.124,281.445l-102.455,32.229l-32.224,102.45c-4.717,14.994-25.624,16.213-31.935,1.676
			L137.629,160.081c-6.157-14.182,8.294-28.598,22.451-22.452L417.799,249.51C432.255,255.787,431.151,276.718,416.124,281.445z`;

    if(status == "active")
    {
        fillColor = "green";
    }else
    {
        fillColor = "gray";
    }

    var icon = {
        path: path,
        fillColor: fillColor,
        backGroundColor:"white",
        fillOpacity: 1,
        //anchor: new google.maps.Point(12,-290),
        strokeWeight: 0,
        scale: .05,
        rotation: rotation
    }

    var contentString = `<b>${item.drvier_name}</b>
    <br><span><i class="fa fa-map-marker" style="color:#498ECB"></i> ${item.description}</span>
    <br><span style="color:#498ECB"><i class="fa fa-phone"></i> ${item.phone}</span>
    <br><span style="color:#498ECB"> # ${item.reference}</span>
    <br><span style="color:#498ECB"> ${item.speed} km/hr</span>`;
    var infowindow = new google.maps.InfoWindow({
        content: contentString,
    });
    var marker = new google.maps.Marker({
        //position: event.latLng,
        position: {lat:lat, lng:lng},
        map: map,
        draggable: false,
        icon: icon,
    });
    marker.addListener("click", () => {
        infowindow.open({
        anchor: marker,
        map,
        shouldFocus: true,
        });
    });
    markers.push(marker);
    infoWindows.push(infowindow);
    //map.panTo([lat:lat, lng:lng]);
    //map.panTo(event.latLng);

    //rotation += 10;
}


function setMapOnAll(map) {
  for (let i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

function hideMarkers() {
  setMapOnAll(null);
}


function getTruckLocation()
{

    $.ajax({
        url: "{{ route('web-hook-list') }}",
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            locations= res.location.results;
            locations.forEach(item=> {
                var lat = parseFloat(item.lat);
                var lng = Number(item.lng);
                var bearing = Number(item.bearing)
                if(item.speed == 0 || item.speed == null)
                {
                    addPoint(lat, lng, bearing, "inactive", item);
                }else
                {
                    addPoint(lat, lng, bearing, "active", item);
                }

            });
            var location_rows = document.querySelector("#location_row");
            var rows = location_rows.rows;

            locations.forEach(element => {
                var newRow = location_rows.insertRow();
                var cell0 = newRow.insertCell();
                var cell1 = newRow.insertCell();
                var cell2 = newRow.insertCell();
                // var cell3 = newRow.insertCell();
                if(element.load_id != null)
                {
                    cell0.innerHTML  = `
                        <input type="hidden" name="id[]" value='${element.id}'>
                        <input type="hidden" name="lat[]" value='${element.lat}'>
                        <input type="hidden" name="lat[]" value='${element.lng}'>
                        <a href="{{url('loads-edit')}}/${element.load_id}" class="hyper">${element.reference}</a>
                    `;
                }else
                {
                    cell0.innerHTML  = `
                        <input type="hidden" name="id[]" value='${element.id}'>
                        <input type="hidden" name="lat[]" value='${element.lat}'>
                        <input type="hidden" name="lat[]" value='${element.lng}'>
                        <a></a>
                    `;
                }
                newRow.addEventListener('click', function(){
                    setCenter(element.lat, element.lng)

                });

                cell1.innerHTML = `${element.drvier_name}`;
                cell2.innerHTML = `<span class="btn btn-success btn-xs">${element.description}</span>`;


                // cell3.innerHTML = ` <a>${element.lat} ${element.lng}</a>`;
            });
        }
    })
}
getTruckLocation();


function setCenter(lat, lng)
{
    var lat = parseFloat(lat);
    var lng = Number(lng);

    map.setCenter({lat:lat, lng:lng, alt:0});
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


function localStringToNumber( s ){
    if(s == "")
    s = 0
    return Number(String(s).replace(/[^0-9.-]+/g,""))
}

</script>
@endpush
