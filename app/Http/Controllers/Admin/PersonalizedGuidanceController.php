<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalizedGuidanceRequest;
use Illuminate\Http\Request;

class PersonalizedGuidanceController extends Controller
{
    public function index()
    {
        $requests = PersonalizedGuidanceRequest::with('user')->latest()->paginate(15);
        return view('admin.personalized_guidance.index', compact('requests'));
    }

    public function markReviewed(PersonalizedGuidanceRequest $guidanceRequest)
    {
        $guidanceRequest->update(['status' => 'reviewed']);
        return back()->with('success', 'Marked as reviewed.');
    }
}
