<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;

/**
 * ProductPolicy - Authorization for product management
 * 
 * Permissions:
 * - Admin: Full access
 * - Manager: Create, edit, view
 * - User: View only
 */
class ProductPolicy extends BasePolicy
{
    /**
     * Create new product (requires view-products permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-product')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create products');
    }

    /**
     * Update product (requires edit-products permission)
     */
    public function update(User $user, Product $product): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-product')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit products');
    }

    /**
     * Delete product (requires delete-products permission)
     */
    public function delete(User $user, Product $product): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-product')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete products');
    }

    /**
     * Restore soft-deleted product (requires delete-products permission)
     */
    public function restore(User $user, Product $product): Response
    {
        return $this->delete($user, $product);
    }

    /**
     * Force delete product (admin only)
     */
    public function forceDelete(User $user, Product $product): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('Only administrators can permanently delete products');
    }
}
