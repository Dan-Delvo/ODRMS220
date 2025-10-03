editTable.blade.php
@extends('layout.blankpage')

@section('content')

<div class="row mb-3">
    <div class="col-md-12">
        <h1 class="mt-4">
            <span class="badge" style="background-color: #1dd3b0; font-size: 2rem;">Processing Requests</span>
        </h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-dark">Dashboard</a></li>
            <li class="breadcrumb-item active">Edit Processing Request</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow border-0 rounded-lg">

            <div class="card-header text-white d-flex align-items-center justify-content-between" style="background-color: #1f2937; height: 70px;">
                <h4 class="mb-0">
                    Edit Processing Request
                </h4>
                <a href="{{ url('ongoing') }}" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">
                    Back
                </a>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('ongoing.update', $ongoing->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $ongoing->id }}">

        
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label>Claimer</label>
                            <input type="text" name="claimer_id" class="form-control" value="{{$ongoing->claimer->full_name}}" />
                            @error('claimer_id') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-lg-6">
                            <label for="inputDocumentId">Requested Document</label>
                            <select class="form-control" id="inputDocumentId" name="document_id">
                                @foreach($DocType as $doc)
                                <option value="{{$doc->id}}">{{$doc->DocType}}</option>
                                @endforeach
                            </select>
                            @error('document_id') {{$message}} @enderror
                        </div>
                    </div>

                    <div class="row">

                        <div class="mb-3 col-lg-8">
                            <label>Requesting School</label>
                            <input type="text" name="request_schl_entity" class="form-control" value="{{$ongoing->request_schl_entity}}" readonly />
                            @error('request_schl_entity') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-lg-4">
                            <label>Request Mode</label>
                            <input type="text" name="requested_sf10" class="form-control" value="{{$ongoing->request_mode}}" readonly />
                            @error('requested_sf10') {{$message}} @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label>Release Mode</label>
                            <input type="text" name="release_mode" class="form-control" value="{{$ongoing->release_mode}}" readonly />
                            @error('release_mode') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label>Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{$ongoing->remarks}}" readonly />
                            @error('remarks') {{$message}} @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label>Request Status</label>
                            <input type="text" name="status" class="form-control" value="{{$ongoing->status}}" readonly />
                            @error('status') {{$message}} @enderror
                        </div>

                        <h5>Edit Date</h5>

                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label>Approved Date</label>
                                <input type="date" name="app_date" class="form-control"/>
                                @error('app_date') {{$message}} @enderror
                            </div>
                        </div>

                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
