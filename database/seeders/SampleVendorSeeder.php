<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * DEVELOPMENT ONLY. Creates clearly labelled sample vendors and menu items so the
 * customer pages have something to show. Safe to run more than once (it never
 * duplicates or overwrites existing rows).
 *
 * Run with:  php artisan db:seed --class=SampleVendorSeeder
 * Sample vendor password: Password1
 */
class SampleVendorSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Sample data is not allowed in production.');

            return;
        }

        foreach ($this->vendors() as $data) {
            // role / status are not mass-assignable, so they are set explicitly.
            $user = User::firstOrNew(['email' => $data['email']]);

            if (! $user->exists) {
                $user->forceFill([
                    'name' => $data['owner'],
                    'phone' => $data['phone'],
                    'address' => $data['business_address'],
                    'role' => UserRole::Vendor,
                    'status' => $data['status'],
                    'password' => 'Password1',
                ])->save();
            }

            if (! $user->vendorProfile()->exists()) {
                $user->vendorProfile()->create([
                    'business_name' => $data['business_name'],
                    'business_address' => $data['business_address'],
                    'description' => $data['description'],
                    'estimated_prep_time' => $data['prep'],
                    'is_available' => $data['open'],
                ]);
            }

            foreach ($data['items'] as [$name, $category, $price, $prep, $available, $description]) {
                $user->menuItems()->firstOrCreate(
                    ['item_name' => $name],
                    [
                        'category' => $category,
                        'description' => $description,
                        'price' => $price,
                        'prep_time' => $prep,
                        'is_available' => $available,
                    ]
                );
            }
        }

        $this->command?->info('Sample vendors and menu items are ready.');
    }

    /** @return array<int, array<string, mixed>> */
    private function vendors(): array
    {
        return [
            [
                'email' => 'juan@sample.binalbaganeats.test',
                'owner' => 'Juan Dela Cruz',
                'phone' => '09171110001',
                'status' => UserStatus::Approved,
                'business_name' => "Juan's Kusina",
                'business_address' => 'Poblacion, Binalbagan, Negros Occidental',
                'description' => 'Home-cooked Filipino favorites and grilled specialties. (Sample vendor)',
                'prep' => 20,
                'open' => true,
                'items' => [
                    ['Chicken Inasal', 'Chicken', 120.00, 20, true, 'Grilled chicken marinated in calamansi and annatto oil.'],
                    ['Pork BBQ', 'Main Viand', 100.00, 15, true, 'Skewered pork barbecue with a sweet-savory glaze.'],
                    ['Pancit Canton', 'Noodles', 90.00, 12, true, 'Stir-fried egg noodles with vegetables.'],
                    ['Plain Rice', 'Rice Meals', 20.00, 5, true, 'A cup of steamed rice.'],
                    ['Halo-Halo', 'Desserts', 65.00, 8, true, 'Shaved ice with sweet beans, fruits and leche flan.'],
                    ['Iced Tea', 'Drinks', 35.00, 3, true, 'Freshly brewed and served cold.'],
                ],
            ],
            [
                'email' => 'maria@sample.binalbaganeats.test',
                'owner' => 'Maria Santos',
                'phone' => '09171110002',
                'status' => UserStatus::Approved,
                'business_name' => "Maria's Kitchen",
                'business_address' => 'Mabini Street, Binalbagan, Negros Occidental',
                'description' => 'Comfort food cooked the way grandma made it. (Sample vendor)',
                'prep' => 25,
                'open' => true,
                'items' => [
                    ['Sinigang na Baboy', 'Main Viand', 150.00, 25, true, 'Pork in sour tamarind broth with vegetables.'],
                    ['Adobo Rice Meal', 'Rice Meals', 110.00, 15, true, 'Chicken and pork adobo served with rice.'],
                    ['Fried Chicken', 'Chicken', 130.00, 20, true, 'Crispy golden fried chicken.'],
                    ['Leche Flan', 'Desserts', 55.00, 5, true, 'Creamy caramel custard.'],
                    ['Calamansi Juice', 'Drinks', 40.00, 5, true, 'Fresh calamansi juice.'],
                    ['Banana Cue', 'Snacks', 30.00, 10, true, 'Caramelized fried banana on a stick.'],
                ],
            ],
            [
                'email' => 'tastebuds@sample.binalbaganeats.test',
                'owner' => 'Pedro Reyes',
                'phone' => '09171110003',
                'status' => UserStatus::Approved,
                'business_name' => 'Taste Buds',
                'business_address' => 'San Isidro Street, Binalbagan, Negros Occidental',
                'description' => 'Burgers, fries and chicken wings for the whole barkada. (Sample vendor)',
                'prep' => 15,
                'open' => true,
                'items' => [
                    ['Cheeseburger', 'Burgers', 85.00, 10, true, 'Beef patty, cheese, lettuce and tomato.'],
                    ['Double Burger', 'Burgers', 135.00, 12, false, 'Two beef patties. (Sample: marked unavailable)'],
                    ['French Fries', 'Snacks', 60.00, 8, true, 'Crispy, lightly salted fries.'],
                    ['Chicken Wings', 'Chicken', 160.00, 18, true, 'Six pieces of glazed chicken wings.'],
                    ['Soda', 'Drinks', 30.00, 2, true, 'Chilled soft drink.'],
                    ['Chocolate Sundae', 'Desserts', 50.00, 3, true, 'Vanilla ice cream with chocolate syrup.'],
                ],
            ],
            [
                'email' => 'foodcorner@sample.binalbaganeats.test',
                'owner' => 'Ana Garcia',
                'phone' => '09171110004',
                'status' => UserStatus::Approved,
                'business_name' => 'Food Corner',
                'business_address' => 'Rizal Street, Binalbagan, Negros Occidental',
                'description' => 'Merienda and rice meals. (Sample vendor, store marked closed)',
                'prep' => 15,
                'open' => false,
                'items' => [
                    ['Pancit Palabok', 'Noodles', 75.00, 12, true, 'Rice noodles with shrimp sauce and toppings.'],
                    ['Lumpia Shanghai', 'Snacks', 50.00, 10, true, 'Crispy pork spring rolls.'],
                    ['Longganisa Rice Meal', 'Rice Meals', 95.00, 12, true, 'Sweet longganisa, garlic rice and egg.'],
                    ['Sago’t Gulaman', 'Drinks', 25.00, 3, true, 'Brown sugar drink with tapioca pearls and gelatin.'],
                ],
            ],
            [
                // Pending vendors must NOT appear anywhere for customers.
                'email' => 'pending@sample.binalbaganeats.test',
                'owner' => 'Pending Owner',
                'phone' => '09171110005',
                'status' => UserStatus::Pending,
                'business_name' => 'Pending Eatery',
                'business_address' => 'Binalbagan, Negros Occidental',
                'description' => 'Sample vendor awaiting approval. It must stay hidden from customers.',
                'prep' => 15,
                'open' => true,
                'items' => [
                    ['Test Silog', 'Rice Meals', 80.00, 10, true, 'Hidden sample item.'],
                    ['Test Drink', 'Drinks', 20.00, 2, true, 'Hidden sample item.'],
                ],
            ],
        ];
    }
}