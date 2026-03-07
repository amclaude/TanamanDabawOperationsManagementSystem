<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $content = $response->getContent();

            $search = [
                '/\>[^\S ]+/s',     // strip whitespaces after tags
                '/[^\S ]+\</s',     // strip whitespaces before tags
                '/(\s)+/s',         // shorten multiple whitespace sequences
            ];

            $replace = ['>', '<', '\\1'];

            $content = preg_replace_callback(
                '/<script.*?>.*?<\/script>|<style.*?>.*?<\/style>|[^<>]+/is',
                function ($matches) {
                    $segment = $matches[0];

                    // skip script/style
                    if (preg_match('/^<script|^<style/i', $segment)) {
                        return $segment;
                    }

                    // remove HTML comments
                    $segment = preg_replace('/<!--.*?-->/s', '', $segment);

                    // collapse whitespace
                    $segment = preg_replace('/\s+/s', ' ', $segment);

                    return $segment;
                },
                $content
            );

            $response->setContent($content);
        }

        return $response;
    }
}
