<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2016 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Fusio\Engine\Connector;
use Doctrine\DBAL;
use Psr\Log\LoggerInterface;
use PSX\Sql\Condition;
use RuntimeException;
use Fusio\Impl\Table;

/**
 * Migration
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Migration
{
    /**
     * @var \Fusio\Engine\Connector
     */
    protected $connector;

    /**
     * @var \Fusio\Impl\Table\Deploy\Migration
     */
    protected $deployTable;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Fusio\Engine\Connector $connector
     * @param \Fusio\Impl\Table\Deploy\Migration $deployTable
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(Connector $connector, Table\Deploy\Migration $deployTable, LoggerInterface $logger)
    {
        $this->connector   = $connector;
        $this->deployTable = $deployTable;
        $this->logger      = $logger;
    }

    public function execute(array $data, $basePath = null)
    {
        $result = [];

        if (isset($data['migration']) && is_array($data['migration'])) {
            foreach ($data['migration'] as $connectionId => $definitionFiles) {
                if (is_array($definitionFiles)) {
                    foreach ($definitionFiles as $definitionFile) {
                        $path = $basePath . '/' . $definitionFile;
                        if (is_file($path)) {
                            try {
                                $this->executeDefinition($connectionId, $path, $definitionFile, $result);
                            } catch (\Exception $e) {
                                $this->logger->error($e->getMessage());

                                $result[] = '[ERROR] ' . $connectionId . ' ' . $definitionFile . ': ' . $e->getMessage();
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function executeDefinition($connectionId, $path, $definitionFile, array &$result)
    {
        $connection = $this->connector->getConnection($connectionId);
        $hash       = sha1_file($path);
        $definition = file_get_contents($path);

        // check whether the script was already executed
        $condition = new Condition();
        $condition->equals('connection', $connectionId);
        $condition->equals('file', $definitionFile);

        $deploy = $this->deployTable->getOneBy($condition);

        if (empty($deploy)) {
            // execute if the file was not already executed
            if ($connection instanceof DBAL\Connection) {
                $this->executeSql($connection, $definition);

                // insert migration
                $this->deployTable->create([
                    'connection' => $connectionId,
                    'file' => $definitionFile,
                    'fileHash' => $hash,
                    'executeDate' => new \DateTime(),
                ]);

                $result[] = '[EXECUTED] ' . $connectionId . ' ' . $definitionFile;
            } else {
                // @TODO handle also other connection types i.e. mongodb

                throw new RuntimeException('Connection type is not supported');
            }
        } else {
            if ($deploy['fileHash'] != $hash) {
                $result[] = '[SKIPPED] ' . $connectionId . ' ' . $definitionFile . ' (The file was already executed, but the content has changed)';
            }
        }
    }

    private function executeSql(DBAL\Connection $connection, $definition)
    {
        $lines = explode("\n", str_replace(["\r\n", "\n", "\r"], "\n", $definition));
        $sql   = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || substr($line, 0, 2) == '--') {
                continue;
            }

            $sql.= $line;

            if (substr($line, -1) == ';') {
                // execute sql
                $connection->exec($sql);

                $sql = '';
            }
        }

        // in the case the sql had no last semicolon
        if (!empty($sql)) {
            // execute sql
            $connection->exec($sql);
        }
    }
}