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
            'subtitle' => 'Last updated: '.now()->format('F Y'),
            'sections' => [
                ['Information we collect', 'Account details (name, email, phone), KYC details required for payouts (PAN, bank/UPI), booking and click activity, device and usage data, and support communications.'],
                ['How we use it', 'To operate the platform, track affiliate clicks and confirm cashback, process payouts, prevent fraud, provide support, and (with consent) send notifications.'],
                ['Sharing', 'We share the minimum necessary data with affiliate networks/providers to attribute bookings, and with payment processors to issue payouts. We never sell your personal data.'],
                ['Security', 'Sensitive data (payout details, provider keys, MFA secrets) is encrypted at rest. Access is role-restricted and audit-logged.'],
                ['Your rights', 'You can access, correct or delete your data, and opt out of marketing notifications, by contacting support.'],
            ],
        ]);
    }

    public function refund(): View
    {
        return view('pages.legal', [
            'title' => 'Cashback & Refund Policy',
            'subtitle' => 'How cashback is tracked, confirmed and paid.',
            'sections' => [
                ['Cashback lifecycle', 'Cashback starts as Pending when a booking is detected, becomes Confirmed once the provider validates the booking, and turns Withdrawable after the provider’s cancellation/return window (typically 30–90 days).'],
                ['When cashback is rejected', 'If a booking is cancelled, returned or deemed ineligible by the provider, the related cashback is reversed. Pending cashback is not guaranteed until confirmed.'],
                ['Withdrawals', 'Confirmed (withdrawable) balance can be withdrawn to UPI, bank or PayPal after completing KYC, subject to the minimum withdrawal amount. Payouts are processed after admin verification.'],
                ['Booking refunds', 'Refunds for the booking itself are handled by the provider you booked with, per their policy. TripCash only manages the cashback layer.'],
            ],
        ]);
    }

    public function terms(): View
    {
        return view('pages.legal', [
            'title' => 'Terms & Conditions',
            'subtitle' => 'Last updated: '.now()->format('F Y'),
            'sections' => [
                ['Acceptance', 'By using TripCash you agree to these terms. If you do not agree, please do not use the platform.'],
                ['Accounts', 'You are responsible for your account security. One account per person. We may suspend accounts involved in fraud or abuse.'],
                ['Cashback', 'Cashback rates are set per offer/provider and may change. Cashback is subject to provider confirmation and our Cashback & Refund Policy.'],
                ['Affiliate bookings', 'Bookings are made with third-party providers. TripCash is not responsible for the provider’s service, pricing or fulfilment.'],
                ['Fraud', 'Any attempt to manipulate clicks, postbacks, referrals or cashback will result in forfeiture of balance and account termination.'],
                ['Liability', 'The platform is provided “as is”. Our liability is limited to the cashback balance in your wallet.'],
            ],
        ]);
    }
}
