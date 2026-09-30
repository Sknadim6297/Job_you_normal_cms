@extends('admin.layouts.app')
@section('title', 'Categories')
@section('content')
<div class="mb-4"><div class="eyebrow mb-2">Content</div><h1 class="h3 mb-1">Job categories</h1><p class="text-muted mb-0">Keep the qualification navigation organized.</p></div>
<div class="row g-4"><div class="col-xl-8"><div class="panel"><div class="panel-header"><h2 class="h5 mb-0">All categories</h2><button type="submit" form="bulk-category-form" class="btn btn-primary btn-sm">Save changes</button></div><form id="bulk-category-form" method="POST" action="{{ route('admin.categories.bulk-update') }}">@csrf<div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Slug</th><th>Jobs</th><th>Display Order</th><th>Status</th><th>Actions</th></tr></thead><tbody>@forelse($categories as $category)<tr><td><input type="hidden" name="items[{{ $category->id }}][id]" value="{{ $category->id }}"><input class="form-control form-control-sm" type="text" name="items[{{ $category->id }}][name]" value="{{ $category->name }}" required></td><td class="text-muted">{{ $category->slug }}</td><td>{{ $category->jobs_count }}</td><td><input class="form-control form-control-sm" style="width:80px" type="number" name="items[{{ $category->id }}][sort_order]" value="{{ $category->sort_order }}" min="0" max="9999" required></td><td><input type="hidden" name="items[{{ $category->id }}][is_active]" value="0"><input type="checkbox" name="items[{{ $category->id }}][is_active]" value="1" {{ $category->is_active ? 'checked' : '' }}></td><td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-row-target="{{ $category->id }}"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-sm btn-outline-danger" data-url="{{ route('admin.categories.destroy', $category) }}" onclick="deleteCategory(this)"><i class="bi bi-trash"></i></button></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-5">No categories yet.</td></tr>@endforelse</tbody></table></div></form></div></div><div class="col-xl-4"><div class="panel"><div class="panel-header"><h2 class="h5 mb-0">Add category</h2></div><div class="panel-body"><form method="POST" action="{{ route('admin.categories.store') }}">@csrf<div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required placeholder="10th Pass"></div><div class="mb-3"><label class="form-label">Slug <span class="text-muted">optional</span></label><input class="form-control" name="slug"></div><div class="mb-3"><label class="form-label">Display order</label><input class="form-control" type="number" name="sort_order" value="0" min="0" max="9999" required></div><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><label class="form-check-label">Active</label></div><div class="mt-3"><button class="btn btn-primary">Add category</button></div></form></div></div></div></div>
@if($categories->hasPages())
<div class="mt-3"><x-pagination :paginator="$categories" /></div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-edit').forEach(function (button) {
            button.addEventListener('click', function () {
                const row = button.closest('tr');
                if (!row) {
                    return;
                }

                const nameInput = row.querySelector('input[name$="[name]"]');
                if (nameInput) {
                    nameInput.focus();
                    nameInput.select();
                    row.classList.add('table-active');
                }
            });
        });
    });

    function deleteCategory(button) {
        const url = button.dataset.url;
        if (!url || !confirm('Delete this category?')) {
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
