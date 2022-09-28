@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')


    <style>
        .table td, .table th {
            padding: 5px 0.4rem;
        }
    </style>


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
            <td>{{$crb->getName()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($crb->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$crb->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="4">Labels</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($crb->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="4">Annotations</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($crb->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Resource information</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Role Reference</th>
        </tr>
        <tr>
            <td><a href="{{ route('clusterrole-details', ['name'=>$cr->getName()]) }}">{{$cr->getName()}}</a></td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Subjects</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Kind</th>
            <th>API Group</th>
        </tr>
        <tr>
            @foreach($crb->getSubjects(false) as $subject)
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
