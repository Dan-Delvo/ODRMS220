@extends('layout.blankpage')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5" style="min-height: 600px;">
            <div class="card-header text-white" style="background-color: #1f2937;">
                <h3 class="text-center font-weight-light my-4">Document Request Form</h3>
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
                <form action="{{ route('walkin.store') }}" method="POST">
                    @csrf

                    <!-- Document Request Information -->
                    <h5>Document Request Information</h5>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputRequestSchlEntity" type="text" name="request_schl_entity" placeholder="Enter Requesting School/Entity" />
                        <label for="inputRequestSchlEntity">Requesting School/Entity</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="inputDocumentId" name="document_id">
                            @foreach($DocType as $doc)
                            <option value="{{$doc->id}}">{{$doc->DocType}}</option>
                            @endforeach
                        </select>
                        <label for="inputDocumentId">Requested Document</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputReleaseMode" type="text" name="release_mode" placeholder="Enter Release Mode" />
                        <label for="inputReleaseMode">Release Mode</label>
                    </div>

                    <!-- Claimer Information -->
                    <h5>Claimer Information</h5>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputFname" type="text" name="Fname" placeholder="Enter First Name" />
                        <label for="inputFname">First Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLname" type="text" name="Lname" placeholder="Enter Last Name" />
                        <label for="inputLname">Last Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputContactNo" type="text" name="contact_no" placeholder="Enter Contact Number" />
                        <label for="inputContactNo">Contact Number</label>
                    </div>

                    <!-- Student Information -->
                    <h5>Student Information</h5>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputStudentFirstName" type="text" name="student_first_name" placeholder="Enter Student's First Name" />
                        <label for="inputStudentFirstName">Student's First Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputStudentLastName" type="text" name="student_last_name" placeholder="Enter Student's Last Name" />
                        <label for="inputStudentLastName">Student's Last Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLRN" type="text" name="lrn" placeholder="Enter LRN" />
                        <label for="inputLRN">LRN</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="inputGradeLevel" name="grade_level">
                            @foreach($grade as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                        <label for="inputGradeLevel">Grade Level</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="inputStudentStatus" name="student_status">
                            @foreach($stat as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        <label for="inputStudentStatus">Student Status</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLastSYAttended" type="text" name="last_sy_attended" placeholder="Enter Last SY Attended" />
                        <label for="inputLastSYAttended">Last SY Attended</label>
                    </div>

                    <div class="mt-2 d-flex align-items-center justify-content-between">
                        <div class="d-grid"><button class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);" type="submit">Submit Request</button></div>
                        <a href="{{ route('dashboard') }}" class="btn text-white fw-semibold" style="background-color: #1f2937; box-shadow: 0 4px 10px #1f2937 ;">
                            Back to dashboard
                        </a>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
