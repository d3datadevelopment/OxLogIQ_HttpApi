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

namespace D3\LoggerExtension;

use D3\OxLogIQ\MonologLoggerFactory;
use D3\OxLogIQ_HttpApi\Interfaces\ConfigurationInterface;
use Nimbly\Shuttle\Shuttle;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ConfigurationExtension implements ConfigurationInterface
{
    /**
     * @param MonologLoggerFactory $innerConfig
     */
    public function __construct(protected ConfigurationInterface $innerConfiguration)
    {
    }

    public function hasHttpApiEndpoint(): bool
    {
        return $this->innerConfiguration->hasHttpApiEndpoint();
    }

    public function getHttpApiEndpoint(): ?string
    {
        return $this->innerConfiguration->getHttpApiEndpoint();
    }

    public function getHttpApiKey(): string
    {
        return $this->innerConfiguration->getHttpApiKey();
    }

    public function getHttpClient(): ClientInterface
    {
        // configure it according to your needs.
        return new Shuttle();
    }

    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        return new Psr17Factory();
    }

    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        return new Psr17Factory();
    }
}
