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

namespace Fusio\Impl\Table\Plan;

use PSX\Sql\TableAbstract;

/**
 * Transaction
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Transaction extends TableAbstract
{
    const STATUS_COMPLETED = 1;
    const STATUS_PENDING = 2;
    const STATUS_CHARGEBACK = 3;
    const STATUS_CANCELED = 0;

    public function getName()
    {
        return 'fusio_plan_transaction';
    }

    public function getColumns()
    {
        return array(
            'id' => self::TYPE_INT | self::AUTO_INCREMENT | self::PRIMARY_KEY,
            'plan_id' => self::TYPE_INT,
            'user_id' => self::TYPE_INT,
            'status' => self::TYPE_INT,
            'transaction_id' => self::TYPE_VARCHAR,
            'insert_date' => self::TYPE_DATETIME,
        );
    }
}
