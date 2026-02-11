<?php
// articles.php - ARTICLES LIST PAGE
include 'includes/header.php';
require_once 'api/blogger-feed.php';

$posts = fetchBloggerPosts(50);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$totalPages = ceil(count($posts) / $perPage);
$offset = ($page - 1) * $perPage;
$pagedPosts = array_slice($posts, $offset, $perPage);
?>
<main class="main-content articles-page">
    <div class="container">
        <section class="page-hero">
            <h1 class="page-title">All Articles (<?php echo count($posts); ?>)</h1>
            <p class="page-subtitle">Complete list of help guides from HelpGuide Blog</p>
        </section>

        <div class="articles-grid posts-grid">
            <?php if (empty($pagedPosts)): ?>
            <div class="empty-state">
                <h3>No articles found</h3>
                <p>Check back soon for new content from HelpGuide Blog</p>
            </div>
            <?php else: ?>
            <?php foreach ($pagedPosts as $post): ?>
            <article class="post-card">
                <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                <p class="post-description"><?php echo $post['description']; ?></p>
                <div class="post-actions">
                    <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" class="btn btn-primary">
                        View Full Article →
                    </a>
                </div>
                <small class="post-date"><?php echo date('M j, Y', $post['pubDate']); ?></small>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="page-link">← Previous</a>
            <?php endif; ?>

            <?php 
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++): 
            ?>
            <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
