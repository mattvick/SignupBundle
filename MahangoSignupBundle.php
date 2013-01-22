<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mahango\Bundle\SignupBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mahango\Bundle\SignupBundle\Signup\Step\DoctrineStep;
use Mahango\Bundle\SignupBundle\Signup\Step\SecretStep;

/**
 * MahangoSignupBundle.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Marc Weistroff <marc.weistroff@sensio.com>
 */
class MahangoSignupBundle extends Bundle
{
    public function boot()
    {
        $signup = $this->container->get('mahango.signup');
        $signup->addStep(new DoctrineStep($signup->getParameters()));
        $signup->addStep(new SecretStep($signup->getParameters()));
    }
}
