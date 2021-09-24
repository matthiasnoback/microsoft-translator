<?php

namespace MatthiasNoback\Tests\Buzz\Client;

use Buzz\Client\BuzzClientInterface;
use PHPUnit\Framework\TestCase;
use MatthiasNoback\Buzz\Client\CachedClient;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CachedClientTest extends TestCase
{
    public function testCacheMiss()
    {
        $request = $this->createMockRequest();
        $response = $this->createMockResponse();

        $cacheKey1 = $cacheKey2 = null;
        $cache = $this->createMockCache();
        // the cache will not contain a response for the current request
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->will($this->returnCallback(function($id) use (&$cacheKey1) {
                $cacheKey1 = $id;

                return false;
            }));

        $client = $this->createMockClient();
        // the client will be used to fetch the response
        $client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->will($this->returnValue($response));

        // the serialized response will be stored in the cache
        $cacheItem = $this->createMockCacheItem();
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with($this->isType('string'))
            ->will($this->returnCallback(function($id) use (&$cacheKey2, $cacheItem) {
                $cacheKey2 = $id;
                return $cacheItem;
            }));

        $cache
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(CacheItemInterface::class));

        $cachedClient = new CachedClient($client, $cache);
        $cachedClient->sendRequest($request);

        $this->assertSame(1, $cachedClient->getMisses());
        $this->assertSame(0, $cachedClient->getHits());
        $this->assertSame($cacheKey1, $cacheKey2);
    }

    public function testCacheHits()
    {
        $request = $this->createMockRequest();
        $response = $this->createMockResponse();
        $cacheKey1 = $cacheKey2 = null;
        $cache = $this->createMockCache();
        // the cache contains a response for the current request

        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->will($this->returnCallback(function($id) use (&$cacheKey1) {
            $cacheKey1 = $id;

            return true;
        }));

        $client = $this->createMockClient();
        // the client should not be called
        $client
            ->expects($this->never())
            ->method('sendRequest');

        $cacheItem = $this->createMockCacheItem();
        $cacheItem
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(serialize($response)));

        // the serialized response will be retrieved from the cache
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnCallback(function($id) use (&$cacheKey2, $cacheItem) {
            $cacheKey2 = $id;

            return $cacheItem;
        }));

        $cachedClient = new CachedClient($client, $cache);
        $cachedClient->sendRequest($request);

        $this->assertSame(0, $cachedClient->getMisses());
        $this->assertSame(1, $cachedClient->getHits());
        $this->assertSame($cacheKey1, $cacheKey2);
    }

    private function createMockCache()
    {
        return $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
    }

    private function createMockCacheItem()
    {
        return $this->getMockBuilder(CacheItemInterface::class)->getMock();
    }

    private function createMockClient()
    {
        return $this->getMockBuilder(BuzzClientInterface::class)->getMock();
    }

    private function createMockRequest()
    {
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        return $request;
    }

    private function createMockResponse()
    {
        return $this->getMockBuilder(ResponseInterface::class)->getMock();
    }
}
