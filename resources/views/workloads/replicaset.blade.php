@extends('layouts.app2', ['namespaces' => $namespaces])

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
                <h3 style="padding-left: 30px"id="deployment_table">Metadata</h3>
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
            <td>{{$replicaset['metadata']['name']}}</td>
            <td>{{$replicaset['metadata']['namespace']}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($replicaset['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$replicaset['metadata']['uid']}}</td>
        </tr>
        <tr>
            <th colspan="8">Labels</th>
        </tr>
        <tr>
            <td colspan="8">
                @foreach($replicaset['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div><br>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="8">Annotations</th>
        </tr>
        <tr>
            <td colspan="8">
                @foreach($replicaset['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
                    @if($key == "")
                        -
                    @else
                        @if(is_array(json_decode($value, true)))
                            {{$key}}: <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#annoJSON" type="button" onclick="updateJSON(this)" value="{{$key}}">JSON</button><br>
                            <div class="{{$key}}" style="display: none">{{$value}}</div>
                        @else
                            {{$key}}: {{$value}}<br>
                        @endif
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
                <h3 style="padding-left: 30px"id="deployment_table">Resource Information</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Selector</th>
            <th>Images</th>
        </tr>
        <tr>
            <td>
                @foreach($replicaset['spec']['selector']['matchLabels']??[''=>''] as $key => $value)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$value}}<br>
                    @endif
                @endforeach
            </td>
            <td>
                @foreach($replicaset['spec']['template']['spec']['containers'] as $container)
                    <div class="badge badge-pill bg-primary">
                            {{$container['image']}}
                        </div><br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Pod Status</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th colspan="4">Running</th>
            <th colspan="4">Desired</th>
        </tr>
        <tr>
            <td colspan="4">{{$replicaset['status']['readyReplicas']??'0'}}</td>
            <td colspan="4">{{$replicaset['status']['replicas']??'-'}}</td>
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
                <td><a href="{{ route('pod-details', ['name'=>$pod['metadata']['name'], 'namespace'=>$pod['metadata']['namespace']]) }}">{{$pod['metadata']['name']}}</a></td>
                <td>{{$pod['metadata']['namespace']}}</td>
                <td>
                    @foreach($pod['spec']['containers'] as $container)
                        <div class="badge badge-pill bg-primary">
                            {{$container['image']}}
                        </div><br>
                    @endforeach
                </td>
                <td>
                    @foreach($pod['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div><br>
                        @endif
                    @endforeach
                </td>
                <td>{{$pod['status']['phase']}}</td>
                <td>
                    @foreach($pod['status']['containerStatuses']??["restartCount"=>"-"] as $status)
                        {{$status['restartCount']??'-'}}<br>
                    @endforeach
                </td>
                <td>{{$pod['spec']['nodeName']??'-'}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($pod['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="services_table">Services</h3>
            </td>
        </tr>
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
                    <td><a href="{{ route('service-details', ['name'=>$service['metadata']['name'], 'namespace'=>$service['metadata']['namespace']??'default']) }}">{{$service['metadata']['name']}}</a></td>
                    <td>{{$service['metadata']['namespace']}}</td>
                    <td>
                        @foreach($service['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div><br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$service['spec']['type']}}</td>
                    <td>
                        @foreach($service['spec']['clusterIPs'] as $clusterIP)
                            {{$clusterIP}}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($service['spec']['ports'] as $port)
                            Name: {{$port['name']??"-"}}; Protocol: {{$port['protocol']}}<br>Port: {{$port['port']}}; Target Port: {{$port['targetPort']}}; Node Port: {{$port['nodePort']??"-"}}<hr>
                        @endforeach
                    </td>
                    <td>
                        @foreach($service['status']['loadBalancer']['ingress']??["-"] as $externalIP)
                            {{$externalIP['ip']??"-"}}<br>
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($service['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
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
            <tr class="text-center">
                <th colspan="8">Resource Not Found...</th>
            </tr>
            @foreach($events as $event)
                @if(str_contains($event['involvedObject']['name']??"", $replicaset['metadata']['name']))
                    <tr>
                        <td>{{$event['metadata']['name']}}</td>
                        <td>{{$event['reason']}}</td>
                        <td>{{$event['message']}}</td>
                        <td>{{$event['source']['component']??"-"}}/{{$event['source']['host']??"-"}}</td>
                        <td>{{$event['involvedObject']['kind']}}/{{$event['involvedObject']['name']??""}}</td>
                        <td>{{$event['count']??"0"}}</td>
                        <td>{{\Carbon\Carbon::createFromTimeString($event['firstTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                        <td>{{\Carbon\Carbon::createFromTimeString($event['lastTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr class="text-center">
                <th colspan="8">Resource Not Found...</th>
            </tr>
        @endif

        </tbody>
    </table>

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($replicaset, 512, 2)}}</div>

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

        let kind = '{{$replicaset['kind']}}';
        let namespace = '{{$replicaset['metadata']['namespace']}}';
        let name = '{{$replicaset['metadata']['name']}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')


@endsection
