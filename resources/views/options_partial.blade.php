@if($data)
    <option value="">{{__("tran.Please Select")}}</option>

    @foreach($data as $key=>$value)
        <option value="{{$key}}" {{$selected==$key ? "selected":""}}>{{$value}}</option>
    @endforeach
@else
    <option>No Data Found</option>
@endif