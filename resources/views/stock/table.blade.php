@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <x-datatable id="myTable"  :title="$title" :subtitle="$subTitle" :cols="$cols" :items="$items" :atts="$atts" />
    </div>
</div>
@endsection