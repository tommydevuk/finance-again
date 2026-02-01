<?php

namespace App\Http\Middleware;

use App\Models\Entity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTeamContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $entity = $request->route('entity');

        if ($entity instanceof Entity) {
            // Set the Spatie team context
            setPermissionsTeamId($entity->id);

            // Force a reload of roles/permissions for this user in the new context
            $user = $request->user();
            if ($user) {
                $user->unsetRelation('roles')->unsetRelation('permissions');
            }
        }

        return $next($request);
    }
}