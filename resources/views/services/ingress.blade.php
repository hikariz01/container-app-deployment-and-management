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
            <td>{{$ingress->getName()}}</td>
            <td>{{$ingress->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($ingress->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$ingress->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($ingress->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="5">Annotations</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($ingress->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Ingress Class Name</th>
        </tr>
        <tr>
            <td>
                <h4>
                    @if($ingress->getSpec('ingressClassName') !== null)
                        <a href="{{route('ingressclass-details', ['name'=>$ingress->getSpec('ingressClassName')])}}">{{$ingress->getSpec('ingressClassName')??'-'}}</a>
                    @else
                        -
                    @endif
                </h4>
            </td>
        </tr>
        <tr>
            <th>Endpoints</th>
        </tr>
{{--            TODO CURL ENDPOINTS AND USE IT--}}
        @foreach($ep as $endpoint)
            <tr>
                <td>
                    @foreach($endpoint['endpoints'] as $address)
                        @foreach($address['addresses'] as $addr)
                            @foreach($endpoint['ports'] as $port)
                                <span class="badge badge-pill bg-primary">{{$addr}}:{{$port['port']}}</span>
                            @endforeach
                        @endforeach
                    @endforeach
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Rules</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Host</th>
            <th>Path</th>
            <th>Path Type</th>
            <th>Service Name</th>
            <th>Service Port</th>
            <th>TLS Secret</th>
        </tr>
        @foreach($ingress->getRules() as $rule)
            <tr>
                <td><a href="{{'https://'.$rule['host']}}" target="_blank">{{$rule['host']}}</a></td>
                @foreach($rule['http']['paths'] as $path)
                    <td>{{$path['path']}}</td>
                    <td>{{$path['pathType']}}</td>
                    <td><a href="{{ route('service-details', ['name'=>$path['backend']['service']['name'], 'namespace'=>$ingress->getMetadata()['namespace']??'default']) }}">{{$path['backend']['service']['name']}}</a></td>
                    <td>{{$path['backend']['service']['port']['number']}}</td>
                @endforeach
                <td>
                    @foreach($ingress->getSpec('tls')??[0=>['secretName'=>'-']] as $host)
                        {{$host['secretName']}}<br>
                    @endforeach
                </td>
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
                @if(str_contains($event->toArray()['involvedObject']['name']??"", $ingress->getName()))
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


    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($ingress->toArray(), 512, 2)}}</div>

    @include('layouts.editFormModal')

    @include('layouts.deleteFormModal')

@endsection


@section('js')

    <script>

        let jsonEditor = document.querySelector('#jsonEditor')
        let aceJSONEditor = ace.edit("jsonEditor");

        aceJSONEditor.setTheme('ace/theme/monokai')
        aceJSONEditor.session.setMode("ace/mode/json");

        aceJSONEditor.setReadOnly(true)

        function updateJSON(e) {
            let data = document.getElementsByClassName(e.value)[0].innerHTML
            let jsonData = JSON.stringify(JSON.parse(data), null, '\t')
            aceJSONEditor.session.setValue(jsonData)
        }

        let aceData = document.querySelector('#data').innerHTML

        let editor = document.querySelector('#editor')
        let aceEditor = ace.edit("editor");

        aceEditor.setTheme('ace/theme/monokai')
        aceEditor.session.setMode("ace/mode/yaml");

        aceEditor.session.setValue(aceData)

        function updateData() {
            document.querySelector('input[name="value"]').value = aceEditor.session.getValue()
        }

        let kind = '{{$ingress->getKind()}}';
        let namespace = '{{$ingress->getNamespace()}}';
        let name = '{{$ingress->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

        function updateDownloadData() {
            let downloadName = document.querySelector('#resourceName')
            let downloadData = document.querySelector('#downloadData')

            downloadName.value = name
            downloadData.value = aceData
        }

    </script>


@endsection
