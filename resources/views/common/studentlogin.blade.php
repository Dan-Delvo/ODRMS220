@extends('layout.loginpage')

@section ('content')
<style>
    html, body {
        height: 100%;
        overflow: hidden;
    }
    body {
        margin: 0;
        background-color: #23272E;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    main {
        width: 100%;
        height: 100%;
    }
</style>

<div style="background-color: #23272E; min-height: 100vh;" class="d-flex justify-content-center align-items-center">
    @include('layout.partials.loginform')
</div>

@endsection
