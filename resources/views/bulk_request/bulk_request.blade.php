@extends('layout.blankpage')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('layout.partials.message')

{{-- Header Section --}}
<div class="row g-2">
    <div class="col-md-6">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #626079; font-size: 2rem;">Bulk Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Bulk Requests</li>
        </ol>
    </div>
</div>

<div class="row g-1">
    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                Featured
            </div>
            <div class="card-body">
                {{-- Content --}}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                Featured
            </div>
            <div class="card-body">
                {{-- Content --}}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                Featured
            </div>
            <div class="card-body">
                {{-- Content --}}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                Featured
            </div>
            <div class="card-body">
                {{-- Content --}}
            </div>
        </div>
    </div>

</div>




@endsection
