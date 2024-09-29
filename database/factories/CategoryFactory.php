<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word; // Generate a unique random word for the name
        $slug = $this->generateUniqueSlug($name); // Generate a unique slug

        return [
            'name' => $name,
            'status' => rand(0, 1),
            'slug' => $slug,
        ];
    }

    /**
     * Generate a unique slug for the category.
     *
     * @param string $name
     * @return string
     */
    protected function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = 1;

        // Check if the slug already exists and append a number if necessary
        while (Category::where('slug', $slug)->exists()) {
            $slug = Str::slug($name . '-' . $count);
            $count++;
        }

        return $slug;
    }
}
