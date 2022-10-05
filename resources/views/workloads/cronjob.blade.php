@extends('layouts.app2', ['namespaces'=>$namespaces])

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
            <td>{{$cronjob->getName()}}</td>
            <td>{{$cronjob->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($cronjob->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$cronjob->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($cronjob->toArray()['metadata']['labels']??[''=>''] as $key => $label)
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
                @foreach($cronjob->toArray()['metadata']['annotations']??[''=>''] as $key => $value)
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
            <th>Schedule</th>
            <th>Active Jobs</th>
            <th>Suspend</th>
            <th>Last schedule</th>
            <th>Concurrency policy</th>
        </tr>
        <tr>
            <td>{{$cronjob->getSpec('schedule')}}</td>
            <td>{{count($cronjob->getActiveJobs()->toArray())}}</td>
            <td>{{$cronjob->getSpec('suspend') ? 'true' : 'false'}}</td>
            <td>{{date('d-m-Y H:i:s',$cronjob->getLastSchedule()->getTimestamp())}}</td>
            <td>{{$cronjob->getSpec('concurrencyPolicy')??'-'}}</td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Active Jobs</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($activeJobArr) != 0)
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??'default', 'all'))
                    <th>Namespace</th>
                @endif
                <th>Images</th>
                <th>Labels</th>
                <th>Pods</th>
                <th>Create Time</th>
            </tr>
            @foreach($activeJobArr as $activeJob)
                <tr>
                    <td>{{$activeJob->getName()}}</td>
                    @if(!strcmp($_GET['namespace']??'default', 'all'))
                        <td>{{$activeJob->getMetadata()['namespace']??'-'}}</td>
                    @endif
                    <td>
                        @foreach($activeJob->getSpec('template')['spec']['containers']??[] as $container)
                            {{$container['image']}}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($activeJob->getLabels() as $key => $label)
                            {{$key}}:{{$label}}<br>
                        @endforeach
                    </td>
                    <td>{{$activeJob->getStatus('ready')}}/{{$activeJob->getStatus('succeeded')??'1'}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($activeJob->toArray()['metadata']['creationTimestamp']??'0', 'UTC')->isToday() ? '-' : \Carbon\Carbon::createFromTimeString($activeJob->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                <th colspan="6">Resource not found...</th>
            </tr>
        @endif

        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Inactive Jobs</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($inactiveJobs) != 0)
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??'default', 'all'))
                    <th>Namespace</th>
                @endif
                <th>Images</th>
                <th>Labels</th>
                <th>Pods</th>
                <th>Create Time</th>
            </tr>
            @foreach($inactiveJobs as $inactiveJob)
                <tr>
                    <td>{{$inactiveJob->getName()}}</td>
                    @if(!strcmp($_GET['namespace']??'default', 'all'))
                        <td>{{$inactiveJob->getMetadata()['namespace']??'-'}}</td>
                    @endif
                    <td>
                        @foreach($inactiveJob->getSpec('template')['spec']['containers']??[] as $container)
                            {{$container['image']}}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($inactiveJob->getLabels() as $key => $label)
                            {{$key}}:{{$label}}<br>
                        @endforeach
                    </td>
                    <td>{{$inactiveJob->getStatus('ready')}}/{{$inactiveJob->getStatus('succeeded')??'1'}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($inactiveJob->toArray()['metadata']['creationTimestamp']??'0', 'UTC')->isToday() ? '-' : \Carbon\Carbon::createFromTimeString($inactiveJob->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>

                </tr>
            @endforeach

        @else
            <tr class="text-center">
                <th colspan="6">Resource not found...</th>
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
                @if(str_contains($event->toArray()['involvedObject']['name']??"", $cronjob->getName()))
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


    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($cronjob->toArray(), 512, 2)}}</div>

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

        let kind = '{{$cronjob->getKind()}}';
        let namespace = '{{$cronjob->getNamespace()}}';
        let name = '{{$cronjob->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')

@endsection
