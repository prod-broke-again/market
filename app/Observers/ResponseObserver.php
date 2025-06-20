<?php

namespace App\Observers;

use App\Models\Chat;
use App\Models\Response;

class ResponseObserver
{
    /**
     * Handle the Response "created" event.
     */
    public function created(Response $response): void
    {
        Chat::create([
            'response_id' => $response->id,
            'seller_id' => $response->seller_id,
            'customer_id' => $response->customerRequest->user_id,
        ]);
    }

    /**
     * Handle the Response "updated" event.
     */
    public function updated(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "deleted" event.
     */
    public function deleted(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "restored" event.
     */
    public function restored(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "force deleted" event.
     */
    public function forceDeleted(Response $response): void
    {
        //
    }
} 