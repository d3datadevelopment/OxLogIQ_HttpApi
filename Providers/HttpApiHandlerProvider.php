<?php

/**
 * Copyright (c) D3 Data Development (Inh. Thomas Dartsch)
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * https://www.d3data.de
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author    D3 Data Development - Daniel Seifert <info@shopmodule.com>
 * @link      https://www.oxidmodule.com
 */

declare(strict_types=1);

namespace D3\OxLogIQ_HttpApi\Providers;

use D3\LoggerFactory\LoggerFactory;
use D3\OxLogIQ\Interfaces\ProviderInterface;
use D3\OxLogIQ_HttpApi\Handlers\HttpApiHandler;
use D3\OxLogIQ_HttpApi\Interfaces\ConfigurationInterface;
use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Configuration\MonologConfigurationInterface;

class HttpApiHandlerProvider implements ProviderInterface
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        protected MonologConfigurationInterface $monologConfiguration,
        protected ConfigurationInterface $configuration
    ) {
    }

    public function register(LoggerFactory $factory): void
    {
        if ($this->configuration->hasHttpApiEndpoint()) {
            $factory->addOtherHandler(
                (new HttpApiHandler(
                    $this->configuration->getHttpApiEndpoint(),
                    $this->configuration->getHttpApiKey(),
                    Logger::toMonologLevel($this->monologConfiguration->getLogLevel()),
                    $this->configuration->getHttpClient(),
                    $this->configuration->getHttpRequestFactory(),
                    $this->configuration->getHttpStreamFactory(),
                ))
            )->setBuffering();
        }
    }
}
