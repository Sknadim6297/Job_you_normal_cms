<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', [
            'items' => NavigationItem::orderBy('sort_order')->orderBy('label')->paginate(15)->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        NavigationItem::create($this->validated($request));
        return back()->with('success', 'Navigation item added.');
    }

    public function update(Request $request, NavigationItem $navigation)
    {
        $navigation->update($this->validated($request));
        return back()->with('success', 'Navigation item updated.');
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:navigation_items,id'],
            'items.*.label' => ['required', 'string', 'max:80'],
            'items.*.route_name' => ['nullable', 'string', 'max:120'],
            'items.*.sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ]);

        foreach ($data['items'] as $itemData) {
            NavigationItem::query()
                ->whereKey($itemData['id'])
                ->update([
                    'label' => $itemData['label'],
                    'route_name' => empty($itemData['route_name']) ? null : $itemData['route_name'],
                    'sort_order' => (int) $itemData['sort_order'],
                    'is_active' => (bool) ($itemData['is_active'] ?? false),
                ]);
        }

        return back()->with('success', 'Navigation menu updated.');
    }

    public function destroy(NavigationItem $navigation)
    {
        $navigation->delete();
        return back()->with('success', 'Navigation item removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'route_name' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
