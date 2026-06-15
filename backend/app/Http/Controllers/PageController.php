<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function privacy(): View
    {
        return view('pages.legal', [
            'title' => 'Privacy Policy',
            'eyebrow' => 'Privacy',
            'icon' => 'shield-check',
            'subtitle' => 'Last updated: '.now()->format('F Y'),
            'intro' => 'Your privacy matters to us. This policy explains, in plain language, what information TripCash collects, why we collect it, how we protect it, and the choices and rights you have. We collect only what we need to run the platform and pay you cashback — and we never sell your personal data.',
            'sections' => [
                ['Information we collect', [
                    'Account details: your name, email and phone number when you sign up or log in (including via Google).',
                    'KYC details (only when you withdraw): full name, PAN, and payout details such as UPI ID or bank account.',
                    'Activity data: searches, the offers you click, bookings attributed to you, cashback and wallet history.',
                    'Technical data: device type, browser, IP address, and app usage to keep the service secure and reliable.',
                    'Communications: messages you send us via support, contact form, reviews or suggestions.',
                ]],
                ['How we use your information', [
                    'To operate the platform — search, click-tracking, and crediting your cashback.',
                    'To verify bookings with affiliate networks and confirm your cashback.',
                    'To process withdrawals to your UPI, bank or PayPal after KYC.',
                    'To prevent fraud, abuse and protect other users.',
                    'To provide support and, only with your consent, send deal alerts and notifications.',
                ]],
                ['What we never do', [
                    'We never sell your personal data to anyone.',
                    'We never share more data than is strictly required to attribute a booking or pay you.',
                    'We never store your card or full banking credentials — payments happen on the provider’s own site.',
                ]],
                ['How we share data', 'We share the minimum necessary information with affiliate networks/providers (to attribute your bookings and confirm cashback) and with payment processors (to issue your payouts). Some service providers (hosting, email/SMS, analytics) process data on our behalf under strict confidentiality. We may disclose data if required by law.'],
                ['Data security', [
                    'Sensitive data — payout details, provider API keys, MFA secrets — is encrypted at rest.',
                    'Access is role-restricted and privileged admin actions are audit-logged.',
                    'Connections are secured with HTTPS/TLS end to end.',
                ]],
                ['Cookies & tracking', 'We use essential cookies to keep you logged in and to attribute affiliate clicks (so your cashback is tracked correctly). You can control non-essential cookies in your browser, but disabling click-attribution cookies may prevent cashback from being credited.'],
                ['Your rights & choices', [
                    'Access, correct or delete your personal data by contacting support.',
                    'Opt out of marketing notifications at any time.',
                    'Request a copy of the data we hold about you.',
                ]],
                ['Data retention', 'We keep your data only as long as your account is active or as needed to provide the service, comply with legal/tax obligations, resolve disputes and enforce our agreements. Cashback and payout records are retained for the period required by law.'],
                ['Children', 'TripCash is intended for users aged 18 and above. We do not knowingly collect data from children. If you believe a minor has provided us data, contact us and we will remove it.'],
                ['Changes to this policy', 'We may update this policy from time to time. Material changes will be highlighted on this page with a new “last updated” date. Continued use after changes means you accept the updated policy.'],
            ],
        ]);
    }

    public function refund(): View
    {
        return view('pages.legal', [
            'title' => 'Cashback & Refund Policy',
            'eyebrow' => 'Cashback & Refunds',
            'icon' => 'badge-percent',
            'subtitle' => 'How cashback is earned, tracked, confirmed and paid out.',
            'intro' => 'TripCash earns an affiliate commission when you book through our links, and shares a large part of it back with you as real cashback. This policy explains exactly how cashback moves from “pending” to money in your wallet, when it can be reversed, and how withdrawals and booking refunds work.',
            'sections' => [
                ['How cashback works', [
                    'Search and compare deals across our partner providers on TripCash.',
                    'Click through and complete your booking on the provider’s site as usual — you pay the same price.',
                    'The provider pays us an affiliate commission; we share a configured percentage of it back to you as cashback.',
                ]],
                ['Cashback lifecycle', [
                    'Pending — added to your wallet when a booking is detected (usually within a few hours). Not yet guaranteed.',
                    'Confirmed — once the provider validates the booking was genuine and not cancelled.',
                    'Withdrawable — after the provider’s cancellation/return window passes (typically 30–90 days), the balance becomes available to withdraw.',
                ]],
                ['When cashback can be rejected or reversed', [
                    'The booking is cancelled, modified or returned.',
                    'The provider deems the booking ineligible (e.g. duplicate, fraudulent, or against their terms).',
                    'Coupon/promo codes from outside TripCash were used, which can void affiliate tracking.',
                    'Ad-blockers or disabled cookies prevented the click from being tracked.',
                    'Pending cashback is never guaranteed until it is confirmed by the provider.',
                ]],
                ['Withdrawals & payouts', [
                    'Complete one-time KYC (name, PAN, payout details) before your first withdrawal.',
                    'Withdraw your confirmed (withdrawable) balance to UPI, bank transfer or PayPal.',
                    'Payouts are processed after admin verification, typically within 24–48 hours.',
                    'A minimum withdrawal amount applies to cover processing — shown in your wallet.',
                ]],
                ['Booking cancellations & refunds', 'Refunds for the booking itself (room, flight, ticket, etc.) are handled entirely by the provider you booked with, according to their cancellation policy. TripCash only manages the cashback layer — we do not process booking refunds. If a booking is refunded or cancelled, any related cashback is reversed.'],
                ['Disputes & missing cashback', 'If cashback doesn’t appear or seems incorrect, contact support with your booking reference and date. We’ll raise a claim with the provider/affiliate network. Resolution can take a few weeks as it depends on the provider confirming the transaction.'],
                ['Changes to cashback rates', 'Cashback rates are set per offer/provider and may change at any time. The rate shown at the time of your click/booking is the one that applies to that booking.'],
            ],
        ]);
    }

    public function terms(): View
    {
        return view('pages.legal', [
            'title' => 'Terms & Conditions',
            'eyebrow' => 'Terms of Service',
            'icon' => 'scroll-text',
            'subtitle' => 'Last updated: '.now()->format('F Y'),
            'intro' => 'These terms govern your use of TripCash. By creating an account or using the platform, you agree to them. Please read them together with our Privacy Policy and Cashback & Refund Policy.',
            'sections' => [
                ['Acceptance of terms', 'By accessing or using TripCash you agree to be bound by these Terms & Conditions and all policies referenced here. If you do not agree, please do not use the platform.'],
                ['Eligibility & accounts', [
                    'You must be at least 18 years old to use TripCash.',
                    'One account per person; provide accurate, up-to-date information.',
                    'You are responsible for keeping your account credentials secure.',
                    'We may suspend or terminate accounts involved in fraud, abuse or policy violations.',
                ]],
                ['How TripCash works', 'TripCash is a travel meta-search and cashback platform. We help you compare deals across third-party providers and earn cashback on bookings made through our affiliate links. We are not a travel agent and do not sell travel inventory ourselves.'],
                ['Cashback terms', 'Cashback rates are set per offer/provider and may change. All cashback is subject to provider confirmation and our Cashback & Refund Policy. Pending cashback is not guaranteed until confirmed.'],
                ['Affiliate bookings & third parties', 'Bookings are completed with third-party providers under their own terms and pricing. TripCash is not responsible for the provider’s service, availability, pricing, fulfilment, cancellations or refunds. Any booking dispute must be resolved with the provider.'],
                ['Prohibited conduct & fraud', [
                    'No manipulation of clicks, postbacks, referrals or cashback.',
                    'No bots, scraping, or automated abuse of the platform.',
                    'No creating multiple accounts to exploit rewards or referrals.',
                    'Violations result in forfeiture of wallet balance and account termination.',
                ]],
                ['Wallet & withdrawals', 'Your wallet reflects cashback earned through the platform. Withdrawals require completed KYC and are subject to the minimum amount, verification and our Cashback & Refund Policy.'],
                ['Intellectual property', 'The TripCash name, logo, design and content are owned by us and may not be copied or used without permission.'],
                ['Limitation of liability', 'The platform is provided “as is” without warranties. To the maximum extent permitted by law, our total liability to you is limited to the cashback balance available in your wallet.'],
                ['Termination', 'You may close your account anytime. We may suspend or terminate access for breach of these terms. Unconfirmed/ineligible cashback is not payable on termination.'],
                ['Changes & governing law', 'We may update these terms; continued use means acceptance. These terms are governed by the laws of India, and disputes are subject to the jurisdiction of the courts there.'],
            ],
        ]);
    }
}
