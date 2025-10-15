@php
    // Ensure $errors is always defined
    $errors = $errors ?? new \Illuminate\Support\MessageBag();
    $hasPasswordError = $errors->has('password');
@endphp

@if(session('success') || session('error') || session('status') || session('danger') || $hasPasswordError)
<div id="floatingAlert" class="floating-attempt
        @if(session('error') || session('danger') || $hasPasswordError) bg-danger
        @elseif(session('success') || session('status')) bg-success
        @endif">
    <i class="fas 
            @if(session('error') || session('danger') || $hasPasswordError) fa-exclamation-circle
            @else fa-check-circle
            @endif me-2"></i>

    {{-- Priority: success → status → error → Danger → error bag --}}
    {{ session('success') ?? session('status') ?? session('error') ?? session('danger') ?? $errors->first('password') }}
</div>
@endif

@if(
    session('Success') || session('Warning') || session('Status') ||
    session('Error') || session('Danger') || session('Declined') ||
    $hasPasswordError
)
<script>
    Swal.fire({
        icon: '{{ 
            session('Warning') ? 'warning' : 
            ((session('Error') || session('Danger') || $hasPasswordError) ? 'error' : 'success') 
        }}',
        title: 'Notice!!!',
        text: "{{ ucfirst(
            session('Success') ??
            session('Status') ??
            session('Error') ??
            session('Danger') ??
            session('Warning') ??
            session('Declined') ??
            $errors->first('password')
        ) }}",
        confirmButtonColor: '#1dd3b0'
    });
</script>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('floatingAlert');
        if (alert) {
            // Auto-dismiss after 4 seconds
            setTimeout(() => {
                alert.classList.add('hide');
                setTimeout(() => alert.remove(), 500);
            }, 4000);

            // Dismiss on click
            alert.addEventListener('click', () => {
                alert.classList.add('hide');
                setTimeout(() => alert.remove(), 500);
            });
        }
    });
</script>
@endpush