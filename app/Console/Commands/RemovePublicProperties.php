<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RemovePublicProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-public-properties {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove public properties from Eloquent models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelsPath = app_path('Models');
        $files = File::glob($modelsPath . '/*.php');
        
        $this->info('Scanning models for public properties...');
        $this->info('Found ' . count($files) . ' model files');
        
        $totalRemoved = 0;
        $filesProcessed = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            $content = File::get($file);
            
            $this->line("Checking: {$filename}");
            
            // Debug: show if we find any public properties
            $hasPublicProps = preg_match('/public\s+(?:int|string|float|bool|array|\?[a-zA-Z_][a-zA-Z0-9_]*)\s+\$[a-zA-Z_][a-zA-Z0-9_]*\s*;/', $content);
            $this->line("  Has public properties: " . ($hasPublicProps ? 'YES' : 'NO'));
            
            // Skip if no public properties found
            if (!$hasPublicProps) {
                continue;
            }
            
            $filesProcessed++;
            $this->line("Processing: {$filename}");
            
            // Find all public properties
            preg_match_all('/public\s+(?:int|string|float|bool|array|\?[a-zA-Z_][a-zA-Z0-9_]*)\s+\$[a-zA-Z_][a-zA-Z0-9_]*\s*;/', $content, $matches);
            
            if (empty($matches[0])) {
                continue;
            }
            
            $propertiesToRemove = $matches[0];
            $this->warn("Found " . count($propertiesToRemove) . " public properties:");
            
            foreach ($propertiesToRemove as $property) {
                $this->line("  - " . trim($property));
            }
            
            if ($this->option('dry-run')) {
                $this->info("Would remove " . count($propertiesToRemove) . " properties from {$filename}");
                $totalRemoved += count($propertiesToRemove);
                continue;
            }
            
            // Remove public properties
            $newContent = preg_replace('/public\s+(?:int|string|float|bool|array|\?[a-zA-Z_][a-zA-Z0-9_]*)\s+\$[a-zA-Z_][a-zA-Z0-9_]*\s*;\s*\n?/', '', $content);
            
            // Clean up multiple empty lines
            $newContent = preg_replace('/\n\s*\n\s*\n/', "\n\n", $newContent);
            
            if ($content !== $newContent) {
                File::put($file, $newContent);
                $this->info("Removed " . count($propertiesToRemove) . " properties from {$filename}");
                $totalRemoved += count($propertiesToRemove);
            }
        }
        
        if ($filesProcessed === 0) {
            $this->info('No models with public properties found.');
            return;
        }
        
        if ($this->option('dry-run')) {
            $this->info("Dry run completed. Would remove {$totalRemoved} public properties from {$filesProcessed} files.");
        } else {
            $this->info("Completed! Removed {$totalRemoved} public properties from {$filesProcessed} files.");
        }
    }
}
