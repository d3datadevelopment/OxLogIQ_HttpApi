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

namespace D3\OxLogIQ_HttpApi;

use D3\OxLogIQ\Release\ReleaseServiceInterface;
use D3\OxLogIQ_HttpApi\Interfaces\ConfigurationInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OxidEsales\Facts\Config\ConfigFile;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Configuration implements ConfigurationInterface
{
    public const CONFIGVAR_HTTPAPI_ENDPOINT = 'oxlogiq_httpApiEndpoint';
    public const CONFIGVAR_HTTPAPI_KEY      = 'oxlogiq_httpApiKey';

    protected ConfigFile $factsConfigFile;

    public function __construct(protected ReleaseServiceInterface $releaseService)
    {
        $this->factsConfigFile = new ConfigFile();
    }

    public function hasHttpApiEndpoint(): bool
    {
        $dsn = $this->getHttpApiEndpoint();

        return isset($dsn) && strlen(trim($dsn));
    }

    public function getHttpApiEndpoint(): ?string
    {
        return $this->factsConfigFile->getVar(self::CONFIGVAR_HTTPAPI_ENDPOINT);
    }

    public function getHttpApiKey(): ?string
    {
        return $this->factsConfigFile->getVar(self::CONFIGVAR_HTTPAPI_KEY);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getHttpClient(): ClientInterface
    {
        return new Client();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        return new HttpFactory();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        return new HttpFactory();
    }
}
