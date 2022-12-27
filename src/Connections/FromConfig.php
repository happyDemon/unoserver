<?php

namespace HappyDemon\UnoServer\Connections;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class FromConfig extends Connection
{
    protected ?string $interface = null;
    protected ?int $port = null;

    public function configKeys(): array
    {
        return [
            'interface',
            'port',
        ];
    }

    public function configure(array $config): FromConfig
    {
        $this->interface = $config['interface'];
        $this->port = (int) $config['port'];

        return $this;
    }

    public function toProcess(string $format, string $document, string $outputPath): Process
    {
        return Process::fromShellCommandline(
            implode(' ', [
                config('unoserver.executables.python') ?: (new ExecutableFinder())->find('python'),
                '-m',
                'unoserver.converter',
                '--interface',
                $this->interface,
                '--port',
                $this->port,
                '--convert-to',
                $format,
                '-', // stdin
                '-', // stdout
                '< ' . escapeshellarg($document),
                '> ' . escapeshellarg($outputPath),
            ]),
        )
            ->setInput($document);
    }
}
