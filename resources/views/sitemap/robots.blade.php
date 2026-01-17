# XileRO Robots.txt
# https://xilero.net

User-agent: *
Allow: /

# Sitemap
Sitemap: {{ url('/sitemap.xml') }}

# Disallow admin and internal paths
Disallow: /admin
Disallow: /app
Disallow: /horizon
Disallow: /livewire
Disallow: /api

# Disallow authentication pages from indexing (optional)
Disallow: /logout
Disallow: /password

# Crawl-delay for polite crawling
Crawl-delay: 1

# Specific rules for common bots
User-agent: Googlebot
Allow: /
Crawl-delay: 0

User-agent: Bingbot
Allow: /
Crawl-delay: 1

# Block bad bots
User-agent: AhrefsBot
Disallow: /

User-agent: SemrushBot
Disallow: /

User-agent: MJ12bot
Disallow: /
