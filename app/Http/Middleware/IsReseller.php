<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsReseller
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->role === 'agent' || Auth::user()->role === 'sub_agent')) {
            return $next($request);
        }

        abort(403, 'این بخش فقط برای نمایندگان فروش همراه سیمرغ در دسترس است.');
    }
}
