<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Services\Affiliate\AffiliateTracker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class RedirectController extends Controller
{
    public function __construct(private readonly AffiliateTracker $tracker) {}

    /**
     * Signed click-out endpoint. Records the click, drops a tracking cookie and
     * 302s the user to the provider's affiliate deep-link.
     */
    public function out(Provider $provider, Request $request): RedirectResponse
    {
        abort_unless($provider->is_active, 404);

        $params = $request->only(['offer_ref', 'amount', 'category', 'url', 'currency']);
        $result = $this->tracker->registerClick($provider, $params, $request);

        $cookieDays = (int) config('travelcash.affiliate.cookie_days', 30);
        $cookie = Cookie::create(
            't_click',
            $result['click']->click_id,
            now()->addDays($cookieDays)->getTimestamp(),
            '/',
            null,
            $request->secure(),
            true,           // httpOnly
            false,
            'lax'
        );

        return redirect()->away($result['redirect_url'])->withCookie($cookie);
    }
}
