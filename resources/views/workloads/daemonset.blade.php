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
            <td>{{$daemonset->getName()}}</td>
            <td>{{$daemonset->getNamespace()}}</td>
            <td>{{$daemonset->toArray()['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$daemonset->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($daemonset->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($daemonset->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Selector</th>
            <th>Images</th>
        </tr>
        <tr>
            <td>
                @foreach($daemonset->getSelectors() as $selector)
                    @foreach($selector as $key=>$value)
                        {{$key}}: {{$value}}<br>
                    @endforeach
                @endforeach
            </td>
            <td>
                @foreach($daemonset->toArray()['spec']['template']['spec']['containers'] as $container)
                    {{$container['image']}}<br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Pod Status</h3>
        </thead>
        <tbody>
        <tr>
            <th>Running</th>
            <th>Desired</th>
        </tr>
        <tr>
            <td>{{$daemonset->getReadyCount()}}</td>
            <td>{{$daemonset->getDesiredCount()}}</td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="pods_table">Pods</h3>
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
                <td>{{$pod->getName()}}</td>
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
                        {{$status['restartCount']}}<br>
                    @endforeach
                </td>
                <td>{{ $pod->toArray()['status']['hostIP']??'-' }}</td>
                <td>{{$pod->toArray()['metadata']['creationTimestamp']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="services_table">Services</h3>
        </thead>
        <tbody>
        @if(!is_null($services) && count($services) != 0)
        <tr>
            <td>Name</td>
            <td>Namespace</td>
            <td>Labels</td>
            <td>Type</td>
            <td>Cluster IP</td>
            <td>Ports</td>
            <td>External IP</td>
            <td>Create Time</td>
        </tr>
        @foreach($services as $service)
            <tr>
                <td>{{$service->getName()}}</td>
                <td>{{$service->getNamespace()}}</td>
                <td>
                    @foreach($service->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$label}}<br>
                        @endif
                    @endforeach
                </td>
                <td>{{$service->toArray()['spec']['type']}}</td>
                <td>
                    @foreach($service->toArray()['spec']['clusterIPs'] as $clusterIP)
                        {{$clusterIP}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($service->toArray()['spec']['ports'] as $port)
                        Name: {{$port['name']??"-"}}; Protocol: {{$port['protocol']}}<br>Port: {{$port['port']}}; Target Port: {{$port['targetPort']}}; Node Port: {{$port['nodePort']??"-"}}<hr>
                    @endforeach
                </td>
                <td>
                    @foreach($service->toArray()['status']['loadBalancer']['ingress']??["-"] as $externalIP)
                        {{$externalIP['ip']??"-"}}<br>
                    @endforeach
                </td>
                <td>{{$service->toArray()['metadata']['creationTimestamp']}}</td>
            </tr>
        @endforeach
        @else
        <tr>
            <th class="text-center">Resources Not Found...</th>
        </tr>
        @endif
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
