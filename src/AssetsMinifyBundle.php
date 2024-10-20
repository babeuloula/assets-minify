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

namespace BaBeuloula\AssetsMinify;

use BaBeuloula\AssetsMinify\Command\AssetsMinifyCommand;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AssetsMinifyBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        /** @var ArrayNodeDefinition $treeBuilder */
        $treeBuilder = $definition->rootNode();

        $treeBuilder
            ->children()
                ->scalarNode('assets_path')->isRequired()->end()
                ->arrayNode('excluded_paths')->end()
            ->end()
        ;
    }

    /** @param array<string, mixed> $config */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $container->services()
            ->get(AssetsMinifyCommand::class)
                ->arg('$assetsPath', $config['assets_path'])
                ->arg('$excludedPaths', $config['excluded_paths'] ?? [])
        ;
    }
}
