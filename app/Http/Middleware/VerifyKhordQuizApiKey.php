<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server-to-server guard for requests coming from the Khord Quiz app.
 * Khord Quiz must send the shared key as `X-Api-Key: <key>` or
 * `Authorization: Bearer <key>`.
 */
class VerifyKhordQuizApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.khord_quiz.api_key');

        if ($expected === '') {
            return response()->json([
                'message' => 'Khord Quiz integration is not configured.',
            ], 503);
        }

        $provided = (string) ($request->header('X-Api-Key') ?: $request->bearerToken());

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
