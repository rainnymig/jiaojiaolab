@extends('common')

@section('content')

<div class="container">
    <h1>Informations</h1>
    <hr>
    @foreach($infos as $info)
        <p>{{ $info }}</p>
    @endforeach
</div>

@endsection
