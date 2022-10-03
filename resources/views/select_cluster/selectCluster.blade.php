@extends('layouts.app2')


@section('content')

    <div style="width: 80vw; height: calc(100vh - 90px); margin-left:auto; margin-right: auto; margin-top: 10px">
        <form action="{{ route('submit-cluster') }}" method="POST">
            <div class="form-group">
                @csrf
                <label for="selectedCluster" style="font-size: 1.1rem">Please select your cluster</label>
                <select name="selectedCluster" id="selectedCluster" class="form-control form-control-lg mb-3">
                    @foreach($clusters as $cluster)
                        <option value="{{$cluster->id}}">{{$cluster->name}}[{{$cluster->url}}]</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-outline-success" style="width: 10vw"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
        </form>
    </div>

@endsection
