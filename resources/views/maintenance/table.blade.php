{{-- resources/views/maintenance/table.blade.php --}}

@if($items->count() > 0)
<div class="table-responsive">
    <table class="table table-striped table-bordered bg-white text-dark">
        <thead class="bg-dark text-white">
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                @foreach($columns as $column)
                    <td>
                        @if(isset($column['callback']))
                            {!! $column['callback']($item) !!}
                        @elseif(str_contains($column['field'], '.'))
                            {{-- Handle nested relationships like 'roles.name' --}}
                            @php
                                $parts = explode('.', $column['field']);
                                $value = $item;
                                foreach($parts as $part) {
                                    $value = $value->{$part} ?? null;
                                }
                            @endphp
                            {{ $value ?? 'N/A' }}
                        @else
                            {{ $item->{$column['field']} ?? 'N/A' }}
                        @endif
                    </td>
                @endforeach

                <td class="d-flex justify-content-start">
                    @if(!empty($permissions['edit']))
                        <a href="{{ route($routePrefix . '.edit', ['id' => $item->{$primaryKey}]) }}" 
                           class="btn btn-success me-2">Edit</a>
                    @endif

                    @if(!empty($permissions['delete']))
                        <form action="{{ route($routePrefix . '.delete', $item->{$primaryKey}) }}" 
                              method="POST" 
                              class="d-inline" 
                              data-swal-loading="true" 
                              data-swal-delete="true">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-delete me-2">Delete</button>
                        </form>
                    @endif

                    @if(!empty($permissions['info']))
                        <a href="{{ route($routePrefix . '.show', ['id' => $item->{$primaryKey}]) }}" 
                           class="btn btn-info">Info</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle me-2"></i>
    {{ $emptyMessage ?? 'No records found matching your search criteria.' }}
</div>
@endif