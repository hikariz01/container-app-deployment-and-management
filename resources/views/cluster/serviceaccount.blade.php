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
            <td>{{$sa->getName()}}</td>
            <td>{{$sa->getNamespace()}}</td>
            <td>{{$sa->toArray()['metadata']['creationTimestamp']}}</td>
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
        <h3 style="padding-left: 30px"id="deployment_table">Secrets</h3>
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
        <h3 style="padding-left: 30px"id="deployment_table">Image Pull Secrets</h3>
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



@endsection
