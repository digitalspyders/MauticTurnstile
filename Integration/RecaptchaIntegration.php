<?php

/*
 * @copyright   2024 Digital Spyders Inc. All rights reserved
 * @author      Digital Spyders
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecaptchaBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;

/**
 * Class RecaptchaIntegration.
 */
class RecaptchaIntegration extends AbstractIntegration
{
    const INTEGRATION_NAME = 'Recaptcha';

    public function getName()
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName()
    {
        return 'Turnstile';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [
            'site_key'   => 'mautic.integration.recaptcha.site_key',
            'secret_key' => 'mautic.integration.recaptcha.secret_key',
        ];
    }

    /**
     * @param FormBuilder|Form $builder
     * @param array            $data
     * @param string           $formArea
     */
public function appendToForm(&$builder, $data, $formArea): void
     {
         if ($formArea === 'keys') {
             $builder->add(
                 'version',
                 ChoiceType::class,
                 [
                     'choices' => [
                         'Turnstile' => 'v2',
                     ],
                     'label'      => 'mautic.recaptcha.version',
                     'label_attr' => ['class' => 'control-label'],
                     'attr'       => [
                         'class'    => 'form-control',
                     ],
                     'required'    => false,
                     'placeholder' => false,
                     'data'=> isset($data['version']) ? $data['version'] : 'v2'
                 ]
             );

             $builder->add(
                 'theme',
                 ChoiceType::class,
                 [
                     'choices' => [
                         'Auto' => 'auto',
                         'Day' => 'light',
                         'Night' => 'dark',
                     ],
                     'label'      => 'Turnstile Theme',
                     'label_attr' => ['class' => 'control-label'],
                     'attr'       => [
                         'class'    => 'form-control',
                     ],
                     'required'    => false,
                     'placeholder' => false,
                     'data'=> isset($data['theme']) ? $data['theme'] : 'auto'
                 ]
             );
         }
     }

}
