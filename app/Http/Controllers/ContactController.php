<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreContactRequest $request)
{
    try {
        $user = auth()->user();

        // Store contact message in DB
        Contact::create([
            'name' => $user->name,
            'email' => $user->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send raw email (consider using a Mailable)
        Mail::raw($request->message, function ($msg) use ($request, $user) {
            $msg->to('shedrackogwuche5@gmail.com')
                ->subject($request->subject)
                ->from('contact@kingsleykhordpiano.com');
        });

        return redirect()->back()->with('success', 'Your message has been sent.');
    } catch (\Throwable $th) {
        // Optionally log the error
        \Log::error('Contact form error: ' . $th->getMessage());

        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
