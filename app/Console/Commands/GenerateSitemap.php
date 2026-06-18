<?php

namespace App\Console\Commands;

use App\Models\HouseListing;
use App\Models\Newsletter;
use App\Models\Product;
use App\Models\ServiceListing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate
                            {--output=public/sitemap.xml : Output path relative to base path}
                            {--base-url= : Base URL override (defaults to APP_URL)}';

    protected $description = 'Auto-generate sitemap.xml from routes/web.php';

    protected array $excludedNames = [
        'login',
        'logout',
        'register',
        'password.*',
        'verification.*',
        'sanctum.*',
        'ignition.*',
        'debugbar.*',
        'horizon.*',
        'telescope.*',
    ];

    protected array $excludedUriPrefixes = [
        'api/',
        '_',
        'seller',
        'marketer',
        '20050619',
        'admin',
        'seller/dashboard',
        'account/',
        'checkout',
        'cart',
        'webhook',
        'livewire',
    ];

    protected array $routeRules = [
        ''                          => ['priority' => '1.0', 'changefreq' => 'daily'],
        'shop'                      => ['priority' => '0.9', 'changefreq' => 'daily'],
        'product/'                  => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'brands/'                   => ['priority' => '0.7', 'changefreq' => 'weekly'],
        'brands'                    => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'services'                  => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'properties'                => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'rider'                     => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'blog'                      => ['priority' => '0.7', 'changefreq' => 'weekly'],
        'contact'                   => ['priority' => '0.6', 'changefreq' => 'monthly'],
        'seller/register'           => ['priority' => '0.7', 'changefreq' => 'monthly'],
        'seller/'                   => ['priority' => '0.5', 'changefreq' => 'monthly'],
        'legal/'                    => ['priority' => '0.4', 'changefreq' => 'monthly'],
    ];

    protected array $defaultRule = ['priority' => '0.5', 'changefreq' => 'monthly'];

    public function handle(): int
    {
        $baseUrl    = rtrim($this->option('base-url') ?: config('app.url'), '/');
        $outputPath = base_path($this->option('output'));

        $this->info("🗺  Generating sitemap for <{$baseUrl}>");

        $urls = $this->collectUrls($baseUrl);

        $productUrls    = $this->collectProductUrls($baseUrl);
        $serviceUrls    = $this->collectServiceUrls($baseUrl);
        $propertyUrls   = $this->collectPropertyUrls($baseUrl);
        $newsletterUrls = $this->collectNewsletterUrls($baseUrl);

        $urls = array_merge($urls, $productUrls, $serviceUrls, $propertyUrls, $newsletterUrls);

        if (empty($urls)) {
            $this->warn('No eligible routes found. Check your exclusion rules.');
            return self::FAILURE;
        }

        $xml = $this->buildXml($urls);
        $this->writeFile($outputPath, $xml);

        $this->info("✅ Sitemap written to: {$outputPath}");
        $this->info('   Static routes : ' . (count($urls) - count($productUrls) - count($serviceUrls) - count($propertyUrls) - count($newsletterUrls)));
        $this->info('   Products      : ' . count($productUrls));
        $this->info('   Services      : ' . count($serviceUrls));
        $this->info('   Properties    : ' . count($propertyUrls));
        $this->info('   Blog posts    : ' . count($newsletterUrls));
        $this->info('   Total URLs    : ' . count($urls));

        return self::SUCCESS;
    }

    protected function collectUrls(string $baseUrl): array
    {
        $routes = Route::getRoutes();
        $urls   = [];
        $seen   = [];

        foreach ($routes as $route) {
            if (! in_array('GET', $route->methods())) {
                continue;
            }

            $uri  = $route->uri();
            $name = $route->getName() ?? '';

            if (Str::contains($uri, '{')) {
                continue;
            }

            if ($this->isExcludedName($name)) {
                continue;
            }

            if ($this->isExcludedUri($uri)) {
                continue;
            }

            $uri = ltrim($uri, '/');

            if (isset($seen[$uri])) {
                continue;
            }
            $seen[$uri] = true;

            $rule = $this->matchRule($uri);

            $urls[] = [
                'loc'        => $baseUrl . '/' . $uri,
                'changefreq' => $rule['changefreq'],
                'priority'   => $rule['priority'],
            ];
        }

        usort($urls, fn($a, $b) =>
            $b['priority'] <=> $a['priority'] ?: $a['loc'] <=> $b['loc']
        );

        return $urls;
    }

    protected function collectProductUrls(string $baseUrl): array
    {
        $urls = [];

        $this->info('   Scanning products table…');

        Product::query()
            ->where('status', 'approved')
            ->whereNotNull('slug')
            ->withoutTrashed()
            ->select(['slug', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->chunk(500, function ($products) use ($baseUrl, &$urls) {
                foreach ($products as $product) {
                    $slug = trim($product->slug);

                    if (empty($slug)) {
                        continue;
                    }

                    $urls[] = [
                        'loc'        => $baseUrl . '/product/' . rawurlencode($slug),
                        'lastmod'    => $product->updated_at
                                            ? $product->updated_at->toDateString()
                                            : now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];
                }
            });

        return $urls;
    }

    protected function collectServiceUrls(string $baseUrl): array
    {
        $urls = [];

        $this->info('   Scanning service_listings table…');

        ServiceListing::query()
            ->where('status', 'approved')
            ->whereNotNull('slug')
            ->withoutTrashed()
            ->select(['slug', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->chunk(500, function ($services) use ($baseUrl, &$urls) {
                foreach ($services as $service) {
                    $slug = trim($service->slug);

                    if (empty($slug)) {
                        continue;
                    }

                    $urls[] = [
                        'loc'        => $baseUrl . '/services/' . rawurlencode($slug),
                        'lastmod'    => $service->updated_at
                                            ? $service->updated_at->toDateString()
                                            : now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];
                }
            });

        return $urls;
    }

    protected function collectPropertyUrls(string $baseUrl): array
    {
        $urls = [];

        $this->info('   Scanning house_listings table…');

        HouseListing::query()
            ->where('status', 'approved')
            ->whereNotNull('slug')
            ->withoutTrashed()
            ->select(['slug', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->chunk(500, function ($properties) use ($baseUrl, &$urls) {
                foreach ($properties as $property) {
                    $slug = trim($property->slug);

                    if (empty($slug)) {
                        continue;
                    }

                    $urls[] = [
                        'loc'        => $baseUrl . '/properties/' . rawurlencode($slug),
                        'lastmod'    => $property->updated_at
                                            ? $property->updated_at->toDateString()
                                            : now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];
                }
            });

        return $urls;
    }

    protected function collectNewsletterUrls(string $baseUrl): array
    {
        $urls = [];

        $this->info('   Scanning newsletters table…');

        Newsletter::query()
            ->where('status', 'sent')
            ->select(['id', 'updated_at', 'sent_at'])
            ->orderBy('sent_at', 'desc')
            ->chunk(500, function ($newsletters) use ($baseUrl, &$urls) {
                foreach ($newsletters as $newsletter) {
                    $urls[] = [
                        'loc'        => $baseUrl . '/blog/' . $newsletter->id,
                        'lastmod'    => $newsletter->updated_at
                                            ? $newsletter->updated_at->toDateString()
                                            : now()->toDateString(),
                        'changefreq' => 'monthly',
                        'priority'   => '0.6',
                    ];
                }
            });

        return $urls;
    }

    protected function isExcludedName(string $name): bool
    {
        foreach ($this->excludedNames as $pattern) {
            if (fnmatch($pattern, $name)) {
                return true;
            }
        }
        return false;
    }

    protected function isExcludedUri(string $uri): bool
    {
        $uri = ltrim($uri, '/');
        foreach ($this->excludedUriPrefixes as $prefix) {
            if (Str::startsWith($uri, $prefix) || $uri === rtrim($prefix, '/')) {
                return true;
            }
        }
        return false;
    }

    protected function matchRule(string $uri): array
    {
        foreach ($this->routeRules as $prefix => $rule) {
            if ($prefix === '' && $uri === '') {
                return $rule;
            }
            if ($prefix !== '' && Str::startsWith($uri, $prefix)) {
                return $rule;
            }
        }
        return $this->defaultRule;
    }

    protected function buildXml(array $urls): string
    {
        $now   = now()->toDateString();
        $items = '';

        foreach ($urls as $url) {
            $loc     = htmlspecialchars($url['loc'], ENT_XML1);
            $lastmod = $url['lastmod'] ?? $now;
            $items .= <<<XML

    <url>
        <loc>{$loc}</loc>
        <lastmod>{$lastmod}</lastmod>
        <changefreq>{$url['changefreq']}</changefreq>
        <priority>{$url['priority']}</priority>
    </url>
XML;
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
{$items}
</urlset>
XML;
    }

    protected function writeFile(string $path, string $content): void
    {
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, $content);
    }
}