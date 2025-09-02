<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        Mail::to('contact@kingsleykhordpiano.com')->send(new ContactMessage($validated));

        return redirect()->back()->with('success', 'Your message has been recieved, we will get back to you.');
    }

    public function store(StoreContactRequest $request)
    {
        try {
            $user = auth()->user();

            // Store contact message in DB
            Contact::create([
                'name' => $user->fist_name. " ".$user->last_name,
                'email' => $user->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            // Send raw email (consider using a Mailable)
            Mail::raw($request->message, function ($msg) use ($request, $user) {
                $msg->to('contact@kingsleykhordpiano.com')
                    ->subject($request->subject);
            });

            return redirect()->back()->with('success', 'Your message has been sent.');
        } catch (\Throwable $th) {
            \Log::error('Contact form error: ' . $th->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
