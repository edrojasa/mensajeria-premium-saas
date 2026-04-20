<?php

namespace App\Http\Controllers;

use App\Finance\ServiceType;
use App\Http\Requests\StoreServiceRateRequest;
use App\Http\Requests\UpdateServiceRateRequest;
use App\Models\ServiceRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceRateController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ServiceRate::class);

        $rates = ServiceRate::query()
            ->orderBy('service_type')
            ->get();

        return view('service_rates.index', compact('rates'));
    }

    public function create(): View
    {
        $this->authorize('create', ServiceRate::class);

        $usedTypes = ServiceRate::query()->pluck('service_type')->all();
        $availableTypes = array_values(array_diff(ServiceType::all(), $usedTypes));

        return view('service_rates.create', compact('availableTypes'));
    }

    public function store(StoreServiceRateRequest $request): RedirectResponse
    {
        $this->authorize('create', ServiceRate::class);

        $data = $request->validated();

        ServiceRate::query()->create([
            'service_type' => $data['service_type'],
            'base_price' => $data['base_price'],
            'price_per_kg' => $data['price_per_kg'] ?? null,
            'price_per_km' => $data['price_per_km'] ?? null,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()
            ->route('service-rates.index')
            ->with('status', __('finance.rate_saved'));
    }

    public function edit(ServiceRate $serviceRate): View
    {
        $this->authorize('update', $serviceRate);

        return view('service_rates.edit', ['rate' => $serviceRate]);
    }

    public function update(UpdateServiceRateRequest $request, ServiceRate $serviceRate): RedirectResponse
    {
        $this->authorize('update', $serviceRate);

        $serviceRate->update([
            'base_price' => $request->validated('base_price'),
            'price_per_kg' => $request->validated('price_per_kg'),
            'price_per_km' => $request->validated('price_per_km'),
            'active' => $request->boolean('active', $serviceRate->active),
        ]);

        return redirect()
            ->route('service-rates.index')
            ->with('status', __('finance.rate_updated'));
    }

    public function destroy(ServiceRate $serviceRate): RedirectResponse
    {
        $this->authorize('delete', $serviceRate);

        $serviceRate->delete();

        return redirect()
            ->route('service-rates.index')
            ->with('status', __('finance.rate_deleted'));
    }
}
