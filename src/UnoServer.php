<?php

namespace HappyDemon\UnoServer;

use HappyDemon\UnoServer\Connections\Connection;
use HappyDemon\UnoServer\Sources\Document;
use HappyDemon\UnoServer\Sources\Source;
use HappyDemon\UnoServer\Sources\Spreadsheet;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;

class UnoServer
{
    protected Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected ?Source $sourceFile = null;
    protected ?string $format = null;

    /**
     * Define which document you want to convert.
     *
     * @param string $pathToDocumentFile
     * @param bool $onSuccessDeleteSourceFile
     *
     * @return $this
     */
    public function fromDocument(string $pathToDocumentFile, bool $onSuccessDeleteSourceFile = false): self
    {
        $this->sourceFile = new Document($pathToDocumentFile, $onSuccessDeleteSourceFile);

        return $this;
    }

    /**
     * Define which spreadsheet you want to convert.
     *
     * @param string $pathToDocumentFile
     * @param bool $onSuccessDeleteSourceFile
     *
     * @return $this
     */
    public function fromSpreadsheet(string $pathToDocumentFile, bool $onSuccessDeleteSourceFile = false): self
    {
        $this->sourceFile = new Spreadsheet($pathToDocumentFile, $onSuccessDeleteSourceFile);

        return $this;
    }

    /**
     * Specify what format you want to convert your source file to.
     *
     * @param string $format
     *
     * @return $this
     * @throws \Exception
     */
    public function toFormat(string $format): self
    {
        if (!$this->sourceFile->validateFormat($format)) {
            throw new \Exception($format . ' is not a valid format to convert to.');
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Converts the source file to the specified format.
     *
     * @return UploadedFile Converted file
     * @throws ProcessFailedException
     */
    public function convert(): UploadedFile
    {
        return $this->sourceFile->convert($this->connection,$this->format ?: 'pdf');
    }
}
