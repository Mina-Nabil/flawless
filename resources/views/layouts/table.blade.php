@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <x-datatable id="myTable" :title="$tableTitle" :subtitle="$tableSubtitle" :cols="$cols" :items="$items" :atts="$atts" />
    </div>
</div>
@endsection