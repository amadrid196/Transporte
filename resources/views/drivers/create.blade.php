@extends('layouts.main')

@push("header_top"){{--less priority css--}}
<!-- Select2 -->
{{--<link rel="stylesheet" href="{{ asset('data_theme/bower_components/select2/dist/css/select2.min.css')}}">--}}

<style>
    .box-title2 {
        background: #a5c2eb;
        border-radius: 5px;
    }
</style>
@endpush

@push("header")
    <script src="https://maps.googleapis.com/maps/api/js?key={{env("MAP_API")}}&libraries=places"></script>
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
                    <input type="hidden" name="id" class="form-control" id="driver_id" value="{{$data->id}}">
                @endif
                <div class="box-body">


                    <div class="form-group col-md-6">
                        <label for="name">{{__("tran.Full Name")}}</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="{{__("tran.Full Name")}}" value="{{isset($data) ? $data->name:""}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="dob">{{__("tran.DOB")}}</label>
                        <input type="date" class="form-control" name="dob" id="dob" placeholder="{{__("tran.DOB")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->dob)->format("Y-m-d"):""}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ssn">{{__("tran.Social Security")}}</label>
                        <input type="text" class="form-control" name="ssn" id="ssn" placeholder="{{__("tran.Social Security")}}" value="{{isset($data) ? $data->ssn:""}}">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="dob">{{__("tran.Comments")}}</label>
                        <textarea class="form-control" name="comment" id="comment" placeholder="{{__("tran.Comments")}}" >{{isset($data) ? $data->comment:""}}</textarea>
                    </div>
                  
                       
                    <div class="form-group col-md-12">
                        <label for="limg">{{__("tran.Attached Image")}}</label>
                        <input type="file" name="cimg" id="cimg"><br>
                        @if(isset($data->cimg))
                        <img src="{{asset("public/images/".$data->cimg)}}" style="max-width: 300px;max-height: 300px;">
                        @endif
                    </div>

                    <!-- TODO: new task -->
                    <div class="form-group col-md-12">
                    <span class="add-button"><a  onClick="showReportModal()"><i class="fa fa-plus"></i> &nbsp;<i><b>{{__('tran.Add Line Item')}}</b></i></a></span>
                    </div>
                    <div class="form-group col-md-12">
                    <table class="table table-borderd">
                        <tr>
                            <th class="col-xs-3">{{__('tran.Date')}}</th>
                            <th class="col-xs-3">{{__('tran.Type')}}</th>
                            <th class="col-xs-3">{{__('tran.Description')}}</th>
                            <th class="col-xs-1">{{__('tran.Attached Images')}}</th>
                        </tr>
                        <tbody  id="note_rows">
                        <script>
                            var noteCount = 0;
                        </script>
                        @if(isset($data)&&$data->notes)
                            @foreach($data->notes as $val)
                            @php
                                $imageName = "";
                                if(!empty($val->images))
                                {
                                    $images = json_decode($val->images);
                                    foreach($images as $image)
                                    {
                                        $imageName = $imageName.$image->name.", ";
                                        
                                    }
                                    

                                }
                            @endphp
                            <tr id="note_row_{{$val->id}}">
                                <td>
                                    <input 
                                        type="hidden",
                                        name="note_id[]" 
                                        
                                        value="{{$val->id}}"
                                    >
                                    <span id="note_date_{{$val->id}}">{{$val->date}}</span>
                                </td>
                                <td>
                                    <span id="note_type_{{$val->id}}">{{$val->type}}</span>
                                </td>
                                <td>
                                    <span id="note_description_{{$val->id}}">{{$val->description}}</span>
                                </td>
                                <td>
                                   <span id="note_images_{{$val->id}}"> {{$imageName}}</span>
                                </td>
                                <td>
                                    <button class="btn btn-primary  btn-xs" type="button" onclick="editNote({{$val->id}})">Edit</button>
                                    <button class="btn btn-danger  btn-xs" type="button" onclick="deleteNote({{$val->id}})">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    </div>                

                    <div class="form-group col-md-6">
                        <label for="hire_date">{{__("tran.Hire date")}}</label>
                        <input type="date" name="hire_date" class="form-control" id="hire_date" placeholder="{{__("tran.Hire date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->hire_date)->format("Y-m-d"):""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="termination">{{__("tran.Termination date")}}</label>
                        <input type="date" name="termination" class="form-control" id="termination" placeholder="{{__("tran.Termination date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->termination)->format("Y-m-d"):""}}">
                    </div>


                    <div class="form-group col-md-6">
                        <label for="contact">{{__("tran.Contact Number")}}</label>
                        <input type="text" name="contact" class="form-control" id="contact" placeholder="{{__("tran.Contact Number")}}" value="{{isset($data) ? $data->contact:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="emp_id">{{__("tran.Employee ID")}}</label>
                        <input type="text" name="emp_id" class="form-control" id="emp_id" placeholder="{{__("tran.Employee ID")}}" value="{{isset($data) ? $data->emp_id:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="rate">{{__("tran.Rate Per Mile")}}</label>
                        <input type="number" step="0.01" name="rate" class="form-control" id="rate" placeholder="{{__("tran.Rate Per Mile")}}" value="{{isset($data) ? $data->rate:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="number">{{__("tran.Email")}}</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="{{__("tran.Email")}}..." value="{{isset($data) ? $data->email:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="license_no">{{__("tran.License No.")}}</label>
                        <input type="text" name="license_no" class="form-control" id="license_no" placeholder="{{__("tran.License No.")}}" value="{{isset($data) ? $data->license_no:""}}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{__("tran.Status")}}</label>

                        <div class="custom-control custom-radio inline">
                            <input class="custom-control-input" type="radio" id="customRadio1" name="status" value="active" {{isset($data) ? (($data->status=="active") ?"checked":""):"checked"}}>
                            <label for="customRadio1" class="custom-control-label">{{__("tran.Active")}}</label>
                        </div>
                        <div class="custom-control custom-radio inline">
                            <input class="custom-control-input" type="radio" id="customRadio2" name="status" value="inactive" {{isset($data) ? (($data->status=="inactive") ?"checked":""):""}}>
                            <label for="customRadio2" class="custom-control-label">{{__("tran.Inactive")}}</label>
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="address">{{__("tran.Address")}}</label>
                        <textarea name="address" class="form-control" id="address" placeholder="Enter address" autocomplete="off" data-language="fr" >{{isset($data) ? $data->address:""}}</textarea>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="emergency">{{__("tran.Emergency Contact Details")}}</label>
                        <textarea name="emergency" class="form-control" id="emergency" placeholder="{{__("tran.Emergency Contact Details")}}">{{isset($data) ? $data->emergency:""}}</textarea>
                    </div>

        
                </div>

                <div class="row">

                    {{--license--}}
                    <div class="col-md-6">
                        <div class="col-md-12 box-title2">
                            <h4>{{__("tran.Driver")}} {{__("tran.License")}}</h4>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="limg">{{__("tran.Driver")}} {{__("tran.License Image")}}</label>
                            <input type="file" name="limg" id="limg">
                        </div>

                        @if(isset($data->limg))
                            <img src="{{asset("public/images/".$data->limg)}}" style="max-width: 300px;max-height: 300px;">
                        @endif

                        <div class="form-group col-md-12">
                            <label for="lexpiration">{{__("tran.Expiration date")}}</label>
                            <input type="date" name="lexpiration" class="form-control" id="lexpiration" placeholder="{{__("tran.Expiration date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->lexpiration)->format("Y-m-d"):""}}">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="lissue">{{__("tran.Issued date")}}</label>
                            <input type="date" name="lissue" class="form-control" id="lissue" placeholder="{{__("tran.Issued date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->lissue)->format("Y-m-d"):""}}">
                        </div>
                    </div>
                    {{--medical card--}}
                    <div class="col-md-6">
                        <div class="col-md-12 box-title2">
                            <h4>{{__("tran.Medical Card")}}</h4>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="dimg">{{__("tran.Medical Card Image")}}</label>
                            <input type="file" name="dimg" id="dimg">
                        </div>

                        @if(isset($data->dimg))
                        <img src="{{asset("public/images/".$data->dimg)}}" style="max-width: 300px;max-height: 300px;">
                            @endif
                        <div class="form-group col-md-12">
                            <label for="dexpiration">{{__("tran.Expiration date")}}</label>
                            <input type="date" name="dexpiration" class="form-control" id="dexpiration" placeholder="{{__("tran.Expiration date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->dexpiration)->format("Y-m-d"):""}}">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="dissue">{{__("tran.Issued date")}}</label>
                            <input type="date" name="dissue" class="form-control" id="dissue" placeholder="{{__("tran.Issued date")}}" value="{{isset($data) ? \Carbon\Carbon::create($data->dissue)->format("Y-m-d"):""}}">
                        </div>

                    </div>

                </div>


                <div class="box-footer text-center">
                    <button type="submit" class="btn btn-primary">{{__("tran.Submit")}}</button>
                </div>
            </form>
        </div>

        
    </div>


<div id="report_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{__("tran.Note Create")}}</h4>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" id="note-form">
            @csrf
            <div class="modal-body">
                <div class="box-body">
                    <input type="hidden" id="report_id" />
                    <div class="form-group">
                        <label for="report_date">{{__("tran.Date")}}</label>
                        <input  type="date" name="report_date" class="form-control" id="report_date" placeholder="Enter number" value="" required>
                    </div>

                    <div class="form-group">
                        <label for="report_type">{{__("tran.Type")}}</label>
                        <input  type="text" name="report_type" class="form-control" id="report_type" placeholder="Type" value="" required>
                    </div>

                    <div class="form-group">
                        <label for="number">{{__("tran.Description")}}</label>
                        <input  type="text" name="report_description" class="form-control" id="report_description" placeholder="Description" value="" required>
                    </div>
                    <div class="form-group">
                    <label for="number">{{__("tran.Attached Images")}}</label>
                    <div class="input-images"></div>
                    </div>
                    
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" > Add</button>
            </div>
            </form>
            
        </div>
    </div>
</div>
@endsection

@push("footer")
<script>

   
    
    
    function showReportModal(id=0)
    {   
        
        $('.input-images').text("");
        $('.input-images').imageUploader();
        
        $('#report_modal').modal('show');
        $('#report_id').val(id);
    }    

    function addNote(data,id)
    {
        console.log(id)
        var images = JSON.parse(data["images"])
        var imageNames = ""
        images.forEach(item=> {
            imageNames += item.name + ", ";
        })
        if(id == 0)
        {
            var row ='<tr id="note_row_'+data.id+'">'+
                    '<td>'+
                        '<input type="hidden" name="note_id[]" value='+data.id+'>'+
                        '<span id="note_date_'+data.id+'">'+data.date+'</span>'+
                    '</td>'+
                    '<td>'+
                    '   <span id="note_type_'+data.id+'">'+data.type+'</span>'+
                    '</td>'+
                    '<td>'+
                        '<span id="note_description_'+data.id+'">'+data.description+'</span>'+
                    '</td>'+
                    '<td>'+
                        '<span id="note_images_'+data.id+'">'+imageNames+'</span>'+
                    '</td>'+
                    '<td>'+
                        '<button class="btn btn-primary btn-xs" type="button" onclick="editNote('+data.id+')">Edit</button>'+
                        '<button class="btn btn-danger  btn-xs" type="button" onclick="deleteNote('+data.id+')">Delete</button>'+
                    '</td>'+
                '</tr>'
    
            $('#note_rows').append(row);
        }else
        {
            document.getElementById('note_date_'+id).text = data.date;
            document.getElementById('note_type_'+id).text = data.type;
            document.getElementById('note_description_'+id).text = data.description;
            document.getElementById('note_images_'+id).text = imageNames;
        }
        
    }   

    
    function editNote(id)
    {
        $.ajax({
            type:'GET',
            url: "{{route('app_url')}}/drivers/editnote/"+id,
            success: function(response)
            {
                if(response.status)
                {
                    var note = response.data;
                    var images = JSON.parse(note.images)
                    var imageArray = [];
                    images.forEach((item, index)=> {
                        imageArray.push({
                           id:item.name, 
                           src:'{{route('app_url')}}/public/'+item.path+'/'+item.name
                        })
                    })
                    $('.input-images').text("");
                    $('.input-images').imageUploader({
                        preloaded: imageArray,
                    })
                    $('#report_date').val(note.date)
                    $('#report_type').val(note.type)
                    $('#report_description').val(note.description)
                    $('#report_id').val(id);
                    $('#report_modal').modal('show');
       
                }
            }
        })
    }

    function deleteNote(id)
    {
        if(confirm("Are you want to delete this item?"))
        {
            $.ajax({
                type:'GET',
                url: "{{route('app_url')}}/drivers/deletenote/"+id,
                cache:false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: (response) => {
                    if(response.status)
                    {
                        alert("Item Deleted Succsfully");
                        $('#note_row_'+id).remove();
                    }else
                    {

                    }   
                }
            })
        }
    }

    $(document).on("submit", "#note-form", function(e){
     
        e.preventDefault();
        var formData = new FormData(this);
        var id = $('#report_id').val();
        let TotalFiles = $('input[name="images[]"]')[0].files.length; //Total files
        
        let files = $('input[name="images[]"]')[0];
        let driver_id = $('#driver_id').val();
        for (let i = 0; i < TotalFiles; i++) {
            formData.append('images' + i, files.files[i]);
        }
        
        formData.append('TotalFiles', TotalFiles);
        $.ajax({
            type:'POST',
            url: "{{route('app_url')}}/drivers/storenote/"+id,
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: (response) => {
                if(response.status)
                {
                    var note = response["data"];
                    addNote(note,id);
                    this.reset()
                    $('#report_modal').modal('hide');
                }else
                {
                   alert("error")
                }
            },
            error: function(data){
               
            }
        })
    })
</script>

@endpush