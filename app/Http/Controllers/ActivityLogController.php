<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Partner;
use App\Models\Transportadora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'entity' => ['nullable', Rule::in(['client', 'partner', 'user', 'transportadora'])],
            'action' => ['nullable', Rule::in(['created', 'updated', 'deleted', 'restored'])],
        ]);

        $entityTypes = [
            'client' => Client::class,
            'partner' => Partner::class,
            'user' => User::class,
            'transportadora' => Transportadora::class,
        ];

        $logs = ActivityLog::query()
            ->with('actor')
            ->when($filters['entity'] ?? null, fn ($query, $entity) => $query->where('subject_type', $entityTypes[$entity]))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->latest('created_at')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('activity-logs.index', [
            'logs' => $logs,
            'filters' => $filters,
            'entityLabels' => [
                Client::class => 'Cliente',
                Partner::class => 'Socio',
                User::class => 'Usuario',
                Transportadora::class => 'Transportadora',
            ],
            'actionLabels' => [
                'created' => 'Creado',
                'updated' => 'Modificado',
                'deleted' => 'Eliminado',
                'restored' => 'Restaurado',
            ],
        ]);
    }
}
