@extends('layouts.app')

@section('content')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $formTitle }}</h4>
                <form class="form pt-3" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row ">

                        <div id="dynamicContainer" class="nopadding row col-lg-12">
                        </div>

                        <div class="row col-lg-12">
                            <div class="col-lg-6">
                                <div class="input-group mb-2">
                                    <select name=stock_ids[] class="form-control select2  custom-select" required>
                                        <option disabled hidden selected value="">Stock Items</option>
                                        @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{$item->STCK_NAME}} - in {{$item->STCK_UNIT}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group mb-3">
                                    <input type="number" step=1 class="form-control amount" placeholder="Items amount" name=amount[] aria-describedby="basic-addon11" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-success" id="dynamicAddButton" type="button" onclick="addToab();"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                </form>
            </div>
        </div>
    </div>

</div>


<script>
    var room = 1;
   function addToab() {
   
   room++;
   var objTo = document.getElementById('dynamicContainer')
   var divtest = document.createElement("div");
   divtest.setAttribute("class", "nopadding row col-lg-12 removeclass" + room);
   var rdiv = 'removeclass' + room;
   var concatString = "";
   concatString +=   `<div class="row col-lg-12">
                            <div class="col-lg-6">
                                <div class="input-group mb-2">
                                    <select name=stock_ids[] class="form-control select2  custom-select" required>
                                        <option disabled hidden selected value="">Stock Items</option>
                                        @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{$item->STCK_NAME}} - in {{$item->STCK_UNIT}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group mb-3">
                                    <input type="number" step=1 class="form-control amount" placeholder="Items amount" name=amount[] aria-describedby="basic-addon11" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger" id="dynamicAddButton" type="button" onclick="removeToab(` + room + `);"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div`;
   
   divtest.innerHTML = concatString;
   
   objTo.appendChild(divtest);
   $(".select2").select2()

   }

   function removeToab(rid) {
    $('.removeclass' + rid).remove();

}
   
</script>

@endsection

@section("js_content")

@endsection