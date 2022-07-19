@extends('layouts.app2')


@section('content')
    @foreach($deployments as $deployment)
        <h1>{{$deployment->getName()}}</h1>

    @endforeach
@endsection
