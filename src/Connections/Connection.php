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

    abstract public function configKeys(): array;

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

    /**
     * @param string $format
     *
     * @return UploadedFile
     * @throws ProcessFailedException
     */
    public function convert(string $format): UploadedFile
    {
        // Create a tmp file where the convert command can write to
        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        // Make sure the temp file is deleted
        app()->terminating(function () use ($tempFile) {
            fclose($tempFile);
        });

        // Runs the conversion command
        try {
            $this->toProcess($format, $this->sourceFile->path(), $tempFilePath)
                ->setTimeout(30)
                ->mustRun();
        }
        catch (ProcessFailedException $processFailedException) {
            dd($processFailedException);
        }


        // Delete the source file, if needed
        if ($this->sourceFile->deletable()) {
            unlink($this->sourceFile->path());
        }

        $tempFileObject = new File($tempFilePath);

        return new UploadedFile(
            $tempFileObject->getPathname(),
            $tempFileObject->getFilename(),
            $tempFileObject->getMimeType(),
            0,
            true
        );
    }
}
