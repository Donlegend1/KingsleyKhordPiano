<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\FailedPaymentReminderMail; 
use Laravel\Cashier\Invoice;

class SendFailedPaymentReminders extends Command
{
    protected $signature = 'email:failed-payment-reminders';
    protected $description = 'Send reminders to users with failed or unpaid Stripe invoices and no successful payment after that.';

    public function handle()
    {
        $users = User::whereNotNull('stripe_id')->get();

        $targetUsers = collect();

        foreach ($users as $user) {
            try {
                // Fetch invoices from Stripe via Cashier
                $invoices = $user->invoicesIncludingPending();

                // Find the most recent failed or unpaid invoice
                $latestFailed = collect($invoices)
                    ->filter(fn ($invoice) => in_array($invoice->status, ['open', 'uncollectible']))
                    ->sortByDesc(fn ($invoice) => $invoice->created)
                    ->first();

                if (! $latestFailed) {
                    continue;
                }

                // Check if user has any successful (paid) invoice after that failed one
                $hasPaidAfter = collect($invoices)->contains(function ($invoice) use ($latestFailed) {
                    return $invoice->status === 'paid' && $invoice->created > $latestFailed->created;
                });

                if (! $hasPaidAfter) {
                    $targetUsers->push($user);
                }
            } catch (\Exception $e) {
                $this->error("Failed to fetch invoices for user ID {$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("Found {$targetUsers->count()} users with unresolved failed payments.");

        foreach ($targetUsers as $user) {
            Mail::to($user->email)->queue(new FailedPaymentReminderMail($user));
        }

        $this->info('Reminder emails queued successfully.');

        return Command::SUCCESS;
    }
}
