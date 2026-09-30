@extends('admin.layouts.app')
@section('title', 'Navigation')
@section('content')
<div class="mb-4"><div class="eyebrow mb-2">Website</div><h1 class="h3 mb-1">Navigation menu</h1><p class="text-muted mb-0">Manage the links shown in the public navbar.</p></div>
<div class="row g-4"><div class="col-xl-8"><div class="panel"><div class="panel-header"><h2 class="h5 mb-0">Menu items</h2><button type="submit" form="bulk-navigation-form" class="btn btn-primary btn-sm">Save changes</button></div><form id="bulk-navigation-form" method="POST" action="{{ route('admin.navigation.bulk-update') }}">@csrf<div class="table-responsive"><table class="table"><thead><tr><th>Label</th><th>Destination</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead><tbody>@forelse($items as $item)<tr><td><input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}"><input class="form-control form-control-sm" type="text" name="items[{{ $item->id }}][label]" value="{{ $item->label }}" required></td><td><input class="form-control form-control-sm" type="text" name="items[{{ $item->id }}][route_name]" value="{{ $item->route_name }}" placeholder="home"></td><td><input class="form-control form-control-sm" style="width:80px" type="number" name="items[{{ $item->id }}][sort_order]" value="{{ $item->sort_order }}" min="0" max="9999" required></td><td><input type="hidden" name="items[{{ $item->id }}][is_active]" value="0"><input type="checkbox" name="items[{{ $item->id }}][is_active]" value="1" {{ $item->is_active ? 'checked' : '' }}></td><td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-row-target="{{ $item->id }}"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-sm btn-outline-danger" data-url="{{ route('admin.navigation.destroy', $item) }}" onclick="deleteNavigationItem(this)"><i class="bi bi-trash"></i></button></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-5">No menu items yet.</td></tr>@endforelse</tbody></table></div></form></div></div><div class="col-xl-4"><div class="panel"><div class="panel-header"><h2 class="h5 mb-0">Add item</h2></div><div class="panel-body"><form method="POST" action="{{ route('admin.navigation.store') }}">@csrf<div class="mb-3"><label class="form-label">Label</label><input class="form-control" name="label" required></div><div class="mb-3"><label class="form-label">Route name</label><input class="form-control" name="route_name" placeholder="home"></div><div class="mb-3"><label class="form-label">Order</label><input class="form-control" type="number" name="sort_order" value="0" min="0" max="9999" required></div><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><label class="form-check-label">Active</label></div><div class="mt-3"><button class="btn btn-primary">Add item</button></div></form></div></div></div></div>
@if($items->hasPages())
<div class="mt-3"><x-pagination :paginator="$items" /></div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-edit').forEach(function (button) {
            button.addEventListener('click', function () {
                const row = button.closest('tr');
                if (!row) {
                    return;
                }

                const labelInput = row.querySelector('input[name$="[label]"]');
                if (labelInput) {
                    labelInput.focus();
                    labelInput.select();
                    row.classList.add('table-active');
                }
            });
        });
    });

    function deleteNavigationItem(button) {
        const url = button.dataset.url;
        if (!url || !confirm('Remove this navigation item?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
