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

        return redirect()->back()->with('success', 'Your message has been sent successfully.');
    }

    public function store(StoreContactRequest $request)
    {
        try {
            $user = auth()->user();

            $attachmentPath = null;
            $attachmentName = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $storedPath = $file->store('contact-attachments', 'public');
                $attachmentPath = $storedPath;
            }

            Contact::create([
                'name' => $user->first_name . " " . $user->last_name,
                'email' => $user->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            Mail::send('emails.contact', [
                'name' => $user->first_name . " " . $user->last_name,
                'email' => $user->email,
                'subject' => $request->subject,
                'messageContent' => $request->message,
            ], function ($msg) use ($request, $user, $attachmentPath, $attachmentName) {
                $msg->to('contact@kingsleykhordpiano.com')
                    ->subject('Contact Form: ' . $request->subject);

                if ($attachmentPath) {
                    $msg->attach(\Storage::disk('public')->path($attachmentPath), [
                        'as' => $attachmentName,
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Your message has been sent successfully.');

        } catch (\Throwable $th) {
            \Log::error('Contact form error: ' . $th->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

}
