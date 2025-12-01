@props(['page', 'filteredCount' => null, 'searchCounts' => []])

@php

    $roleId = Auth::user()->role_id;

    $tabs = [
        'Pending' => ['route' => 'pending.index', 'label' => 'Pending', 'status' => 'Pending'],
        'Processing' => ['route' => 'ongoing.index', 'label' => 'Processing', 'status' => 'Processing'],
        'ForRelease' => ['route' => 'tables.index', 'label' => 'For Release', 'status' => 'For Release'],
        'Claimed' => ['route' => 'claimed-documents.index', 'label' => 'Claimed', 'status' => 'Claimed'],
        'Declined' => ['route' => 'declined-documents.index', 'label' => 'Declined', 'status' => 'Declined'],
    ];

    $permissions = [
        'Pending' => App\Models\PermissionRoleModel::getPermission('pending', $roleId),
        'Processing' => App\Models\PermissionRoleModel::getPermission('ongoing', $roleId),
        'ForRelease' => App\Models\PermissionRoleModel::getPermission('completed', $roleId),
        'Claimed' => App\Models\PermissionRoleModel::getPermission('claimed', $roleId),
        'Declined' => App\Models\PermissionRoleModel::getPermission('declined', $roleId),
    ];
    
    $hasSearchResults = !empty($searchCounts) && array_sum($searchCounts) > 0;
    $hasActiveSearch = !empty(request('search'));
@endphp

<div class="tabs-container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <ul class="nav nav-tabs custom-tabs flex-grow-1">
            @foreach($tabs as $key => $tab)
            @if(!empty($permissions[$key]))
                <li class="nav-item">
                    <a class="nav-link {{ $page == $key ? 'active' : '' }}" 
                       href="{{ route($tab['route']) }}" 
                       data-tab-link="{{ $tab['route'] }}"
                       data-tab-key="{{ $key }}">
                        <span class="tab-label">{{ $tab['label'] }}</span>
                        @if($hasActiveSearch && isset($searchCounts[$tab['status']]))
                            <span class="badge rounded-pill tab-badge {{ $searchCounts[$tab['status']] > 0 ? 'bg-success' : 'bg-secondary' }}" 
                                  data-status="{{ $tab['status'] }}">
                                {{ $searchCounts[$tab['status']] }}
                            </span>
                        @else
                            <span class="badge rounded-pill tab-badge bg-secondary" 
                                  data-status="{{ $tab['status'] }}"
                                  style="display: none;">
                                0
                            </span>
                        @endif
                    </a>
                </li>
            @endif
            @endforeach
        </ul>
        
        <div class="global-search-wrapper">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="tabSearchInput" 
                       placeholder="Global search across all tabs..." 
                       value="{{ request('search') }}">
                @if(request('search'))
                <button class="btn btn-clear" 
                        type="button" 
                        id="tabClearBtn"
                        title="Clear search">
                    <i class="fas fa-times"></i>
                </button>
                @endif
                <button class="btn btn-search" 
                        type="button" 
                        id="tabSearchBtn">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
            <small class="search-hint">
                <i class="fas fa-info-circle"></i> Search across all status tabs simultaneously
            </small>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabSearchInput = document.getElementById('tabSearchInput');
    const tabSearchBtn = document.getElementById('tabSearchBtn');
    const tabClearBtn = document.getElementById('tabClearBtn');
    const pageSearchInput = document.getElementById('searchInput');
    let searchTimeout = null;
    
    // Sync global search with page search on load
    if (pageSearchInput && tabSearchInput.value) {
        const currentPageValue = pageSearchInput.value;
        const globalValue = tabSearchInput.value;
        
        // Only sync if values differ
        if (currentPageValue !== globalValue) {
            pageSearchInput.value = globalValue;
            // Trigger the page's search
            const event = new Event('input', { bubbles: true });
            pageSearchInput.dispatchEvent(event);
        }
    }
    
    // Only sync global search to page search (one-way)
    if (tabSearchInput && pageSearchInput) {
        tabSearchInput.addEventListener('input', function() {
            pageSearchInput.value = this.value;
            // Trigger the page's search input event
            const event = new Event('input', { bubbles: true });
            pageSearchInput.dispatchEvent(event);
            
            // Update badge counts
            const searchValue = this.value.trim();
            clearTimeout(searchTimeout);
            if (searchValue.length >= 2) {
                searchTimeout = setTimeout(() => performAjaxTabSearch(searchValue), 500);
            } else if (searchValue.length === 0) {
                clearTabBadges();
            }
        });
    }
    
    // Handle tab clicks with search parameter
    document.querySelectorAll('.nav-link[data-tab-link]').forEach(tabLink => {
        tabLink.addEventListener('click', function(e) {
            const searchValue = tabSearchInput.value.trim();
            
            // If there's an active search, add it to the URL
            if (searchValue) {
                e.preventDefault();
                const route = this.getAttribute('href');
                const url = new URL(route, window.location.origin);
                url.searchParams.set('search', searchValue);
                window.location.href = url.toString();
            }
            // Normal navigation if no search
        });
    });
    
    if (tabSearchBtn) {
        tabSearchBtn.addEventListener('click', function() {
            performTabSearch();
        });
    }
    
    if (tabClearBtn) {
        tabClearBtn.addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.delete('filter');
            currentUrl.searchParams.delete('sort');
            window.location.href = currentUrl.toString();
        });
    }
    
    if (tabSearchInput) {
        // Live search with debounce
        tabSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchValue = this.value.trim();
            
            if (searchValue.length >= 2) {
                searchTimeout = setTimeout(() => performAjaxTabSearch(searchValue), 500);
            } else if (searchValue.length === 0) {
                // Clear badges when search is empty
                clearTabBadges();
            }
        });
        
        tabSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                performTabSearch();
            }
        });
    }
    
    function performTabSearch() {
        const searchValue = tabSearchInput.value.trim();
        const currentUrl = new URL(window.location.href);
        
        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        // Remove filter and sort when doing global search
        currentUrl.searchParams.delete('filter');
        currentUrl.searchParams.delete('sort');
        
        window.location.href = currentUrl.toString();
    }
    
    function performAjaxTabSearch(searchValue) {
        if (!searchValue) {
            clearTabBadges();
            return;
        }
        
        // Show loading state
        tabSearchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        tabSearchBtn.disabled = true;
        
        fetch(`/api/search-counts?search=${encodeURIComponent(searchValue)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateTabBadges(data);
            tabSearchBtn.innerHTML = '<i class="fas fa-search me-1"></i> Search';
            tabSearchBtn.disabled = false;
        })
        .catch(err => {
            console.error('Search error:', err);
            tabSearchBtn.innerHTML = '<i class="fas fa-search me-1"></i> Search';
            tabSearchBtn.disabled = false;
        });
    }
    
    function updateTabBadges(counts) {
        document.querySelectorAll('.tab-badge').forEach(badge => {
            const status = badge.getAttribute('data-status');
            if (counts[status] !== undefined) {
                badge.textContent = counts[status];
                badge.style.display = 'inline-block';
                
                if (counts[status] > 0) {
                    badge.classList.remove('bg-secondary');
                    badge.classList.add('bg-success');
                } else {
                    badge.classList.remove('bg-success');
                    badge.classList.add('bg-secondary');
                }
            }
        });
    }
    
    function clearTabBadges() {
        document.querySelectorAll('.tab-badge').forEach(badge => {
            badge.style.display = 'none';
        });
    }
});
</script>

<style>
    /* ===== NAV TABS STYLING ===== */
    .custom-tabs .nav-link {
        color: #000000;
        border-bottom: 3px solid transparent;
    }
    
    .custom-tabs .nav-link:hover {
        color: #000000;
        border-bottom-color: #1f2937;
    }
    
    .custom-tabs .nav-link.active {
        color: #ffffff;
        background-color: #1f2937;
        border-bottom-color: #1f2937;
    }
    
    .custom-tabs {
        border-bottom: 2px solid #e5e7eb;
    }
    
    #tabSearchInput:focus {
        border-color: #1dd3b0;
        box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
    }
    
    .nav-link .badge {
        vertical-align: middle;
    }
</style>
