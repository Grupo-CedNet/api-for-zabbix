<?php

namespace App\Http\Middleware;

use App\Traits\AppResponse;
use App\Traits\LoadMessages;
use Closure;
use Illuminate\Http\Request;

class IsSuperAdmin
{
    use AppResponse, LoadMessages;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (checkNivel(auth()->user()->id) == 0) {
            return $next($request);
        }
        return $this->error($this->getMessage("apperror", "ErrorUnauthorizedRoute"), 401);
    }
}
