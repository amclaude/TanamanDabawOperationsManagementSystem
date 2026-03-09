<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $clients = [];
        for ($i = 0; $i < 50; $i++) {
            $clients[] = Client::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->numerify('09#########'),
                'address' => $faker->address(),
            ])->id;
        }

        $roles = ['Head Landscaper', 'Field Crew'];
        $employeeUserIds = [];
        for ($i = 0; $i < 50; $i++) {
            $name = $faker->name();
            $username = Str::slug($name) . $faker->unique()->numberBetween(1000, 9999);
            $user = User::create([
                'name' => $name,
                'email' => $faker->unique()->safeEmail(),
                'username' => $username,
                'password' => bcrypt('password'),
                'role' => $roles[array_rand($roles)],
                'status' => 'Active',
                'phone' => $faker->numerify('09#########'),
                'location' => $faker->city(),
                'bio' => $faker->sentence(8),
            ]);
            DB::table('employees')->insert([
                'name' => $name,
                'phone' => $faker->numerify('09#########'),
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $employeeUserIds[] = $user->id;
        }

        $categoryIds = InventoryCategory::pluck('id')->all();
        if (empty($categoryIds)) {
            $categoryIds = [null];
        }
        for ($i = 0; $i < 50; $i++) {
            Inventory::create([
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'item_name' => ucfirst($faker->words(2, true)),
                'sku' => strtoupper(Str::random(3)) . '-' . $faker->unique()->numerify('#####'),
                'price' => number_format($faker->randomFloat(2, 50, 20000), 2, '.', ''),
                'stock_level' => $faker->numberBetween(0, 500),
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            $clientId = $clients[array_rand($clients)];
            $quote = Quote::create([
                'client_id' => $clientId,
                'subject' => ucfirst($faker->words(3, true)),
                'quote_date' => Carbon::now()->subDays($faker->numberBetween(0, 60))->toDateString(),
                'valid_until' => Carbon::now()->addDays($faker->numberBetween(7, 30))->toDateString(),
                'total_amount' => 0,
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
            ]);
            $itemsCount = $faker->numberBetween(2, 5);
            $total = 0;
            for ($j = 0; $j < $itemsCount; $j++) {
                $qty = $faker->numberBetween(1, 10);
                $price = $faker->randomFloat(2, 100, 10000);
                $subtotal = $qty * $price;
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => ucfirst($faker->words(4, true)),
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }
            $quote->update(['total_amount' => $total]);
        }

        $headIds = User::where('role', 'Head Landscaper')->pluck('id')->all();
        for ($i = 0; $i < 50; $i++) {
            $clientId = $clients[array_rand($clients)];
            $start = Carbon::now()->subDays($faker->numberBetween(0, 90));
            $end = (clone $start)->addDays($faker->numberBetween(7, 60));
            $project = Project::create([
                'project_name' => ucfirst($faker->words(3, true)),
                'quote_id' => Quote::inRandomOrder()->value('id'),
                'project_budget' => $faker->randomFloat(2, 5000, 500000),
                'is_active' => $faker->boolean(70),
                'project_start_date' => $start->toDateString(),
                'project_end_date' => $end->toDateString(),
                'project_description' => $faker->sentence(10),
                'project_location' => $faker->address(),
                'client_id' => $clientId,
                'head_landscaper_id' => !empty($headIds) ? $headIds[array_rand($headIds)] : null,
            ]);
        }

        $projectPairs = Project::with('client')->get()->map(function ($p) {
            return [$p->id, $p->client_id];
        })->all();
        for ($i = 0; $i < 50; $i++) {
            if (empty($projectPairs)) {
                break;
            }
            $pair = $projectPairs[array_rand($projectPairs)];
            $issue = Carbon::now()->subDays($faker->numberBetween(0, 30));
            $due = (clone $issue)->addDays($faker->numberBetween(7, 30));
            $invoice = Invoice::create([
                'project_id' => $pair[0],
                'client_id' => $pair[1],
                'issue_date' => $issue->toDateString(),
                'due_date' => $due->toDateString(),
                'total_amount' => 0,
                'status' => $faker->randomElement(['draft', 'sent', 'paid', 'overdue']),
            ]);
            $itemsCount = $faker->numberBetween(1, 4);
            $total = 0;
            for ($j = 0; $j < $itemsCount; $j++) {
                $qty = $faker->numberBetween(1, 10);
                $price = $faker->randomFloat(2, 100, 10000);
                $lineTotal = $qty * $price;
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => ucfirst($faker->words(4, true)),
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $lineTotal,
                ]);
                $total += $lineTotal;
            }
            $invoice->update(['total_amount' => $total]);
        }
    }
}
