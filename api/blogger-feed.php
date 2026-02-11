<?php
// api/blogger-feed.php
function fetchBloggerPosts($limit = 20) {
    $rssUrl = 'https://helpguide-blog.blogspot.com/feeds/posts/default?alt=rss&max-results=' . $limit;
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'Mozilla/5.0 (compatible; KnowledgeBase/1.0)'
        ]
    ]);
    
    $rssContent = @file_get_contents($rssUrl, false, $context);
    if (!$rssContent) {
        return getFallbackPosts();
    }
    
    $xml = simplexml_load_string($rssContent);
    if (!$xml || !isset($xml->channel->item)) {
        return getFallbackPosts();
    }
    
    $posts = [];
    foreach ($xml->channel->item as $item) {
        $description = (string)$item->description;
        $text = strip_tags($description);
        $words = explode(' ', $text);
        $shortText = trim(implode(' ', array_slice($words, 0, 25))) . '...';
        
        $posts[] = [
            'title' => (string)$item->title,
            'link' => (string)$item->link,
            'description' => $shortText,
            'pubDate' => strtotime($item->pubDate),
            'guid' => (string)$item->guid
        ];
    }
    
    // Sort by date (newest first)
    usort($posts, function($a, $b) {
        return $b['pubDate'] - $a['pubDate'];
    });
    
    return $posts;
}

function getFallbackPosts() {
    return [
        [
            'title' => 'Quicken Download Guide',
            'link' => 'https://helpguide-blog.blogspot.com',
            'description' => 'Learn how to safely download Quicken software. Step-by-step instructions...',
            'pubDate' => time(),
            'guid' => 'fallback-1'
        ]
    ];
}
?>
