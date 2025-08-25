@extends('layout.blankpage')

@section('content')

<style>
.tooltip {
  position: relative;
  display: inline-block;
}

.tooltip .tooltip-text {
  visibility: hidden;
  width: 160px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 6px;
  border-radius: 4px;
  
  position: absolute;
  z-index: 1;
  bottom: 125%; /* Position above */
  left: 50%;
  transform: translateX(-50%);
  
  opacity: 0;
  transition: opacity 0.3s;
}

.tooltip:hover .tooltip-text {
  visibility: visible;
  opacity: 1;
}
</style>

<div class="row justify-content-center">

        @if(session('Success'))
        <div class="alert alert-success">
            {{ session('Success') }}
        </div>
        @endif


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
                        <input class="form-control" id="inputRequestSchlEntity" type="text" name="request_schl_entity" placeholder="Enter Requesting School/Entity" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Enter the name of the school requesting the document."
                        required />
                        <label for="inputRequestSchlEntity">Requesting School/Entity *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="inputDocumentId" name="document_id" required>
                            <option value="" disabled selected>Select Document Type</option>
                            @foreach($DocType as $doc)
                            <option value="{{$doc->id}}">{{$doc->DocType}}</option>
                            @endforeach
                        </select>
                        <label for="inputDocumentId">Requested Document *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputReleaseMode" type="text" value = "Pickup" name="release_mode" placeholder="Enter Release Mode" readonly/>
                        <label for="inputReleaseMode">Release Mode *</label>
                    </div>

                    <!-- Student Information -->
                    <h5>Student Information</h5>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputStudentFirstName" type="text" name="student_first_name" placeholder="Enter Student's First Name" required />
                        <label for="inputStudentFirstName">Student's First Name *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputStudentLastName" type="text" name="student_last_name" placeholder="Enter Student's Last Name" required />
                        <label for="inputStudentLastName">Student's Last Name *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLRN" type="text" name="lrn" placeholder="Enter LRN" />
                        <label for="inputLRN">LRN (OPTIONAL)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="inputGradeLevel" name="grade_level" required>
                            <option value="" disabled selected>Select Grade Level</option>
                            @foreach($grade as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                        <label for="inputGradeLevel">Grade Level *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-control" id="inputStudentStatus" name="student_status" required>
                            <option value="" disabled selected>Select Student Status</option>
                            @foreach($stat as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        <label for="inputStudentStatus">Student Status *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLastSYAttended" type="text" name="last_sy_attended" placeholder="Enter Last SY Attended" required />
                        <label for="inputLastSYAttended">Last SY Attended *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="email_address" type="email" name="email_address" placeholder="Enter Email Address" required />
                        <label for="inputEmailAddress">Email Address*</label>
                    </div>

                    <div class="mt-2 d-flex align-items-center justify-content-between">
                        <div class="d-grid"><button class="btn text-black fw-semibold" style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);" type="submit">Submit Request</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
