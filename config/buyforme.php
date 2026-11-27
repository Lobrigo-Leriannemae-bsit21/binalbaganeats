<?php

return [

    // Share of the estimated total (food estimate + delivery fee) the customer pays up front by GCash.
    // The advance is rounded UP to a whole peso.
    'advance_percent' => 50,

    // The rider may record an actual food expense at most this much above the estimate.
    // Beyond it, the rider contacts the customer and an admin adjusts the amount.
    'overrun_percent' => 10,

    // Most open (not delivered / cancelled) requests one customer may have at once.
    'max_open_requests' => 3,

    // Sanity ceiling for a rider's estimate, in pesos.
    'max_estimate' => 100000,

];