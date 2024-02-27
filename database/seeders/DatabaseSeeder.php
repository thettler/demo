<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Blog\Author;
use App\Models\Blog\Category as BlogCategory;
use App\Models\Blog\Post;
use App\Models\Shop\Customer;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::raw('SET time_zone=\'+00:00\'');

        // Clear images
        Storage::deleteDirectory('public');

        // Admin
        $this->command->warn(PHP_EOL . 'Creating admin user...');
        $user = $this->withProgressBar(1, fn () => User::factory(1)->create([
            'name' => 'Demo User',
            'email' => 'admin@filamentphp.com',
        ]));
        $this->command->info('Admin user created.');

        $this->command->warn(PHP_EOL . 'Creating shop customers...');
        $customers = $this->withProgressBar(50, fn () => Customer::factory(1)
            ->has(Address::factory()->count(rand(1, 3)))
            ->create());
        $this->command->info('Shop customers created.');

        // Blog
        $this->command->warn(PHP_EOL . 'Creating blog categories...');
        $blogCategories = $this->withProgressBar(20, fn () => BlogCategory::factory(1)
            ->count(20)
            ->create());
        $this->command->info('Blog categories created.');

        $this->command->warn(PHP_EOL . 'Creating blog authors and posts...');
        $this->withProgressBar(20, fn () => Author::factory(1)
            ->has(
                Post::factory()
                    ->count(5)
                    ->afterCreating(fn (Post $post) => $post->setTranslations(
                        'title',
                        ['en' => 'ENGLISCH ' . fake()->words(30,true), 'es' => 'Spanish']
                    )->save())
                    ->state(fn (
                        array $attributes,
                        Author $author
                    ) => [
                        'blog_category_id' => $blogCategories->random(1)->first()->id,
                    ]),
                'posts'
            )

            ->create());
        $this->command->info('Blog authors and posts created.');

    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);

        $progressBar->start();

        $items = new Collection();

        foreach (range(1, $amount) as $i) {
            $items = $items->merge(
                $createCollectionOfOne()
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->command->getOutput()->writeln('');

        return $items;
    }
}
