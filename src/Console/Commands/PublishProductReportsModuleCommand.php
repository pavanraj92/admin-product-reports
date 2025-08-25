<?php

namespace admin\product_reports\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishProductReportsModuleCommand extends Command
{
    protected $signature = 'product_reports:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish Product Reports module files with proper namespace transformation';

    public function handle()
    {
        $this->info('Publishing Product Reports module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/Reports');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();
        
        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'product_report',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('Reports module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/product_reports/src

        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/ReportManagerController.php' => base_path('Modules/Reports/app/Http/Controllers/Admin/ReportManagerController.php'),

            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/Reports/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                
                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);
                
                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\product_reports\\Controllers;' => 'namespace Modules\\Reports\\app\\Http\\Controllers\\Admin;',

            // Use statements transformations
            'use admin\\product_reports\\Controllers\\' => 'use Modules\\Reports\\app\\Http\\Controllers\\Admin\\',

            // Class references in routes
            'admin\\product_reports\\Controllers\\ReportManagerController' => 'Modules\\Reports\\app\\Http\\Controllers\\Admin\\ReportManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = str_replace(
                'use admin\\products\\Models\\Order;',
                'use Modules\\Products\\app\\Models\\Order;',
                $content
            );
            $content = str_replace(
                'use admin\\products\\Models\\Transaction;',
                'use Modules\\Products\\app\\Models\\Transaction;',
                $content
            );
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\Reports\\'])) {
            $composer['autoload']['psr-4']['Modules\\Reports\\'] = 'Modules/Reports/app/';

            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}
