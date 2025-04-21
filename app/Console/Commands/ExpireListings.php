<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Models\ListingStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired listings as Closed-Expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the status IDs
        $openStatusId = ListingStatus::where('name', 'Open')->first()->id ?? null;
        $expiredStatusId = ListingStatus::where('name', 'Closed-Expired')->first()->id ?? null;

        // Only validate the statuses we need for this specific operation
        if (!$openStatusId) {
            $this->error("Required 'Open' status not found in listing_statuses table.");
            return 1;
        }

        if (!$expiredStatusId) {
            $this->warning("'Closed-Expired' status not found. Using fallback method.");

            // Try to get any 'Closed-' prefixed status as a fallback
            $fallbackStatus = ListingStatus::where('name', 'like', 'Closed-%')->first();

            if ($fallbackStatus) {
                $expiredStatusId = $fallbackStatus->id;
                $this->info("Using '{$fallbackStatus->name}' status as fallback.");
            } else {
                $this->error("No suitable 'Closed-' status found. Cannot expire listings.");
                return 1;
            }
        }

        // Calculate today's date (without time)
        $today = Carbon::now()->startOfDay();
        // Get current timestamp for expired_at field
        $currentTimestamp = Carbon::now();

        // Find open listings that have expired
        $expiredListings = Listing::where('status_id', $openStatusId)
            ->get()
            ->filter(function ($listing) use ($today) {
                // Calculate expiry date (using only the date portion, ignoring time)
                $expiryDate = $listing->published_at->copy()
                    ->addDays($listing->expiry_days)
                    ->startOfDay();

                // Check if expiry date is in the past
                return $expiryDate->lt($today);
            });

        $count = 0;

        // Update the expired listings
        foreach ($expiredListings as $listing) {
            $listing->status_id = $expiredStatusId;
            // Set the expired_at timestamp to current time
            $listing->expired_at = $currentTimestamp;
            $listing->save();
            $count++;

            // Optionally log or notify about the expiration
            Log::info("Listing #{$listing->id} ({$listing->title}) marked as expired at {$currentTimestamp}");
        }

        $this->info("Successfully expired {$count} listings.");

        return 0;
    }
}