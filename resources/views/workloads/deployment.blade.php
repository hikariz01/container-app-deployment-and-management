@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')

    <style>
        .table td, .table th {
            padding: 5px 0.4rem;
        }
    </style>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Metadata</h3>
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
            <td>{{$deployment->toArray()['metadata']['creationTimestamp']}}</td>
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
        <h3 style="padding-left: 30px"id="deployment_table">Resource Information</h3>
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
        <h3 style="padding-left: 30px"id="deployment_table">Rolling Update Strategy</h3>
        </thead>
        <tbody>
        <tr>
            <th>Max surge</th>
            <th>Max Unavailable</th>
        </tr>
        <tr>
            <td>{{$deployment->toArray()['spec']['strategy']['rollingUpdate']['maxSurge']}}</td>
            <td>{{$deployment->toArray()['spec']['strategy']['rollingUpdate']['maxUnavailable']}}</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Pod Status</h3>
        </thead>
        <tbody>
        <tr>
            <th>Updated</th>
            <th>Total</th>
            <th>Available</th>
        </tr>
        <tr>
            <td>{{$deployment->toArray()['status']['updatedReplicas']}}</td>
            <td>{{$deployment->getDesiredReplicasCount()}}</td>
            <td>{{$deployment->getAvailableReplicasCount()}}</td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Conditions</h3>
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
                    <td>{{$condition['lastUpdateTime']}}</td>
                    <td>{{$condition['lastTransitionTime']}}</td>
                    <td>{{$condition['reason']}}</td>
                    <td>{{$condition['message']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">New Replica Set</h3>
        </thead>
        <tbody>
        <tr>
            <th>Updated</th>
            <th>Total</th>
            <th>Available</th>
        </tr>
        <tr>
            <td>To be Updated</td>
            <td>To be Updated</td>
            <td>To be Updated</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Old Replica Set</h3>
        </thead>
        <tbody>
        <tr>
            <th>Updated</th>
            <th>Total</th>
            <th>Available</th>
        </tr>
        <tr>
            <td>To be Updated</td>
            <td>To be Updated</td>
            <td>To be Updated</td>
        </tr>
        </tbody>
    </table>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Horizontal Pod Autoscalers</h3>
        </thead>
        <tbody>
        <tr class="text-center">
            <th>Items : To be Updated</th>
        </tr>
        </tbody>
    </table>

    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="events_table">Events</h3>
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
                    <td>{{$event->toArray()['source']['component']??"-"}}/{{$event->toArray()['source']['host']??"-"}}</td>
                    <td>{{$event->toArray()['involvedObject']['kind']}}/{{$event->toArray()['involvedObject']['name']??""}}</td>
                    <td>{{$event->toArray()['count']??"0"}}</td>
                    <td>{{$event->toArray()['firstTimestamp']}}</td>
                    <td>{{$event->toArray()['lastTimestamp']}}</td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource Not Found...</th>
            </tr>
        @endif

        </tbody>
    </table>

@endsection
