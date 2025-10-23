<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SanitizeRequestBody
{
    /**
     * Handle an incoming request.
     * We intentionally run the sanitization after $next($request) so that
     * controllers (for example the login action) can still read the raw
     * credentials. This middleware then overwrites sensitive values in the
     * server-side Request object and masks them in JSON/text responses to
     * reduce the chance they end up in logs or returned payloads.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Execute the request first so authentication still works
        $response = $next($request);

        // Fields considered sensitive — adapt if your app uses different names
        $sensitive = ['password', 'password_confirmation', 'email_address', 'email'];

        // Overwrite sensitive fields in the in-memory request data
        foreach ($sensitive as $field) {
            if ($request->request->has($field)) {
                $request->request->set($field, '[FILTERED]');
            }
        }

        // If the response is JSON, scrub any sensitive keys in the response body
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $this->maskArray($data, $sensitive);
            $response->setData($data);
        } else {
            // For textual responses (including application/json string bodies),
            // do a best-effort mask. Avoid touching binary responses.
            $contentType = $response->headers->get('Content-Type', '');
            if (str_contains($contentType, 'application/json') || str_contains($contentType, 'text/')) {
                $content = $response->getContent();
                foreach ($sensitive as $field) {
                    $pattern = '/("' . preg_quote($field, '/') . '"\s*:\s*")([^"]+)(")/i';
                    $content = preg_replace($pattern, '$1[FILTERED]$3', $content);
                }
                $response->setContent($content);
            }
        }

        // Small, non-sensitive log to indicate sanitization occurred for this path
        Log::debug('SanitizeRequestBody applied', ['path' => $request->path()]);

        return $response;
    }

    protected function maskArray(array &$arr, array $sensitive)
    {
        foreach ($arr as $k => &$v) {
            if (is_array($v)) {
                $this->maskArray($v, $sensitive);
            } else {
                if (in_array($k, $sensitive, true)) {
                    $v = '[FILTERED]';
                }
            }
        }
    }
}
