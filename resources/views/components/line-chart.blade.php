@push('scripts')
<link href="{{asset('assets/node_modules/chartist-js/dist/chartist.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/node_modules/chartist-js/dist/chartist-init.css')}}" rel="stylesheet">
<link href="{{asset('assets/node_modules/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet">
<link href="{{asset('assets/node_modules/css-chart/css-chart.css')}}" rel="stylesheet">
<link href="{{asset('assets/node_modules/morrisjs/morris.css')}}" rel="stylesheet">
<link href="{{asset('dist/css/pages/widget-page.css')}}" rel="stylesheet">
@endpush

<div class="card row">
    <div class="card-body">
        <div class="d-flex no-block align-items-center">
            <div>
                <h3>{{$chartTitle}}</h3>
                <h6 class="card-subtitle">{{$chartSubtitle}}</h6>
            </div>
            <div class="ml-auto">
                <ul class="list-inline">

                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="total-revenue4" style="height: 350px;"></div>
            </div>
            <?php $arrayCount = count($totals) ?>
            <?php $width = ($arrayCount > 0) ? 12 / count($totals) : 0 ?>

            @foreach($totals as $total)
            <div class="col-lg-{{$width}} col-md-6 m-b-30 m-t-20 text-center">
                <h1 class="m-b-0 font-light">{{number_format($total['value'])}}{{$total['unit'] ?? ''}}</h1>
                <h6 class="text-muted">{{$total['title']}}</h6>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="{{asset('assets/node_modules/chartist-js/dist/chartist.min.js')}}"></script>
<script src="{{asset('assets/node_modules/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js')}}"></script>
<script src="{{asset('assets/node_modules/sparkline/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('assets/node_modules/echarts/echarts-all.js')}}"></script>
<script>
    $(function () {
    "use strict";
    // ============================================================== 
    // Total revenue chart
    // ============================================================== 
    new Chartist.Line('.total-revenue4', {
        labels: [ <?php
             foreach($labels as $label){ 
                 echo( '\'' . $label . '\'' . ',') ;
             } 
            ?>  , ' ' ]
            
        , series: [
            <?php
             foreach($graphs as $graph){ 
                 echo('[' . implode(', ', $graph) . '],') ;
             } 
            ?>
            ]
    }, {
        high: {{$max}}
        , low: 0
        , showArea: true
        , fullWidth: true
        , plugins: [
        Chartist.plugins.tooltip()
      ], // As this is axis specific we need to tell Chartist to use whole numbers only on the concerned axis
        axisY: {
            onlyInteger: true
            , offset: 100
            , labelInterpolationFnc: function (value) {
                return (value ) ;
            }
        }
    });
});
</script>

@endpush