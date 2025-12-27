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

namespace D3\OxLogIQ_HttpApi\Tests\Providers\Handlers;

use D3\LoggerFactory\LoggerFactory;
use D3\LoggerFactory\Options\OtherLoggerHandlerOption;
use D3\OxLogIQ\MonologConfiguration;
use D3\OxLogIQ_HttpApi\Configuration;
use D3\OxLogIQ_HttpApi\Providers\Handlers\HandlerProvider;
use D3\TestingTools\Development\CanAccessRestricted;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionException;

#[Small]
#[CoversMethod(HandlerProvider::class, 'isActive')]
#[CoversMethod(HandlerProvider::class, 'provide')]
class HandlerProviderTest extends TestCase
{
    use CanAccessRestricted;

    /**
     * @throws ReflectionException
     * @dataProvider isActiveDataProvider
     */
    #[DataProvider('isActiveDataProvider')]
    public function testIsActive($hasHttpEndpoint): void
    {
        $monologConfigurationMock = $this->getMockBuilder(MonologConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasHttpApiEndpoint'])
            ->getMock();
        $configurationMock->expects(self::once())->method('hasHttpApiEndpoint')->willReturn($hasHttpEndpoint);

        $sut = oxNew(HandlerProvider::class, $monologConfigurationMock, $configurationMock);

        $this->assertSame(
            $hasHttpEndpoint,
            $this->callMethod(
                $sut,
                'isActive',
            )
        );
    }

    public static function isActiveDataProvider(): Generator
    {
        yield [false];
        yield [true];
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function testRegister(): void
    {
        $monologConfigurationMock = $this->getMockBuilder(MonologConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHttpApiEndpoint', 'getHttpApiKey', 'getHttpClient', 'getHttpRequestFactory', 'getHttpStreamFactory'])
            ->getMock();
        $configurationMock->expects(self::once())->method('getHttpApiEndpoint');
        $configurationMock->expects(self::once())->method('getHttpApiKey');
        $configurationMock->expects(self::once())->method('getHttpClient')->willReturn(new Client());
        $configurationMock->expects(self::once())->method('getHttpRequestFactory')->willReturn(new HttpFactory());
        $configurationMock->expects(self::once())->method('getHttpStreamFactory')->willReturn(new HttpFactory());

        $sut = oxNew(HandlerProvider::class, $monologConfigurationMock, $configurationMock);

        $mailLoggerHandlerOptionMock = $this->getMockBuilder(OtherLoggerHandlerOption::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setBuffering'])
            ->getMock();
        $mailLoggerHandlerOptionMock->expects(self::once())->method('setBuffering');

        $factoryMock = $this->getMockBuilder(LoggerFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addOtherHandler'])
            ->getMock();
        $factoryMock->expects(self::once())->method('addOtherHandler')
            ->willReturn($mailLoggerHandlerOptionMock);

        $this->callMethod($sut, 'provide', [$factoryMock]);
    }
}
