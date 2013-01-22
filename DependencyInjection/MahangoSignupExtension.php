<?php

namespace Mahango\Bundle\SignupBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * MahangoSignupExtension.
 *
 * @author Marc Weistroff <marc.weistroff@sensio.com>
 */
class MahangoSignupExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('signup.xml');
    }

    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/symfony/mahangosignup';
    }

    public function getAlias()
    {
        return 'mahango_signup';
    }
}
