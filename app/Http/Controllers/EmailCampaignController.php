<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Http\Requests\StoreEmailCampaignRequest;
use App\Http\Requests\UpdateEmailCampaignRequest;
use App\Services\EmailCampaign\CreateEmailCampaignService;
use App\Services\EmailCampaign\IndexEmailCampaignService;
use App\Services\EmailCampaign\ResendEmailCampaignService;


class EmailCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.email-campaign.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.email-campaign.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreEmailCampaignRequest $request,
        CreateEmailCampaignService $service
    ) {
        $campaign = $service->create($request);
    
        return response()->json([
            'message' => 'Email campaign created successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function resend(EmailCampaign $emailCampaign, ResendEmailCampaignService $service)
    {
    
        $campaign = $service->resend($emailCampaign);
    
        return response()->json([
            'message' => 'Email sent successfully',
            'data' => $campaign
        ]);
    }

    public function listEmailCampaign(IndexEmailCampaignService $service)
    {
        $result = $service->all();
    
        return response()->json([
            'message' => 'Email campaigns fetched successfully',
            'data' => $result['campaigns'],  
            'counts' => $result['counts'],
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailCampaign $emailCampaign)
    {
        //
    }
}
