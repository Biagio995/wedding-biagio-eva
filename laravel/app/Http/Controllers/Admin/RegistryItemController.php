<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistryItemRequest;
use App\Http\Requests\UpdateRegistryItemRequest;
use App\Models\RegistryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistryItemController extends Controller
{
    public function index(): View
    {
        $items = RegistryItem::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with('claimedBy')
            ->get();

        return view('admin.registry.index', [
            'items' => $items,
        ]);
    }

    public function store(StoreRegistryItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? 0;

        RegistryItem::query()->create($data);

        return redirect()
            ->route('admin.registry.index')
            ->with('status', __('Item added.'));
    }

    public function edit(RegistryItem $registryItem): View
    {
        return view('admin.registry.edit', [
            'item' => $registryItem,
        ]);
    }

    public function update(UpdateRegistryItemRequest $request, RegistryItem $registryItem): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $clearClaim = $request->boolean('clear_claim');
        unset($data['clear_claim']);

        if ($clearClaim) {
            $data['claimed_by_guest_id'] = null;
            $data['claimed_at'] = null;
            $data['claimed_by_name'] = null;
        }

        $registryItem->update($data);

        return redirect()
            ->route('admin.registry.index')
            ->with('status', __('Item updated.'));
    }

    public function destroy(RegistryItem $registryItem): RedirectResponse
    {
        $registryItem->delete();

        return redirect()
            ->route('admin.registry.index')
            ->with('status', __('Item deleted.'));
    }
}
