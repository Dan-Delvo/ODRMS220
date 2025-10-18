@props(['request', 'students'])

<div class="modal fade" id="modal-{{ $request->Request_ID }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel-{{ $request->Request_ID }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h1 class="modal-title fs-5" id="modalLabel-{{ $request->Request_ID }}">Request Details</h1>
                    <p class="text-muted mb-0 small">{{ $request->School_Name }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Document Type:</strong> {{ $request->Doc_Type }}</p>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-primary">{{ $request->Status }}</span></p>
                    <p class="mb-3"><strong>Total Students:</strong> {{ $request->students_count }}</p>
                </div>

                <h6 class="mb-3">Student List</h6>

                @php
                    $filteredStudents = $students->filter(function($std) use ($request) {
                        return $std->Request_ID == $request->Request_ID;
                    });
                @endphp

                @if($filteredStudents->count() > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                @foreach($filteredStudents as $index => $std)
                                    @if($loop->iteration <= ceil($filteredStudents->count() / 2))
                                        <li class="list-group-item">{{ $std->Student_Name }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                @foreach($filteredStudents as $index => $std)
                                    @if($loop->iteration > ceil($filteredStudents->count() / 2))
                                        <li class="list-group-item">{{ $std->Student_Name }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> No students found for this request.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
