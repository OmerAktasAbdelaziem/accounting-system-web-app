@php
    $selectedBranchIds = old('branch_ids', $selectedBranchIds ?? []);
    if (!is_array($selectedBranchIds)) {
        $selectedBranchIds = [$selectedBranchIds];
    }
    $selectedBranchIds = array_map('strval', $selectedBranchIds);
@endphp

<div class="mb-3">
    <label class="form-label d-block fw-bold">{{ __('messages.branch') }}</label>
    <div class="border rounded p-3 @error('branch_ids') border-danger @enderror">
        @forelse($branches ?? [] as $branch)
            <div class="form-check mb-2">
                <input
                    type="checkbox"
                    name="branch_ids[]"
                    value="{{ $branch->id }}"
                    id="branch_{{ $branch->id }}"
                    class="form-check-input"
                    {{ in_array((string) $branch->id, $selectedBranchIds, true) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="branch_{{ $branch->id }}">
                    <strong>{{ $branch->name }}</strong> <small class="text-muted">({{ $branch->code }})</small>
                </label>
            </div>
        @empty
            <p class="text-muted mb-0">No branches available</p>
        @endforelse
    </div>
    @error('branch_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
