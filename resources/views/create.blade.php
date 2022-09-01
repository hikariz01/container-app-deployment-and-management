@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <div style="width: 80vw; height: calc(100vh - 90px); margin-left:auto; margin-right: auto; margin-top: 30px">
        <form action="{{ route('create-resource') }}" method="POST">
            <div class="form-group">
                @csrf
                <label for="selectResourceType" style="font-size: 1.1rem">Please select resource type</label>
                <select name="selectResourceType" id="selectResourceType" class="form-control form-control-lg mb-3">
                    @foreach($resourceTypes as $key => $value)
                        <optgroup label="{{$key}}">
                            @foreach($value as $resource)
                                <option value="{{$resource}}">{{$resource}}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-outline-success" style="width: 10vw"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
        </form>
    </div>

@endsection
