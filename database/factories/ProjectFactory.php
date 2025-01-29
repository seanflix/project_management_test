<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3, false),
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['not_started', 'in_progress', 'completed', 'on_hold']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'team_id' => Team::factory(), // Generates a related Team
        ];
    }
}
