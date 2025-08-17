<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class UpdatePaymentRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = Reader::createFromPath(storage_path('payments.csv'), 'r');
        $csv->setHeaderOffset(0); // First row = headers

        foreach ($csv as $record) {
            $user = User::where('email', $record['User Email'])
                ->where('first_name', $record['First_Name'])
                ->where('last_name', $record['Last_Name'])
                ->first();

            if ($user) {
                // Clean up amount -> only digits + decimal
                $amount = (float) preg_replace('/[^0-9.]/', '', $record['Amount'] ?? '0');

                // Optional: update user payment info if columns exist
                if (isset($user->payment_status)) {
                    $user->payment_status = "successful";
                    $user->payment_method = $record['Gateway'] ?? null;
                    $user->last_payment_reference = $record['Transaction_Id'] ?? null;
                    $user->last_payment_amount = $amount;
                    $user->last_payment_at = Carbon::parse($record['Payment_Date'])->format('Y-m-d H:i:s');
                    $user->save();
                }

                // Insert or update payment record
               Payment::create([
                'user_id'        => $user->id,
                'amount'         => $amount, // now a number
                'starts_at'      => Carbon::parse($record['Payment_Date'])->format('Y-m-d H:i:s'),
                'ends_at'        => $record['Membership'] === 'Monthly Membership'
                    ? Carbon::parse($record['Payment_Date'])->copy()->addMonth()->format('Y-m-d H:i:s')
                    : ($record['Membership'] === 'Quarterly Membership'
                        ? Carbon::parse($record['Payment_Date'])->copy()->addMonths(3)->format('Y-m-d H:i:s')
                        : Carbon::parse($record['Payment_Date'])->copy()->addYear()->format('Y-m-d H:i:s')
                    ),
                'payment_method' => $record['Gateway'] ?? null,
                'reference'      => $record['Transaction_Id'] ?? null,
                'status'         =>  'successful',
            ]);

            }
        }
    }
}
