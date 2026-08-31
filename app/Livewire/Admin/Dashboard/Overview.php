<?php

namespace App\Livewire\Admin\Dashboard;

use App\Queries\Dashboard\DashboardSummaryQuery;
use App\Services\Settings\SiteSettings;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Overview extends Component
{
    public function render(): View
    {
        return view('livewire.admin.dashboard.overview', [
            'summary' => app(DashboardSummaryQuery::class)->forUser(auth()->user()),
            'siteTimezone' => app(SiteSettings::class)->get()->timezone,
        ])->layout('components.layouts.admin', [
            'title' => 'Dashboard',
        ]);
    }
}
