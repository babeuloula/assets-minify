<?php

/**
 * @author      BaBeuloula <info@babeuloula.fr>
 * @copyright   Copyright (c) BaBeuloula
 * @license     MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace BaBeuloula\AssetsMinify\Command;

use MatthiasMullie\Minify\Minify;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'assets:minify',
    description: 'Minify assets from Asset Mapper component',
)]
final class AssetsMinifyCommand extends Command
{
    protected const DEFAULT_EXCLUDED_PATH = ['@symfony', 'vendor'];

    /** @param string[] $excludedPaths */
    public function __construct(
        private readonly string $assetsPath,
        private readonly array $excludedPaths,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $files = (new Finder())
            ->files()
            ->notPath(array_unique(array_merge(self::DEFAULT_EXCLUDED_PATH, $this->excludedPaths)))
            ->name(['*.js', '*.css'])
            ->in($this->assetsPath)
        ;

        foreach ($files as $file) {
            $filename = str_replace($this->assetsPath, '', $file->getPathname());

            $class = strtoupper($file->getExtension());
            /** @var Minify $minify */
            $minify = new ('MatthiasMullie\Minify\\' . $class)($file->getPathname());

            try {
                $minify->minify($file->getPathname());

                $io->writeln(" <info>✔</info> $filename");
            } catch (\Throwable) {
                $io->writeln(" <error>✘</error> $filename");
            }
        }

        return Command::SUCCESS;
    }
}
