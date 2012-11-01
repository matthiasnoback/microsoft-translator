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
    public function get($scope, $grantType);

    /**
     * @abstract
     * @param $scope
     * @param string $grantType
     * @return boolean
     */
    public function has($scope, $grantType);

    /**
     * @abstract
     * @param string $scope
     * @param string $grantType
     * @param $accessToken
     * @return boolean
     */
    public function set($scope, $grantType, $accessToken);
}
