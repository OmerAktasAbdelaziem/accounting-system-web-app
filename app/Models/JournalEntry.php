<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'description',
        'description_ar',
        'reference_number',
        'reference_type',
        'reference_id',
        'branch_id',
        'created_by',
        'total_debit',
        'total_credit',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    /**
     * Get journal entry items
     */
    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    /**
     * Get the user who created this entry
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Add journal entry item
     */
    public function addItem($accountId, $debit = 0, $credit = 0, $description = null)
    {
        return $this->items()->create([
            'account_id' => $accountId,
            'debit' => $debit,
            'credit' => $credit,
            'description' => $description,
        ]);
    }

    /**
     * Validate the journal entry totals
     */
    public function post(): bool
    {
        // Verify debits equal credits
        $totalDebit = $this->items->sum('debit');
        $totalCredit = $this->items->sum('credit');

        if ($totalDebit != $totalCredit) {
            return false;
        }

        $this->update([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);

        return true;
    }

    /**
     * Reverse the journal entry
     */
    public function reverse($reversalDate = null): ?JournalEntry
    {
        $reversalEntry = static::create([
            'date' => $reversalDate ?? now(),
            'description' => "Reversal of: {$this->description}",
            'reference_number' => "REV-{$this->id}",
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'branch_id' => $this->branch_id,
            'created_by' => auth()->id(),
        ]);

        // Create reverse items
        foreach ($this->items as $item) {
            $reversalEntry->addItem(
                $item->account_id,
                $item->credit, // Swap debit and credit
                $item->debit,
                "Reversal of: {$item->description}"
            );
        }

        // Post the reversal
        $reversalEntry->post();

        return $reversalEntry;
    }

    /**
     * Scope to get entries in date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
