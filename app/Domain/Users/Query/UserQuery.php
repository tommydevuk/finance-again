<?php

namespace App\Domain\Users\Query;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserQuery
{
    protected Builder $query;

    public function __construct(?Builder $query = null)
    {
        $this->query = $query ?? User::query();
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }
}
