<?php

namespace App\Jobs;

use App\Models\Region;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RefreshRegionSummary implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $regionId) {}

    public function handle(): void
    {
        // Yoğunluk özeti kuyrukta hesaplanır; kullanıcı kayıt sonrası beklemez.
        Region::find($this->regionId)?->hives()->where('expires_at', '<', now())->update(['status' => 'expired']);
    }
}
