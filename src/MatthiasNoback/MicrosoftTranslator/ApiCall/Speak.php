<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall;

class Speak extends AbstractMicrosoftTranslatorApiCall
{
    const MAXIMUM_LENGTH_OF_TEXT = '2000';

    private $text;
    private $language;
    private $format;
    private $options;

    public function __construct($text, $language, $format = null, $options = null)
    {
        if (strlen($text) > self::MAXIMUM_LENGTH_OF_TEXT) {
            throw new \InvalidArgumentException(sprintf('Text may not be longer than %d characters', self::MAXIMUM_LENGTH_OF_TEXT));
        }

        $supportedFormats = array('audio/wav', 'audio/mp3');
        if ($format !== null && !in_array($format, $supportedFormats)) {
            throw new \InvalidArgumentException(sprintf('Format not supported, choose one of: %s', implode(', ', $supportedFormats)));
        }

        $supportedOptions = array('MaxQuality', 'MinSize');
        if ($options !== null && !in_array($options, $supportedOptions)) {
            throw new \InvalidArgumentException(sprintf('Options not supported, choose one of: %s', implode(', ', $supportedOptions)));
        }

        $this->text = $text;
        $this->language = $language;
        $this->format = $format;
        $this->options = $options;
    }

    public function getApiMethodName()
    {
        return 'Speak';
    }

    public function getHttpMethod()
    {
        return 'GET';
    }

    public function getRequestContent()
    {
    }

    public function getQueryParameters()
    {
        return array(
            'text'     => $this->text,
            'language' => $this->language,
            'format'   => $this->format,
            'options'  => $this->options,
        );
    }

    public function parseResponse($response)
    {
        return $response;
    }
}
