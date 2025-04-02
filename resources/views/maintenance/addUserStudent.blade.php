@extends('layout.blankpage')

@section ('content')

<!-- Main Content Wrapper -->
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5" style="min-height: 400px;">
            <div class="card-header bg-dark text-white">
                <h3 class="text-center font-weight-light my-4">Create Account</h3>
            </div>
            <div class="card-body bg-light">

                <!-- Error Messages -->
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Form to create user -->
                <form action="{{ route('userStud.store') }}" method="POST">
                    @csrf

                    <!-- Personal Information Section -->
                    <h4 class="mb-3 text-dark">Personal Information</h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                                <input class="form-control" id="inputFirstName" type="text" name="FirstName" placeholder="Enter your first name" />
                                <label for="inputFirstName">First Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="inputLastName" type="text" name="LastName" placeholder="Enter your last name" />
                                <label for="inputLastName">Last Name</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputMiddleName" type="text" name="MiddleName" placeholder="Enter your middle name" />
                        <label for="inputMiddleName">Middle Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputSuffix" type="text" name="Suffix" placeholder="Enter your suffix" />
                        <label for="inputSuffix">Suffix (Optional)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLRN" type="text" name="LRN" placeholder="Enter your LRN" />
                        <label for="inputLRN">LRN (Learner's Reference Number)</label>
                    </div>

                    <div class="form-floating mb-3">
                        <div class="form-group">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <select class="form-control" id="grade_level" name="Grade_level">
                                <option value="" disabled selected>Select Grade Level</option>
                                @foreach ($grade as $level)
                                    <option value="{{ $level }}">{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <div class="form-group">
                            <label for="std_status" class="form-label">Student Status</label>
                            <select class="form-control" id="inputStdStatus" name="Std_status">
                                <option value="" disabled selected>Select Status</option>
                                @foreach ($stat as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputLastSYAttended" type="text" name="Last_sy_attended" placeholder="Enter last school year attended" />
                        <label for="inputLastSYAttended">Last School Year Attended</label>
                    </div>

                    <!-- Account Information Section -->
                    <h4 class="mb-3 text-dark">Account Information</h4>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputEmail" type="email" name="email_address" placeholder="name@example.com" />
                        <label for="inputEmail">Email Address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputUsername" type="text" name="username" placeholder="Enter your username" />
                        <label for="inputUsername">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Create a password" />
                        <label for="inputPassword">Password</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="inputPasswordConfirm" type="password" name="password_confirmation" placeholder="Confirm password" />
                        <label for="inputPasswordConfirm">Confirm Password</label>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 mb-0">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-block" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
