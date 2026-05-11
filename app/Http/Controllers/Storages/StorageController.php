<?php

namespace App\Http\Controllers\Storages;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStorageRequest;
use App\Http\Requests\UpdateStorageRequest;
use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\StorageTransfer;
use App\Models\Product;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        $storages = Storage::with('items')->paginate(20);
        $stats = [
            'total_items' => StorageItem::sum('quantity'),
            'total_storages' => Storage::count(),
            'active_storages' => Storage::where('is_active', true)->count(),
        ];
        return view('storages.index', compact('storages', 'stats'));
    }

    public function create()
    {
        $storage = null;
        return view('storages.form', compact('storage'));
    }

    public function store(StoreStorageRequest $request)
    {
        $validated = $request->validated();
        Storage::create($validated);
        return redirect()->route('storages.index')->with('success', 'Storage created successfully!');
    }

    public function edit(Storage $storage)
    {
        return view('storages.form', compact('storage'));
    }

    public function update(UpdateStorageRequest $request, Storage $storage)
    {
        $validated = $request->validated();
        $storage->update($validated);
        return redirect()->route('storages.index')->with('success', 'Storage updated successfully!');
    }

    public function destroy(Storage $storage)
    {
        $storage->delete();
        return response()->json(['success' => true]);
    }

    public function items(Storage $storage)
    {
        $items = $storage->items()->with('product')->paginate(20);
        $products = Product::all();
        return view('storages.items', compact('storage', 'items', 'products'));
    }

    public function storeItem(Request $request, Storage $storage)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'location_code' => 'nullable|string|max:255',
            'entry_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['storage_id'] = $storage->id;
        
        // Check for existing item
        $existingItem = StorageItem::where('storage_id', $storage->id)
            ->where('product_id', $validated['product_id'])
            ->where('location_code', $validated['location_code'] ?? null)
            ->first();

        if ($existingItem) {
            $existingItem->update(['quantity' => $existingItem->quantity + $validated['quantity']]);
        } else {
            StorageItem::create($validated);
        }

        return redirect()->route('storages.items', $storage->id)->with('success', 'Item added successfully!');
    }

    public function updateItem(Request $request, $itemId)
    {
        $item = StorageItem::findOrFail($itemId);

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            'location_code' => 'nullable|string|max:255',
            'entry_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('storages.items', $item->storage_id)->with('success', 'Item updated successfully!');
    }

    public function destroyItem($itemId)
    {
        $item = StorageItem::findOrFail($itemId);
        $storageId = $item->storage_id;
        $item->delete();

        return response()->json(['success' => true, 'storage_id' => $storageId]);
    }

    public function transfer(Request $request, Storage $storage)
    {
        $validated = $request->validate([
            'to_storage_id' => 'required|exists:storages,id|different:from_storage_id',
            'from_storage_id' => 'required',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if item exists in source storage
        $sourceItem = StorageItem::where('storage_id', $storage->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$sourceItem || $sourceItem->quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient quantity in source storage'
            ], 422);
        }

        // Check if destination storage exists
        $destinationStorage = Storage::findOrFail($validated['to_storage_id']);

        // Begin transaction
        \DB::beginTransaction();

        try {
            // Reduce quantity from source storage
            $sourceItem->quantity -= $validated['quantity'];
            if ($sourceItem->quantity <= 0) {
                $sourceItem->delete();
            } else {
                $sourceItem->save();
            }

            // Add or update quantity in destination storage
            $destinationItem = StorageItem::where('storage_id', $validated['to_storage_id'])
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($destinationItem) {
                $destinationItem->quantity += $validated['quantity'];
                $destinationItem->save();
            } else {
                StorageItem::create([
                    'storage_id' => $validated['to_storage_id'],
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'location_code' => null,
                    'entry_date' => now(),
                    'notes' => 'Transferred from ' . $storage->name,
                ]);
            }

            // Record the transfer
            StorageTransfer::create([
                'from_storage_id' => $storage->id,
                'to_storage_id' => $validated['to_storage_id'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'],
                'transfer_date' => now(),
                'transferred_by' => auth()->id(),
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product transferred successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error transferring product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transferHistory(Storage $storage)
    {
        $transfers = StorageTransfer::where('from_storage_id', $storage->id)
            ->orWhere('to_storage_id', $storage->id)
            ->with(['fromStorage', 'toStorage', 'product', 'transferredBy'])
            ->orderBy('transfer_date', 'desc')
            ->paginate(20);

        return view('storages.transfer-history', compact('storage', 'transfers'));
    }
}
