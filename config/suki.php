<?php

return [

    // Pesos of FOOD SUBTOTAL (delivery fee excluded) needed to earn 1 point. Whole points only.
    'earn_peso_per_point' => 50,

    // Peso discount that 1 point is worth when redeemed.
    'redeem_value_pesos' => 1,

    // Smallest number of points a customer may redeem on one order.
    'min_redeem_points' => 10,

    // Points may cover at most this percentage of the food subtotal.
    // The delivery fee can never be discounted.
    'max_discount_percent' => 50,

];