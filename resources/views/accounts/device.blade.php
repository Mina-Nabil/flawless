@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$formTitle}}</h4>
                <h6 class="card-subtitle">{{$formSubtitle}}</h6>

                <div class="col-12 form-group">
                    <label>Device</label>
                    <select class="select2 form-control  col-md-12 mb-3" style="width:100%" id=deviceID>
                        @foreach($devices as $device)
                        <option value="{{$device->id}}"> {{$device->DVIC_NAME}}</option>
                        @endforeach
                    </select>
                    <small class="text-danger">{{$errors->first('deviceID')}}</small>
                </div>

                <div class=row>
                    <div class="col-6 form-group">
                        <label>From*</label>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" id=from>
                        </div>
                    </div>


                    <div class="col-6 form-group">
                        <label>To*</label>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" id=to>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Total</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id=loadHere readonly>
                    </div>
                </div>

                <button type="button" onclick="loadTotal()" class="btn btn-success mr-2">Load</button>


            </div>
        </div>
    </div>

</div>

@endsection

@section('js_content')

<script>
    function loadTotal(){
    var device      =   $('#deviceID').val();
    var from        =   $('#from').val();
    var to      =   $('#to').val();

    var formData = new FormData();
    formData.append('_token','{{ csrf_token() }}');
    formData.append("deviceID", device)
    formData.append("from", from)
    formData.append("to", to)

    var url = "{{$getDeviceTotal}}";

    var http = new XMLHttpRequest();
    http.open("POST", url);
    http.setRequestHeader("Accept", "application/json");

    http.onreadystatechange = function (){
    if(this.readyState==4 && this.status == 200 && isJson(this.responseText) ){
        var res = JSON.parse(this.responseText)
        $('#loadHere').val(res["total"]);
   
    } else if(this.readyState==4 && this.status == 422 && isJson(this.responseText)) {
        try {
            var errors = JSON.parse(this.responseText)
            var errorMesage = "" ;
            if(errors.errors["deviceID"]){
                errorMesage += errors.errors.deviceID + " " ;
            }
            if(errors.errors["from"]){
                errorMesage += errors.errors["from"] + " " ;
            }
            if(errors.errors["to"]){
                errorMesage += errors.errors["to"] + " " ;
            }
         
            errorMesage = errorMesage.replace('deviceID', 'Selected Device')
            errorMesage = errorMesage.replace('from', 'From Date')
            errorMesage = errorMesage.replace('to', 'To Date')

            Swal.fire({
                title: "Error!",
                text: errorMesage ,
                icon: "error"
            })
        } catch (r){
            console.log(r)
            Swal.fire({
                title: "Error!",
                text: "Oops Something went wrong! Please try again",
                icon: "error"
            })
        }
        } else if(this.readyState==4) {
            Swal.fire({
                title: "Error!",
                text: "Oops Something went wrong! Please try again",
                icon: "error"
            })
        }
    }

    http.send(formData)
}
</script>
@endsection