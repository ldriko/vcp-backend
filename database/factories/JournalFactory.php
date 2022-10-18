<?php

namespace Database\Factories;

use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Factory<Journal>
 */
class JournalFactory extends Factory
{
    protected $model = Journal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomCodes = [Str::random(4), Str::random(4), Str::random(4)];
        $code = Str::lower(Arr::join($randomCodes, '-'));
        $title = $this->faker->sentence();
        $titleForSlug = Str::of($title)->replaceMatches('/[^A-Za-z0-9\-]/', '');

        return [
            'code' => $code,
            'slug' => fn(array $attributes) => Str::slug(
                $attributes['title'] ?? $titleForSlug . ' ' . Arr::last($randomCodes)
            ),
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'title' => $title,
            'short_desc' => $this->faker->sentence(20),
            'path' => 'sample.pdf',
            'is_published' => true,
            'published_at' => Carbon::now()->toDateTimeString()
        ];
    }
}
