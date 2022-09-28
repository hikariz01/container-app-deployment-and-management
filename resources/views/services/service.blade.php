@extends('layouts.app2', ['namespaces'=>$namespaces])

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
            <td>{{$service->getName()}}</td>
            <td>{{$service->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($service->getMetadata()['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$service->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($service->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($service->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Type</th>
            <th>Cluster IP</th>
            <th>Session Affinity</th>
        </tr>
        <tr>
            <td>{{$service->getSpec('type')??'-'}}</td>
            <td>
                @foreach($service->getSpec('clusterIPs')??[0=>'-'] as $clusterIP)
                    {{$clusterIP}}<br>
                @endforeach
            </td>
            <td>{{$service->getSpec('sessionAffinity')??'-'}}</td>
        </tr>
        <tr>
            <th colspan="5">Selector</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($service->getSelectors()??[''=>''] as $key => $value)
                    @if($key === '')
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
                <h3 style="padding-left: 30px"id="deployment_table">Endpoints</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($endpoints) != 0)
            <tr>
            <th>Host</th>
            <th>Ports (Name, Port, Protocol)</th>
            <th>Node</th>
            <th>Ready</th>
            </tr>
            @foreach($endpoints['subsets'] as $subset)
                @foreach($subset['ports'] as $port)
                        @foreach($subset['addresses'] as $key => $address)
                        <tr>
                            <td>{{$address['ip']}}</td>
                            <td>{{$port['name']??'-'}}, {{$port['port']??'-'}}, {{$port['protocol']??'-'}}</td>
                            <td>{{$address['nodeName']??'none'}}</td>
                            <td>true</td>
                        </tr>
                        @endforeach
                        @foreach($subset['notReadyAddresses']??[] as $key => $address)
                            <tr>
                                <td>{{$address['ip']}}</td>
                                <td>{{$port['name']??'-'}}, {{$port['port']??'-'}}, {{$port['protocol']??'-'}}</td>
                                <td>{{$address['nodeName']??'none'}}</td>
                                <td>false</td>
                            </tr>
                        @endforeach
                @endforeach
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource not found...</th>
            </tr>
        @endif
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="pods_table">Pods</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Images</th>
            <th>Labels</th>
            <th>Status</th>
            <th>Restarts</th>
            <th>Running on Host</th>
            <th>Create Time</th>
        </tr>
        @foreach($pods as $pod)
            <tr>
                <td><a href="{{ route('pod-details', ['name'=>$pod->getName(), 'namespace'=>$pod->getMetadata()['namespace']]) }}">{{$pod->getName()}}</a></td>
                <td>{{$pod->getNamespace()}}</td>
                <td>
                    @foreach($pod->toArray()['spec']['containers'] as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($pod->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$label}}<br>
                        @endif
                    @endforeach
                </td>
                <td>{{$pod->toArray()['status']['phase']}}</td>
                <td>
                    @foreach($pod->toArray()['status']['containerStatuses']??["restartCount"=>"-"] as $status)
                        {{$status['restartCount']??'-'}}<br>
                    @endforeach
                </td>
                <td>{{$pod->getSpec('nodeName')??'-'}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($pod->getMetadata()['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Ingresses</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($ingresses) != 0)
            <tr>
                <th>Name</th>
                <th>Namespace</th>
                <th>Labels</th>
                <th>Host</th>
                <th>Paths</th>
                <th>Service</th>
                <th>Create Time</th>
            </tr>
            @foreach($ingresses as $ingress)
                <tr>
{{--                    TODO LINK INGRESS--}}
                    <td><a href="{{ route('ingress-details', ['name'=>$ingress->getName(), 'namespace'=>$ingress->getNamespace()]) }}">{{$ingress->getName()}}</a></td>
                    <td>{{$ingress->toArray()['metadata']['namespace']}}</td>
                    <td>
                        @foreach($ingress->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            <a href="{{'https://'.$rule['host']}}" target="_blank">{{$rule['host']}}</a><br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            @foreach($rule['http']['paths'] as $path)
                                {{$path['path']}}<br>
                            @endforeach
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            @foreach($rule['http']['paths'] as $path)
                                {{$path['backend']['service']['name']}} (Port: {{$path['backend']['service']['port']['number']}})<br>
                            @endforeach
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($ingress->getMetadata()['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource not found...</th>
            </tr>
        @endif
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px" >
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
                @if(str_contains($event->toArray()['involvedObject']['name']??"", $service->getName()))
                    <tr>
                        <td>{{$event->getName()}}</td>
                        <td>{{$event->toArray()['reason']}}</td>
                        <td>{{$event->toArray()['message']}}</td>
                        <td>{{$event->toArray()['source']['component']??"-"}}/{{$event->toArray()['source']['host']??"-"}}</td>
                        <td>{{$event->toArray()['involvedObject']['kind']}}/{{$event->toArray()['involvedObject']['name']??""}}</td>
                        <td>{{$event->toArray()['count']??"0"}}</td>
                        <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['firstTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                        <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['lastTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource Not Found...</th>
            </tr>
        @endif
        </tbody>
    </table>


@endsection
