<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtnesion\Context\RawMinkContaxt;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Description of MinkOverrideContext
 *
 */
class MinkOverrideContext extends MinkContext
{

    protected $baseUrl;

    public function __construct($base_url) {
        $this->setMinkParameter('base_url', $base_url);
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        foreach ($environment->getContexts() as $context) {
            if ($context instanceof RawMinkContext) {
                $context->setMinkParameter('base_url', $this->baseUrl);
            }
        }
    }
}
