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

namespace D3\OxLogIQ_HttpApi\Tests\Providers;

use D3\LoggerFactory\LoggerFactory;
use D3\LoggerFactory\Options\MailLoggerHandlerOption;
use D3\LoggerFactory\Options\OtherLoggerHandlerOption;
use D3\OxLogIQ\MonologConfiguration;
use D3\OxLogIQ\Providers\MailHandlerProvider;
use D3\OxLogIQ_HttpApi\Configuration;
use D3\OxLogIQ_HttpApi\Handlers\HttpApiHandler;
use D3\OxLogIQ_HttpApi\Providers\HttpApiHandlerProvider;
use D3\TestingTools\Development\CanAccessRestricted;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionException;

#[Small]
#[CoversMethod(HttpApiHandlerProvider::class, 'register')]
class HttpApiHandlerProviderTest extends TestCase
{
    use CanAccessRestricted;

    /**
     * @throws ReflectionException
     */
    #[Test]
    #[DataProvider('registerDataProvider')]
    public function testRegister(bool $hasApiEndpoint, int $invocation): void
    {
        $monologConfigurationMock = $this->getMockBuilder(MonologConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasHttpApiEndpoint', 'getHttpApiEndpoint', 'getHttpApiKey', 'getHttpClient', 'getHttpRequestFactory', 'getHttpStreamFactory'])
            ->getMock();
        $configurationMock->method('hasHttpApiEndpoint')->willReturn($hasApiEndpoint);
        $configurationMock->expects(self::exactly($invocation))->method('getHttpApiEndpoint');
        $configurationMock->expects(self::exactly($invocation))->method('getHttpApiKey');
        $configurationMock->expects(self::exactly($invocation))->method('getHttpClient')->willReturn(new Client());
        $configurationMock->expects(self::exactly($invocation))->method('getHttpRequestFactory')->willReturn(new HttpFactory());
        $configurationMock->expects(self::exactly($invocation))->method('getHttpStreamFactory')->willReturn(new HttpFactory());

        $sut = oxNew(HttpApiHandlerProvider::class, $monologConfigurationMock, $configurationMock);

        $mailLoggerHandlerOptionMock = $this->getMockBuilder(OtherLoggerHandlerOption::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setBuffering'])
            ->getMock();
        $mailLoggerHandlerOptionMock->expects(self::exactly($invocation))->method('setBuffering');

        $factoryMock = $this->getMockBuilder(LoggerFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addOtherHandler'])
            ->getMock();
        $factoryMock->expects(self::exactly($invocation))->method('addOtherHandler')
            ->willReturn($mailLoggerHandlerOptionMock);

        $this->callMethod($sut, 'register', [$factoryMock]);
    }

    public static function registerDataProvider(): Generator
    {
        yield 'no enpoint' => [false, 0];
        yield 'given endpoint' => [true, 1];
    }
}
