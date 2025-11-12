@props(['page'])

@php

    $roleId = Auth::user()->role_id;

    $tabs = [
        'Pending' => ['route' => 'pending.index', 'label' => 'Pending'],
        'Processing' => ['route' => 'ongoing.index', 'label' => 'Processing'],
        'ForRelease' => ['route' => 'tables.index', 'label' => 'For Release'],
        'Claimed' => ['route' => 'claimed-documents.index', 'label' => 'Claimed'],
        'Declined' => ['route' => 'declined-documents.index', 'label' => 'Declined'],
    ];

    $permissions = [
        'Pending' => App\Models\PermissionRoleModel::getPermission('pending', $roleId),
        'Processing' => App\Models\PermissionRoleModel::getPermission('ongoing', $roleId),
        'ForRelease' => App\Models\PermissionRoleModel::getPermission('completed', $roleId),
        'Claimed' => App\Models\PermissionRoleModel::getPermission('claimed', $roleId),
        'Declined' => App\Models\PermissionRoleModel::getPermission('declined', $roleId),
    ];
@endphp

<div>
    <ul class="nav nav-tabs" data-bs-theme="dark">
        @foreach($tabs as $key => $tab)
        @if(!empty($permissions[$key]))
            <li class="nav-item">
                <a class="nav-link {{ $page == $key ? 'active' : 'text-dark' }}" href="{{ route($tab['route']) }}" wire:navigate>
                    {{ $tab['label'] }}
                </a>
            </li>
        @endif
        @endforeach
    </ul>
</div>
