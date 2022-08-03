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
            <td>{{$ingress->getName()}}</td>
            <td>{{$ingress->getNamespace()}}</td>
            <td>{{$ingress->toArray()['metadata']['creationTimestamp']}}</td>
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
                @foreach($ingress->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
        <h3 style="padding-left: 30px"id="deployment_table">Resource Information</h3>
        </thead>
        <tbody>
        <tr>
            <th>Ingress Class Name</th>
        </tr>
        <tr>
            <td><h4>{{$ingress->getSpec('ingressClassName')??'-'}}</h4></td>
        </tr>
        <tr>
            <th>Endpoints</th>
        </tr>
        <tr>
{{--            TODO CURL ENDPOINTS AND USE IT--}}
            <td></td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Rules</h3>
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
        <h3 style="padding-left: 30px" id="events_table">Events</h3>
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
                        <td>{{$event->toArray()['firstTimestamp']}}</td>
                        <td>{{$event->toArray()['lastTimestamp']}}</td>
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


@endsection
