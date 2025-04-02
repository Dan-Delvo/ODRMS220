@extends('layout.studentpage')

@section ('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light" style="min-height: 450px;">
            <!-- Dark Header -->
            <div class="card-header bg-dark text-white text-center">
                <h3 class="my-3 fw-bold">Document Request Form</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('studentrequest.store') }}" method="POST">
                    @csrf

                    <h5 class="fw-bold">Document Request Information</h5>

                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputRequestSchlEntity" type="text" name="request_schl_entity" placeholder="Enter Requesting School/Entity" />
                        <label for="inputRequestSchlEntity">Requesting School/Entity</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select rounded-pill" id="inputDocumentId" name="document_id">
                            @foreach($DocType as $doc)
                            <option value="{{$doc->id}}">{{$doc->DocType}}</option>
                            @endforeach
                        </select>
                        <label for="inputDocumentId">Requested Document</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputReleaseMode" type="text" name="release_mode" placeholder="Enter Release Mode" />
                        <label for="inputReleaseMode">Release Mode</label>
                    </div>

                    <h5 class="fw-bold">Claimer Information</h5>

                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputFname" type="text" name="Fname" placeholder="Enter First Name" />
                        <label for="inputFname">First Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputLname" type="text" name="Lname" placeholder="Enter Last Name" />
                        <label for="inputLname">Last Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill" id="inputContactNo" type="text" name="contact_no" placeholder="Enter Contact Number" />
                        <label for="inputContactNo">Contact Number</label>
                    </div>

                    <div class="mt-4 mb-0">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg fw-bold" type="submit">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer text-center py-3 bg-dark">
                <div class="small"><a href="{{ route('dashboard') }}" class="text-light">Back to Dashboard</a></div>
            </div>
        </div>
    </div>
</div>

@endsection
