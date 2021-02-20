@extends('layouts.app')

@section('content')

<div class=row>



    <div class="col-lg-6 col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="m-b-0 text-white">Manage Devices</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class=row>
                        <input type="hidden" class="form-control" id=deviceID value=0>
                        <div class=col-12>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-donate"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Name*" id=deviceName required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=row>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="submitDeviceForm()" class="btn btn-success">Submit</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="cancelDeviceForm()" class="btn btn-dark">Cancel</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" style="display: none" id="deleteDeviceButton" class="btn btn-danger mr-2">Delete</button>
                        </div>
                    </div>
                </form>
                <hr>
                <label>Devices</label>
                <ul class="list-group" id=deviceList>
                    @foreach($devices as $device)
                    <a id="deviceItem{{$device->id}}" href="javascript:void(0)" onclick="setupEditDevice({{$device->id}})" class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-dark" id="deviceName{{$device->id}}">{{$device->DVIC_NAME}}</h5>
                        </div>
                    </a>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="m-b-0 text-white">Manage Areas</h4>
            </div>
            <div class="card-body">
                <form>
                    <input type="hidden" class="form-control" id=areaID value=0>
                    <div class=row>
                        <div class=col-12>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-list"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Name*" id=areaName required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=row>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="submitAreaForm()" class="btn btn-success mr-2">Submit</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="cancelAreaForm()" class="btn btn-dark mr-2">Cancel</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" style="display: none" id="deleteAreaButton" class="btn btn-danger mr-2">Delete</button>
                        </div>
                    </div>
                </form>
                <hr>
                <label>Areas</label>
                <ul class="list-group" id=areaList>
                    @foreach($areas as $area)
                    <a id="areaItem{{$area->id}}" href="javascript:void(0)" onclick="setupEditArea({{$area->id}})"
                        class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-dark" id="areaName{{$area->id}}">{{$area->AREA_NAME}}</h5>
                        </div>
                    </a>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_content')
    
<script>

///////devices functions
function submitDeviceForm() {
        var deviceID = document.getElementById('deviceID')
        var deviceName = document.getElementById('deviceName')

        formBool = deviceName.reportValidity();
        if(!formBool) return;

        var deviceID = deviceID.value
        var deviceName = deviceName.value

        var formData = new FormData();
        formData.append('_token','{{ csrf_token() }}');
        formData.append("name", deviceName)

        var url;
        if(deviceID > 0) {
            url = "{{$editDeviceURL}}"
            formData.append("id", deviceID)
        }
        else 
            url = "{{$addDeviceURL}}"

        var http = new XMLHttpRequest();
        http.open("POST", url)

        http.onreadystatechange = function (){
            if(this.readyState=4 && this.status == 200 && IsNumeric(this.responseText) ){
                    Swal.fire({
                        title: "Success!",
                        text: "Device successfully added",
                        icon: "success"
                    })
                    if(deviceID > 0) {
                        editDeviceItem(deviceID, deviceName)
                    } else {
                        addDeviceItem(this.responseText, deviceName)
                    }      
            } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong!",
                        icon: "error"
                    })
                }
            }

        http.send(formData)
    }
  


    function addDeviceItem(id, name){
        var deviceList = document.getElementById('deviceList')
        deviceList.innerHTML += '  <a id="deviceItem'+id+'" href="javascript:void(0)" onclick="setupEditDevice('+id+')"\
                class="list-group-item list-group-item-action flex-column align-items-start">\
                        <div class="d-flex w-100 justify-content-between">\
                            <h5 class="mb-1 text-dark" id="deviceName'+id+'">'+name+'</h5>\
                        </div>\
                    </a>';
        
        cancelDeviceForm(false)
    }


    function editDeviceItem(id, name){
        var deviceListItem = document.getElementById('deviceItem' +id)
        deviceListItem.innerHTML = ' <div class="d-flex w-100 justify-content-between">\
                            <h5 class="mb-1 text-dark" id="deviceName'+id+'">'+name+'</h5>\
                        </div>'
        cancelDeviceForm(false)
    }

    function setupEditDevice(id){
        document.getElementById('deviceID').value   = id
        document.getElementById('deviceName').value = document.getElementById('deviceName' + id).innerHTML

        var deleteDeviceButton = document.getElementById('deleteDeviceButton')
        deleteDeviceButton.style = "display: block"
        deleteDeviceButton.setAttribute( "onClick", "deleteDeviceItem(" + id + ")" );
        $.toast({
                    heading: 'Device Data loaded',
                    text: 'You can now edit device data.',
                    position: 'top-right',
                    loaderBg:'blue',
                    icon: 'success',
                    hideAfter: 1000, 
                    stack: 6,
                    type: 'success'
                    });
    }
    

    function deleteDeviceItem(id){
        Swal.fire({
            title: "Warning!",
            text: "Are you sure you want to delete this device and its data from pricing?",
            icon: "warning"
        }).then((confirmed) => {
            if(confirmed.value) {
                var deviceListItem = document.getElementById('deviceItem' +id)
                var formData = new FormData();
                formData.append('_token','{{ csrf_token() }}');
                formData.append("id", id)
                var http = new XMLHttpRequest();
                http.open("POST", "{{$delDeviceURL}}")
                http.onreadystatechange = function (){
                    if(this.readyState=4 && this.status == 200 && IsNumeric(this.responseText) ){
                            Swal.fire({
                                title: "Success!",
                                text: "Device successfully deleted, please refresh to update the plans table",
                                icon: "success"
                            })    
                            deviceListItem.style = "display: none"
                    } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Oops Something went wrong!",
                                icon: "error"
                            })
                        }
                    }
                    http.send(formData)

                    cancelDeviceForm(false)
            }
        })

    }
    
        
    function cancelDeviceForm(showToast=true){
        document.getElementById('deviceID').value   = 0
        document.getElementById('deviceName').value = ""

        var deleteDeviceButton = document.getElementById('deleteDeviceButton')
        deleteDeviceButton.style = "display: none"
        deleteDeviceButton.onclick = ""
        if(showToast)
        $.toast({
                    heading: 'Device Data cleared',
                    position: 'top-right',
                    loaderBg:'blue',
                    icon: 'success',
                    hideAfter: 1000, 
                    stack: 6,
                    type: 'success'
                    });
    }
    

    ///////area functions
    function submitAreaForm(){
        var areaID = document.getElementById('areaID')
        var areaName = document.getElementById('areaName')

        formBool = areaName.reportValidity();
        if(!formBool) return;

        var areaID = areaID.value
        var areaName = areaName.value

        var formData = new FormData();
        formData.append('_token','{{ csrf_token() }}');
        formData.append("name", areaName)

        var url;
        if(areaID > 0) {
            url = "{{$editAreaURL}}"
            formData.append("id", areaID)
        }
        else 
            url = "{{$addAreaURL}}"

        var http = new XMLHttpRequest();
        http.open("POST", url)

        http.onreadystatechange = function (){
            if(this.readyState=4 && this.status == 200 && IsNumeric(this.responseText) ){
                    Swal.fire({
                        title: "Success!",
                        text: "Area successfully added",
                        icon: "success"
                    })
                    if(areaID > 0) {
                        editAreaItem(areaID, areaName)
                    } else {
                        addAreaItem(this.responseText, areaName)
                    }      
            } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Oops Something went wrong!",
                        icon: "error"
                    })
                }
            }

        http.send(formData)

    }
    

    function addAreaItem(id, name){
        var areaList = document.getElementById('areaList')

        areaList.innerHTML += '  <a id="areaItem'+id+'" href="javascript:void(0)" onclick="setupEditArea('+id+')"\
                class="list-group-item list-group-item-action flex-column align-items-start">\
                        <div class="d-flex w-100 justify-content-between">\
                            <h5 class="mb-1 text-dark" id="areaName'+id+'">'+name+'</h5>\
                        </div>\
                    </a>';
        
        cancelAreaForm(false)
    }

    

    function editAreaItem(id, name){
        var areaListItem = document.getElementById('areaItem' +id)
        areaListItem.innerHTML = ' <div class="d-flex w-100 justify-content-between">\
                            <h5 class="mb-1 text-dark" id="areaName'+id+'">'+name+'</h5>\
                        </div>';
        cancelAreaForm(false)
    }

    function setupEditArea(id){
        document.getElementById('areaID').value   = id
        document.getElementById('areaName').value = document.getElementById('areaName' + id).innerHTML

        var deleteAreaButton = document.getElementById('deleteAreaButton')
        deleteAreaButton.style = "display: block"
        deleteAreaButton.setAttribute( "onClick", "deleteAreaItem(" + id + ")" );
        $.toast({
                    heading: 'Area Data loaded',
                    text: 'You can now edit area data.',
                    position: 'top-right',
                    loaderBg:'blue',
                    icon: 'success',
                    hideAfter: 1000, 
                    stack: 6,
                    type: 'success'
                    });
    }

    function deleteAreaItem(id){
        Swal.fire({
                title: "Warning!",
                text: "Are you sure you want to delete this device and its data from pricing?",
                icon: "warning"
            }).then((confirmed) => {
                var areaListItem = document.getElementById('areaItem' +id)
                var formData = new FormData();
                formData.append('_token','{{ csrf_token() }}');
                formData.append("id", id)
                var http = new XMLHttpRequest();
                http.open("POST", "{{$delAreaURL}}")
                http.onreadystatechange = function (){
                    if(this.readyState=4 && this.status == 200 && IsNumeric(this.responseText) ){
                            Swal.fire({
                                title: "Success!",
                                text: "Area successfully deleted",
                                icon: "success"
                            })    
                            areaListItem.style = "display: none"
                    } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Oops Something went wrong!",
                                icon: "error"
                            })
                        }
                    }

                http.send(formData)

                cancelAreaForm(false)
        })
    }

    function cancelAreaForm(showToast=true){
        document.getElementById('areaID').value   = 0
        document.getElementById('areaName').value = ""

        var deleteAreaButton = document.getElementById('deleteAreaButton')
        deleteAreaButton.style = "display: none"
        deleteAreaButton.onclick = ""
        if(showToast)
        $.toast({
                    heading: 'Area Data cleared',
                    position: 'top-right',
                    loaderBg:'blue',
                    icon: 'success',
                    hideAfter: 1000, 
                    stack: 6,
                    type: 'success'
                    });
    }

    function IsNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    } 


</script>
@endsection