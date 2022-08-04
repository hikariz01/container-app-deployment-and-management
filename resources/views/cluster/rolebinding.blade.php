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
            <td>{{$rolebinding->getName()}}</td>
            <td>{{$rolebinding->getNamespace()}}</td>
            <td>{{$rolebinding->toArray()['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$rolebinding->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($rolebinding->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($rolebinding->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
        <h3 style="padding-left: 30px"id="deployment_table">Resource information</h3>
        </thead>
        <tbody>
        <tr>
            <th>Role Reference</th>
        </tr>
        <tr>
            <td><a href="{{ route('role-details', ['name'=>$role->getName(), 'namespace'=>$role->getMetadata()['namespace']??'default']) }}">{{$role->getName()}}</a></td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Subjects</h3>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Kind</th>
            <th>API Group</th>
        </tr>
        <tr>
            @foreach($rolebinding->getSubjects(false) as $subject)
                {{--                TODO add link to serviceAccount--}}
                @if($subject['kind'] === 'ServiceAccount')
                    <td><a href="{{ route('serviceaccount-details', ['name'=>$subject['name'], 'namespace'=>$subject['namespace']??'default']) }}">{{$subject['name']??'-'}}</a></td>
                @else
                    <td>{{$subject['name']??'-'}}</td>
                @endif
                <td>{{$subject['namespace']??'-'}}</td>
                <td>{{$subject['kind']??'-'}}</td>
                <td>{{$subject['apiGroup']??'-'}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>


@endsection
