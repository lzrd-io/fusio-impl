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

namespace Fusio\Impl\Event\Schema;

use Fusio\Impl\Authorization\UserContext;
use Fusio\Impl\Event\EventAbstract;
use Fusio\Impl\Table\Generated\SchemaRow;
use Fusio\Model\Backend\SchemaUpdate;

/**
 * UpdatedEvent
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org
 */
class UpdatedEvent extends EventAbstract
{
    private SchemaUpdate $schema;
    private SchemaRow $existing;

    public function __construct(SchemaUpdate $schema, SchemaRow $existing, UserContext $context)
    {
        parent::__construct($context);

        $this->schema = $schema;
        $this->existing = $existing;
    }

    public function getSchema(): SchemaUpdate
    {
        return $this->schema;
    }

    public function getExisting(): SchemaRow
    {
        return $this->existing;
    }
}
