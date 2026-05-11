<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Safe;
use App\Models\Storage;
use PHPUnit\Framework\TestCase;

class BranchFillableTest extends TestCase
{
    public function test_branch_id_is_fillable_on_branch_scoped_models(): void
    {
        $models = [
            new Product(),
            new Employee(),
            new Storage(),
            new Safe(),
            new JournalEntry(),
        ];

        foreach ($models as $model) {
            $this->assertContains('branch_id', $model->getFillable(), get_class($model) . ' should allow branch_id mass assignment');
        }
    }
}
