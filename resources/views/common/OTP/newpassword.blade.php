@extends('layout.loginpage')


@section('content')

<!-- @if (session('success'))
<div id="successAlert" class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow-lg" role="alert" style="z-index: 1050; width: 20%;">
        {{ session('success') }} 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif -->

<div class="d-flex justify-content-center align-items-center" style= "background-color: #23272e; height: 100vh;">
    <div class="container p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px">
        <div class="container">
            <h3 class= "text-center text-warning fw-semibold">Change Your Password</h3>
            <p class ="text-center text-light">Enter a new password below to change your password</p>

            <form action="{{ route('newpassword.submit')}}" method="POST" onsubmit="return compare()">
            @csrf
                <label for="password" class="form-label mt-3 text-light">Password</label>
                <div class="input-group mb-3">
                    <input type="password" id="password" class="form-control" name ="password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                        <i id="toggleIcon1" class="bi bi-eye"></i>
                    </button>
                </div>


                <label for="confirmPassword" class="form-label text-light">Confirm Password</label>
                <div class="input-group mb-3">
                    <input type="password" id="confirmPassword" class="form-control">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                        <i id="toggleIcon2" class="bi bi-eye"></i>
                    </button>
                </div>

                <div id="passwordHelpBlock" class="form-text text-light mb-3">
                    Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-warning">Confirm</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }

    function compare() {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirmPassword').value;

        if (password === "") {
            alert("Password field cannot be empty.");
            return false;
        } 
        if (password.length < 8 || password.length > 20) {
            alert("Password must be between 8-20 characters.");
            return false;
        }
        if (password !== confirmPassword) {
            alert("Passwords do not match. Try again.");
            return false;
        }

        return true;
    }
</script>


@endsection