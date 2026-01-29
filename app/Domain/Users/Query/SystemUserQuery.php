<?php

namespace App\Domain\Users\Query;

use Illuminate\Http\Request;

class SystemUserQuery extends UserQuery
{
    public function filter(Request $request): self
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $this->query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort')) {
            // simple sort handling, can be expanded
             $sort = $request->input('sort');
             if (in_array($sort, ['name', 'email', 'created_at'])) {
                 $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
                 $this->query->orderBy($sort, $direction);
             }
        } else {
             $this->query->orderBy('created_at', 'desc');
        }

        return $this;
    }
}
