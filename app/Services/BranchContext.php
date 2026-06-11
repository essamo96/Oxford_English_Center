<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BranchContext
{
    /**
     * Get the active branch ID for the currently authenticated admin.
     * Returns null if the user is a super admin (sees all branches).
     */
    public function getId(): ?int
    {
        $check = auth()->guard('admin')->check();
        $user  = auth()->guard('admin')->user();

        Log::debug('[BranchContext] check=' . ($check ? 'true' : 'false')
            . ' user_id=' . ($user?->id ?? 'null')
            . ' branch_id=' . ($user?->branch_id ?? 'null'));

        if (!$check) {
            return null;
        }

        $branchId = $user->branch_id;

        return $branchId ? (int) $branchId : null;
    }

    /**
     * Check if the current admin is scoped to a specific branch.
     */
    public function isScoped(): bool
    {
        return $this->getId() !== null;
    }

    /**
     * Get the full Branch model for the current admin, or null for super admin.
     */
    public function getBranch(): ?\App\Models\Branch
    {
        $id = $this->getId();
        return $id ? \App\Models\Branch::find($id) : null;
    }
}
