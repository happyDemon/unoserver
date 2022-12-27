<?php

namespace HappyDemon\UnoServer\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\ExecutableFinder;
use Touhidurabir\StubGenerator\Facades\StubGenerator;

class UnoServerGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:unoserver-cmd
        {server? : server from the config file}
        {--ip=127.0.0.1}
        {--port=2002}
        {--unoserver : Generate unoserver in the app\'s bin folder}
        {--unoconvert : Generate unoconvert in the app\'s bin folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate helper bash scripts.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $installable = [
            'unoserver' => $this->option('unoserver'),
            'unoconvert' => $this->option('unoconvert'),
        ];

        $suffix = '';
        if ($this->hasArgument('server') && !empty($this->argument('server'))) {
            $suffix = '-' . $this->argument('server');
        }

        $configPath = 'unoserver.servers.' . ($this->argument('server') ?: config('unoserver.default_server'));

        // If both haven't been defined, all are generated
        if (!$installable['unoserver'] && !$installable['unoconvert']) {
            $installable['unoserver'] = $installable['unoconvert'] = true;
        }

        // Path we'll be installing the scripts
        $scriptPath = base_path('bin');

        // Path to package in vendor dir
        $vendorDir = base_path('vendor/happydemon/unoserver');

        if ($installable['unoserver']) {
            $this->clearExistingScript($scriptPath . '/unoserver' . $suffix);

            StubGenerator::from($vendorDir . '/stubs/unoserver.stub', true)
                ->to($scriptPath, true, true)
                ->as('unoserver' . $suffix)
                ->noExt()
                ->withReplacers([
                    'python' => config('unoserver.executables.python') ?: (new ExecutableFinder())->find('python'),
                    'executable' => config('unoserver.executables.libreoffice'),
                    'server' => $this->option('ip') ?: config($configPath . '.interface', '127.0.0.1'),
                    'port' => $this->option('port') ?: config($configPath . '.port', 2002),
                ])
                ->save();

            $this->correctRights($scriptPath . '/unoserver' . $suffix);

            $this->line('Generated bin/unoserver' . $suffix . ' for starting up a unoserver.');
        }

        if ($installable['unoconvert']) {
            $this->clearExistingScript($scriptPath . '/unoconvert' . $suffix);

            StubGenerator::from($vendorDir . '/stubs/unoconvert.stub', true)
                ->to($scriptPath, true, true)
                ->as('unoconvert' . $suffix)
                ->noExt()
                ->withReplacers([
                    'python' => config('unoserver.executables.python') ?: (new ExecutableFinder())->find('python'),
                    'server' => $this->option('ip') ?: config($configPath . '.interface', '127.0.0.1'),
                    'port' => $this->option('port') ?: config($configPath . '.port', 2002),
                ])
                ->save();

            $this->correctRights($scriptPath . '/unoconvert' . $suffix);

            $this->line('Generated bin/unoconvert' . $suffix . '.');
        }

        return 0;
    }

    /**
     * We'll be overwriting previous versions.
     *
     * @param string $filePath
     *
     * @return void
     */
    public function clearExistingScript(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Make sure the generated scripts have correct file permissions.
     *
     * @param string $filePath
     *
     * @return void
     */
    public function correctRights(string $filePath): void
    {
        chmod($filePath, 0755);
    }
}
