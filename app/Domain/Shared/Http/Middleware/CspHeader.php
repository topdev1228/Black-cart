<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CspHeader
{
    /**
     * Ensures that the request is setting the appropriate CSP frame-ancestor directive.
     *
     * See https://shopify.dev/docs/apps/store/security/iframe-protection for more information
     */
    public function handle(Request $request, Closure $next): RedirectResponse|Response
    {
        $shop = $request->query('shop', '');

        $domainHost = $shop ? "https://$shop" : '*.myshopify.com';
        $allowedDomains = "$domainHost https://admin.shopify.com";

        /** @var Response $response */
        $response = $next($request);

        $currentHeader = $response->headers->get('Content-Security-Policy');
        if ($currentHeader) {
            $values = preg_split("/;\s*/", $currentHeader);

            // Replace or add the URLs the frame-ancestors directive
            $found = false;
            foreach ($values as $index => $value) {
                if (mb_strpos($value, 'frame-ancestors') === 0) {
                    $values[$index] = preg_replace('/^(frame-ancestors)/', "$1 $allowedDomains", $value);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $values[] = "frame-ancestors $allowedDomains";
            }

            $headerValue = implode('; ', $values);
        } else {
            $headerValue = "frame-ancestors $allowedDomains;";
        }

        $response->headers->set('Content-Security-Policy', $headerValue);

        return $response;
    }
}
