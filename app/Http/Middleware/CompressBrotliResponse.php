<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressBrotliResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Chỉ nén nếu client chấp nhận Brotli
        if (
            strpos($request->header('Accept-Encoding'), 'br') !== false &&
            function_exists('brotli_compress') &&
            $response->headers->get('Content-Type') === 'application/json'
        ) {
            $compressed = brotli_compress($response->getContent());

            $response->setContent($compressed);
            $response->headers->set('Content-Encoding', 'br');
            $response->headers->set('Content-Length', strlen($compressed));
        }

        return $response;
    }
}
