@extends('layout.loginpage')
@section('content')

<!-- @if (session('success'))
<div id="successAlert" class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow-lg" role="alert" style="z-index: 1050; width: 20%;">
        {{ session('success') }} 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
<div id="errorAlert" class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 shadow-lg" role="alert" style="z-index: 1050; width: 20%;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif -->

<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #23272e">
        <div class="p-4 rounded shadow p-3 mb-5 rounded" style="background-color: #343a40; width: 500px " >
            <div class="container text-white text-center">
                <h4>Verify Your Email</h4>
                <p>Please enter the verification code we sent to your gmail</p>
            </div>

            <form action="{{route('verifyotp.submit')}}"  method="POST">
                @csrf
                <div class="d-flex justify-content-center align-items-center p-3 gap-1" >
                    <input type="text" name = "first" class="otp-input form-control text-center" maxlength="1" 
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "second" class="otp-input form-control text-center" maxlength="1" 
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "third" class="otp-input form-control text-center" maxlength="1" 
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "fourth" class="otp-input form-control text-center" maxlength="1" 
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "fifth" class="otp-input form-control text-center" maxlength="1" 
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">
                    <input type="text" name = "sixth" class="otp-input form-control text-center" maxlength="1" 
                    style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: bold; text-align: center; color: black; background-color: white; border: 2px solid black; width: 50px; height: 50px; border-radius: 8px;">

                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Verify</button>
                </div>
            </form>

        </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll(".otp-input");

        // Auto-move on input
        inputs.forEach((input, index) => {
            input.addEventListener("input", (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus(); // Move to next input
                }
            });

            // Move back on backspace
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && index > 0 && e.target.value === "") {
                    inputs[index - 1].focus();
                }
            });

            // Restrict to numbers only
            input.addEventListener("keypress", (e) => {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });

            // Handle pasting full OTP
            input.addEventListener("paste", (e) => {
                e.preventDefault(); // Stop default paste behavior
                let pastedData = e.clipboardData.getData("text"); // Get clipboard text
                let digits = pastedData.replace(/\D/g, "").split("").slice(0, inputs.length); // Extract digits only

                // Fill each input with pasted digits
                inputs.forEach((inp, i) => {
                    inp.value = digits[i] || ""; // Fill available digits
                });

                // Focus last filled input
                if (digits.length > 0) {
                    let lastIndex = Math.min(digits.length, inputs.length) - 1;
                    inputs[lastIndex].focus();
                }
            });
        });
    });
</script>

@endsection