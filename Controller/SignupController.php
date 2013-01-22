<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mahango\Bundle\SignupBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * SignupController.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SignupController extends ContainerAware
{
    /**
     * @return Response A Response instance
     */
    public function stepAction($index = 0)
    {
        $signup = $this->container->get('mahango.signup');

        $step = $signup->getStep($index);
        $form = $this->container->get('form.factory')->create($step->getFormType(), $step);

        $request = $this->container->get('request');
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {

                $signup->mergeParameters($step->update($form->getData()));
                $signup->write();

                $index++;

                if ($index < $signup->getStepCount()) {
                    return new RedirectResponse($this->container->get('router')->generate('_signup_step', array('index' => $index)));
                }

                return new RedirectResponse($this->container->get('router')->generate('_signup_final'));
            }
        }

        return $this->container->get('templating')->renderResponse($step->getTemplate(), array(
            'form'    => $form->createView(),
            'index'   => $index,
            'count'   => $signup->getStepCount(),
            'version' => $this->getVersion(),
        ));
    }

    public function checkAction()
    {
        $signup = $this->container->get('mahango.signup');

        // Trying to get as much requirements as possible
        $majors = $signup->getRequirements();
        $minors = $signup->getOptionalSettings();

        $url = $this->container->get('router')->generate('_signup_step', array('index' => 0));

        if (empty($majors) && empty($minors)) {
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('MahangoSignupBundle::Signup/check.html.twig', array(
            'majors'  => $majors,
            'minors'  => $minors,
            'url'     => $url,
            'version' => $this->getVersion(),
        ));
    }

    public function finalAction()
    {
        $signup = $this->container->get('mahango.signup');
        $signup->clean();

        try {
            $welcomeUrl = $this->container->get('router')->generate('_welcome');
        } catch (\Exception $e) {
            $welcomeUrl = null;
        }

        return $this->container->get('templating')->renderResponse('MahangoSignupBundle::Signup/final.html.twig', array(
            'welcome_url' => $welcomeUrl,
            'parameters'  => $signup->render(),
            'yml_path'    => $this->container->getParameter('kernel.root_dir').'/config/parameters.yml',
            'is_writable' => $signup->isFileWritable(),
            'version'     => $this->getVersion(),
        ));
    }

    public function getVersion()
    {
        $kernel = $this->container->get('kernel');

        return $kernel::VERSION;
    }
}
