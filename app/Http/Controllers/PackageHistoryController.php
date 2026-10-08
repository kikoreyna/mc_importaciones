<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;

class PackageHistoryController extends Controller
{
    public function show(Package $package)
    {
        $logs = $package->logs()->latest('created_at')->latest('id')->get();

        $participants = $logs
            ->filter(fn ($log) => filled($log->user_name))
            ->groupBy(fn ($log) => $log->user_id ?? $log->user_name)
            ->map(fn ($entries) => [
                'name' => $entries->first()->user_name,
                'role' => User::roleLabels()[$entries->first()->user_role] ?? $entries->first()->user_role,
                'count' => $entries->count(),
            ])
            ->sortByDesc('count')
            ->values();

        return view('packages.history', compact('package', 'logs', 'participants'));
    }
}
