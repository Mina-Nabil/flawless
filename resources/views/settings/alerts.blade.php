@extends('layouts.app')

@section('content')

<div class="row">
    <!-- Create alert -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $formTitle }}</h4>
                <h6 class="card-subtitle">{{ $formSubtitle }}</h6>
                <form class="form pt-3" method="post" action="{{ $addAlertURL }}">
                    @csrf

                    <div class="form-group">
                        <label>Message*</label>
                        <textarea class="form-control" name="message" rows="3"
                            placeholder="Write the alert message..." required>{{ old('message') }}</textarea>
                        <small class="text-danger">{{ $errors->first('message') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Recipients*</label>
                        <select class="select2 select2-multiple form-control" multiple="multiple" name="recipients[]"
                            style="width:100%" required>
                            @foreach ($users as $groupName => $groupUsers)
                                <optgroup label="{{ $groupName }}">
                                    @foreach ($groupUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->DASH_USNM }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <small class="text-danger">{{ $errors->first('recipients') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Expiry Date (optional)</label>
                        <input type="date" class="form-control" name="expiry" value="{{ old('expiry') }}">
                        <small class="text-muted">Leave empty to keep showing until each recipient confirms</small>
                    </div>

                    <button type="submit" class="btn btn-success mr-2">Send Alert</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing alerts -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Alerts</h4>
                <h6 class="card-subtitle">Track who has confirmed reading each alert</h6>

                @forelse ($alerts as $alert)
                    @php
                        $total = $alert->recipientRows->count();
                        $confirmed = $alert->recipientRows->whereNotNull('ALRC_READ_AT')->count();
                    @endphp
                    <div class="card border {{ $alert->ALRT_ACTV ? 'border-warning' : 'border-light' }} mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">{{ $alert->ALRT_TEXT }}</h5>
                                    <small class="text-muted">
                                        By {{ $alert->creator->DASH_USNM ?? 'N/A' }} ·
                                        {{ $alert->created_at?->format('d-M-Y h:i A') }}
                                        @if ($alert->ALRT_EXPR)
                                            · Expires {{ $alert->ALRT_EXPR->format('d-M-Y') }}
                                        @endif
                                    </small>
                                </div>
                                <div class="text-right" style="white-space: nowrap">
                                    <span class="label {{ $alert->ALRT_ACTV ? 'label-success' : 'label-danger' }}">
                                        {{ $alert->ALRT_ACTV ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="label label-info">{{ $confirmed }} / {{ $total }} read</span>
                                </div>
                            </div>

                            <ul class="list-unstyled mt-3 mb-2">
                                @foreach ($alert->recipientRows as $row)
                                    <li>
                                        @if ($row->ALRC_READ_AT)
                                            <i class="fas fa-check-circle" style="color:lightgreen"></i>
                                            {{ $row->user->DASH_USNM ?? 'N/A' }}
                                            <small class="text-muted">— confirmed
                                                {{ $row->ALRC_READ_AT->format('d-M-Y h:i A') }}</small>
                                        @else
                                            <i class="far fa-clock" style="color:#fec107"></i>
                                            {{ $row->user->DASH_USNM ?? 'N/A' }}
                                            <small class="text-muted">— pending</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ url('alerts/toggle/' . $alert->id) }}"
                                class="btn btn-sm {{ $alert->ALRT_ACTV ? 'btn-secondary' : 'btn-success' }} mr-2">
                                {{ $alert->ALRT_ACTV ? 'Deactivate' : 'Activate' }}
                            </a>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="confirmDeleteAlert('{{ url('alerts/delete/' . $alert->id) }}')">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mt-3">No alerts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteAlert(url) {
        Swal.fire({
            text: 'Are you sure you want to delete this alert?',
            icon: 'warning',
            showCancelButton: true,
        }).then(function (result) {
            if (result.value) {
                window.location.href = url;
            }
        });
    }
</script>

@endsection
