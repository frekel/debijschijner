<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->select(['id', 'content_blocks'])
            ->orderBy('id')
            ->each(function (object $page): void {
                $blocks = json_decode($page->content_blocks ?? '[]', true);

                if (! is_array($blocks)) {
                    return;
                }

                $normalizedBlocks = [];
                $reviewsPlaceholderAdded = false;

                foreach ($blocks as $block) {
                    if (($block['type'] ?? null) !== 'review') {
                        $normalizedBlocks[] = $block;
                        $reviewsPlaceholderAdded = false;

                        continue;
                    }

                    $data = is_array($block['data'] ?? null) ? $block['data'] : [];

                    DB::table('posts')->insert([
                        'type' => 'reviewer',
                        'name' => $data['reviewer_name'] ?? null,
                        'title' => $data['title'] ?? null,
                        'text' => $data['text'] ?? null,
                        'image' => $data['image'] ?? null,
                        'button_text' => $data['button_text'] ?? null,
                        'sort_order' => DB::table('posts')->where('type', 'reviewer')->max('sort_order') + 1,
                        'is_published' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (! $reviewsPlaceholderAdded) {
                        $normalizedBlocks[] = ['type' => 'reviews', 'data' => []];
                        $reviewsPlaceholderAdded = true;
                    }
                }

                DB::table('pages')->where('id', $page->id)->update([
                    'content_blocks' => json_encode($normalizedBlocks),
                ]);
            });
    }

    public function down(): void
    {
        // Reviewer posts are independent content after this migration. Deleting or
        // copying them back into pages during rollback would risk content loss.
    }
};
