@extends('layouts.app')

@section('header')
<!-- Calendar CSS -->
<link href="{{asset('assets/node_modules/calendar/dist/fullcalendar.css')}}" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-2">
        <div class="row">
            <div class="col-lg-12">
                <select class="select form-control" style="width:fit-content" onchange="changeRoom()" id=calendarRoom>
                    <option value=0 {{(0==session('branch')) ? 'selected' : '' }}>All rooms</option>
                    @foreach($rooms as $r)
                    <option value="{{$r->id}}" {{($room && $r->id == $room->id) ? 'selected' : ''}}
                        > {{$r->branch->BRCH_NAME}} - {{$r->ROOM_NAME}} </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-12">
                <br>
            </div>
            <div class="col-lg-12">
                <select class="select form-control" style="width:fit-content" onchange="changeSelectedDoctor()" id=calendarDoctor>
                    <option disabled selected>Show times doctor</option>
                    <option value=0>None</option>
                    @foreach($doctors as $d)
                    <option value="{{$d->id}}"> {{$d->DASH_USNM}} </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <div class="card">
            <div class="">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body b-l calender-sidebar">
                            @if($room !== null)
                            <div id="calendar"></div>
                            @else
                            Please select a room
                            @endif
                            {{-- <iframe src="https://calendar.google.com/calendar/embed?src=flawless7clinic%40gmail.com&ctz=Africa%2FCairo" style="border: 0" width="800" height="600" frameborder="0"
                                scrolling="yes"></iframe> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_content')
<!-- Calendar JavaScript -->
<script src="{{asset('assets/node_modules/calendar/jquery-ui.min.js')}}"></script>
<script src="{{asset('assets/node_modules/calendar/dist/fullcalendar.min.js')}}"></script>
<script>
    !function($) {
        "use strict";
        
        var roomEventsCallback = (start, end, tz, successCallback ) => {
            $.ajax({
            method: "POST",
            url: "{{$getSessionsAPI}}",
            data: {
                _token: "{{ csrf_token() }}" ,
                start_date: start.format('YYYY-MM-DD'),
                end_date: end.format('YYYY-MM-DD'),
                branchID: "{{ isset($branch) ? $branch->id : 0 }}",
                roomID: "{{ isset($room) ? $room->id : -1 }}",
            }
            }).done((data, status, xhr) => {
                successCallback(data)
            })
        }
        
        var allDayEventsCallback = (start, end, tz, successCallback ) => {
            $.ajax({
            method: "GET",
            url: "{{$allDayEventsAPI}}",
            data: {
                _token: "{{ csrf_token() }}" ,
                start_date: start.format('YYYY-MM-DD'),
                end_date: end.format('YYYY-MM-DD'),
                roomID: "{{ isset($room) ? $room->id : null }}",
            }
            }).done((data, status, xhr) => {
                successCallback(data)
            })
        }
        
        var doctorEventsCallback = (start, end, tz, successCallback ) => {
            if(!$('#calendarDoctor').val()) return successCallback([])
            $.ajax({
            method: "POST",
            url: "{{$getDoctorTimesAPI}}",
            data: {
                _token: "{{ csrf_token() }}" ,
                start_date: start.format('YYYY-MM-DD'),
                end_date: end.format('YYYY-MM-DD'),
                doctorID: $('#calendarDoctor').val(),
            }
            }).done((data, status, xhr) => {
                successCallback(data)
            })
        }

        var CalendarApp = function() {
            this.$body = $("body")
            this.$calendar = $('#calendar'),
            this.$event = ('#calendar-events div.calendar-events'),
            this.$categoryForm = $('#add-new-event form'),
            this.$extEvents = $('#calendar-events'),
            this.$modal = $('#my-event'),
            this.$saveCategoryBtn = $('.save-category'),
            this.$calendarObj = null
        };


        /* Initializing */
        CalendarApp.prototype.init = function() {
            // this.enableDrag();
            /*  Initialize the calendar  */
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var form = '';
            var today = new Date($.now());

            var $this = this;
            $this.$calendarObj = $this.$calendar.fullCalendar({
                slotDuration: '00:15:00', /* If we want to split day time each 15minutes */
                minTime: '10:00:00',
                maxTime: '23:59:59',  
                defaultView: 'agendaWeek',  
                handleWindowResize: true,   
                
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek,agendaDay'
                },
                eventSources: [roomEventsCallback, doctorEventsCallback, allDayEventsCallback],
                expandRows: true,
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!
                eventLimit: true, // allow "more" link when too many events
                allDaySlot: true,
                allDayText: "notes",
                selectable: true,
                nowIndicator: true,
                drop: function(date) { $this.onDrop($(this), date); },
                select: function (start, end, allDay) { if(allDay) {
                    openDayNoteForm(start.format('YYYY-MM-DD'), "{{ isset($room) ? $room->id : null }}")
                } else openBookingForm(start, end) },
                eventClick: function(calEvent, jsEvent, view) { 
                    if(calEvent.id.startsWith('all-day')){
                        var id = calEvent.id.substring(7)
                        console.log(calEvent)
                        openDayNoteForm(calEvent.start.format('YYYY-MM-DD'), calEvent.roomID ?? 0, id, calEvent.original_title, calEvent.note)
                    } else {
                        window.open('{{url("sessions/details") . "/" }}' + calEvent.id,  '_blank');
                    }
                 },

            });

        
        },

        //init CalendarApp
        $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp
        
    }(window.jQuery)

    //initializing CalendarApp
    var CalendarInitModule = function($) {
        "use strict";
        $.CalendarApp.init()
        return {
            reloadEvents: () => $.CalendarApp.$calendarObj.fullCalendar('refetchEvents')
        }

    }(window.jQuery);


    async function openBookingForm(start_date, end_date){
        await getPatientsArray();
        $('#sessionDate').val(start_date.format('YYYY-MM-DD'));
        $('#sessionStartTime').val(start_date.format('HH:mm'));
        $('#sessionEndTime').val(end_date.format('HH:mm'));
        @if($room)
        $('#sessionRoom').val({{ $room->id }});
        $('#sessionRoom').trigger('change');
        @endif
        $('#add-session-modal').modal("show")
    }

    function changeRoom(){
        var calendarRoom = $('#calendarRoom').val();
        window.location.assign("{{url('calendar')}}" + "/"+ calendarRoom)
    }

    function changeSelectedDoctor(){
        CalendarInitModule.reloadEvents()
    }


</script>


@endsection