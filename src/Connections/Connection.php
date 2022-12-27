<?php

namespace HappyDemon\UnoServer\Connections;

use HappyDemon\UnoServer\Sources\Source;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class Connection
{
    protected Source $sourceFile;

    /**
     * @param Source $sourceFile
     *
     * @return $this
     */
    public function withSourceFile(Source $sourceFile): self
    {
        $this->sourceFile = $sourceFile;

        return $this;
    }

    /**
     * Define which config keys are required for this connection to work.
     * @return array
     */
    abstract public function configKeys(): array;

    /**
     * Configure this connection object based on the provided data.
     * @param array $config
     *
     * @return $this
     */
    abstract public function configure(array $config): self;

    /**
     * Sets up a `Process` object that converts documents when its run.
     *
     * @param string $format Format we're converting to
     * @param string $document The file path to the document that will be converted
     * @param string $outputPath The file path for the rendered file
     *
     * @return Process
     */
    abstract public function toProcess(string $format, string $document, string $outputPath): Process;
}
