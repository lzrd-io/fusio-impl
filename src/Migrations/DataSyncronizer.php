<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Impl\Migrations;

use Doctrine\DBAL\Connection;

/**
 * DataSyncronizer
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org
 */
class DataSyncronizer
{
    /**
     * Method which checks all entries from the new installation data to the current installation and inserts
     * missing entries, note it does not update existing entries
     */
    public static function sync(Connection $connection): void
    {
        $data = NewInstallation::getData();

        $configs = $data->getData('fusio_config');
        foreach ($configs as $config) {
            $configId = $connection->fetchOne('SELECT id FROM fusio_config WHERE name = :name', [
                'name' => $config['name']
            ]);

            if (empty($configId)) {
                $connection->insert('fusio_config', $config);
            }
        }

        $routes = $data->getData('fusio_routes');
        $routeMap = [];
        foreach ($routes as $row) {
            $routeId = $connection->fetchOne('SELECT id FROM fusio_routes WHERE path = :path', [
                'path' => $row['path']
            ]);

            if (empty($routeId)) {
                $connection->insert('fusio_routes', $row);
                $routeId = $connection->lastInsertId();

                $routeMap[$data->getId('fusio_routes', $row['path'])] = $routeId;

                $methods = $data->getData('fusio_routes_method', 'route_id', $data->getId('fusio_routes', $row['path']));
                foreach ($methods as $method) {
                    $method['route_id'] = $routeId;
                    $connection->insert('fusio_routes_method', $method);

                    $methodId = $connection->lastInsertId();
                    $responses = $data->getData('fusio_routes_response', 'method_id', $data->getId('fusio_routes_method', $row['path'] . $method['method']));
                    foreach ($responses as $response) {
                        $response['method_id'] = $methodId;
                        $connection->insert('fusio_routes_response', $response);
                    }
                }
            }
        }

        $actions = $data->getData('fusio_action');
        foreach ($actions as $action) {
            $actionId = $connection->fetchOne('SELECT id FROM fusio_action WHERE name = :name', [
                'name' => $action['name']
            ]);

            if (empty($actionId)) {
                $connection->insert('fusio_action', $action);
            }
        }

        $schemas = $data->getData('fusio_schema');
        foreach ($schemas as $schema) {
            $schemaId = $connection->fetchOne('SELECT id FROM fusio_schema WHERE name = :name', [
                'name' => $schema['name']
            ]);

            if (empty($schemaId)) {
                $connection->insert('fusio_schema', $schema);
            }
        }

        $events = $data->getData('fusio_event');
        foreach ($events as $event) {
            $eventId = $connection->fetchOne('SELECT id FROM fusio_event WHERE name = :name', [
                'name' => $event['name']
            ]);

            if (empty($eventId)) {
                $connection->insert('fusio_event', $event);
            }
        }

        $cronjobs = $data->getData('fusio_cronjob');
        foreach ($cronjobs as $cronjob) {
            $cronjobId = $connection->fetchOne('SELECT id FROM fusio_cronjob WHERE name = :name', [
                'name' => $cronjob['name']
            ]);

            if (empty($cronjobId)) {
                $connection->insert('fusio_cronjob', $cronjob);
            }
        }

        $scopes = $data->getData('fusio_scope');
        $scopeMap = [];
        foreach ($scopes as $scope) {
            $scopeId = $connection->fetchOne('SELECT id FROM fusio_scope WHERE name = :name', [
                'name' => $scope['name']
            ]);

            if (empty($scopeId)) {
                $connection->insert('fusio_scope', $scope);
                $scopeId = $connection->lastInsertId();

                $scopeMap[$data->getId('fusio_scope', $scope['name'])] = $scopeId;
            }
        }

        $scopeRoutes = $data->getData('fusio_scope_routes');
        foreach ($scopeRoutes as $scopeRoute) {
            if (isset($scopeMap[$scopeRoute['scope_id']]) && isset($routeMap[$scopeRoute['route_id']])) {
                $scopeRoute['scope_id'] = $scopeMap[$scopeRoute['scope_id']];
                $scopeRoute['route_id'] = $routeMap[$scopeRoute['route_id']];

                $connection->insert('fusio_scope_routes', $scopeRoute);
            }
        }
    }
}