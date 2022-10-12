<?php

namespace App\Http\Middleware\Pages;

use App\Traits\AppResponse;
use App\Traits\LoadMessages;
use Closure;
use Illuminate\Http\Request;

class GroupUserPage
{
    use LoadMessages, AppResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (checkNivel(auth()->user()->id, "ugroup") || checkNivel(auth()->user()->id, "*")) {
            return $next($request);
        }
        return $this->error($this->getMessage("apperror", "ErrorUnauthorizedRoute"),  $code=401);
    }
}
