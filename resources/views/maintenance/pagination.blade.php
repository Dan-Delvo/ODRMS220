{{-- resources/views/maintenance/pagination.blade.php --}}

<div class="d-flex flex-column justify-content-center align-items-center mt-3">
    {{ $items->appends(request()->query())->links() }}
    <small class="text-muted mt-2">
        Showing {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} of {{ $items->total() }}
    </small>
</div>