class FastBlogLoader {
    constructor() {
        this.rssUrl = 'https://helpguide-blog.blogspot.com/feeds/posts/default?alt=rss&max-results=12';
        this.proxyUrls = [
            'https://api.allorigins.win/raw?url=',
            'https://corsproxy.io/?',
            'https://thingproxy.freeboard.io/fetch/'
        ];
        this.currentProxyIndex = 0;
        this.init();
    }

    async init() {
        const posts = await this.fetchWithFallback();
        if (posts.length) {
            this.renderPosts(posts);
        } else {
            this.showError();
        }
    }

    async fetchWithFallback() {
        for (let i = 0; i < this.proxyUrls.length; i++) {
            try {
                return await this.fetchPosts(this.proxyUrls[this.currentProxyIndex]);
            } catch (e) {
                console.warn(`Proxy ${this.currentProxyIndex} failed:`, e);
                this.currentProxyIndex = (this.currentProxyIndex + 1) % this.proxyUrls.length;
            }
        }
        return [];
    }

    async fetchPosts(proxyUrl) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 8000); // 8s timeout

        const url = `${proxyUrl}${encodeURIComponent(this.rssUrl)}`;
        const response = await fetch(url, {
            signal: controller.signal,
            cache: 'default'
        });

        clearTimeout(timeoutId);

        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const rssText = await response.text();
        return this.parseRSS(rssText);
    }

    parseRSS(rssXml) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(rssXml, 'text/xml');
        if (xmlDoc.querySelector('parsererror')) throw new Error('Parse error');

        return Array.from(xmlDoc.querySelectorAll('item')).slice(0, 12).map(item => {
            const title = item.querySelector('title')?.textContent?.trim() || 'Untitled';
            const link = item.querySelector('link')?.textContent?.trim() || '#';
            const descRaw = item.querySelector('description')?.textContent || '';
            
            return {
                title,
                description: this.truncate(this.stripHtml(descRaw), 100),
                url: link
            };
        }).filter(post => post.title !== 'Untitled');
    }

    stripHtml(html) {
        const div = document.createElement('div');
        div.innerHTML = html;
        return div.textContent?.trim() || '';
    }

    truncate(text, maxLen) {
        return text.length > maxLen ? text.slice(0, maxLen).trim() + '...' : text;
    }

    renderPosts(posts) {
        const container = document.getElementById('postsGrid');
        container.innerHTML = posts.map(post => `
            <article class="post-card" role="article">
                <h3 class="post-title">${this.escapeHtml(post.title)}</h3>
                <p class="post-description">${post.description}</p>
                <a href="${post.url}" class="view-btn" target="_blank" rel="noopener noreferrer">
                    View Full Blog →
                </a>
            </article>
        `).join('');
    }

    escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    showError() {
        document.getElementById('postsGrid').innerHTML = `
            <div class="error">
                <p>Posts loading temporarily unavailable. Refresh to retry.</p>
            </div>
        `;
        document.getElementById('loading').style.display = 'none';
    }
}

// Load immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new FastBlogLoader());
} else {
    new FastBlogLoader();
}
