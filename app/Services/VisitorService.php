<?php

namespace App\Services;

use App\Models\User;
use App\Models\Visitor;

class VisitorService
{
    /**
     * Store a new visitor record only if:
     * - Email does NOT exist in users table
     * - Email does NOT already exist in visitors table
     *
     * @param string $email
     * @param string|null $source
     * @return \App\Models\Visitor|null
     */
    public function store(string $email, string $source = 'visitor_page'): ?Visitor
    {
        // 1. Do not store if email exists in users table
        $emailExistsInUsers = User::where('email', $email)->exists();
        if ($emailExistsInUsers) {
            return null;
        }

        // 2. Do not store if email already exists in visitors
        $existingVisitor = Visitor::where('email', $email)->first();
        if ($existingVisitor) {
            return $existingVisitor;
        }

        // 3. Store new visitor
        return Visitor::create([
            'email'  => $email,
            'source' => $source,
        ]);
    }
}
