@extends('layouts.app')

@section('content')

<div class=row>
    <div class="col-lg-12 col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h4 class="m-b-0 text-white">Manage Patient Messages</h4>
            </div>
            <div class="card-body">
                <form>
                    <input type="hidden" class="form-control" id=patientMessageID value="{{ $selectedMessage ? $selectedMessage->id : 0 }}">
                    <div class=row>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>Device*</label>
                                <select class="form-control" id=deviceSelect required>
                                    <option value="">Select Device</option>
                                    @foreach($devices as $device)
                                    <option value="{{$device->id}}" {{ $selectedMessage && $selectedMessage->PTMS_DVIC_ID == $device->id ? 'selected' : '' }}>
                                        {{$device->DVIC_NAME}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>Area (Optional)</label>
                                <select class="form-control" id=areaSelect>
                                    <option value="">Select Area (Optional)</option>
                                    @foreach($areas as $area)
                                    <option value="{{$area->id}}" {{ $selectedMessage && $selectedMessage->PTMS_AREA_ID == $area->id ? 'selected' : '' }}>
                                        {{$area->AREA_NAME}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class=row>
                        <div class=col-12>
                            <div class="form-group">
                                <label>Message*</label>
                                <textarea class="form-control" id=messageText rows="4" placeholder="Enter message text... Use {patient} to insert patient name dynamically" required>{{ $selectedMessage ? $selectedMessage->PTMS_MSSG : '' }}</textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Use <code>{patient}</code> placeholder to dynamically insert the patient's name. Use <code>\r\n</code> for new lines. Example: "Hello {patient}, your session is ready!\r\nSee you soon!"
                                </small>
                                <div class="mt-2 p-2 bg-light rounded" id="messagePreview" style="display: none;">
                                    <strong>Preview:</strong> 
                                    <div class="text-info" id="messagePreviewText" style="white-space: pre-line;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=row>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="submitPatientMessageForm()" class="btn btn-success">Submit</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" onclick="cancelPatientMessageForm()" class="btn btn-dark">Cancel</button>
                        </div>
                        <div class="col-2 m-r-10">
                            <button type="button" style="display: {{ $selectedMessage ? 'block' : 'none' }}" id="deletePatientMessageButton" class="btn btn-danger mr-2">Delete</button>
                        </div>
                    </div>
                </form>
                <hr>
                <label>Patient Messages</label>
                <ul class="list-group" id=patientMessageList>
                    @foreach($patientMessages as $message)
                    <a id="patientMessageItem{{$message->id}}" href="javascript:void(0)" onclick="setupEditPatientMessage({{$message->id}})" class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <div>
                                <h5 class="mb-1 text-dark">
                                    <strong>Device:</strong> {{$message->device->DVIC_NAME ?? 'N/A'}}
                                    @if($message->area)
                                    | <strong>Area:</strong> {{$message->area->AREA_NAME}}
                                    @endif
                                </h5>
                                <p class="mb-1" id="patientMessageText{{$message->id}}">
                                    <strong>Original:</strong> 
                                    <span style="white-space: pre-line;">{{ strlen($message->PTMS_MSSG) > 100 ? substr($message->PTMS_MSSG, 0, 100) . '...' : str_replace('\r\n', "\r\n", $message->PTMS_MSSG) }}</span>
                                </p>
                                @if($sampleSession)
                                <p class="mb-1 text-info" style="font-size: 0.9em;">
                                    <strong>Preview:</strong> 
                                    @php
                                        $previewText = $message->getMessageForSession($sampleSession);
                                        $previewDisplay = strlen($previewText) > 100 ? substr($previewText, 0, 100) . '...' : $previewText;
                                    @endphp
                                    <span style="white-space: pre-line;">{{ $previewDisplay }}</span>
                                </p>
                                @endif
                            </div>
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

var patientMessagesData = {
    @foreach($patientMessages as $message)
    {{$message->id}}: {
        device_id: {{$message->PTMS_DVIC_ID}},
        area_id: {{$message->PTMS_AREA_ID ?? 'null'}},
        message: {!! json_encode($message->PTMS_MSSG) !!}
    },
    @endforeach
};

function submitPatientMessageForm() {
    var patientMessageID = document.getElementById('patientMessageID')
    var deviceSelect = document.getElementById('deviceSelect')
    var areaSelect = document.getElementById('areaSelect')
    var messageText = document.getElementById('messageText')

    formBool = deviceSelect.reportValidity() && messageText.reportValidity();
    if(!formBool) return;

    var patientMessageID = patientMessageID.value
    var deviceID = deviceSelect.value
    var areaID = areaSelect.value || null
    var message = messageText.value

    var formData = new FormData();
    formData.append('_token','{{ csrf_token() }}');
    formData.append("device_id", deviceID)
    formData.append("area_id", areaID)
    formData.append("message", message)

    var url;
    if(patientMessageID > 0) {
        url = "{{$editPatientMessageURL}}"
        formData.append("id", patientMessageID)
    }
    else 
        url = "{{$addPatientMessageURL}}"

    var http = new XMLHttpRequest();
    http.open("POST", url)

    http.onreadystatechange = function (){
        if(this.readyState==4 && this.status == 200 && IsNumeric(this.responseText) ){
                Swal.fire({
                    title: "Success!",
                    text: "Patient message successfully " + (patientMessageID > 0 ? "updated" : "added"),
                    icon: "success"
                })
                if(patientMessageID > 0) {
                    editPatientMessageItem(patientMessageID, deviceID, areaID, message)
                } else {
                    // Reload page to show new item
                    window.location.reload();
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

function addPatientMessageItem(id, deviceID, areaID, message){
    var patientMessageList = document.getElementById('patientMessageList')
    var deviceName = document.getElementById('deviceSelect').options[document.getElementById('deviceSelect').selectedIndex].text
    var areaName = areaID ? document.getElementById('areaSelect').options[document.getElementById('areaSelect').selectedIndex].text : ''
    
    var areaText = areaName ? ' | <strong>Area:</strong> ' + areaName : ''
    var messagePreview = message.length > 100 ? message.substring(0, 100) + '...' : message
    
    patientMessageList.innerHTML += '  <a id="patientMessageItem'+id+'" href="javascript:void(0)" onclick="setupEditPatientMessage('+id+')"\
            class="list-group-item list-group-item-action flex-column align-items-start">\
                    <div class="d-flex w-100 justify-content-between">\
                        <div>\
                            <h5 class="mb-1 text-dark"><strong>Device:</strong> '+deviceName+areaText+'</h5>\
                            <p class="mb-1" id="patientMessageText'+id+'">'+messagePreview+'</p>\
                        </div>\
                    </div>\
                </a>';
    
    cancelPatientMessageForm(false)
}

function editPatientMessageItem(id, deviceID, areaID, message){
    var patientMessageListItem = document.getElementById('patientMessageItem' + id)
    var deviceName = document.getElementById('deviceSelect').options[document.getElementById('deviceSelect').selectedIndex].text
    var areaName = areaID ? document.getElementById('areaSelect').options[document.getElementById('areaSelect').selectedIndex].text : ''
    
    var areaText = areaName ? ' | <strong>Area:</strong> ' + areaName : ''
    var messagePreview = message.length > 100 ? message.substring(0, 100) + '...' : message
    
    patientMessageListItem.innerHTML = ' <div class="d-flex w-100 justify-content-between">\
                        <div>\
                            <h5 class="mb-1 text-dark"><strong>Device:</strong> '+deviceName+areaText+'</h5>\
                            <p class="mb-1" id="patientMessageText'+id+'">'+messagePreview+'</p>\
                        </div>\
                    </div>'
    
    // Update data object
    patientMessagesData[id] = {
        device_id: parseInt(deviceID),
        area_id: areaID ? parseInt(areaID) : null,
        message: message
    }
    
    cancelPatientMessageForm(false)
}

function setupEditPatientMessage(id){
    if(!patientMessagesData[id]) {
        // Reload page to get fresh data
        window.location.href = "{{ url('patient-messages') }}/" + id;
        return;
    }
    
    var data = patientMessagesData[id]
    document.getElementById('patientMessageID').value = id
    document.getElementById('deviceSelect').value = data.device_id
    document.getElementById('areaSelect').value = data.area_id || ""
    document.getElementById('messageText').value = data.message

    var deletePatientMessageButton = document.getElementById('deletePatientMessageButton')
    deletePatientMessageButton.style.display = "block"
    deletePatientMessageButton.setAttribute( "onClick", "deletePatientMessageItem(" + id + ")" );
    $.toast({
                heading: 'Patient Message Data loaded',
                text: 'You can now edit patient message data.',
                position: 'top-right',
                loaderBg:'blue',
                icon: 'success',
                hideAfter: 1000, 
                stack: 6,
                type: 'success'
                });
}

function deletePatientMessageItem(id){
    Swal.fire({
        title: "Warning!",
        text: "Are you sure you want to delete this patient message?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
    }).then((confirmed) => {
        if(confirmed.value) {
            var patientMessageListItem = document.getElementById('patientMessageItem' + id)
            var formData = new FormData();
            formData.append('_token','{{ csrf_token() }}');
            formData.append("id", id)
            var http = new XMLHttpRequest();
            http.open("POST", "{{$delPatientMessageURL}}")
            http.onreadystatechange = function (){
                if(this.readyState==4 && this.status == 200 && this.responseText.trim() == "1" ){
                        Swal.fire({
                            title: "Success!",
                            text: "Patient message successfully deleted",
                            icon: "success"
                        })    
                        patientMessageListItem.style.display = "none"
                        delete patientMessagesData[id]
                } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Oops Something went wrong!",
                            icon: "error"
                        })
                    }
                }
                http.send(formData)

                cancelPatientMessageForm(false)
        }
    })
}

function cancelPatientMessageForm(showToast=true){
    document.getElementById('patientMessageID').value = 0
    document.getElementById('deviceSelect').value = ""
    document.getElementById('areaSelect').value = ""
    document.getElementById('messageText').value = ""

    var deletePatientMessageButton = document.getElementById('deletePatientMessageButton')
    deletePatientMessageButton.style.display = "none"
    deletePatientMessageButton.onclick = ""
    
    // Clear URL if editing
    if(window.location.pathname.includes('/patient-messages/')) {
        window.history.pushState({}, '', '{{ url("patient-messages") }}');
    }
    
    if(showToast)
    $.toast({
                heading: 'Patient Message Data cleared',
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

// Pre-load form if selectedMessage exists
@if($selectedMessage)
$(document).ready(function() {
    setupEditPatientMessage({{ $selectedMessage->id }});
});
@endif

// Live preview for message text
@if($sampleSession)
@php
    $fullName = $sampleSession->patient->PTNT_NAME ?? 'Patient';
    $nameParts = explode(' ', $fullName);
    $firstName = $nameParts[0] ?? 'Patient';
@endphp
var samplePatientFirstName = "{{ $firstName }}";
$(document).ready(function() {
    $('#messageText').on('input', function() {
        var messageText = $(this).val();
        if (messageText) {
            // Replace {patient} placeholder with first name only
            var previewText = messageText.replace(/{patient}/g, samplePatientFirstName);
            
            // Convert \r\n to actual newlines
            previewText = previewText.replace(/\\r\\n/g, '\r\n');
            previewText = previewText.replace(/\\n/g, '\n');
            previewText = previewText.replace(/\\r/g, '\r');
            
            $('#messagePreviewText').text(previewText);
            $('#messagePreview').show();
        } else {
            $('#messagePreview').hide();
        }
    });
    
    // Trigger on page load if there's already text
    if ($('#messageText').val()) {
        $('#messageText').trigger('input');
    }
});
@endif

</script>
@endsection
