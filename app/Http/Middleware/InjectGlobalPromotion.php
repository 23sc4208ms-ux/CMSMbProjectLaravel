<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectGlobalPromotion
{
    /**
     * Inject the promo banner into HTML responses.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('app.global_promotion_enabled', true)) {
            return $response;
        }

        // Don't inject promotion on maintenance page
        if ($request->routeIs('maintenance') || $request->routeIs('home') || $request->routeIs('dashboard') || $request->routeIs('about-us') || $request->routeIs('degrees.index')) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if (stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        if (stripos($content, 'site-promo-strip') !== false) {
            return $response;
        }

        $banner = view('partials.promo-banner')->render();
        $updated = preg_replace('/(<body\b[^>]*>)/i', '$1' . $banner, $content, 1);

        if (is_string($updated)) {
            $response->setContent($updated);
        }

        return $response;
    }
}
