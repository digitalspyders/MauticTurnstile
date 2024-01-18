<?php

/*
 * @copyright   2024 Digital Spyders Inc. All rights reserved
 * @author      Digital Spyders
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecaptchaBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\FormBundle\Entity\Field;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecaptchaBundle\Integration\RecaptchaIntegration;
use Mautic\PluginBundle\Integration\AbstractIntegration;

class RecaptchaClient
{
    const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * FormSubscriber constructor.
     *
     * @param IntegrationHelper $integrationHelper
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {
        $integrationObject = $integrationHelper->getIntegrationObject(RecaptchaIntegration::INTEGRATION_NAME);

        if ($integrationObject instanceof AbstractIntegration) {
            $keys            = $integrationObject->getKeys();
            $this->siteKey   = isset($keys['site_key']) ? $keys['site_key'] : null;
            $this->secretKey = isset($keys['secret_key']) ? $keys['secret_key'] : null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [];
    }


    /**
     * @param string $response
     * @param Field  $field
     *
     * @return bool
     */
    public function verify($response, Field $field)
    {
        $client   = new GuzzleClient(['timeout' => 10]);
        $response = $client->post(
            self::VERIFY_URL,
            [
                'form_params' => [
                    'secret'   => $this->secretKey,
                    'response' => $response,
                ],
            ]
        );


        $response = json_decode($response->getBody(), true);
        if (array_key_exists('success', $response) && $response['success'] === true) {

            $score = (float) ArrayHelper::getValue('score', $response);
            $scoreValidation = ArrayHelper::getValue('scoreValidation', $field->getProperties());
            $minScore = (float)  ArrayHelper::getValue('minScore', $field->getProperties());
            if ($score && $scoreValidation && $minScore > $score) {
                return false;
            }

            return true;
        }


        return false;
    }
}
