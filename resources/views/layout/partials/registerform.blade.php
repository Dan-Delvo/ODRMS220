<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light">

            <!-- Dark Header -->
            <div class="card-header text-white text-center py-4" style="background-color: #1f2937">
                <h3 class="my-0 fw-bold" style="color: #1dd3b0">Create Account</h3>
                <p class="text-white-50">Fill in the details below to create your account</p>
            </div>

            <form action="{{ route('student.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <!-- @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif -->

                    <!-- Name Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                                <input class="form-control rounded-pill @error('FirstName') is-invalid @enderror"
                                    value="{{ old('FirstName') }}"
                                    id="inputFirstName"
                                    type="text"
                                    name="FirstName"
                                    placeholder="Enter your first name"
                                    required />
                                <label for="inputFirstName">First Name</label>
                                @error('FirstName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control rounded-pill @error('MiddleName') is-invalid @enderror"
                                    value="{{ old('MiddleName') }}"
                                    id="inputMiddleName"
                                    type="text"
                                    name="MiddleName"
                                    placeholder="Enter your middle name" />
                                <label for="inputMiddleName">Middle Name (Optional)</label>
                                @error('MiddleName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill @error('LastName') is-invalid @enderror"
                            value="{{ old('LastName') }}"
                            id="inputLastName"
                            type="text"
                            name="LastName"
                            placeholder="Enter your last name"
                            required />
                        <label for="inputLastName">Last Name</label>
                        @error('LastName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Suffix -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill @error('Suffix') is-invalid @enderror"
                            value="{{ old('Suffix') }}"
                            id="inputSuffix"
                            type="text"
                            name="Suffix"
                            placeholder="Enter your suffix" />
                        <label for="inputSuffix">Suffix (Optional)</label>
                        @error('Suffix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- LRN -->
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill @error('LRN') is-invalid @enderror"
                            value="{{ old('LRN') }}"
                            id="inputLRN"
                            type="text"
                            name="LRN"
                            placeholder="Enter your LRN"
                            required />
                        <label for="inputLRN">LRN (Learner's Reference Number)</label>
                        @error('LRN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Grade Level -->
                    <div class="form-floating mb-3">
                        <select class="form-control rounded-pill @error('Grade_level') is-invalid @enderror"
                                id="grade_level"
                                name="Grade_level"
                                required>
                            <option value="" disabled {{ old('Grade_level') ? '' : 'selected' }}>Select Grade Level</option>
                            @foreach ($grade as $level)
                                <option value="{{ $level }}" @selected(old('Grade_level') == $level)>
                                    {{ $level }}
                                </option>
                            @endforeach
                        </select>
                        <label for="grade_level">Grade Level</label>
                        @error('Grade_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Student Status -->
                    <div class="form-floating mb-3">
                        <select class="form-control rounded-pill @error('Std_status') is-invalid @enderror"
                                id="inputStdStatus"
                                name="Std_status"
                                required>
                            <option value="" disabled {{ old('Std_status') ? '' : 'selected' }}>Select Status</option>
                            @foreach ($stat as $status)
                                <option value="{{ $status }}" @selected(old('Std_status') == $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        <label for="inputStdStatus">Student Status</label>
                        @error('Std_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Last School Year Attended -->  
                    <div class="form-floating mb-3">
                        <input class="form-control rounded-pill @error('Last_sy_attended') is-invalid @enderror"
                            value="{{ old('Last_sy_attended') }}"
                            id="inputLastSYAttended"
                            type="text"
                            name="Last_sy_attended"
                            placeholder="Enter last school year attended"
                            required />
                        <label for="inputLastSYAttended">Last School Year Attended</label>
                        @error('Last_sy_attended')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer py-3 d-flex justify-content-between align-items-center" style="background-color: #1f2937;">
                    <button class="btn btn-block" type="submit" style="background-color: #1dd3b0; color: white;">
                        Next
                    </button>
                    <div class="small" style="color: #1dd3b0;">
                        <a href="{{ route('login') }}" style="color: #1dd3b0;">Have an account? Go to login</a>
                    </div>
                </div>
            </form>
      
                 
        </div>
    </div>
</div>
