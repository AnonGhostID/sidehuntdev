<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceLowercaseUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri(); // Termasuk path dan query string
        $parsedUrl = parse_url($uri);

        $path = $parsedUrl['path'] ?? '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

        // Pisahkan path jadi segmen dan ubah tiap segmen jadi kapital huruf pertama
        $segments = explode('/', trim($path, '/'));
        $properSegments = array_map(function ($segment) {
            return ucfirst(strtolower($segment));
        }, $segments);
        $properPath = '/' . implode('/', $properSegments);

        $properUri = $properPath . $query;
        // dd($uri,$properUri);
        if ($uri !== $properUri) {
            return redirect()->to($properUri, 301); // Permanent redirect
        }
        else{
            return $next($request);
        }

    }

}
