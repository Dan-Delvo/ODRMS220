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
                <h4 style="font-family: 'Poppins', sans-serif;"> PENDING </h4>
            </div>
            <div class="card-body">

                @foreach($requests as $req)
                    @if($req->Status == 'Pending')
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title"> {{$req->School_Name}} </h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">School Name</h6>
                                <p class="card-text">Document: {{$req->Doc_Type}}</p>
                                <button type="button" class="btn btn-outline-primary">Number of Student: {{$req->students_count}}</button>
                                <button type="button" class="btn btn-outline-success mt-1">Move to Processing</button>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                <h4 style="font-family: 'Poppins', sans-serif;"> PROCESSING </h4>
            </div>
            <div class="card-body">

                @php $hasForRelease = false; @endphp
                @foreach($requests as $req)
                    @if($req->Status == 'Processing')
                        @php $hasForRelease = true; @endphp
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title"> {{$req->School_Name}} </h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">School Name</h6>
                                <p class="card-text">Document: {{$req->Doc_Type}}</p>
                                <button type="button" class="btn btn-outline-primary">Number of Student: {{$req->students_count}}</button>
                                <button type="button" class="btn btn-outline-success mt-1">Move to For Release</button>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if(!$hasForRelease)
                    <div class="empty-state">
                        <p class="mb-0">No requests in Processing</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                <h4 style="font-family: 'Poppins', sans-serif;"> FOR RELEASE </h4>
            </div>
            <div class="card-body">

                @foreach($requests as $req)
                    @if($req->Status == 'For Release')
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title"> {{$req->School_Name}} </h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">School Name</h6>
                                <p class="card-text">Document: {{$req->Doc_Type}}</p>
                                <button type="button" class="btn btn-outline-primary">Number of Student: {{$req->students_count}}</button>
                                <button type="button" class="btn btn-outline-success mt-1">Move to Claimed</button>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card" style="width: auto;">
            <div class="card-header">
                <h4 style="font-family: 'Poppins', sans-serif;">CLAIMED</h4>
            </div>
            <div class="card-body">

                @foreach($requests as $req)
                    @if($req->Status == 'Claimed')
                        <div class="card text-center mt-2">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title"> {{$req->School_Name}} </h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">School Name</h6>
                                <p class="card-text">Document: {{$req->Doc_Type}}</p>
                                <button type="button" class="btn btn-outline-primary">Number of Student: {{$req->students_count}}</button>
                                <button type="button" class="btn btn-outline-success mt-1">Move to Processing</button>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>

</div>




@endsection
