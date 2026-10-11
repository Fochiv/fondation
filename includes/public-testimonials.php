<?php
$stories = require __DIR__ . '/community-stories.php';
try {
    $publishedStories = db()->query(
        'SELECT full_name, country, city, activity, quote, photo_url, published_at FROM testimonials WHERE is_active = 1 ORDER BY published_at DESC, id DESC'
    )->fetchAll();

    if ($publishedStories) {
        $seenNames = [];
        foreach ($publishedStories as $story) {
            $seenNames[mb_strtolower(trim($story['full_name']))] = true;
        }
        foreach ($stories as $story) {
            $name = mb_strtolower(trim($story['full_name']));
            if (!isset($seenNames[$name])) {
                $publishedStories[] = $story;
                $seenNames[$name] = true;
            }
        }
        $stories = $publishedStories;
    }
} catch (Throwable) {
    // The uploaded stories remain visible while the database is unavailable.
}
return $stories;
