<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Phase 0 only: proves PHP, Laravel, MySQL and the migrations are working.
 * Phase 1 replaces the "/" route with the real landing page.
 */
class FoundationController extends Controller
{
    /** Tables that must exist after running the project migrations. */
    private const EXPECTED_TABLES = [
        'users',
        'password_reset_tokens',
        'sessions',
        'delivery_zones',
        'vendor_profiles',
        'menu_items',
        'orders',
        'order_items',
        'payments',
        'deliveries',
        'suki_point_transactions',
        'buy_for_me_requests',
        'order_flags',
        'status_histories',
    ];

    public function __invoke(): View
    {
        // Diagnostic details must never be visible in production.
        abort_unless(app()->isLocal(), 404);

        $driver = config('database.default');
        $connected = false;
        $error = null;

        try {
            DB::connection()->getPdo();
            $connected = true;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        $tables = [];
        $migrationsRun = 0;

        if ($connected) {
            foreach (self::EXPECTED_TABLES as $table) {
                $tables[$table] = Schema::hasTable($table);
            }

            if (Schema::hasTable('migrations')) {
                $migrationsRun = DB::table('migrations')->count();
            }
        }

        return view('foundation', [
            'phpVersion'     => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'environment'    => app()->environment(),
            'timezone'       => config('app.timezone'),
            'driver'         => $driver,
            'usingMysql'     => in_array($driver, ['mysql', 'mariadb'], true),
            'database'       => config("database.connections.{$driver}.database"),
            'connected'      => $connected,
            'error'          => $error,
            'tables'         => $tables,
            'migrationsRun'  => $migrationsRun,
        ]);
    }
}