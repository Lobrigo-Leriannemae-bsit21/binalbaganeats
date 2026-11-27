<?php

return [

    // Largest quantity of a single menu item allowed in the cart / an order line.
    'max_item_quantity' => 20,

    'vendors_per_page' => 9,

    // Display order for menu categories. Categories not listed here sort last, A-Z.
    'category_order' => [
        'Rice Meals',
        'Main Viand',
        'Chicken',
        'Burgers',
        'Noodles',
        'Snacks',
        'Drinks',
        'Desserts',
    ],

    // Bootstrap Icons shown for each category (fallback: bi-egg-fried).
    'category_icons' => [
        'Rice Meals' => 'bi-egg-fried',
        'Main Viand' => 'bi-fire',
        'Chicken' => 'bi-fire',
        'Burgers' => 'bi-bag',
        'Noodles' => 'bi-cup-hot',
        'Snacks' => 'bi-basket2',
        'Drinks' => 'bi-cup-straw',
        'Desserts' => 'bi-cake2',
    ],

];