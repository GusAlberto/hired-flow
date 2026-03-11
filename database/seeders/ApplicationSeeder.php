<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->isEmpty()) {
            $users = collect([
                User::firstOrCreate(
                    ['email' => 'test@example.com'],
                    [
                        'name' => 'Test User',
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                    ]
                ),
            ]);
        }

        $statuses = ['applied', 'waiting', 'interview', 'rejected', 'offer'];
        $cities = ['Sao Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Curitiba', 'Recife'];

        foreach ($users as $user) {
            // Replace only records created by this seeder, preserving real user data.
            Application::withTrashed()
                ->where('user_id', $user->id)
                ->where('notes', 'like', '[FAKE_SEED]%')
                ->forceDelete();

            for ($i = 0; $i < 5; $i++) {
                $status = fake()->randomElement($statuses);

                Application::create([
                    'user_id' => $user->id,
                    'company' => fake()->company(),
                    'position' => fake()->jobTitle(),
                    'city' => fake()->randomElement($cities),
                    'location' => fake()->randomElement(['remote', 'hybrid', 'on-site']),
                    'stage' => $status,
                    'is_favorite' => fake()->boolean(20),
                    'status' => $status,
                    // Keep recent dates so records stay visible in active columns.
                    'applied_at' => fake()->dateTimeBetween('-10 days', 'now')->format('Y-m-d'),
                    'job_url' => fake()->url(),
                    'personal_score' => fake()->numberBetween(1, 10),
                    'salary_offered' => fake()->randomFloat(2, 4000, 25000),
                    'salary_expected' => fake()->randomFloat(2, 4000, 30000),
                    'notes' => '[FAKE_SEED] ' . fake()->sentence(),
                ]);
            }
        }
    }
}
