<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader;
use Zbox\UnifiedPushBundle\Exception\RuntimeException;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ZboxUnifiedPushExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = $this->getFileLoader($container);
        $loader->load('services.xml');
        $loader->load('unified_push.xml');
        $loader->load('controllers_api.xml');
        $loader->load('forms.xml');

        $this->processServiceConfig($config, $container);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return 'http://zbox.github.io/schema/dic/unified_push';
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    /**
     * @param ContainerBuilder $container
     * @return Loader\XmlFileLoader
     */
    public function getFileLoader($container)
    {
        return new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function processServiceConfig(array $config, ContainerBuilder $container)
    {
        $this->validateConfig($config);

        if (isset($config['APNS']['credentials'])) {
            $default = current($config['APNS']['credentials']);
            $container->setParameter('zbox.unified_push.library.apns.certificate', $default['certificate']);
            $container->setParameter('zbox.unified_push.library.apns.certificate_pass_phrase', $default['certificate_pass_phrase']);
        }

        if (isset($config['GCM']['credentials'])) {
            $container->setParameter('zbox.unified_push.library.gcm.auth_token', current($config['GCM']['credentials'])['auth_token']);
        }
    }

    /**
     * @param array $config
     */
    protected function validateConfig(array $config)
    {
        array_walk_recursive(
            $config,
            function ($value, $key) {
                if ($key == 'certificate' && !file_exists($value)) {
                    throw new  RuntimeException(
                        sprintf('Certificate file not found at %s', $value)
                    );
                }
            }
        );
    }
}
