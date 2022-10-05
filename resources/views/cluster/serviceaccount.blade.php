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
            <td>{{$sa->getName()}}</td>
            <td>{{$sa->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($sa->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$sa->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($sa->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($sa->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
                <h3 style="padding-left: 30px"id="deployment_table">Secrets</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($sa->toArray()['secrets']??[]) != 0)
            <tr>
                <th>Name</th>
                <th>Namespace</th>
                <th>Kind</th>
            </tr>
            @foreach($sa->toArray()['secrets']??[] as $secret)
                <tr>
                    <td>{{$secret['name']??'-'}}</td>
                    <td>{{$secret['namespace']??'-'}}</td>
                    <td>{{$secret['kind']??'-'}}</td>
                </tr>
            @endforeach

        @endif
        <tr class="text-center">
            <th>Resource not found...</th>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Image Pull Secrets</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(count($sa->toArray()['imagePullSecrets']??[]) != 0)
            <tr>
                <th>Name</th>
            </tr>
            @foreach($sa->toArray()['imagePullSecrets']??[] as $imgPullSecret)
                <tr>
                    <td>{{$imgPullSecret['name']??'-'}}</td>
                </tr>
            @endforeach
        @endif
        <tr class="text-center">
            <th>Resource not found...</th>
        </tr>
        </tbody>
    </table>


    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($sa->toArray(), 512, 2)}}</div>

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

        let kind = '{{$sa->getKind()}}';
        let namespace = '{{$sa->getNamespace()}}';
        let name = '{{$sa->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')

@endsection
