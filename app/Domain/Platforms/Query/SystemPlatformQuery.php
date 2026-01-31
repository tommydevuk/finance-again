<?php

namespace App\Domain\Platforms\Query;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SystemPlatformQuery
{
    protected Builder $query;

    public function __construct(?Builder $query = null)
    {
        $this->query = $query ?? Platform::query();
    }

    public function filter(Request $request): self
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $this->query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $this->query->where('type', $request->input('type'));
        }

        if ($request->filled('sort')) {
            $sort = $request->input('sort');
            if (in_array($sort, ['name', 'type', 'created_at'])) {
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
