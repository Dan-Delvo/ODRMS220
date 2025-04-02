@extends('layout.blankpage')

@section('content')

<h1 class="mt-4">Edit Student</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student') }}">Students List</a></li>
    <li class="breadcrumb-item active">Edit Student</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header bg-dark text-white">
                <h4>Edit Student
                    <a href="{{ route('student') }}" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>

            <div class="card-body bg-light">
                <form action="{{ route('student.update', $student->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Student ID -->
                    <div class="mb-3">
                        <label for="id" class="form-label">Student ID</label>
                        <input type="text" name="id" id="id" class="form-control" value="{{ $student->id }}" readonly>
                    </div>

                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="FirstName" class="form-label">First Name</label>
                        <input type="text" name="FirstName" id="FirstName" class="form-control" value="{{ $student->FirstName }}">
                        @error('FirstName') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="LastName" class="form-label">Last Name</label>
                        <input type="text" name="LastName" id="LastName" class="form-control" value="{{ $student->LastName }}">
                        @error('LastName') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- LRN -->
                    <div class="mb-3">
                        <label for="LRN" class="form-label">LRN</label>
                        <input type="text" name="LRN" id="LRN" class="form-control" value="{{ $student->LRN }}">
                        @error('LRN') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Grade Level -->
                    <div class="mb-3">
                        <label for="Grade_level" class="form-label">Grade Level</label>
                        <select name="Grade_level" id="Grade_level" class="form-select">
                            @foreach ($gradeLevels as $level)
                                <option value="{{ $level }}" {{ $level == $student->Grade_level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('Grade_level') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Student Status -->
                    <div class="mb-3">
                        <label for="Std_status" class="form-label">Student Status</label>
                        <select name="Std_status" id="Std_status" class="form-select">
                            <option value="Active" {{ $student->Std_status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $student->Std_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('Std_status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Last School Year Attended -->
                    <div class="mb-3">
                        <label for="Last_sy_attended" class="form-label">Last School Year Attended</label>
                        <input type="text" name="Last_sy_attended" id="Last_sy_attended" class="form-control" value="{{ $student->Last_sy_attended }}">
                        @error('Last_sy_attended') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Save Button -->
                    <div>
                        <button type="submit" class="btn btn-primary float-end">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
