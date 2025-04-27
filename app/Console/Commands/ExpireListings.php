<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Models\ListingStatus;
use App\Models\Quote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Mark expired listings as Closed-Expired and update associated quotes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the listing status IDs
        $openListingStatusId = ListingStatus::where('name', 'Open')->first()->id ?? null;
        $expiredListingStatusId = ListingStatus::where('name', 'Closed-Expired')->first()->id ?? null;

        // Only validate the statuses we need for this specific operation
        if (!$openListingStatusId) {
            $this->error("Required 'Open' status not found in listing_statuses table.");
            return 1;
        }

        if (!$expiredListingStatusId) {
            $this->warning("'Closed-Expired' status not found. Using fallback method.");

            // Try to get any 'Closed-' prefixed status as a fallback
            $fallbackStatus = ListingStatus::where('name', 'like', 'Closed-%')->first();

            if ($fallbackStatus) {
                $expiredListingStatusId = $fallbackStatus->id;
                $this->info("Using '{$fallbackStatus->name}' status as fallback.");
            } else {
                $this->error("No suitable 'Closed-' status found. Cannot expire listings.");
                return 1;
            }
        }

        // Get quote status IDs
        $openQuoteStatusId = DB::table('quote_statuses')->where('name', 'Open')->value('id');
        $rejectedQuoteStatusId = DB::table('quote_statuses')->where('name', 'Closed-Rejected')->value('id');

        if (!$openQuoteStatusId || !$rejectedQuoteStatusId) {
            $this->error("Required quote statuses not found. Cannot update quotes.");
            return 1;
        }

        // Calculate today's date (without time)
        $today = Carbon::now()->startOfDay();
        // Get current timestamp for expired_at field
        $currentTimestamp = Carbon::now();

        // Find open listings that have expired
        $expiredListings = Listing::where('status_id', $openListingStatusId)
            ->get()
            ->filter(function ($listing) use ($today) {
                // Calculate expiry date (using only the date portion, ignoring time)
                $expiryDate = Carbon::parse($listing->published_at)
                    ->addDays($listing->expiry_days)
                    ->startOfDay();

                // Check if expiry date is in the past
                return $expiryDate->lt($today);
            });

        $listingCount = 0;
        $quoteCount = 0;

        // Process each expired listing within a transaction
        foreach ($expiredListings as $listing) {
            DB::beginTransaction();

            try {
                // Update the listing status
                $listing->status_id = $expiredListingStatusId;
                $listing->expired_at = $currentTimestamp;
                $listing->save();
                $listingCount++;

                // Find and update any open quotes associated with this listing
                $openQuotes = Quote::where('listing_id', $listing->id)
                    ->where('status_id', $openQuoteStatusId)
                    ->get();

                foreach ($openQuotes as $quote) {
                    $quote->status_id = $rejectedQuoteStatusId;
                    $quote->save();
                    $quoteCount++;

                    Log::info("Quote #{$quote->id} for Listing #{$listing->id} marked as Closed-Rejected due to listing expiration");
                }

                DB::commit();

                // Log the expiration
                Log::info("Listing #{$listing->id} ({$listing->title}) marked as expired at {$currentTimestamp}. {$openQuotes->count()} quotes updated to Closed-Rejected.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error expiring listing #{$listing->id}: " . $e->getMessage());
                $this->error("Error expiring listing #{$listing->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully expired {$listingCount} listings and updated {$quoteCount} quotes to Closed-Rejected status.");

        return 0;
    }
}