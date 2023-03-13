@extends('layouts.app')

@section('content')

<div class=row>



    <div class="col-lg-6 col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="m-b-0 text-white">Available Pricelists</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class=row>
                        <input type="hidden" class="form-control" id=pricelistID value=0>
                        <div class=col-6>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-donate"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Name*" id=pricelistName required>
                                </div>
                            </div>
                        </div>
                        <div class=col-6>
                            <div class="form-group bt-switch">
                                <div class=" m-b-15">
                                    <input type="checkbox" data-size="small" id=defaultPricelist data-on-color="success" data-off-color="danger" data-on-text="Default" data-off-text="No"
                                        name="isDefault">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=row>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="submitPricelistForm()" class="btn btn-success">Submit</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="cancelPricelistForm()" class="btn btn-dark">Cancel</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" style="display: none" id="deletePricelistButton" class="btn btn-danger mr-2">Delete</button>
                        </div>
                    </div>
                </form>
                <hr>
                <label>Pricelists</label>
                <ul class="list-group" id=pricelistList>
                    @foreach($pricelists as $pricelist)
                    <a id="pricelistItem{{$pricelist->id}}" href="javascript:void(0)" onclick="setupEditPricelist({{$pricelist->id}})"
                        class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-dark" id="pricelistName{{$pricelist->id}}">{{$pricelist->PRLS_NAME}}</h5>
                            <small id="defaultPricelist{{$pricelist->id}}" class="defaultText">{{$pricelist->PRLS_DFLT ? "Default" : "Not Default"}}</small>
                        </div>
                    </a>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-12" id="itemsDiv" style="display: none">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="m-b-0 text-white">Pricelist Items</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{$syncPricelistItemsURL}}">
                    @csrf
                    <input id=selectedPricelistID name=id type="hidden">
                    <div id="itemsContainer"> </div>
                    <div class="col-12 m-r-10">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js_content')

<script>
    ///////pricelists functions
    function submitPricelistForm() {
        var pricelistID = document.getElementById('pricelistID')
        var pricelistName = document.getElementById('pricelistName')
        var defaultPricelist = document.getElementById('defaultPricelist')

        formBool = pricelistName.reportValidity();
        if(!formBool) return;

        var pricelistID = pricelistID.value
        var pricelistName = pricelistName.value
        var pricelistDefault = defaultPricelist.checked
    
        var formData = new FormData();
        formData.append('_token','{{ csrf_token() }}');
        formData.append("name", pricelistName)
        formData.append("isDefault", pricelistDefault)

        var url;
        if(pricelistID > 0) {
            url = "{{$editPricelistURL}}"
            formData.append("id", pricelistID)
        }
        else 
            url = "{{$addPricelistURL}}"

        var http = new XMLHttpRequest();
        http.open("POST", url)

        http.onreadystatechange = function (){
            if(this.readyState=4 && this.status == 200 && IsNumeric(this.responseText) ){
                    Swal.fire({
                        title: "Success!",
                        text: "Pricelist successfully added",
                        icon: "success"
                    })
                    if(pricelistID > 0) {
                        editPricelistItem(pricelistID, pricelistName, pricelistDefault)
                    } else {
                        addPricelistItem(this.responseText, pricelistName, pricelistDefault)
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
  
    function addPricelistItem(id, name, isDefault){
        var pricelistList = document.getElementById('pricelistList')
        pricelistList.innerHTML += '  <a id="pricelistItem'+id+'" href="javascript:void(0)" onclick="setupEditPricelist('+id+')"\
                class="list-group-item list-group-item-action flex-column align-items-start">\
                        <div class="d-flex w-100 justify-content-between">\
                            <h5 class="mb-1 text-dark" id="pricelistName'+id+'">'+name+'</h5>\
                            <small id="defaultPricelist'+id+'" class="defaultText">' + ((isDefault) ? "Default" : "Not Default") +'</small>\
                        </div>\
                    </a>';
         if(isDefault){
            setDefaultValue(id)
        }
        cancelPricelistForm(false)
    }

    function editPricelistItem(id, name, isDefault){
        var pricelistListItem = document.getElementById('pricelistItem' +id)
        pricelistListItem.innerHTML = ' <div class="d-flex w-100 justify-content-between">\
                                            <h5 class="mb-1 text-dark" id="pricelistName'+id+'">'+name+'</h5>\
                                            <small id="defaultPricelist'+id+'" class="defaultText">' + ((isDefault) ? "Default" : "Not Default") +'</small>\
                                        </div>'
        if(isDefault){
            setDefaultValue(id)
        }
        cancelPricelistForm(false)
    }

    function setupEditPricelist(id){
        document.getElementById('pricelistID').value   = id
        document.getElementById('pricelistName').value = document.getElementById('pricelistName' + id).innerHTML
        var defaultStr = document.getElementById('defaultPricelist' + id).innerHTML
        if(defaultStr == "Default") {
            $('#defaultPricelist').bootstrapSwitch('state', true, true);
        } else {
            $('#defaultPricelist').bootstrapSwitch('state', false, true);
        }
    

        var deletePricelistButton = document.getElementById('deletePricelistButton')
        deletePricelistButton.style = "display: block"
        deletePricelistButton.setAttribute( "onClick", "deletePricelistItem(" + id + ")" );
        loadItems(id);
        $.toast({
                    heading: 'Pricelist Data loaded',
                    text: 'You can now edit pricelist data.',
                    position: 'top-right',
                    loaderBg:'blue',
                    icon: 'success',
                    hideAfter: 1000, 
                    stack: 6,
                    type: 'success'
                    });
    }   

    function deletePricelistItem(id){
        Swal.fire({
            title: "Warning!",
            text: "Are you sure you want to delete this pricelist?",
            icon: "warning"
        }).then((confirmed) => {
            if(confirmed.value) {
                var pricelistListItem = document.getElementById('pricelistItem' +id)
                var formData = new FormData();
                formData.append('_token','{{ csrf_token() }}');
                formData.append("id", id)
                var http = new XMLHttpRequest();
                http.open("POST", "{{$delPricelistURL}}")
                http.onreadystatechange = function (){
                    if(this.readyState=4 && this.status == 200 && IsNumeric(this.responseText) ){
                            Swal.fire({
                                title: "Success!",
                                text: "Pricelist successfully deleted",
                                icon: "success"
                            })    
                            pricelistListItem.style = "display: none"
                    } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Oops Something went wrong!",
                                icon: "error"
                            })
                        }
                    }
                    http.send(formData)

                    cancelPricelistForm(false)
            }
        })

    }
          
    function cancelPricelistForm(showToast=true){
        document.getElementById('pricelistID').value   = 0
        document.getElementById('pricelistName').value = ""
        $('#defaultPricelist').bootstrapSwitch('state', false, true);

        var deletePricelistButton = document.getElementById('deletePricelistButton')
        deletePricelistButton.style = "display: none"
        deletePricelistButton.onclick = ""
        if(showToast)
        $.toast({
                    heading: 'Pricelist Data cleared',
                    position: 'top-right',
                    loaderBg:'blue',
                    icon: 'success',
                    hideAfter: 1000, 
                    stack: 6,
                    type: 'success'
                    });
    }

    function setDefaultValue(id){
        var defaults = $('.defaultText')

        defaults.each( function(index, defaya){
            console.log(defaya.id)
           if(defaya.id.localeCompare("defaultPricelist" + id ) != 0 ) {
            defaya.innerHTML = "Not Default"
           } else {
               defaya.innerHTML = "Default"
           }
        } )
    }

    function loadItems(id){
        room = 1;
        $("#itemsContainer").html("")
        $("#itemsDiv").css("display", "block");
        $("#selectedPricelistID").val(id)
 
        var formData = new FormData();
        formData.append('_token','{{ csrf_token() }}');
        formData.append("id", id)

        var url = "{{$getPricelistItems}}"

        var http = new XMLHttpRequest();
        http.open("POST", url, false)

        http.onreadystatechange = function (){
            if(this.readyState=4 && this.status == 200 && isJson(this.responseText) ){          
                items = JSON.parse(this.responseText)
                console.log(items)
                items.forEach(function (itemaya, index){
                    addListItemRow(itemaya.PLIT_DVIC_ID, itemaya.PLIT_AREA_ID, itemaya.PLIT_PRCE, itemaya.PLIT_DURT);
                })
            }
        }

        http.send(formData)

        if(room==1  )
        addListItemRow();
    }

    function addListItemRow(deviceID = null, areaID = null, price = null, duration = null){

        room++;
        var objTo = document.getElementById('itemsContainer')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "nopadding row col-lg-12 removeclass" + room);
        var rdiv = 'removeItem' + room;
        var concatString = "";
        concatString +=   '<div class=row>\
                            <div class=col-3>\
                                <div class="form-group">\
                                    <div class="input-group mb-3">\
                                        <select name=device[] class="select form-control custom-select" style="width: 100%; height:36px;" required>\
                                            <option value="" disabled selected>Devices</option>"'
                                            @foreach($devices as $device)
                                            concatString += '<option value="{{ $device->id }}" ' + (({{ $device->id }} == deviceID) ? 'selected' : '' ) +'  >{{$device->DVIC_NAME}}</option>'
                                            @endforeach
                                            concatString += '</select>\
                                    </div>\
                                </div>\
                            </div>';

            concatString += '<div class=col-3>\
                                <div class="form-group">\
                                    <div class="input-group mb-3">\
                                        <select name=service[] class="select form-control custom-select" style="width: 100%; height:36px;" required>\
                                            <option value="" disabled selected>Services</option>\
                                            <option value="-1"  ' +  ((areaID == -1) ? 'selected' : '' )  +  '  >Pulse</option>\
                                            <option value="0" ' +  ((areaID == 0) ? 'selected' : '' )  +  ' >Session</option>'
                                            @foreach($areas as $area)
                                            concatString += ' <option value="{{ $area->id }}" ' + (({{ $area->id }} == areaID) ? 'selected' : '' ) +'   >{{$area->AREA_NAME}}</option> '
                                            @endforeach
                    concatString +=   '</select>\
                                    </div>\
                                </div>\
                            </div>';
            
        concatString +=    `    <div class="col-lg-3">\
                                    <div class="input-group mb-3">\
                                        <input type="number" step="1" class="form-control" placeholder="Service Duration*" name=duration[] value="` + duration + `" required>
                                    </div>
                                </div>
                                    `
        concatString +=    '    <div class="col-lg-3">\
                                <div class="input-group mb-3">\
                                    <input type="number" step="0.01" class="form-control" placeholder="Service Price*" name=price[] value="' + price + '" required>\
                                    <div class="input-group-append">'
        if(room == 2)
        concatString +=    '   <button class="btn btn-success" type="button" onclick="addListItemRow();"><i class="fa fa-plus"></i></button>' 
        else
        concatString +=    '   <button class="btn btn-danger" type="button" onclick="removeListItemRow('+room+');"><i class="fa fa-minus"></i></button>'
        concatString +=    '</div>\
                                </div>\
                            </div>\
                        </div>';
        
        divtest.innerHTML = concatString;
        
        objTo.appendChild(divtest);
    }

    function removeListItemRow(rid) {
        $('.removeclass' + rid).remove();
    }

    function IsNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    } 


</script>
@endsection