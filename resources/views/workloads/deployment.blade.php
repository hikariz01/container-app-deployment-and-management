@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')

    <style>
        .table td, .table th {
            padding: 5px 0.4rem;
        }
    </style>

    @include('layouts.resourceNav')

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Metadata</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Created</th>
            <th>Age</th>
            <th>UID</th>
        </tr>
        <tr>
            <td>{{$deployment->getName()}}</td>
            <td>{{$deployment->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($deployment->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$deployment->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($deployment->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="5">Annotations</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($deployment->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
                    @if($key == "")
                        -
                    @elseif(is_array(json_decode($value, true)))
                        {{$key}}:
                        <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#annoJSON" type="button" onclick="updateJSON(this)" value="{{$key}}">
                            JSON
                        </button><br>
                        <div class="{{$key}}" style="display: none">{{$value}}</div>
                    @else
                        {{$key}}: {{$value}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    @include('layouts.jsonViewModal')


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Resource Information</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Strategy</th>
            <th>Min ready seconds</th>
            <th>Revision history limit</th>
        </tr>
        <tr>
            <td>{{$deployment->toArray()['spec']['strategy']['type']}}</td>
            <td>{{$deployment->getMinReadySeconds()??"0"}}</td>
            <td>{{$deployment->toArray()['spec']['revisionHistoryLimit']}}</td>
        </tr>
        <tr>
            <th colspan="5">Selector</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($deployment->toArray()['spec']['selector']['matchLabels']??json_decode('{"":""}') as $key => $value)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$value}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Rolling Update Strategy</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Max surge</th>
            <th>Max Unavailable</th>
        </tr>
        <tr>
            <td>{{$deployment->toArray()['spec']['strategy']['rollingUpdate']['maxSurge']??'none'}}</td>
            <td>{{$deployment->toArray()['spec']['strategy']['rollingUpdate']['maxUnavailable']??'none'}}</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Pod Status</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Updated</th>
            <th>Total</th>
            <th>Available</th>
        </tr>
        <tr>
            <td>{{$deployment->toArray()['status']['updatedReplicas']??'0'}}</td>
            <td>{{$deployment->getDesiredReplicasCount()??'-'}}</td>
            <td>{{$deployment->getAvailableReplicasCount()??'0'}}</td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Conditions</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Type</th>
            <th>Status</th>
            <th>Last Probe Time</th>
            <th>Last transition time</th>
            <th>Reason</th>
            <th>Message</th>
        </tr>
        @foreach($conditions as $condition)
            <tr>
                <td>{{$condition['type']}}</td>
                <td>{{$condition['status']}}</td>
                <td>{{!\Carbon\Carbon::createFromTimeString($condition['lastUpdateTime']??'9999', 'UTC')->isValid() ? '-' : \Carbon\Carbon::createFromTimeString($condition['lastUpdateTime']??'9999', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                <td>{{!\Carbon\Carbon::createFromTimeString($condition['lastTransitionTime']??'9999', 'UTC')->isValid() ? '-' : \Carbon\Carbon::createFromTimeString($condition['lastTransitionTime']??'9999', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                <td>{{$condition['reason']}}</td>
                <td>{{$condition['message']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Replica Set</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Age</th>
            <th>Pods</th>
        </tr>
        @foreach($replicasets as $key => $replicaset)
            <tr>
                <td>
                    <a href="{{ route('replicaset-details', ['name'=>$replicaset['metadata']['name'], 'namespace'=>$replicaset['metadata']['namespace']??'default']) }}">{{$replicaset['metadata']['name']}}</a>
                </td>
                <td>{{$replicaset['metadata']['namespace']}}</td>
                <td>{{$replicasetAge[$key]}}</td>
                <td>{{$replicaset['status']['readyReplicas']??'0'}}/{{$replicaset['status']['replicas']??'-'}}</td>
            </tr>
            <tr>
                <th colspan="5">Labels</th>
            </tr>
            <tr>
                <td colspan="5">
                    @foreach($replicaset['metadata']['labels']??json_decode('{"":""}') as $key => $value)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$value}}<br>
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <th colspan="5">Images</th>
            </tr>
            <tr>
                <td colspan="5">
                    @foreach($replicaset['spec']['template']['spec']['containers'] as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{--    <table class="table table-secondary table-borderless" style="padding-left: 30px">--}}
    {{--        <thead>--}}
    {{--        <h3 style="padding-left: 30px"id="deployment_table">Old Replica Set</h3>--}}
    {{--        </thead>--}}
    {{--        <tbody>--}}
    {{--        <tr>--}}
    {{--            <th>Updated</th>--}}
    {{--            <th>Total</th>--}}
    {{--            <th>Available</th>--}}
    {{--        </tr>--}}
    {{--        <tr>--}}
    {{--            <td>To be Updated</td>--}}
    {{--            <td>To be Updated</td>--}}
    {{--            <td>To be Updated</td>--}}
    {{--        </tr>--}}
    {{--        </tbody>--}}
    {{--    </table>--}}

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="deployment_table">Horizontal Pod Autoscalers</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($hrztPodAutoScaler) != 0)
            <tr>
                <th>Name</th>
                <th>Max Replicas</th>
                <th>Min Replicas</th>
                <th>Current Replicas</th>
                <th>Scale Target</th>
                <th>Target CPU Utilization</th>
                <th>Last Scale Time</th>
                <th>Created</th>
            </tr>
            @foreach($hrztPodAutoScaler as $podAutoScaler)
                <tr>
                    <td>{{$podAutoScaler['metadata']['name']}}</td>
                    <td>{{$podAutoScaler['spec']['maxReplicas']??'-'}}</td>
                    <td>{{$podAutoScaler['spec']['minReplicas']??'-'}}</td>
                    <td>{{$podAutoScaler['status']['currentReplicas']??'-'}}</td>
                    <td>{{$podAutoScaler['spec']['scaleTargetRef']['name']??'-'}}</td>
                    <td>{{$podAutoScaler['spec']['targetCPUUtilizationPercentage']}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($podAutoScaler['status']['lastScaleTime']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($podAutoScaler['metadata']['creationTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                {{--            TODO CHECK HORIZONTAL POD AUTOSCALER--}}
                <th colspan="10">Resource Not Found...</th>
            </tr>
        @endif
        </tbody>
    </table>

    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="events_table">Events</h3>
            </td>
        </tr>
        </thead>
        <tbody>

        @if(count($events) != 0 && !is_null($events))
            <tr>
                <td>Name</td>
                <td>Reason</td>
                <td>Message</td>
                <td>Source</td>
                <td>Sub-Object</td>
                <td>Count</td>
                <td>First Seen</td>
                <td>Last Seen</td>
            </tr>
            @foreach($events as $event)
                <tr>
                    <td>{{$event->getName()}}</td>
                    <td>{{$event->toArray()['reason']}}</td>
                    <td>{{$event->toArray()['message']}}</td>
                    <td>{{$event->toArray()['source']['component']??"-"}}
                        /{{$event->toArray()['source']['host']??"-"}}</td>
                    <td>{{$event->toArray()['involvedObject']['kind']}}
                        /{{$event->toArray()['involvedObject']['name']??""}}</td>
                    <td>{{$event->toArray()['count']??"0"}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['firstTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['lastTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource Not Found...</th>
            </tr>
        @endif

        </tbody>
    </table>

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($deployment->toArray(), 512, 2)}}</div>

    @include('layouts.editFormModal')

    @include('layouts.deleteFormModal')

@endsection


@section('js')

    <script>

        let aceData = document.querySelector('#data').innerHTML

        let editor = document.querySelector('#editor')
        let aceEditor = ace.edit("editor");

        aceEditor.setTheme('ace/theme/monokai')
        aceEditor.session.setMode("ace/mode/yaml");

        aceEditor.session.setValue(aceData)

        function updateData() {
            document.querySelector('input[name="value"]').value = aceEditor.session.getValue()
        }

        let kind = '{{$deployment->getKind()}}';
        let namespace = '{{$deployment->getNamespace()}}';
        let name = '{{$deployment->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')

@endsection
