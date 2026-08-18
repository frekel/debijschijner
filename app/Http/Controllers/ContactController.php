<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactController extends Controller
{
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bsforms.fields.0.first' => ['required', 'string', 'max:120'],
            'bsforms.fields.0.last' => ['required', 'string', 'max:120'],
            'bsforms.fields.1' => ['required', 'email', 'max:255'],
            'bsforms.fields.9' => ['required', 'string', 'max:40'],
            'bsforms.fields.3' => ['required', 'string', 'max:5000'],
        ]);

        $honeypot = Arr::get($request->input('bsforms', []), 'fields.2');

        if (! empty($honeypot)) {
            return back()->with('contact_success', 'Bedankt! Je bericht is ontvangen.');
        }

        try {
            ContactSubmission::create([
                'first_name' => Arr::get($validated, 'bsforms.fields.0.first'),
                'last_name' => Arr::get($validated, 'bsforms.fields.0.last'),
                'email' => Arr::get($validated, 'bsforms.fields.1'),
                'phone' => Arr::get($validated, 'bsforms.fields.9'),
                'message' => Arr::get($validated, 'bsforms.fields.3'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (Throwable) {
            Log::warning('Contact form submission could not be stored in database.');
        }

        return back()->with('contact_success', 'Bedankt! Je bericht is ontvangen.');
    }
}
