<?php

/*
 * (c) Alexander Zhukov <zbox82@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zbox\UnifiedPushBundle\DependencyInjection;

use Zbox\UnifiedPush\NotificationService\NotificationServices;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zbox_unified_push');

        foreach ($this->getNotificationServices() as $service) {
            $rootNode
                ->children()
                    ->arrayNode($service)
                        ->append(
                            $this->getCredentialConfigByService($service)
                        )
                    ->end()
                ->end();
        }

        return $treeBuilder;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function getCertificateCredentials()
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root('credentials');
        $node
            ->useAttributeAsKey('name')
            ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('certificate')
                            ->info('Path to certificate.')
                        ->end()
                        ->scalarNode('certificate_pass_phrase')
                            ->defaultValue('certificate_pass_phrase')
                        ->end()
                        ->booleanNode('is_dev')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function getAuthTokenCredentials()
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root('credentials');
        $node
            ->useAttributeAsKey('name')
            ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('auth_token')
                    ->end()
                    ->booleanNode('is_dev')
                        ->defaultFalse()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return array
     */
    protected function getNotificationServices()
    {
        return NotificationServices::getAvailableServices();
    }

    /**
     * @param string $service
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    protected function getCredentialConfigByService($service)
    {
        $credentialsType = NotificationServices::getCredentialsTypeByService($service);

        switch ($credentialsType) {
            case NotificationServices::CREDENTIALS_CERTIFICATE:
                return $this->getCertificateCredentials();
            case NotificationServices::CREDENTIALS_AUTH_TOKEN:
                return $this->getAuthTokenCredentials();
        }
    }
}
