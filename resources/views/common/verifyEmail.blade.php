@extends('layout.loginpage')

@section ('content')

@include('layout.partials.message')
    <div style="height: 100vh; background-color:rgb(53, 56, 62);">
        @include('layout.partials.verifyOtpForm')
    </div>

@endsection
