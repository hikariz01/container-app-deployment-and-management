@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')


    @if(!is_null($services) && count($services) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="services_table">Services</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <td>Namespace</td>
                @endif
                <td>Labels</td>
                <td>Type</td>
                <td>Cluster IP</td>
                <td>Ports</td>
                <td>External IP</td>
                <td>Create Time</td>
            </tr>
            @foreach($services as $service)
                <tr>
                    <td><a href="{{ route('service-details', ['name'=>$service->getName(), 'namespace'=>$service->getMetadata()['namespace']??'default']) }}">{{$service->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$service->toArray()['metadata']['namespace']}}</td>
                    @endif
                    <td>
                        @foreach($service->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$service->toArray()['spec']['type']}}</td>
                    <td>
                        @foreach($service->toArray()['spec']['clusterIPs'] as $clusterIP)
                            {{$clusterIP}}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($service->toArray()['spec']['ports'] as $port)
                            Name: {{$port['name']??"-"}}; Protocol: {{$port['protocol']}}<br>Port: {{$port['port']}}; Target Port: {{$port['targetPort']}}; Node Port: {{$port['nodePort']??"-"}}<hr>
                        @endforeach
                    </td>
                    <td>
                        @foreach($service->toArray()['status']['loadBalancer']['ingress']??["-"] as $externalIP)
                            {{$externalIP['ip']??"-"}}<br>
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($service->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($ingresses) && count($ingresses) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="ingresses_table">Ingresses</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <td>Namespace</td>
                @endif
                <td>Labels</td>
                <td>Host</td>
                <td>Paths</td>
                <td>Service</td>
                <td>Create Time</td>
            </tr>
            @foreach($ingresses as $ingress)
                <tr>
                    <td><a href="{{ route('ingress-details', ['name'=>$ingress->getName(), 'namespace'=>$ingress->getMetadata()['namespace']??'default']) }}">{{$ingress->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$ingress->toArray()['metadata']['namespace']}}</td>
                    @endif
                    <td>
                        @foreach($ingress->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            <a href="{{'https://'.$rule['host']}}" target="_blank">{{$rule['host']}}</a><br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            @foreach($rule['http']['paths'] as $path)
                                {{$path['path']}}<br>
                            @endforeach
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            @foreach($rule['http']['paths'] as $path)
                                {{$path['backend']['service']['name']}} (Port: {{$path['backend']['service']['port']['number']}})<br>
                            @endforeach
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($ingress->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

{{--    TODO INGRESS CLASS PAGE--}}

    @if(!is_null($ingressclasses) && count($ingressclasses) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="ingressclasses_table">Ingress Classes</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Controller</td>
                <td>Create Time</td>
            </tr>
            @foreach($ingressclasses as $ingressclass)
                <tr>
                    <td><a href="{{ route('ingressclass-details', ['name'=>$ingressclass['metadata']['name']]) }}">{{$ingressclass['metadata']['name']}}</a></td>
                    <td>{{$ingressclass['spec']['controller']}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($ingressclass['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

@endsection
