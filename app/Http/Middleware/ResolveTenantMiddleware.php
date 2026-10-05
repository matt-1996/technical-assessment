<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Tenancy\TenancyContext;
use App\Models\Tenant;

class ResolveTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant_id = $request->header('tenant_id');

        abort_unless($tenant_id, 400 , 'Tenant is required');

        $tenant = Tenant::find($tenant_id);

        if ($tenant == null) {
            abort(404 , 'Tenant not found');
        }

        app(TenancyContext::class)->set($tenant);

        return $next($request);
    }
}
