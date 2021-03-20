<script>
    function confirmAndGoTo(url, action){
    Swal.fire({
        text: "Are you sure you want to " + action + "?",
        icon: "warning",
        showCancelButton: true,
        }).then((isConfirm) => {
    if(isConfirm.value){

    window.location.href = url;
        }
    });

}

</script>

<div class="card">
    <div class="card-body">
        @if($cardTitle)
        <h4 class="card-title">{{$title}}</h4>
        <h6 class="card-subtitle">{{$subtitle}}</h6>
        @endif
        <div class="table-responsive m-t-5">
            <table id="{{$id}}" class="table color-bordered-table table-striped full-color-table full-info-table hover-table" data-display-length='-1' data-order="[]">
                <thead>
                    <tr>
                        @foreach($cols as $col)
                        <th>{{$col}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="{{$id}}Body">

                    @foreach($items as $item)
                    <tr>
                        @foreach($atts as $att)

                        @if(is_array($att))
                        @if(array_key_exists('edit', $att))
                        <td><a href="{{ url( $att['edit']['url'] . $item->{$att['edit']['att']}) }}"><img src="{{ asset('images/edit.png') }}" width=25 height=25></a></td>
                        @elseif(array_key_exists('editJS', $att))
                        <td>
                            <div onclick="{{$att['editJS']['func']}}({{$item->id}})"><a href="javascript:void(0)"><img src="{{ asset('images/edit.png') }}" width=25 height=25></a>
                            </div>
                        </td>
                        @elseif(array_key_exists('del', $att))
                        <td>
                            <div onclick="confirmAndGoTo('{{url( $att['del']['url'] . $item->{$att['del']['att']})}}', '{{$att['del']['msg'] ?? 'delete this row'}}')"><a href="javascript:void(0)"><img
                                        src="{{ asset('images/del.png') }}" width=25 height=25></a></div>
                        </td>
                        @elseif(array_key_exists('foreign', $att))
                        <td>{{ $item->{$att['foreign']['rel']}->{$att['foreign']['att']} ?? '' }}</td>
                        @elseif(array_key_exists('foreignForeign', $att))
                        <td>{{ $item->{$att['foreignForeign']['rel1']}->{$att['foreignForeign']['rel2']}->{$att['foreignForeign']['att']} ?? '' }}</td>
                        @elseif(array_key_exists('sumForeign', $att))
                        <td>{{ $item->{$att['sumForeign']['rel']}->sum($att['sumForeign']['att'])  }}</td>
                        @elseif(array_key_exists('url', $att))
                        <td><a href="{{ url($att['url'][0]) }}">{{ $item->{$att['url']['att']}  }}</a></td>
                        @elseif(array_key_exists('remoteURL', $att))
                        <td><a target="_blank" href="http://{{ $item->{$att['remoteURL']['att']} }}">
                                {{ (strlen($item->{$att['remoteURL']['att']}) < 15 ) ? $item->{$att['remoteURL']['att']} : substr($item->{$att['remoteURL']['att']},0,26).'..'  }}</a></td>
                        @elseif(array_key_exists('verified', $att))
                        <td>{{ $item->{$att['verified']['att']} ?? '' }}
                            @isset($item->{$att['verified']['att']})
                            @if($item->{$att['verified']['isVerified']})
                            <i class="fas fa-check-circle" style="color:lightgreen">
                                @else
                                <i class=" fas fa-exclamation-circle" style="color:red"></i>
                                @endif
                                @endisset
                        </td>
                        @elseif(array_key_exists('verifiedRel', $att))
                        <td>{{ $item->{$att['verifiedRel']['rel']}->{$att['verifiedRel']['relAtt']} ?? '' }}
                            @isset($item->{$att['verifiedRel']['rel']}->{$att['verifiedRel']['relAtt']})
                            @if($item->{$att['verifiedRel']['isVerified']})
                            <i title="{{$att['verifiedRel']['iconTitle']}}" class="fas fa-check-circle" style="color:lightgreen"></i>
                            @else
                            <i title="{{$att['verifiedRel']['iconTitle']}}" class=" fas fa-exclamation-circle" style="color:red"></i>
                            @endif
                            @endisset
                        </td>
                        @elseif(array_key_exists('dynamicUrl', $att))
                        <td><a href="{{ url($att['dynamicUrl']['baseUrl'].$item->{$att['dynamicUrl']['val']}) }}">{{ $item->{$att['dynamicUrl']['att']}  }}</a></td>
                        @elseif(array_key_exists('state', $att))
                        <td><span class="label {{ $att['state']['classes'][$item->{$att['state']['att']}] }}">{{ $att['state']['text'][$item->{$att['state']['att']}]  }}</span></td>
                        @elseif(array_key_exists('relState', $att))
                        <td><span class="label {{ $att['state']['classes'][$item->{$att['state']['att']}] }}">{{ $item->{$att['state']['rel']}->{$att['state']['foreignAtt']}  }}</span></td>
                        @elseif(array_key_exists('stateQuery', $att))
                        <td>
                            @if(array_key_exists('url', $att['stateQuery']))
                            <a href="{{ url($att['stateQuery']['url'] . $item->{$att['stateQuery']['urlAtt']})}}">
                                @endif
                                <button class="label {{ $att['stateQuery']['classes'][$item->{$att['stateQuery']['att']}] }}">{{ $item->{$att['stateQuery']['foreignAtt']}  }}</button>
                                @if(array_key_exists('url', $att['stateQuery']))
                            </a>
                            @endif
                        </td>
                        @elseif(array_key_exists('toggle', $att))
                        <td><a href="javascript:void(0);">
                                <button class="label {{ $att['toggle']['classes'][$item->{$att['toggle']['att']}] }}"
                                    onclick="confirmAndGoTo('{{url($att['toggle']['url'] . $item->id)}}', '{{ $att['toggle']['actions'][$item->{$att['toggle']['att']}] }}')">
                                    {{ $att['toggle']['states'][$item->{$att['toggle']['att']}]  }}</button>
                            </a>
                        </td>
                        @elseif(array_key_exists('date', $att))
                        <td>{{  $item->{$att['date']['att']}->format($att['date']['format'] ?? 'd-M-y h:i A')  }}</a></td>
                        @elseif(array_key_exists('number', $att))
                        <td>{{ number_format($item->{$att['number']['att']}, $att['number']['decimals'] ?? 2)  }}</a></td>
                        @elseif(array_key_exists('attUrl', $att))
                        <td><a href="{{ url($att['attUrl']['url'] . '/' . $item->{$att['attUrl']['urlAtt']}) }}">{{ $item->{$att['attUrl']['shownAtt']}  }}</a></td>
                        @elseif(array_key_exists('modelFunc', $att))
                        <td>{{ $item->{$att['modelFunc']['funcName']}()  }}</td>
                        @elseif(array_key_exists('urlOrStatic', $att))
                        @isset($item->{$att['urlOrStatic']['shownAtt']})
                        <td><a href="{{ url($att['urlOrStatic']['url'] . '/' . $item->{$att['urlOrStatic']['urlAtt']}) }}">{{ $item->{$att['urlOrStatic']['shownAtt']}  }}</a></td>
                        @else
                        <td>{{ $item->{$att['urlOrStatic']['static']}  }}</td>
                        @endisset
                        @elseif(array_key_exists('foreignUrl', $att))
                        <td><a href="{{ url($att['foreignUrl']['baseUrl'] . '/' . $item->{$att['foreignUrl']['urlAtt']}) }}">{{ $item->{$att['foreignUrl']['rel']}->{$att['foreignUrl']['att']}  }}</a>
                        </td>
                        @elseif(array_key_exists('assetImg', $att))
                        <td>
                            @isset($item->{$att['assetImg']['att']})
                            <img src="{{ asset( 'storage/'. $item->{$att['assetImg']['att']}) }}" height="36" style="width: auto" />
                            @endisset
                        </td>
                        @elseif(array_key_exists('comment', $att))
                        <td>
                            <button type="button" style="padding:.1rem" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="bottom"
                                data-content="{{$item->{$att['comment']['att']} }}" data-original-title="{{ $att['comment']['title'] ?? 'Comment:'}}">
                                <div style="display: none">{{$item->{$att['comment']['att']} }}</div><i class="far fa-list-alt"></i>
                            </button>
                        </td>
                        @elseif(array_key_exists('hidden', $att))
                        <input type=hidden id="{{ $att['hidden']['id'] }}{{ $item->id }}" value="{{ $item->{$att['hidden']['valueAtt']} }}" />
                        @elseif(array_key_exists('hiddenRel', $att))
                        <input type=hidden id="{{ $att['hiddenRel']['id']}}{{$item->id }}" value="{{$item->{$att['hiddenRel']['rel']}->{$att['hiddenRel']['valueAtt']} }}" />
                        @endif
                        @else
                        <td>{{ $item->{$att} }}</td>
                        @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/datatables/datatables.min.js') }}"></script>
<script>
    $(function () {
            $(function () {

                var table = $('#{{$id}}').DataTable({
                    "displayLength": 25,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            title: 'Flawless',
                            footer: true,
                            className: 'btn-info'
                        }
                    ]
                });
            })
        })
</script>