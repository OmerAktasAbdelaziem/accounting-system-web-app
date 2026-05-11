<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\Response;

/**
 * CategoryPolicy - Authorization for category management
 */
class CategoryPolicy extends BasePolicy
{
    /**
     * Create new category (requires create-category permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-category')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create categories');
    }

    /**
     * Update category (requires edit-category permission)
     */
    public function update(User $user, Category $category): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-category')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit categories');
    }

    /**
     * Delete category (requires delete-category permission)
     */
    public function delete(User $user, Category $category): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-category')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete categories');
    }
}
