<?php
/**
 * Framework-free sanity checks for the pure business logic.
 * Runs without Composer/vendor so the core algorithms can be verified anywhere.
 *   php tools/sanity-check.php
 */

spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $path = __DIR__.'/../backend/app/'.str_replace('\\', '/', substr($class, 4)).'.php';
        if (is_file($path)) {
            require $path;
        }
    }
});

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Services\Affiliate\AffiliateTracker;

$passed = 0;
$failed = 0;
function check(string $name, bool $cond): void
{
    global $passed, $failed;
    if ($cond) { $passed++; echo "  \033[32mPASS\033[0m  $name\n"; }
    else       { $failed++; echo "  \033[31mFAIL\033[0m  $name\n"; }
}

echo "\n== SearchQuery DTO ==\n";
$q = SearchQuery::fromArray(['category' => 'hotels', 'destination' => 'Goa', 'currency' => 'INR']);
check('fromArray maps category', $q->category === 'hotels');
check('fromArray maps destination', $q->destination === 'Goa');
check('cacheKey is deterministic', $q->cacheKey() === SearchQuery::fromArray(['category'=>'hotels','destination'=>'Goa','currency'=>'INR'])->cacheKey());
check('cacheKey differs by destination', $q->cacheKey() !== SearchQuery::fromArray(['category'=>'hotels','destination'=>'Bali','currency'=>'INR'])->cacheKey());

echo "\n== NormalizedOffer DTO ==\n";
$o = new NormalizedOffer(
    providerId: 1, providerSlug: 'booking-com', providerName: 'Booking.com',
    category: 'hotels', title: 'Grand Plaza', price: 5000.0, currency: 'INR'
);
$o->withCashback(259.2);
$arr = $o->toArray();
check('toArray price rounded', $arr['price'] === 5000.0);
check('cashback applied', $arr['cashback'] === 259.2);
check('hash is stable 64-char sha256', strlen($o->hash()) === 64 && $o->hash() === $o->hash());
check('hash differs by price', $o->hash() !== (new NormalizedOffer(1,'booking-com','Booking.com','hotels','Grand Plaza',5001.0))->hash());

echo "\n== AffiliateTracker HMAC postback verification ==\n";
// Instantiate without constructor (its deps aren't needed for the pure method).
$tracker = (new ReflectionClass(AffiliateTracker::class))->newInstanceWithoutConstructor();
$secret = 'super-secret';
$payload = ['provider' => 'booking-com', 'booking_ref' => 'BK123', 'amount' => 5400, 'status' => 'confirmed'];
$canonical = $payload; ksort($canonical);
$validSig = hash_hmac('sha256', http_build_query($canonical), $secret);
check('valid signature accepted', $tracker->verifySignature($secret, $payload, $validSig) === true);
check('tampered amount rejected', $tracker->verifySignature($secret, array_merge($payload, ['amount' => 999999]), $validSig) === false);
check('wrong secret rejected', $tracker->verifySignature('wrong', $payload, $validSig) === false);
check('signature key ignored in canonicalisation', $tracker->verifySignature($secret, array_merge($payload, ['sig' => $validSig]), $validSig) === true);

echo "\n----------------------------------------\n";
echo "Result: \033[32m$passed passed\033[0m, ".($failed ? "\033[31m$failed failed\033[0m" : "0 failed")."\n\n";
exit($failed ? 1 : 0);
