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

namespace D3\OxLogIQ_HttpApi\Tests;

use D3\OxLogIQ\Release\ReleaseService;
use D3\OxLogIQ_HttpApi\Configuration;
use D3\TestingTools\Development\CanAccessRestricted;
use Generator;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionException;

#[Small]
#[CoversMethod(Configuration::class, 'hasHttpApiEndpoint')]
#[CoversMethod(Configuration::class, 'getHttpApiEndpoint')]
#[CoversMethod(Configuration::class, 'getHttpApiKey')]
class ConfigurationTest extends TestCase
{
    use CanAccessRestricted;

    /**
     * @throws ReflectionException
     */
    #[Test]
    #[DataProvider('hasHttpApiEndpointDataProvider')]
    public function testHasHttpApiEndpoint($endpoint, $isset, $expected): void
    {
        $releaseMock = $this->getMockBuilder(ReleaseService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factsMock = $this->getMockBuilder(ConfigFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getVar'])
            ->getMock();
        $factsMock->method('getVar')->with(
            $this->identicalTo(Configuration::CONFIGVAR_HTTPAPI_ENDPOINT)
        )->willReturn($endpoint);

        $sut = new Configuration($releaseMock, $factsMock);

        self::assertSame(
            $isset,
            $this->callMethod($sut, 'hasHttpApiEndpoint')
        );
        self::assertSame(
            $expected,
            $this->callMethod($sut, 'getHttpApiEndpoint')
        );
    }

    public static function hasHttpApiEndpointDataProvider(): Generator
    {
        yield 'not set' => [null, false, null];
        yield 'set' => ['endpointFixture', true, 'endpointFixture'];
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    #[DataProvider('getHttpApiKeyDataProvider')]
    public function testGetHttpApiKey($apiKey, $expected): void
    {
        $releaseMock = $this->getMockBuilder(ReleaseService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factsMock = $this->getMockBuilder(ConfigFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getVar'])
            ->getMock();
        $factsMock->method('getVar')->with(
            $this->identicalTo(Configuration::CONFIGVAR_HTTPAPI_KEY)
        )->willReturn($apiKey);

        $sut = new Configuration($releaseMock, $factsMock);

        self::assertSame(
            $expected,
            $this->callMethod($sut, 'getHttpApiKey')
        );
    }

    public static function getHttpApiKeyDataProvider(): Generator
    {
        yield 'not set' => [null, null];
        yield 'set' => ['keyFixture', 'keyFixture'];
    }
}
