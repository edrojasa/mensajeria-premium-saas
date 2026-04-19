<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mensajeros usan la vista dedicada /my-shipment(s); redirige desde listado y alta genéricos.
 */
class RedirectCourierFromStaffShipmentRoutes
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->isMessenger()) {
            return redirect()->route('courier.shipments.index');
        }

        return $next($request);
    }
}
