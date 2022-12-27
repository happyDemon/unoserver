<?php

namespace HappyDemon\UnoServer\Sources;

use HappyDemon\UnoServer\Connections\Connection;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;

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

    /**
     * @param Connection $connection
     * @param string $format
     *
     * @return UploadedFile
     * @throws ProcessFailedException
     */
    public function convert(Connection $connection, string $format): UploadedFile
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
            $connection->toProcess($format, $this->path(), $tempFilePath)
                ->setTimeout(30)
                ->mustRun();
        }
        catch (ProcessFailedException $processFailedException) {
            dd($processFailedException);
        }


        // Delete the source file, if needed
        if ($this->deletable()) {
            unlink($this->path());
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
