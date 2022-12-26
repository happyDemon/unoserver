<?php

namespace HappyDemon\UnoServer\Connections;

use Symfony\Component\Process\Process;

class FromCommand extends Connection
{
    protected ?string $pathToBashFile = null;

    public function configKeys(): array
    {
        return [
            'command'
        ];
    }

    public function configure(array $config): FromCommand
    {
        $this->pathToBashFile = $config['command'];

        return $this;
    }

    public function toProcess(string $format, string $document, string $outputPath): Process
    {
        $process =  Process::fromShellCommandline(
            implode(' ', [
                $this->pathToBashFile,
                '--convert-to',
                $format,
                '-', // stdin
                '-', // stdout
                '< ' . escapeshellarg($document),
                '> ' . escapeshellarg($outputPath),
            ]),
        );

        $process->setInput($document);

        return $process;
    }
}
