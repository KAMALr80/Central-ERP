<?php
// app/Http/Controllers/Agent/SupportController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function index()
    {
        return view('agent.support.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $user = Auth::user();

        // Send email to admin
        Mail::send('emails.support', [
            'user' => $user,
            'subject' => $request->subject,
            'messageContent' => $request->message
        ], function ($mail) use ($request, $user) {
            $mail->to(config('mail.support_email', 'support@invozia.com'))
                ->subject('Support Request: ' . $request->subject)
                ->from($user->email, $user->name);
        });

        return redirect()->route('agent.support')->with('success', 'Your support request has been sent. We will get back to you soon.');
    }
}
