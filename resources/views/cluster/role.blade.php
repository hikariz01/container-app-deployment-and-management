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
            <td>{{$role->getName()}}</td>
            <td>{{$role->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($role->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$role->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($role->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($role->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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

    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Rules</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Resources</th>
            <th>Non-resource URL</th>
            <th>Resource Names</th>
            <th>Verbs</th>
            <th>API Groups</th>
        </tr>
        @foreach($role->getRules(false) as $rule)
            <tr>
                @if(count($rule['resources']??[]) != 0)
                    <td>
                        @foreach($rule['resources']??[] as $key => $resource)
                            @if($key === array_key_last($rule['resources']))
                                {{$resource}}
                            @else
                                {{$resource.', '}}
                            @endif
                        @endforeach
                    </td>
                @else
                    <td>-</td>
                @endif
                @if(count($rule['nonResourceURLs']??[]) != 0)
                    <td>
                        @foreach($rule['nonResourceURLs']??[] as $key => $nonResourceURL)
                            @if($key === array_key_last($rule['nonResourceURLs']))
                                {{$nonResourceURL}}
                            @else
                                {{$nonResourceURL.', '}}
                            @endif
                        @endforeach
                    </td>
                @else
                    <td>-</td>
                @endif
                @if(count($rule['resourceNames']??[]) != 0)
                    <td>
                        @foreach($rule['resourceNames']??[] as $key => $resourceName)
                            @if($key === array_key_last($rule['resourceNames']))
                                {{$resourceName}}
                            @else
                                {{$resourceName.', '}}
                            @endif
                        @endforeach
                    </td>
                @else
                    <td>-</td>
                @endif
                @if(count($rule['verbs']??[]) != 0)
                    <td>
                        @foreach($rule['verbs']??[] as $key => $verb)
                            @if($key === array_key_last($rule['verbs']))
                                {{$verb}}
                            @else
                                {{$verb.', '}}
                            @endif
                        @endforeach
                    </td>
                @else
                    <td>-</td>
                @endif
                @if(count($rule['apiGroups']??[]) != 0)
                    <td>
                        @foreach($rule['apiGroups']??[] as $key => $apiGroup)
                            @if($key === array_key_last($rule['apiGroups']))
                                @if(strcmp($apiGroup, ''))
                                    {{$apiGroup}}
                                @else
                                    -
                                @endif
                            @else
                                {{$apiGroup.', '}}
                            @endif
                        @endforeach
                    </td>
                @else
                    <td>-</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($role->toArray(), 512, 2)}}</div>

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

        let kind = '{{$role->getKind()}}';
        let namespace = '{{$role->getNamespace()}}';
        let name = '{{$role->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

    </script>

    @include('layouts.jsonEditor')

@endsection
