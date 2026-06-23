<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeHtmlEncodedQueryString
{
    public function handle(Request $request, Closure $next): Response
    {
        $queryString = (string) $request->server->get('QUERY_STRING', '');

        if (str_contains($queryString, '&amp;')) {
            $queryString = str_replace('&amp;', '&', $queryString);
            $request->server->set('QUERY_STRING', $queryString);

            parse_str($queryString, $query);
            $request->query->replace($query);
        }

        return $next($request);
    }
}
