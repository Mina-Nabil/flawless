@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{$formTitle}}</h4>
                <h6 class="card-subtitle">{{$formSubtitle}}</h6>
                <form class="form pt-3" method="post">
                    @csrf

                    <div class="form-group col-md-6 m-t-0">
                        <label>Channels</label>
                        <select class="select2  select2-multiple form-control" multiple="multiple" name=channel_ids[]>
                          <option value="-1" selected>All Channels</option>
                          <?php foreach ($channels as $channel) { ?>
                            <option value="<?= $channel['id'] ?>"><?= $channel['CHNL_NAME'] ?></option>
                          <?php } ?>
                        </select>
                      </div>

                    <div class="form-group col-md-6 m-t-0">
                        <label>Location</label>
                        <select class="select2  select2-multiple form-control" multiple="multiple" name=location_ids[]>
                          <option value="-1" selected>All Locations</option>
                          <?php foreach ($locations as $location) { ?>
                            <option value="<?= $location['id'] ?>"><?= $location['LOCT_NAME'] ?></option>
                          <?php } ?>
                        </select>
                      </div>

                    <button type="submit" class="btn btn-success mr-2">Submit</button>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection