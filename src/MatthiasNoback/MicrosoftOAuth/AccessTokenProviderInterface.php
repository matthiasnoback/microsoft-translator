<?php

namespace MatthiasNoback\MicrosoftOAuth;

interface AccessTokenProviderInterface
{
    public function getAccessToken($scope, $grantType);
}
