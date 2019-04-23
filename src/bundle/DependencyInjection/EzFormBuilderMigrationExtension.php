<?php
/**
 * NovaeZFormBuilderMigrationBundle.
 *
 * @package   NovaeZFormBuilderMigrationBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZFormBuilderMigrationBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\EzFormBuilderMigrationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class EzFormBuilderMigrationExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var string
     */
    protected $configDirPath = __DIR__.'/../Resources/config';

    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('field_converters.yml');
        $loader->load('services.yml');
        $loader->load('validator_converters.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependBlocks($container);
    }

    protected function prependBlocks(ContainerBuilder $container): void
    {
        $configFile = __DIR__.'/../Resources/config/blocks.yml';
        $config     = Yaml::parse((string) file_get_contents($configFile));
        $container->prependExtensionConfig('ezplatform_page_fieldtype', $config);
        $container->addResource(new FileResource($configFile));
    }
}
