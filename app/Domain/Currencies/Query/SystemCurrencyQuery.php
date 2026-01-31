<?php

namespace App\Domain\Currencies\Query;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SystemCurrencyQuery
{
    protected Builder $query;

    public function __construct(?Builder $query = null)
    {
        $this->query = $query ?? Currency::query();
    }

    public function filter(Request $request): self
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $this->query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $this->query->where('type', $request->input('type'));
        }

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if (in_array($sort, ['name', 'code', 'type', 'created_at'])) {
                $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
                $this->query->orderBy($sort, $direction);
            }
        } else {
            $this->query->orderBy('created_at', 'desc');
        }

        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }
}
