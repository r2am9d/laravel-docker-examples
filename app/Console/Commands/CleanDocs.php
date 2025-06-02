<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final class CleanDocs extends Command
{
    protected $signature = 'make:clean-docs';

    protected $description = 'Removes unnecessary docs annotations from models';

    public function handle(): int
    {
        $directory = app_path('Models');

        $files = new RegexIterator(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)),
            '/^.+\.php$/i',
            RegexIterator::GET_MATCH
        );

        foreach ($files as $file) {
            $filePath = $file[0];
            $contents = file_get_contents($filePath);
            $originalContents = $contents;

            // 1. Remove the "Created by Reliese Model" comment block
            $contents = preg_replace(
                '#/\*\*\s*\n\s*\*\s*Created by Reliese Model\.\s*\n\s*\*/\s*\n+#',
                '',
                $contents,
                1
            );

            // 2. Clean the class-level docblock ABOVE the final class line
            $contents = preg_replace_callback(
                '#(/\*\*(?:(?!\*/).)*\*/)(\s*final\s+class\s+\w+)#is',
                function (array $matches): string {
                    $docblock = $matches[1];
                    $classDeclaration = $matches[2];

                    // Clean lines inside docblock
                    $lines = explode("\n", $docblock);
                    $cleaned = [];

                    foreach ($lines as $line) {
                        if (
                            preg_match('/@property\b/', $line) ||
                            preg_match('/@property-read\b/', $line) ||
                            preg_match('/^\s*\*\s*Class\s+\w+/i', $line) ||
                            preg_match('/^\s*\*\s*$/', $line)
                        ) {
                            $cleaned[] = $line;
                        }
                    }

                    // Remove trailing empty `*` lines
                    while (end($cleaned) !== false && preg_match('/^\s*\*\s*$/', end($cleaned))) {
                        array_pop($cleaned);
                    }

                    return "/**\n".implode("\n", $cleaned)."\n */".$classDeclaration;
                },
                (string) $contents
            );

            if ($contents !== $originalContents) {
                file_put_contents($filePath, $contents);
                $this->info('Cleaned: '.$filePath);
            }
        }

        $this->info('Finished cleaning model docs annotations.');

        return 0;
    }
}
