---
name: website-design
description: >-
  Design patterns for XileRO website UI components. Activates when creating panels, cards,
  news sections, download sections, or any visual UI components; or when the user mentions
  panel design, card design, hover effects, borders, or UI consistency.
---

# XileRO Website Design Patterns

## When to Apply

Activate this skill when:

- Creating new card or panel components
- Adding hover effects to interactive elements
- Building news/updates sections
- Designing download or CTA sections
- Ensuring UI consistency across the site

## Color Palette

The site uses a dark theme with amber/gold accents:

| Purpose | Color | Hex |
|---------|-------|-----|
| Background | Dark blue-gray | `#0b0d16` (clash-bg) |
| Card background | Dark gray | `#12141f` |
| Card border | Subtle gray | `#191b2e` |
| Primary accent | Amber/Gold | `#d4a84b` (xilero-gold) |
| Text primary | Light gray | `text-gray-100` |
| Text secondary | Medium gray | `text-gray-400` |
| Text muted | Dark gray | `text-gray-500` |

## Panel/Card Design

### Standard Card (block-home)

Use for static content cards without hover animation:

<code-snippet name="Standard Card" lang="html">
<div class="block-home rounded-lg p-6">
    <h3 class="text-gray-100 font-semibold">Card Title</h3>
    <p class="text-gray-400">Card content...</p>
</div>
</code-snippet>

### Interactive Card with Spinning Border (card-glow)

Use for clickable cards like news posts, featuring a spinning purple/gold gradient border on hover:

<code-snippet name="Interactive Card Structure" lang="html">
<a href="#" class="group card-glow-wrapper transition-all duration-300 hover:-translate-y-1 no-underline">
    <div class="card-glow-inner">
        <!-- Card content goes here -->
        <div class="relative h-48 overflow-hidden bg-gray-800">
            <!-- Image with overlay -->
            <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="..." alt="...">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent"></div>

            <!-- Badge positioned on image -->
            <div class="absolute top-4 right-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    Badge
                </span>
            </div>
        </div>

        <!-- Text content -->
        <div class="p-5">
            <h3 class="text-lg font-semibold text-gray-100 mb-2 group-hover:text-amber-400 transition-colors">
                Title
            </h3>
            <p class="text-gray-400 text-sm leading-relaxed line-clamp-2">
                Description...
            </p>
        </div>
    </div>
</a>
</code-snippet>

### Card Glow CSS

The spinning border effect requires these CSS classes in `resources/css/app.css`:

<code-snippet name="Card Glow CSS" lang="css">
/* Spinning gradient border card */
.card-glow-wrapper {
  position: relative;
  padding: 2px;
  border-radius: 0.5rem;
  overflow: hidden;
  background: #191b2e;
}

.card-glow-wrapper::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 150%;
  height: 150%;
  background: conic-gradient(
    #3b1578,
    #6b5020,
    #4a1772,
    #7a4a10,
    #3b1578
  );
  transform: translate(-50%, -50%);
  opacity: 0;
  transition: opacity 0.4s ease;
}

.card-glow-wrapper:hover::before {
  opacity: 1;
  animation: spin-glow 3s linear infinite;
}

.card-glow-wrapper:hover {
  box-shadow: 0 0 15px rgba(124, 58, 237, 0.1);
}

.card-glow-inner {
  background: url(../assets/block-top-bg.png) center top repeat-x;
  background-color: #12141f;
  border-radius: 0.375rem;
  overflow: hidden;
  position: relative;
  z-index: 1;
  height: 100%;
}

@keyframes spin-glow {
  from {
    transform: translate(-50%, -50%) rotate(0deg);
  }
  to {
    transform: translate(-50%, -50%) rotate(360deg);
  }
}
</code-snippet>

## Section Headers

Use consistent header patterns with optional "View all" links:

<code-snippet name="Section Header" lang="html">
<div class="flex items-end justify-between mb-10">
    <div>
        <h2 class="text-3xl font-bold text-gray-100 mb-2">Section Title</h2>
        <p class="text-gray-400">Section description.</p>
    </div>
    <a href="#" class="hidden md:inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
        View all
        <i class="fas fa-arrow-right text-sm"></i>
    </a>
</div>
</code-snippet>

## Step-Based Layouts

For multi-step flows (like download sections), use numbered headings:

<code-snippet name="Step Heading" lang="html">
<h2 class="mt-0 mb-2 text-2xl font-bold text-gray-100">
    <span class="mr-2">1.</span> Step Title
</h2>
<p class="mb-8 text-amber-500">Step description in amber.</p>
</code-snippet>

## Buttons

### Primary Button (btn-primary)

Gradient purple/blue button for main CTAs:

<code-snippet name="Primary Button" lang="html">
<a href="#" class="no-underline truncate text-gray-900 btn text-left btn-primary">
    <i class="fas fa-icon mr-3"></i>
    Button Text
</a>
</code-snippet>

### Secondary Button (btn-secondary)

Muted gradient for secondary actions:

<code-snippet name="Secondary Button" lang="html">
<a href="#" class="no-underline truncate text-gray-900 btn text-left btn-secondary">
    <i class="fas fa-icon mr-3"></i>
    Button Text
</a>
</code-snippet>

## Hover Effects

Standard hover patterns used across the site:

- **Lift effect**: `hover:-translate-y-1` with `transition-all duration-300`
- **Text color change**: `group-hover:text-amber-400 transition-colors`
- **Image zoom**: `transition-transform duration-500 group-hover:scale-105`
- **Arrow nudge**: `transition-transform group-hover:translate-x-1`

## Responsive Grid

Standard grid pattern for cards:

<code-snippet name="Responsive Card Grid" lang="html">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cards -->
</div>
</code-snippet>

## Common Pitfalls

- Forgetting `height: 100%` on `.card-glow-inner` causes border to show at bottom
- Using `block-home` instead of `card-glow-wrapper` for interactive cards
- Missing `group` class on parent when using `group-hover:` utilities
- Forgetting `no-underline` on anchor tags wrapping cards
- Not using `line-clamp-2` for consistent card heights with varying content
