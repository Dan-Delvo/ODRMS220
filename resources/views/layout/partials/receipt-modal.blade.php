<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal{{ $receipt->id }}" tabindex="-1" aria-labelledby="receiptModalLabel{{ $receipt->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="receiptModalLabel{{ $receipt->id }}">Receipt #{{ $receipt->receipt_no }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Document Type:</strong>
                        <p class="text-muted mb-0">{{ $receipt->document->DocType }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Amount:</strong>
                        <p class="text-muted mb-0">₱{{ number_format($receipt->doc_amount, 2) }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Requested By (Student ID):</strong>
                        <p class="text-muted mb-0">{{ $receipt->name_request }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Requested At:</strong>
                        <p class="text-muted mb-0">{{ $receipt->time_request->format('F d, Y h:i A') }}</p>
                    </div>
                </div>

                {{-- Add more fields if needed --}}
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
