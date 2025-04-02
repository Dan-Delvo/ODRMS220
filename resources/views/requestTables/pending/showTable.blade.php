@extends('layout.blankpage')

@section ('content')

<h1 class="mt-4">Pending Requests</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
    <li class="breadcrumb-item active">Complteted Requests</li>
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

                <div class="mb-3">
                    <label>ID</label>
                    <p>
                    {{$table->id}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>claimer_id</label>
                    <p>
                    {{$table->claimer->full_name}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>student_information_id</label>
                    <p>
                    {{$table->student_information_id}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>document_id</label>
                    <p>
                    {{$table->document_id}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>request_schl_entity</label>
                    <p>
                    {{$table->request_schl_entity}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>requested_sf10</label>
                    <p>
                    {{$table->requested_sf10}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>release_mode</label>
                    <p>
                    {{$table->release_mode}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>remarks</label>
                    <p>
                    {{$table->remarks}}
                    </p>
                </div>

                <div class="mb-3">
                    <label>status</label>
                    <p>
                    {{$table->status}}
                    </p>

                </div>


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
