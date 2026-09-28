<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', ['items' => NavigationItem::orderBy('sort_order')->orderBy('label')->get()]);
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

    public function destroy(NavigationItem $navigation)
    {
        $navigation->delete();
        return back()->with('success', 'Navigation item removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url' => ['nullable', 'url', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
