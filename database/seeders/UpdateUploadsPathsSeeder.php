<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateUploadsPathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = Page::all();
        $this->command->info("Updating " . $pages->count() . " pages");
        
        $updated = 0;
        foreach ($pages as $page) {
            $oldHtml = $page->html;
            $newHtml = preg_replace(
                '#/wp-content/uploads/#',
                '/images/uploads/',
                $oldHtml
            );
            
            if ($newHtml !== $oldHtml) {
                $page->update(['html' => $newHtml]);
                $this->command->line("✓ Updated: " . $page->slug);
                $updated++;
            }
        }
        
        $this->command->info("✅ Database update complete - $updated pages updated");
    }
}
