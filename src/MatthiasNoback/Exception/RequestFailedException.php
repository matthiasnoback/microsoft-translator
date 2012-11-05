<?php

namespace MatthiasNoback\Exception;

/**
 * This kind of exception will be thrown when a
 * call to the Microsoft API failed
 */
class RequestFailedException extends \RuntimeException implements MicrosoftApiExceptionInterface
{
}
