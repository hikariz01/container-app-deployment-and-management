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
            <td>{{$statefulset->getName()}}</td>
            <td>{{$statefulset->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($statefulset->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$statefulset->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($statefulset->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
            <th colspan="5">Annotations</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($statefulset->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
                    @if($key == "")
                        -
                    @elseif(is_array(json_decode($value, true)))
                        {{$key}}: <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#annoJSON" type="button" onclick="updateJSON(this)" value="{{$key}}">JSON</button><br>
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
                <h3 style="padding-left: 30px"id="deployment_table">Resource Information</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Images</th>
        </tr>
        <tr>
            <td>
                @foreach($statefulset->getTemplate('spec')->getContainers(false) as $container)
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
            <th>Running</th>
            <th>Desired</th>
        </tr>
        <tr>
            <td>{{$statefulset->getStatus('availableReplicas')}}</td>
            <td>{{$statefulset->getStatus('replicas')}}</td>
        </tr>
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
                        <div class="badge badge-pill bg-primary">
                            {{$container['image']}}
                        </div><br>
                    @endforeach
                </td>
                <td>
                    @foreach($pod->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div><br>
                        @endif
                    @endforeach
                </td>
                <td>{{$pod->toArray()['status']['phase']}}</td>
                <td>
                    @foreach($pod->toArray()['status']['containerStatuses']??["restartCount"=>"-"] as $status)
                        {{$status['restartCount']}}<br>
                    @endforeach
                </td>
                <td>{{$pod->getSpec('nodeName')??'-'}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($pod->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

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
                @if(str_contains($event->toArray()['involvedObject']['name']??"", $statefulset->getName()))
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

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($statefulset->toArray(), 512, 2)}}</div>

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

        let kind = '{{$statefulset->getKind()}}';
        let namespace = '{{$statefulset->getNamespace()}}';
        let name = '{{$statefulset->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')


@endsection
