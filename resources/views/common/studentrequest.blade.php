
@extends('layout.studentpage')

@section('content')

<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        background-color: #0f172a; /* Deep Navy */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #e2e8f0;
    }

    .form-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .form-container {
        width: 100%;
        max-width: 1100px;
        background-color: #1e293b;
        border: 2px solid #334155;
        border-radius: 1rem;
        padding: 2rem 3rem;
    }

    .form-title {
        text-align: center;
        font-size: 1.8rem;
        font-weight: 600;
        color: #1dd3b0; /* changed from #facc15 */
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #1dd3b0; /* changed from #facc15 */
        margin-bottom: 1rem;
    }

    .form-control,
    .form-select {
        background-color: #334155;
        border: none;
        color: #f1f5f9;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #475569;
        color: #fff;
        box-shadow: none;
    }

    .btn-submit {
        background-color: #1dd3b0; /* changed from #facc15 */
        color: #1e293b;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        border-radius: 0.4rem;
        border: none;
    }

    .btn-submit:hover {
        background-color: #38d9a9; /* lighter teal for hover */
    }

    .text-warning-link {
        color: #1dd3b0; /* changed from #facc15 */
        text-decoration: none;
    }

    .text-warning-link:hover {
        text-decoration: underline;
    }
</style>

<div class="form-page">
    <div class="form-container">
        <div class="form-title">📄 Document Request Form</div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('studentrequest.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6 pe-md-4 mb-4">
                    <div class="form-section-title">Document Request Info</div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="request_schl_entity" name="request_schl_entity" placeholder="Requesting Entity">
                        <label for="request_schl_entity">Requesting School/Entity</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="document_id" name="document_id">
                            @foreach($DocType as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->DocType }}</option>
                            @endforeach
                        </select>
                        <label for="document_id">Requested Document</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="release_mode" name="release_mode" placeholder="Release Mode">
                        <label for="release_mode">Release Mode</label>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6 ps-md-4 mb-4">
                    <div class="form-section-title">Claimer Info</div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="Fname" name="Fname" placeholder="First Name">
                        <label for="Fname">First Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="Lname" name="Lname" placeholder="Last Name">
                        <label for="Lname">Last Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact Number">
                        <label for="contact_no">Contact Number</label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="dashboard.html" class="text-warning-link">← Back to Dashboard</a>
                <button type="submit" class="btn btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
