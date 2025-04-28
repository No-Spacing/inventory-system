<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'user' => function () {
                $user = Auth::user();
                return $user ? [
                    [
                        'user' => $user,
                    ],
                    'can' => [
                        'view' => $user->can('view', User::class),
                        'create' => $user->can('create', User::class),
                        'update' => $user->can('update', User::class),
                        'delete' => $user->can('delete', User::class),
                    ]
                ] : null;
            }
        ];
    }
}
