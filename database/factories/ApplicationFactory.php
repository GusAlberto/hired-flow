<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['applied', 'waiting', 'interview', 'rejected', 'offer']);

        return [
            'user_id' => User::factory(),
            'company' => fake()->company(),
            'position' => fake()->jobTitle(),
            'city' => fake()->city(),
            'location' => fake()->randomElement(['Remote', 'Hybrid', 'On-site']),
            'status' => $status,
            'stage' => $status,
            'is_favorite' => false,
            'applied_at' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'job_url' => fake()->url(),
            'notes' => fake()->sentence(),
            'personal_score' => fake()->numberBetween(0, 10),
            'salary_offered' => fake()->randomFloat(2, 1500, 15000),
            'salary_expected' => fake()->randomFloat(2, 1500, 18000),
            'interview_date' => $status === 'interview' ? fake()->dateTimeBetween('now', '+10 days')->format('Y-m-d') : null,
            'interview_time' => $status === 'interview' ? fake()->time('H:i:s') : null,
            'interview_location' => $status === 'interview' ? fake()->city() : null,
            'interview_is_remote' => false,
            'interview_platform' => null,
            'interview_address' => null,
        ];
    }
}