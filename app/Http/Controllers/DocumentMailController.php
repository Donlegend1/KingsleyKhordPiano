<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentMail;
use Illuminate\Support\Facades\Log;

class DocumentMailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $filePath = public_path('piano.pdf');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        try {
            Mail::to($email)->send(new DocumentMail($filePath))->from('info@kingsleykhordpiano.com');
            return redirect()->back()->with('success', 'Roadmap sent successfully!');
        } catch (\Throwable $e) {
            Log::error('Failed to send document email: ' . $e->getMessage(), [
                'file' => $filePath,
                'email' => $email,
                'trace' => $e->getTraceAsString(),
            ]);

            Log::error('Failed to send document email: ' . $filePath);

            return redirect()->back()->with('error', 'Failed to send document. Please try again later.');
        }
    }
}
