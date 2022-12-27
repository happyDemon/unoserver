<?php

namespace HappyDemon\UnoServer;

use HappyDemon\UnoServer\Connections\Connection;
use HappyDemon\UnoServer\Connections\FromCommand;
use HappyDemon\UnoServer\Connections\FromConfig;
use Illuminate\Support\Facades\Config;

class UnoServerFactory
{
    /**
     * Prepares a UnoServer object based on the provided config key.
     *
     * @param string|null $connectionName Defaults to `unoserver.default_server` config.
     *
     * @return UnoServer|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function connect(?string $connectionName = null): ?UnoServer
    {
        $config = config('unoserver.servers.' . $connectionName ?: config('unoserver.default_server'));

        $connectionTypes = [
            FromCommand::class,
            FromConfig::class
        ];

        foreach ($connectionTypes as $connectionType) {
            /** @var Connection $conn */
            $conn = new $connectionType;

            // If the config keys are set up correctly
            if (
                collect($conn->configKeys())
                    ->map(fn($configKey) => isset($config[$configKey]))
                    ->filter(fn($config) => !$config)
                    ->count() == 0
            ) {
                // setup and return a UnoServer
                return app()->make(UnoServer::class, ['connection' => $conn->configure($config)]);
            }
        }

        return null;
    }

    /**
     * Returns a `UnoServer` based on the provided configuration data.
     *
     * @param array $config
     *
     * @return UnoServer|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function fromConfig(array $config): ?UnoServer
    {
        $connectionName = '_internal_custom';
        Config::set('unoserver.servers.' . $connectionName, $config);

        return $this->connection($connectionName);
    }
}
