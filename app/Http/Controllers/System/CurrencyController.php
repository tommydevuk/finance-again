<?php

namespace App\Http\Controllers\System;

use App\Domain\Currencies\Query\SystemCurrencyQuery;
use App\Enums\CurrencyTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\CurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $query = new SystemCurrencyQuery;
        $currencies = $query->filter($request)->getQuery()->paginate(10)->withQueryString();

        return Inertia::render('System/Currencies/Index', [
            'currencies' => CurrencyResource::collection($currencies),
            'filters' => $request->only(['search', 'sort', 'direction', 'type']),
            'types' => collect(CurrencyTypeEnum::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('System/Currencies/Create', [
            'types' => collect(CurrencyTypeEnum::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    public function store(CurrencyRequest $request)
    {
        Currency::create($request->validated());

        return redirect()->route('system.currencies.index')->with('success', 'Currency created successfully.');
    }

    public function edit(Currency $currency)
    {
        return Inertia::render('System/Currencies/Edit', [
            'currency' => new CurrencyResource($currency),
            'types' => collect(CurrencyTypeEnum::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    public function update(CurrencyRequest $request, Currency $currency)
    {
        $currency->update($request->validated());

        return redirect()->route('system.currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('system.currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
