<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateDatabaseAssetPathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = Page::all();
        $this->command->info("Updating " . $pages->count() . " pages");
        
        $patterns = [
            // CSS
            '#/css/content/plugins/([^/]+)/#' => '/css/$1/',
            '#/css/content/themes/([^/]+)/#' => '/css/$1/',
            '#/css/content/uploads/#' => '/css/uploads/',
            
            // JS
            '#/js/content/plugins/([^/]+)/#' => '/js/$1/',
            '#/js/content/themes/([^/]+)/#' => '/js/$1/',
            '#/js/content/uploads/#' => '/js/uploads/',
            
            // Images
            '#/images/content/plugins/([^/]+)/#' => '/images/$1/',
            '#/images/content/themes/([^/]+)/#' => '/images/$1/',
            '#/images/content/uploads/#' => '/images/uploads/',
        ];
        
        foreach ($pages as $page) {
            $oldHtml = $page->html;
            $newHtml = $oldHtml;
            
            foreach ($patterns as $pattern => $replacement) {
                $newHtml = preg_replace($pattern, $replacement, $newHtml);
            }
            
            if ($newHtml !== $oldHtml) {
                $page->update(['html' => $newHtml]);
                $this->command->line("✓ Updated: " . $page->slug);
            }
        }
        
        $this->command->info("✅ Database update complete");
    }
}
