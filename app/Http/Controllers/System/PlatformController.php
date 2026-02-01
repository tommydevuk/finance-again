<?php

namespace App\Http\Controllers\System;

use App\Domain\Platforms\Query\SystemPlatformQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\PlatformRequest;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        $query = new SystemPlatformQuery;
        $platforms = $query->filter($request)->getQuery()->paginate(10)->withQueryString();

        return Inertia::render('System/Platforms/Index', [
            'platforms' => PlatformResource::collection($platforms),
            'filters' => $request->only(['search', 'sort', 'direction', 'type']),
            'types' => collect(\App\Enums\PlatformTypeEnum::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('System/Platforms/Create', [
            'types' => collect(\App\Enums\PlatformTypeEnum::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    public function store(PlatformRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Platform::create($data);

        return redirect()->route('system.platforms.index')->with('success', 'Platform created successfully.');
    }

    public function edit(Platform $platform)
    {
        return Inertia::render('System/Platforms/Edit', [
            'platform' => new PlatformResource($platform),
            'types' => collect(\App\Enums\PlatformTypeEnum::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    public function update(PlatformRequest $request, Platform $platform)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $platform->update($data);

        return redirect()->route('system.platforms.index')->with('success', 'Platform updated successfully.');
    }

    public function destroy(Platform $platform)
    {
        $platform->delete();

        return redirect()->route('system.platforms.index')->with('success', 'Platform deleted successfully.');
    }
}
