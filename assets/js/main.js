class BlogFeedLoader {
    constructor() {
        this.rssUrl = 'https://helpguide-blog.blogspot.com/feeds/posts/default?alt=rss';
        this.proxyUrl = 'https://api.allorigins.win/raw?url=';
        this.init();
    }

    async init() {
        try {
            this.showLoading();
            const posts = await this.fetchPosts();
            this.renderPosts(posts);
            this.hideLoading();
        } catch (error) {
            console.error('Failed to load blog feed:', error);
            this.showError();
        }
    }

    async fetchPosts() {
        // Use CORS proxy for Blogger RSS
        const url = `${this.proxyUrl}${encodeURIComponent(this.rssUrl)}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/rss+xml, application/xml, text/xml'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const rssText = await response.text();
        const posts = this.parseRSS(rssText);
        
        // Limit to 12 most recent posts
        return posts.slice(0, 12);
    }

    parseRSS(rssXml) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(rssXml, 'text/xml');
        
        // Check for parsing errors
        const parserError = xmlDoc.querySelector('parsererror');
        if (parserError) {
            throw new Error('RSS parsing failed');
        }

        const items = xmlDoc.querySelectorAll('item');
        const posts = [];

        items.forEach(item => {
            const title = item.querySelector('title')?.textContent || 'Untitled';
            const link = item.querySelector('link')?.textContent || '#';
            const description = item.querySelector('description')?.textContent || '';
            
            // Clean and truncate description
            const cleanDesc = this.truncateDescription(description, 120);
            
            posts.push({
                title: this.sanitizeTitle(title),
                description: cleanDesc,
                url: link
            });
        });

        return posts;
    }

    truncateDescription(text, maxWords) {
        const words = text.replace(/<[^>]*>/g, '').trim().split(/\s+/);
        if (words.length <= maxWords) return text;
        
        return words.slice(0, maxWords).join(' ') + '...';
    }

    sanitizeTitle(title) {
        return title.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    }

    renderPosts(posts) {
        const container = document.getElementById('postsGrid');
        if (!posts.length) {
            container.innerHTML = '<p class="no-posts">No articles found.</p>';
            return;
        }

        container.innerHTML = posts.map(post => `
            <article class="post-card">
                <h2 class="post-title">${post.title}</h2>
                <div class="post-description">${post.description}</div>
                <a href="${post.url}" class="view-btn" target="_blank" rel="noopener noreferrer">
                    View Full Blog →
                </a>
            </article>
        `).join('');
    }

    showLoading() {
        document.getElementById('loading').style.display = 'flex';
        document.getElementById('error').style.display = 'none';
        document.getElementById('postsGrid').innerHTML = '';
    }

    hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    showError() {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('error').style.display = 'block';
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new BlogFeedLoader();
});

// Refresh feed every 5 minutes for freshness
setInterval(() => {
    if (document.visibilityState === 'visible') {
        new BlogFeedLoader();
    }
}, 5 * 60 * 1000);
