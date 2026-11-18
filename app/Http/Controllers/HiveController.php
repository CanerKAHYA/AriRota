<?php

namespace App\Http\Controllers;

use App\Jobs\RefreshRegionSummary;
use App\Models\Hive;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HiveController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'aks_number' => ['required', 'string', 'max:60'],
            'hive_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:35,43'],
            'longitude' => ['nullable', 'numeric', 'between:25,33'],
        ]);

        try {
            $hive = DB::transaction(function () use ($data, $request) {
                $region = Region::query()->lockForUpdate()->findOrFail($data['region_id']);
                $occupied = (int) $region->hives()->where('status', 'active')->sum('hive_count');
                if (! $region->is_active || $occupied + $data['hive_count'] > $region->capacity) {
                    throw new \DomainException('Bu bölgede seçilen sayı için yeterli kota yok.');
                }
                return Hive::create($data + [
                    'user_id' => $request->user()->id,
                    'status' => 'active',
                    'expires_at' => now()->addDays(30),
                ]);
            });
            RefreshRegionSummary::dispatch($hive->region_id);
            return back()->with('success', 'Kovan konumu kaydedildi; harita kotası güncellendi.');
        } catch (\DomainException $exception) {
            return back()->withErrors(['hive_count' => $exception->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request, Hive $hive)
    {
        abort_unless($hive->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        $hive->delete();
        RefreshRegionSummary::dispatch($hive->region_id);
        return back()->with('success', 'Kovan kaydı kaldırıldı.');
    }
}
