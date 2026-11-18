<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class MapDashboardController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::query()
            ->where('is_active', true)
            ->with(['floras', 'alerts' => fn ($query) => $query->where('is_active', true)->latest()])
            ->withSum([
                'hives as occupied' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now())),
            ], 'hive_count')
            ->orderBy('province')
            ->get()
            ->map(function (Region $region) {
                $occupied = (int) $region->occupied;
                $ratio = $region->capacity > 0 ? $occupied / $region->capacity : 1;
                $alert = $region->alerts->first();
                $status = $alert?->severity === 'danger' || $ratio >= .9 ? 'riskli' : ($alert || $ratio >= .65 ? 'uyari' : 'uygun');
                return [
                    'id' => $region->id,
                    'baslik' => $region->name,
                    'il' => $region->province,
                    'lat' => (float) $region->latitude,
                    'lng' => (float) $region->longitude,
                    'rakim' => $region->elevation,
                    'rakım' => $region->elevation,
                    'kapasite_mevcut' => $occupied,
                    'kapasite_max' => $region->capacity,
                    'flora' => $region->floras->pluck('name')->join(' · ') ?: 'Flora bilgisi bekleniyor',
                    'flora_icon' => $region->floras->first()?->icon ?? '✦',
                    'mevsim' => $region->floras->first()?->season ?? 'diger',
                    'sicaklik' => 22,
                    'nem' => 48,
                    'ruzgar' => 11,
                    'ilaclama_riski' => $region->alerts->contains('type', 'pesticide'),
                    'durum' => $status,
                    'renk' => ['uygun' => 'yesil', 'uyari' => 'sari', 'riskli' => 'kirmizi'][$status],
                    'durum_neden' => $alert?->message ?? ($ratio >= .65 ? 'Bölge doluluk oranı yükseliyor; konum eklemeden önce kotayı kontrol edin.' : 'Kota ve kayıtlı saha uyarıları bakımından uygun görünüyor.'),
                    'alerts' => $region->alerts->map(fn ($alert) => ['title' => $alert->title, 'severity' => $alert->severity]),
                ];
            });

        $myHives = $request->user()?->hives()->with('region')->latest()->get() ?? collect();
        return view('dashboard', [
            'locations' => $regions,
            'regions' => $regions,
            'myHives' => $myHives,
        ]);
    }
}
