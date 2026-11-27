<?php

namespace App\Policies;

use App\Enums\BuyForMeSettlement;
use App\Enums\BuyForMeStatus;
use App\Models\BuyForMeRequest;
use App\Models\User;

class BuyForMeRequestPolicy
{
    /** Customers may only see their own requests. */
    public function view(User $user, BuyForMeRequest $request): bool
    {
        return $user->isCustomer() && (int) $request->customer_id === (int) $user->id;
    }

    /** Cancel button: allowed until the advance has been submitted. */
    public function cancel(User $user, BuyForMeRequest $request): bool
    {
        return $this->view($user, $request)
            && $request->request_status->customerCancellable()
            && $request->settlement_status !== BuyForMeSettlement::AdvanceSubmitted;
    }

    public function submitAdvance(User $user, BuyForMeRequest $request): bool
    {
        return $this->view($user, $request)
            && $request->request_status === BuyForMeStatus::AwaitingAdvance
            && $request->settlement_status === BuyForMeSettlement::AwaitingAdvance;
    }
}