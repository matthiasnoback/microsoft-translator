<?php

namespace MatthiasNoback\Exception;

/**
 * This kind of exception will be thrown when the response
 * did not contain the expected elements
 */
class InvalidResponseException extends \UnexpectedValueException implements MicrosoftApiExceptionInterface
{
}
