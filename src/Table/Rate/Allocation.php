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

namespace Fusio\Impl\Table\Rate;

use Fusio\Engine\Model;
use PSX\Sql\TableAbstract;

/**
 * Allocation
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Allocation extends TableAbstract
{
    public function getName()
    {
        return 'fusio_rate_allocation';
    }

    public function getColumns()
    {
        return array(
            'id' => self::TYPE_INT | self::AUTO_INCREMENT | self::PRIMARY_KEY,
            'planId' => self::TYPE_INT,
            'routeId' => self::TYPE_INT,
            'appId' => self::TYPE_INT,
            'scopes' => self::TYPE_VARCHAR,
            'parameters' => self::TYPE_VARCHAR,
        );
    }

    public function getPlan($routeId, Model\App $app)
    {
        $sql = '    SELECT ratePlan.rateLimit,
                           ratePlan.timespan
                      FROM fusio_rate_allocation rateAllocation
                INNER JOIN fusio_rate_plan ratePlan
                        ON rateAllocation.planId = ratePlan.id 
                     WHERE (rateAllocation.routeId IS NULL OR rateAllocation.routeId = :routeId)
                       AND (rateAllocation.appId IS NULL OR rateAllocation.appId = :appId)';

        $params = [
            'routeId' => $routeId,
            'appId'   => $app->getId(),
        ];

        $scopes = $app->getScopes();
        if (!empty($scopes)) {
            $sql.= ' AND (rateAllocation.scopes IS NULL OR ';
            $sql.= $this->connection->getDatabasePlatform()->getLocateExpression(':scopes', 'rateAllocation.scopes');
            $sql.= ' > 0)';

            $params['scopes'] = implode(',', $scopes);
        }

        $parameters = $app->getParameters();
        if (!empty($parameters)) {
            $sql.= ' AND (rateAllocation.parameters IS NULL OR ';
            $sql.= $this->connection->getDatabasePlatform()->getLocateExpression(':parameters', 'rateAllocation.parameters');
            $sql.= ' > 0)';

            $params['parameters'] = http_build_query($parameters, '', '&');
        }

        return $this->connection->fetchAssoc($sql, $params);
    }
}
