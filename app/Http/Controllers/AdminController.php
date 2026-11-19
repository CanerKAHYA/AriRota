<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $regions = Region::with(['floras', 'alerts' => fn ($query) => $query->where('is_active', true)])->withSum(['hives as occupied' => fn ($query) => $query->where('status', 'active')], 'hive_count')->get();
        return view('admin.index', [
            'regions' => $regions,
            'users' => User::latest()->take(8)->get(),
            'hiveTotal' => (int) $regions->sum('occupied'),
        ]);
    }

    public function storeRegion(Request $request)
    {
        $data = $this->regionData($request);
        Region::create($data);
        return back()->with('success', 'Bölge oluşturuldu.');
    }

    public function updateRegion(Request $request, Region $region)
    {
        $data = $this->regionData($request);
        $occupied = $region->occupiedCount();
        if ($data['capacity'] < $occupied) {
            return back()->withErrors(['capacity' => "Kapasite, mevcut {$occupied} kovandan düşük olamaz."]);
        }
        $region->update($data);
        return back()->with('success', 'Bölge bilgileri güncellendi.');
    }

    public function storeAlert(Request $request)
    {
        $data = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'type' => ['required', 'in:weather,pesticide,capacity,other'],
            'severity' => ['required', 'in:info,warning,danger'],
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:500'],
        ]);
        Alert::create($data + ['user_id' => $request->user()->id, 'is_active' => true]);
        return back()->with('success', 'Uyarı yayınlandı.');
    }

    public function exportRegions()
    {
        $filename = 'arirota-bolge-raporu-'.now()->format('Ymd').'.csv';
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Bölge', 'İl', 'Kapasite', 'Dolu Kovan', 'Boş Kota']);
            Region::withSum(['hives as occupied' => fn ($query) => $query->where('status', 'active')], 'hive_count')->each(function (Region $region) use ($out) {
                $occupied = (int) $region->occupied;
                fputcsv($out, [$region->name, $region->province, $region->capacity, $occupied, max(0, $region->capacity - $occupied)]);
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function regionData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'province' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:35,43'],
            'longitude' => ['required', 'numeric', 'between:25,33'],
            'elevation' => ['required', 'integer', 'min:0', 'max:5000'],
            'capacity' => ['required', 'integer', 'min:0', 'max:10000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
