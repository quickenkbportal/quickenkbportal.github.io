<?php
// index.php - BANNER REDIRECT TO YOUR WEBSITE
include 'includes/header.php';
require_once 'api/blogger-feed.php';

$posts = fetchBloggerPosts(12);
?>
<main class="main-content">
    <div class="container">
        <!-- HERO BANNER WITH EXTERNAL REDIRECT -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">Quicken Knowledge Base</h1>
                <p class="hero-subtitle">Your complete resource for downloading, installing, activating, and troubleshooting Quicken software.</p>
                
                <!-- EXTERNAL WEBSITE BUTTON -->
                <div class="hero-cta">
                    <a href="https://link72.com/?1eGz61VJppAuSRXJUaoRdMiEN2RLPAHuyVRq3HdfOmKvn" target="_blank" class="hero-btn-external">
                        🔍 Visit Quicken Now
                    </a>
                </div>
                
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo count($posts); ?></span>
                        <span class="stat-label">Live Guides</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">✅</span>
                        <span class="stat-label">Auto-Updated</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">🚀</span>
                        <span class="stat-label">Fast Loading</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- REST SAME AS BEFORE -->
        <section class="quick-start">
            <h2 class="section-title">Quick Start Guides</h2>
            <div class="quick-grid">
                <div class="quick-card download">
                    <div class="quick-icon">📥</div>
                    <h3>Download Quicken</h3>
                    <p>Official download links and verification steps</p>
                </div>
                <div class="quick-card install">
                    <div class="quick-icon">⚙️</div>
                    <h3>Install Quicken</h3>
                    <p>Windows & Mac installation guide</p>
                </div>
                <div class="quick-card activate">
                    <div class="quick-icon">✅</div>
                    <h3>Activate Quicken</h3>
                    <p>Product key activation steps</p>
                </div>
            </div>
        </section>

        <!-- Articles section unchanged -->
        <section class="articles-section">
            <div class="section-header">
                <h2 class="section-title">📚 Latest Help Guides (<?php echo count($posts); ?>)</h2>
                <a href="articles.php" class="btn btn-secondary">View All Articles</a>
            </div>
            <div class="posts-grid">
                <?php foreach (array_slice($posts, 0, 6) as $post): ?>
                <article class="post-card">
                    <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="post-description"><?php echo $post['description']; ?></p>
                    <div class="post-actions">
                        <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" class="btn btn-primary">
                            View Full Blog →
                        </a>
                    </div>
                    <small class="post-date"><?php echo date('M j, Y', $post['pubDate']); ?></small>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
