<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RedirectCanonicalDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $canonicalUrl = rtrim((string) config('site.canonical_url'), '/');
        $canonicalHost = parse_url($canonicalUrl, PHP_URL_HOST);
        $canonicalScheme = parse_url($canonicalUrl, PHP_URL_SCHEME) ?: 'https';

        if (! is_string($canonicalHost) || $canonicalHost === '') {
            return $next($request);
        }

        $host = Str::lower($request->getHost());
        $canonicalHost = Str::lower($canonicalHost);

        if (! in_array($host, $this->canonicalHosts($canonicalHost), true)) {
            return $next($request);
        }

        $path = $this->normalizePath($request->getPathInfo());
        $shouldRedirect = $host !== $canonicalHost
            || $request->getScheme() !== $canonicalScheme
            || $path !== $request->getPathInfo();

        if ($shouldRedirect) {
            return redirect()->away($this->buildUrl($canonicalScheme, $canonicalHost, $path, $request->getQueryString()), 301);
        }

        return $next($request);
    }

    /**
     * @return array<int, string>
     */
    private function canonicalHosts(string $canonicalHost): array
    {
        return collect(config('site.canonical_hosts', []))
            ->push($canonicalHost)
            ->map(fn (mixed $host): string => Str::lower((string) $host))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizePath(string $path): string
    {
        if ($path === '/public') {
            return '/';
        }

        if (Str::startsWith($path, '/public/')) {
            return Str::replaceStart('/public', '', $path);
        }

        return $path;
    }

    private function buildUrl(string $scheme, string $host, string $path, ?string $queryString): string
    {
        $url = $scheme.'://'.$host.$path;

        if (filled($queryString)) {
            return $url.'?'.$queryString;
        }

        return $url;
    }
}
