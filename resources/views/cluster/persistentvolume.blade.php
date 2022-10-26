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
            <th>Created</th>
            <th>Age</th>
            <th>UID</th>
        </tr>
        <tr>
            <td>{{$pv->getName()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($pv->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$pv->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="4">Labels</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($pv->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
            <th colspan="4">Annotations</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($pv->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Status</th>
            <th>Claim</th>
            <th>Reclaim policy</th>
            <th>Storage class</th>
            <th>Access modes</th>
        </tr>
        <tr>
            <td>{{$pv->getPhase()}}</td>
            <td><a href="{{ route('pvc-details', ['name'=>$pv->getSpec('claimRef')['name'], 'namespace'=>$pv->getSpec('claimRef')['namespace']]) }}">{{$pv->getSpec('claimRef')['namespace'].'/'.$pv->getSpec('claimRef')['name']}}</a></td>
            <td>{{$pv->getSpec('persistentVolumeReclaimPolicy')??'-'}}</td>
            <td>{{$pv->getStorageClass()??'-'}}</td>
            <td>
                @foreach($pv->getAccessModes() as $accessMode)
                    {{$accessMode}}<br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Source</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($types as $type)
                @if(!is_null($pv->getSpec($type)))
                    <th>Type</th>
                    @foreach($pv->getSpec($type) as $key => $value)
                        <th>{{ucwords($key)}}</th>
                    @endforeach
                @endif
            @endforeach
        </tr>
        <tr>
            @foreach($types as $type)
                @if(!is_null($pv->getSpec($type)))
                    <td>{{strtoupper($type)}}</td>
                    @foreach($pv->getSpec($type) as $key => $value)
                        <td>{{$value}}</td>
                    @endforeach
                @endif
            @endforeach
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Capacity</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Resource name</th>
            <th>Quantity</th>
        </tr>
        @foreach($pv->getSpec('capacity') as $name => $capacity)
            <tr>
                <td>{{$name}}</td>
                <td>{{$capacity}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($pv->toArray(), 512, 2)}}</div>

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

        let kind = '{{$pv->getKind()}}';
        let namespace = '{{$pv->getNamespace()}}';
        let name = '{{$pv->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')

@endsection
