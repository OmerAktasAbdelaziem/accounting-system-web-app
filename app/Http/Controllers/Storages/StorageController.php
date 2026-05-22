<?php

namespace App\Http\Controllers\Storages;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStorageRequest;
use App\Http\Requests\UpdateStorageRequest;
use App\Models\Branch;
use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\StorageTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $storages = Storage::with('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('location', 'like', '%' . $search . '%')
                      ->orWhere('storage_type', 'like', '%' . $search . '%');
            })
            ->paginate(6)
            ->withQueryString();
        $stats = [
            'total_items' => StorageItem::sum('quantity'),
            'total_value' => StorageItem::sum('total_price'),
            'total_storages' => Storage::count(),
            'active_storages' => Storage::where('is_active', true)->count(),
        ];
        return view('storages.index', compact('storages', 'stats'));
    }

    public function create()
    {
        $storage = null;
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('storages.form', compact('storage', 'branches', 'selectedBranchIds'));
    }

    public function store(StoreStorageRequest $request)
    {
        $validated = $request->validated();
        $storage = Storage::create($validated);
        $storage->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('storages.index')->with('success', 'Storage created successfully!');
    }

    public function edit(Storage $storage)
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $storage->branches()->pluck('branches.id')->all();
        return view('storages.form', compact('storage', 'branches', 'selectedBranchIds'));
    }

    public function update(UpdateStorageRequest $request, Storage $storage)
    {
        $validated = $request->validated();
        $storage->update($validated);
        $storage->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('storages.index')->with('success', 'Storage updated successfully!');
    }

    public function destroy(Storage $storage)
    {
        $storage->delete();
        return response()->json(['success' => true]);
    }

    public function items(Storage $storage)
    {
        $items = $storage->items()->latest()->paginate(20);
        $summary = [
            'entry_count' => $storage->items()->count(),
            'total_quantity' => (float) $storage->items()->sum('quantity'),
            'total_weight' => (float) $storage->items()->sum('weight'),
            'total_value' => (float) $storage->items()->sum('total_price'),
        ];
        $otherStorages = Storage::where('is_active', true)
            ->whereKeyNot($storage->id)
            ->orderBy('name')
            ->get();

        return view('storages.items', compact('storage', 'items', 'otherStorages', 'summary'));
    }

    public function storeItem(Request $request, Storage $storage)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'weight' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        StorageItem::create([
            'storage_id' => $storage->id,
            'product_name' => $validated['product_name'],
            'quantity' => $validated['quantity'],
            'weight' => $validated['weight'],
            'unit_price' => $validated['unit_price'],
            'total_price' => $validated['quantity'] * $validated['unit_price'],
        ]);

        $this->refreshStorageUsage($storage);

        return redirect()->route('storages.items', $storage->id)->with('success', 'Item added successfully!');
    }

    public function updateItem(Request $request, $itemId)
    {
        $item = StorageItem::findOrFail($itemId);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'weight' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $item->update([
            'product_name' => $validated['product_name'],
            'quantity' => $validated['quantity'],
            'weight' => $validated['weight'],
            'unit_price' => $validated['unit_price'],
            'total_price' => $validated['quantity'] * $validated['unit_price'],
        ]);

        $this->refreshStorageUsage($item->storage);

        return redirect()->route('storages.items', $item->storage_id)->with('success', 'Item updated successfully!');
    }

    public function destroyItem($itemId)
    {
        $item = StorageItem::findOrFail($itemId);
        $storageId = $item->storage_id;
        $item->delete();

        $this->refreshStorageUsage(Storage::find($storageId));

        return redirect()->route('storages.items', $storageId)->with('success', 'Item deleted successfully!');
    }

    public function transfer(Request $request, Storage $storage)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:storage_items,id',
            'to_storage_id' => 'required|exists:storages,id|not_in:' . $storage->id,
            'quantity' => 'required|numeric|min:0.01',
            'weight' => 'required|numeric|min:0.01',
        ]);

        $sourceItem = StorageItem::where('storage_id', $storage->id)->findOrFail($validated['item_id']);

        if ((float) $validated['quantity'] > (float) $sourceItem->quantity || (float) $validated['weight'] > (float) $sourceItem->weight) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient quantity or weight in source storage',
            ], 422);
        }

        $destinationStorage = Storage::findOrFail($validated['to_storage_id']);
        $transferPrice = (float) $validated['quantity'] * (float) $sourceItem->unit_price;

        try {
            DB::transaction(function () use ($sourceItem, $validated, $destinationStorage, $storage, $transferPrice) {
                $remainingQuantity = (float) $sourceItem->quantity - (float) $validated['quantity'];
                $remainingWeight = (float) $sourceItem->weight - (float) $validated['weight'];

                if ($remainingQuantity <= 0 || $remainingWeight <= 0) {
                    $sourceItem->delete();
                } else {
                    $sourceItem->update([
                        'quantity' => $remainingQuantity,
                        'weight' => $remainingWeight,
                        'total_price' => $remainingQuantity * (float) $sourceItem->unit_price,
                    ]);
                }

                StorageItem::create([
                    'storage_id' => $destinationStorage->id,
                    'product_name' => $sourceItem->product_name,
                    'quantity' => $validated['quantity'],
                    'weight' => $validated['weight'],
                    'unit_price' => $sourceItem->unit_price,
                    'total_price' => $transferPrice,
                ]);

                StorageTransfer::create([
                    'from_storage_id' => $storage->id,
                    'to_storage_id' => $destinationStorage->id,
                    'product_name' => $sourceItem->product_name,
                    'quantity' => $validated['quantity'],
                    'weight' => $validated['weight'],
                    'unit_price' => $sourceItem->unit_price,
                    'total_price' => $transferPrice,
                    'transfer_date' => now(),
                    'transferred_by' => auth()->id(),
                ]);

                $this->refreshStorageUsage($storage);
                $this->refreshStorageUsage($destinationStorage);
            });

            return redirect()->route('storages.items', $storage)->with('success', 'Product transferred successfully');
        } catch (\Exception $e) {
            return back()->withErrors([
                'transfer' => 'Error transferring product: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function transferHistory(Storage $storage)
    {
        $transfers = StorageTransfer::where('from_storage_id', $storage->id)
            ->orWhere('to_storage_id', $storage->id)
            ->with(['fromStorage', 'toStorage', 'transferredBy'])
            ->orderByDesc('transfer_date')
            ->paginate(20);
        $transferStats = [
            'outgoing' => StorageTransfer::where('from_storage_id', $storage->id)->count(),
            'incoming' => StorageTransfer::where('to_storage_id', $storage->id)->count(),
        ];

        return view('storages.transfer-history', compact('storage', 'transfers', 'transferStats'));
    }

    private function refreshStorageUsage(?Storage $storage): void
    {
        if (!$storage) {
            return;
        }

        $storage->update([
            'current_usage' => (float) StorageItem::where('storage_id', $storage->id)->sum('quantity'),
        ]);
    }
}
