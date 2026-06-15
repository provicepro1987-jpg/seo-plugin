# Advanced SEO Plugin for WordPress

A comprehensive SEO plugin for WordPress with full-featured optimization tools.

## Features

### 1. **Meta Tags Management**
- Custom meta title and description for each post/page
- Character counters with optimal length recommendations
- Open Graph tags (Facebook, LinkedIn)
- Twitter Card support
- Robots meta tags (noindex, nofollow)

### 2. **XML Sitemap Generation**
- Automatic XML sitemap generation at `/sitemap.xml`
- Includes all published posts and pages
- Includes categories and taxonomies
- Proper lastmod and priority attributes

### 3. **Structured Data (Schema.org)**
- Blog posting schema for articles
- Organization schema for site
- Author information
- Publisher information
- Featured image support
- Date published and modified

### 4. **Keywords Analysis**
- Keyword density calculation
- Keyword suggestions based on content
- Check keyword presence in headings
- Focus keyword tracking

### 5. **Readability Analysis**
- Flesch Reading Ease score
- Word count and sentence count
- Average words per sentence
- Average word length
- Grade level interpretation
- Syllable counting

### 6. **Admin Settings**
- Company information (name, logo)
- Feature toggles
- SEO analysis dashboard
- Post-by-post SEO review
- Completion status indicators

## Installation

1. Clone this repository to your WordPress plugins directory:
   ```bash
   git clone https://github.com/provicepro1987-jpg/seo-plugin.git wp-content/plugins/seo-plugin
   ```

2. Activate the plugin from WordPress admin panel

3. Go to SEO settings to configure

## Usage

### Setting Up SEO Meta Tags

1. Edit any post or page
2. Scroll to "SEO Settings" meta box
3. Add:
   - Meta Title (60 chars recommended)
   - Meta Description (160 chars recommended)
   - Focus Keyword
   - Robots settings (noindex/nofollow)

### Checking Your Sitemap

Your XML sitemap is automatically available at:
```
https://yoursite.com/sitemap.xml
```

Submit this URL to Google Search Console and Bing Webmaster Tools.

### Admin Dashboard

1. Go to SEO > General Settings
2. Enter your company information
3. Upload your company logo (600x600px recommended)
4. Toggle features on/off as needed
5. Go to SEO > Analysis to see all posts' SEO status

## Plugin Structure

```
seo-plugin/
├── seo-plugin.php              # Main plugin file
├── includes/
│   ├── class-seo-plugin.php           # Core plugin class
│   ├── class-meta-tags.php            # Meta tags handler
│   ├── class-sitemap.php              # Sitemap generator
│   ├── class-structured-data.php      # Schema.org markup
│   ├── class-admin-settings.php       # Admin settings
│   ├── class-keywords-analysis.php    # Keyword analysis
│   └── class-readability.php          # Readability checker
├── assets/
│   ├── css/
│   │   └── admin.css                  # Admin styles
│   └── js/
│       └── admin.js                   # Admin scripts
└── README.md
```

## Database Table

The plugin creates a custom table `wp_seo_meta` to store SEO metadata:

- `id` - Auto-increment ID
- `post_id` - Associated post ID
- `meta_title` - SEO meta title
- `meta_description` - SEO meta description
- `focus_keyword` - Target keyword
- `readability_score` - Readability score
- `keyword_density` - Keyword density percentage
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

## Configuration

### Options Stored

- `aseo_company_name` - Your company/site name
- `aseo_company_logo` - Company logo URL
- `aseo_enable_meta_tags` - Enable/disable meta tags
- `aseo_enable_sitemap` - Enable/disable sitemap
- `aseo_enable_structured_data` - Enable/disable structured data

## Best Practices

1. **Meta Titles**: Keep between 50-60 characters
2. **Meta Descriptions**: Keep between 150-160 characters
3. **Focus Keywords**: One primary keyword per page
4. **Readability**: Aim for a Flesch Reading Ease score of 60+
5. **Keyword Density**: 1-3% is optimal
6. **Headings**: Include focus keyword in H1 tag
7. **Images**: Always add alt text

## API Hooks

### Filters

```php
// Modify meta tags before output
apply_filters('aseo_meta_tags', $tags);

// Modify sitemap URLs
apply_filters('aseo_sitemap_urls', $urls);

// Modify schema markup
apply_filters('aseo_schema_markup', $schema);
```

### Actions

```php
// Before meta tags output
do_action('aseo_before_meta_tags');

// After meta tags output
do_action('aseo_after_meta_tags');
```

## Helper Functions

### Keyword Analysis

```php
// Analyze keyword density
$density = ASEO_Keywords_Analysis::analyze_keyword_density($content, $keyword);

// Get keyword suggestions
$suggestions = ASEO_Keywords_Analysis::get_keyword_suggestions($content);

// Check keyword in headings
$found = ASEO_Keywords_Analysis::check_keyword_in_headings($content, $keyword);
```

### Readability Analysis

```php
// Get reading ease score
$score = ASEO_Readability::calculate_reading_ease($content);

// Get full readability analysis
$analysis = ASEO_Readability::analyze_readability($content);

// Get score interpretation
$interp = ASEO_Readability::get_readability_interpretation($score);
```

## Troubleshooting

### Sitemap not generating
- Check that permalinks are enabled in WordPress Settings > Permalinks
- Clear your browser cache
- Try flushing rewrite rules: Go to Settings > Permalinks and save

### Meta tags not showing
- Ensure "Enable Meta Tags" is checked in SEO Settings
- Check that you've set meta information for the post
- Verify your theme isn't overriding the tags

### Schema markup not showing
- Ensure "Enable Structured Data" is checked
- Verify your company logo URL is set
- Use Google's Structured Data Testing Tool to validate

## Support

For issues, questions, or feature requests, please open an issue on GitHub:
https://github.com/provicepro1987-jpg/seo-plugin/issues

## License

GPL v2 or later

## Changelog

### Version 1.0.0
- Initial release
- Meta tags management
- XML sitemap generation
- Structured data support
- Keywords analysis
- Readability checker
- Admin settings panel
- SEO analysis dashboard
