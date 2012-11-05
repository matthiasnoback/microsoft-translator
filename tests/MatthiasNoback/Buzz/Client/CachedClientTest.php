<?php

namespace MatthiasNoback\Tests\Buzz\Client;

use MatthiasNoback\Buzz\Client\CachedClient;

class CachedClientTest extends \PHPUnit_Framework_TestCase
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
            ->method('contains')
            ->will($this->returnCallback(function($id) use (&$cacheKey1) {
                $cacheKey1 = $id;

                return false;
            }));

        $client = $this->createMockClient();
        // the client will be used to fetch the response
        $client
            ->expects($this->once())
            ->method('send')
            ->with($request, $response);

        // the serialized response will be stored in the cache
        $cache
            ->expects($this->once())
            ->method('save')
            ->with($this->isType('string'), serialize($response))
            ->will($this->returnCallback(function($id) use (&$cacheKey2) {
                $cacheKey2 = $id;
            }));

        $cachedClient = new CachedClient($client, $cache);

        $cachedClient->send($request, $response);

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
            ->method('contains')
            ->will($this->returnCallback(function($id) use (&$cacheKey1) {
            $cacheKey1 = $id;

            return true;
        }));

        $client = $this->createMockClient();
        // the client should not be called
        $client
            ->expects($this->never())
            ->method('send');

        $cachedResponse = new \Buzz\Message\Response();
        $cachedHeaders = array('CachedHeader');
        $cachedContent = 'Cached response';
        $cachedResponse->setHeaders($cachedHeaders);
        $cachedResponse->setContent($cachedContent);

        // the serialized response will be retrieved from the cache
        $cache
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnCallback(function($id) use (&$cacheKey2, $cachedResponse) {
            $cacheKey2 = $id;

            return serialize($cachedResponse);
        }));

        $response
            ->expects($this->once())
            ->method('setHeaders')
            ->with($cachedHeaders);
        $response
            ->expects($this->once())
            ->method('setContent')
            ->with($cachedContent);

        $cachedClient = new CachedClient($client, $cache);

        $cachedClient->send($request, $response);

        $this->assertSame(0, $cachedClient->getMisses());
        $this->assertSame(1, $cachedClient->getHits());
        $this->assertSame($cacheKey1, $cacheKey2);
    }

    private function createMockCache()
    {
        return $this->getMock('Doctrine\\Common\\Cache\\Cache');
    }

    private function createMockClient()
    {
        return $this->getMock('Buzz\\Client\\ClientInterface');
    }

    private function createMockRequest(array $headers = array())
    {
        $request = $this->getMock('Buzz\\Message\\RequestInterface');
        $request
            ->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        return $request;
    }

    private function createMockResponse()
    {
        return $this->getMock('Buzz\Message\Response');
    }
}
