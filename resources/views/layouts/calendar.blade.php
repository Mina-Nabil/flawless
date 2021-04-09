@extends('layouts.app')

@section('header')
      <!-- Calendar CSS -->
      <link href="{{asset('assets/node_modules/calendar/dist/fullcalendar.css')}}" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card-body">
                            <h4 class="card-title m-t-10">Sessions Calendar</h4>
                          
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card-body b-l calender-sidebar">
                            <div id="calendar"></div>
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
<script src="{{asset('assets/node_modules/moment/moment.js')}}"></script>
<script src="{{asset('assets/node_modules/calendar/dist/fullcalendar.min.js')}}"></script>
<script>


!function($) {
    "use strict";

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


    CalendarApp.prototype.enableDrag = function() {
        //init events
        $(this.$event).each(function () {
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim($(this).text()) // use the element's text as the event title
            };
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
        });
    }
    /* Initializing */
    CalendarApp.prototype.init = function() {
        this.enableDrag();
        /*  Initialize the calendar  */
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var form = '';
        var today = new Date($.now());

        var defaultEvents =  [
            
            @foreach($sessions as $session)
            {
                title: '{{$session->patient->PTNT_NAME}}',
                start: new Date('{{$session->SSHN_DATE->format("Y-m-d")}}'+'T'+'{{$session->SSHN_STRT_TIME}}'),
                end: new Date('{{$session->SSHN_DATE->format("Y-m-d")}}'+'T'+'{{$session->SSHN_END_TIME}}'),
                className: '{{($session->SSHN_STTS=="New") ? "bg-info" : ($session->SSHN_STTS=="Pending Payment") ? "bg-dark" : ($session->SSHN_STTS=="Cancelled") ? "bg-danger" : ($session->SSHN_STTS=="Done") ? "bg-success" : "bg-info"  }}'
            },
            @endforeach
            ];

        var $this = this;
        $this.$calendarObj = $this.$calendar.fullCalendar({
            slotDuration: '00:30:00', /* If we want to split day time each 15minutes */
            minTime: '09:00:00',
            maxTime: '22:00:00',  
            defaultView: 'agendaWeek',  
            handleWindowResize: true,   
             
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: defaultEvents,
            editable: true,
            droppable: false, // this allows things to be dropped onto the calendar !!!
            eventLimit: true, // allow "more" link when too many events
            selectable: true,
            drop: function(date) { $this.onDrop($(this), date); },
            select: function (start, end, allDay) { $this.onSelect(start, end, allDay); },
            eventClick: function(calEvent, jsEvent, view) { $this.onEventClick(calEvent, jsEvent, view); }

        });

    
    },

   //init CalendarApp
    $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp
    
}(window.jQuery),

//initializing CalendarApp
function($) {
    "use strict";
    $.CalendarApp.init()
}(window.jQuery);

</script>


@endsection