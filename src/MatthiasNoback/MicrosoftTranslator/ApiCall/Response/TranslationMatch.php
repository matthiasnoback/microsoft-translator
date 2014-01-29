<?php

namespace MatthiasNoback\MicrosoftTranslator\ApiCall\Response;

class TranslationMatch
{
    private $translatedText;
    private $degree;

    public function __construct($translatedText, $degree)
    {
        $this->translatedText = $translatedText;
        $this->degree = $degree;
    }

    public function __toString()
    {
        return $this->translatedText;
    }

    public function getTranslatedText()
    {
        return $this->translatedText;
    }

    public function getDegree()
    {
        return $this->degree;
    }
}
