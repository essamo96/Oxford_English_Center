<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $branchId = app(\App\Services\BranchContext::class)->getId();

        if ($branchId !== null) {
            $builder->where($model->getTable() . '.branch_id', $branchId);
        }
    }
}
