<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class AssistantController extends Controller
{
    public function show(): View
    {
        return view('assistant', [
            'enabled' => (bool) Setting::get('ai.enabled', true),
            'assistantName' => (string) Setting::get('ai.assistant_name', 'TripCash AI'),
            'welcome' => (string) Setting::get('ai.welcome_message', "Hi! I'm your travel assistant. Ask me anything."),
            'suggestions' => array_values(array_filter(array_map(
                'trim',
                preg_split('/\r\n|\r|\n/', (string) Setting::get('ai.suggestions', ''))
            ))),
            'categories' => config('tripcash.categories'),
        ]);
    }
}
