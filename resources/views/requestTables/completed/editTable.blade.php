@extends('layout.blankpage')

@section('content')
    <h1 class="mt-4">Completed Requests</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
        <li class="breadcrumb-item active">Completed Requests</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Requests
                    <a href="{{ url('pending') }}" class="btn btn-danger float-end">Back</a>
                    </h4>

                </div>

                <div class="card-body">
                    <form action="{{ route('pending.update', $pending->id) }}" method="POST">
                        @csrf
                        @method('PUT')


                    <input type="hidden" name="id" value="{{ $pending->id }}">


                    <div class="mb-3">
                        <label>Claimer</label>
                        <input type="text" name="claimer_id" class="form-control" value="{{$pending->claimer->full_name}}" />
                        @error('claimer_id') {{$message}} @enderror
                    </div>

                    <div class="mb-3">
                        <label for="inputDocumentId">Requested Document</label>
                        <select class="form-control" id="inputDocumentId" name="document_id">
                            @foreach($DocType as $doc)
                            <option value="{{$doc->id}}">{{$doc->DocType}}</option>
                            @endforeach
                        </select>
                        @error('document_id') {{$message}} @enderror
                    </div>

                    <div class="mb-3">
                        <label>Requesting School</label>
                        <input type="text" name="request_schl_entity" class="form-control" value="{{$pending->request_schl_entity}}"/>
                        @error('request_schl_entity') {{$message}} @enderror
                    </div>

                    <div class="mb-3">
                        <label>Request Mode</label>
                        <input type="text" name="requested_sf10" class="form-control" value="{{$pending->request_mode}}"/>
                        @error('requested_sf10') {{$message}} @enderror
                    </div>

                    <div class="mb-3">
                        <label>Release Mode</label>
                        <input type="text" name="release_mode" class="form-control" value="{{$pending->release_mode}}"/>
                        @error('release_mode') {{$message}} @enderror
                    </div>

                    <div class="mb-3">
                        <label>Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{$pending->remarks}}"/>
                        @error('remarks') {{$message}} @enderror
                    </div>

                    <div class="mb-3">
                        <label>Request Status</label>
                        <input type="text" name="status" class="form-control" value="{{$pending->status}}"/>
                        @error('status') {{$message}} @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary float-end">Save</button>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
