<?php
namespace AventureCloud\MultiTenancy\Scopes;

use AventureCloud\MultiTenancy\Facades\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class TenantOwnedScope
 *
 * @package AventureCloud\MultiTenancy\Scopes
 */
class TenantOwnedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where(config('multitenancy.foreign_key'), '=', Tenancy::tenant()->id);
    }

    /**
     * @param Builder $builder
     */
    public function extend(Builder $builder)
    {
        $this->addWithAll($builder);
    }

    /**
     * @param Builder $builder
     */
    protected function addWithAll(Builder $builder)
    {
        $builder->macro('withAll', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}