<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use json;

class UpdateFlattenedAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load path mappings
        $mappingsFile = base_path('asset_mappings.json');
        if (!File::exists($mappingsFile)) {
            $this->command->error('asset_mappings.json not found');
            return;
        }
        
        $mappings = json_decode(File::get($mappingsFile), true);
        $allMappings = [];
        
        // Flatten all mappings
        foreach ($mappings as $assetType => $items) {
            foreach ($items as $mapping) {
                $allMappings[] = $mapping;
            }
        }
        
        $this->command->info("Loaded " . count($allMappings) . " path mappings");
        
        // Get all pages
        $pages = Page::all();
        $this->command->info("Found " . $pages->count() . " pages to update");
        
        foreach ($pages as $page) {
            $oldContent = $page->html;
            $newContent = $oldContent;
            
            // Replace all old paths with new paths
            foreach ($allMappings as $mapping) {
                $oldPath = $mapping[0];
                $newPath = $mapping[1];
                
                // Handle paths with query strings using # as delimiter (paths contain /)
                $pattern = '#' . preg_quote($oldPath, '#') . '(\?[^\s"\'>;]*)?#';
                $replacement = $newPath . '$1';
                $newContent = preg_replace($pattern, $replacement, $newContent);
            }
            
            // Update if changed
            if ($newContent !== $oldContent) {
                $page->update(['html' => $newContent]);
                $this->command->line("✓ Updated page: " . $page->slug);
            }
        }
        
        $this->command->info("✅ Database update complete");
    }
}
