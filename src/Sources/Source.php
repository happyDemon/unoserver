<?php

namespace HappyDemon\UnoServer\Sources;

abstract class Source
{
    protected string $filePath;
    /**
     * @var false
     */
    protected bool $deleteOnConversion;

    /**
     * @param string $filePath
     * @param bool $deleteOnConversion
     */
    public function __construct(string $filePath, bool $deleteOnConversion = false)
    {
        $this->filePath = $filePath;
        $this->deleteOnConversion = $deleteOnConversion;
    }

    public function path(): string
    {
        return $this->filePath;
    }

    public function deletable(): bool
    {
        return $this->deleteOnConversion;
    }

    abstract public function validateFormat(string $format): bool;
}
