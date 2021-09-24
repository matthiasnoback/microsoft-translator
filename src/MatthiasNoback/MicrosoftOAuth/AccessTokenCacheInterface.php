<?php

namespace MatthiasNoback\MicrosoftOAuth;

interface AccessTokenCacheInterface
{
    /**
     * @abstract
     * @param string $scope
     * @param string $grantType
     * @return string
     */
    public function get(string $scope, string $grantType): string;

    /**
     * @abstract
     * @param string $scope
     * @param string $grantType
     * @return bool
     */
    public function has(string $scope, string $grantType): bool;

    /**
     * @abstract
     * @param string $scope
     * @param string $grantType
     * @param string $accessToken
     * @return bool
     */
    public function set(string $scope, string $grantType, string $accessToken): bool;
}
