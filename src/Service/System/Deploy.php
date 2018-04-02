<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Impl\Service\System;

use Fusio\Impl\Service\System\Deploy\EnvProperties;
use Fusio\Impl\Service\System\Deploy\IncludeDirective;
use Fusio\Impl\Service\System\Deploy\TransformerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * The deploy service basically transforms a deploy yaml config into a json 
 * format which is then used by the import service. Also it handles the 
 * database migration
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Deploy
{
    const TYPE_MIGRATION = 'migration';
    const TYPE_SERVER = 'server';

    /**
     * @var \Fusio\Impl\Service\System\Import
     */
    protected $importService;

    /**
     * @var \Fusio\Impl\Service\System\Migration
     */
    protected $migrationService;

    /**
     * @var \Fusio\Impl\Service\System\WebServer
     */
    protected $webServerService;

    /**
     * @param \Fusio\Impl\Service\System\Import $importService
     * @param \Fusio\Impl\Service\System\Migration $migrationService
     * @param \Fusio\Impl\Service\System\WebServer $webServerService
     */
    public function __construct(Import $importService, Migration $migrationService, WebServer $webServerService)
    {
        $this->importService    = $importService;
        $this->migrationService = $migrationService;
        $this->webServerService = $webServerService;
    }

    /**
     * @param string $data
     * @param string|null $basePath
     * @param boolean $force
     * @return \Fusio\Impl\Service\System\Import\Result
     */
    public function deploy($data, $basePath = null, $force = false)
    {
        $data   = Yaml::parse(EnvProperties::replace($data));
        $import = new \stdClass();

        if (empty($basePath)) {
            $basePath = getcwd();
        }

        $transformers = [
            SystemAbstract::TYPE_CONFIG     => new Deploy\Transformer\Config(),
            SystemAbstract::TYPE_CONNECTION => new Deploy\Transformer\Connection(),
            SystemAbstract::TYPE_SCHEMA     => new Deploy\Transformer\Schema(),
            SystemAbstract::TYPE_ACTION     => new Deploy\Transformer\Action(),
            SystemAbstract::TYPE_ROUTES     => new Deploy\Transformer\Routes(),
            SystemAbstract::TYPE_CRONJOB    => new Deploy\Transformer\Cronjob(),
            SystemAbstract::TYPE_RATE       => new Deploy\Transformer\Rate(),
        ];

        // resolve includes
        foreach ($transformers as $type => $transformer) {
            if (isset($data[$type]) && is_string($data[$type])) {
                $data[$type] = IncludeDirective::resolve($data[$type], $basePath, $type);
            }
        }

        // run transformer
        foreach ($transformers as $type => $transformer) {
            /** @var TransformerInterface $transformer */
            $transformer->transform($data, $import, $basePath);
        }

        // import definition
        $result = $this->importService->import(json_encode($import));

        // migration
        $migration = isset($data[self::TYPE_MIGRATION]) ? $data[self::TYPE_MIGRATION] : [];
        $migration = IncludeDirective::resolve($migration, $basePath, self::TYPE_MIGRATION);

        if (is_array($migration)) {
            $result->merge($this->migrationService->execute($migration, $basePath, $force));
        }

        // web server
        $server = isset($data[self::TYPE_SERVER]) ? $data[self::TYPE_SERVER] : [];
        $server = IncludeDirective::resolve($server, $basePath, self::TYPE_SERVER);

        if (is_array($server)) {
            $result->merge($this->webServerService->generate($server));
        }

        return $result;
    }
}
