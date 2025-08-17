@extends('layout.blankpage')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Activity Log</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Activity Log</li>
        </ol>
    </div>
    <div class="col-md-6 text-end">
        <h1 class="mt-4 text-dark">
            <span class="badge" style="background-color:#1f2937; font-size: 2rem;">Total Activity Logs: </span>
        </h1>
    </div>
</div>

@endsection