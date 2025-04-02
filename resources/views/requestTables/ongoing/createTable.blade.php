@extends('layout.blankpage')

@section ('content')

<h1 class="mt-4">Ongoing Requests</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
    <li class="breadcrumb-item active">Ongoing Requests</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">

                <h4>Ongoing Requests
                <a href="{{ url('ongoing') }}" class="btn btn-danger float-end">Back</a>
                </h4>

            </div>

            <div class="card-body">
                <form action="{{ route('ongoing.store') }}" method="POST">
                    @csrf
                <div class="mb-3">
                    <label>ID</label>
                    <input type="text" name="id" class="form-control" />
                    @error('id') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>claimer_id</label>
                    <input type="text" name="claimer_id" class="form-control" />
                    @error('claimer_id') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>student_information_id</label>
                    <input type="text" name="student_information_id" class="form-control" />
                    @error('student_information_id') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>document_id</label>
                    <input type="text" name="document_id" class="form-control" />
                    @error('document_id') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>request_schl_entity</label>
                    <input type="text" name="request_schl_entity" class="form-control" />
                    @error('request_schl_entity') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>requested_sf10</label>
                    <input type="text" name="requested_sf10" class="form-control" />
                    @error('requested_sf10') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>release_mode</label>
                    <input type="text" name="release_mode" class="form-control" />
                    @error('release_mode') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>remarks</label>
                    <input type="text" name="remarks" class="form-control" />
                    @error('remarks') {{$message}} @enderror
                </div>

                <div class="mb-3">
                    <label>status</label>
                    <input type="text" name="status" class="form-control" />
                    @error('status') {{$message}} @enderror
                </div>

                <!-- <div class="input-group mb-3">
                    <label class="input-group-text" for="inputGroupSelect01">status</label>
                    <select class="form-select" name="status" id="inputGroupSelect01">
                        <option selected>Choose...</option>
                        <option value="1">Pending</option>
                        <option value="2">Ongoing</option>
                        <option value="3">Ongoing</option>
                    </select>
                    @error('status') {{$message}} @enderror
                </div> -->

                <div>
                    <button type="submit" class="btn btn-primary float-end">Save</button>
                </div>

                </form>
            </div>
        </div>
    </div>

    <!-- 'claimer_id' !,
        'student_information_id' !,
        'approval_id',
        'document_id' !,
        'request_time' ,
        'request_date',
        'request_schl_entity' !,
        'requested_sf10' !,
        'release_mode', !
        'remarks' !,
        'status' !-->


    @endsection
