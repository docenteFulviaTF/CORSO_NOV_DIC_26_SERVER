<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware didattico: misura quanto impiega la richiesta
 * e aggiunge l'header X-Response-Time alla risposta.
 *
 * Mostra la struttura di ogni middleware:
 *   codice PRIMA  -> $next($request) -> codice DOPO
 *
 * Generato con: php artisan make:middleware AddResponseTime
 */
class AddResponseTime
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);           // PRIMA: la richiesta non e' ancora arrivata al controller

        $response = $next($request);        // passa la richiesta al prossimo strato (alla fine il controller)

        $ms = round((microtime(true) - $start) * 1000, 1);
        $response->headers->set('X-Response-Time', $ms . 'ms');   // DOPO: modifico la risposta

        return $response;
    }
}
