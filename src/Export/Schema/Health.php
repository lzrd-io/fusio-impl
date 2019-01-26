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

namespace Fusio\Impl\Export\Schema;

use PSX\Schema\SchemaAbstract;

/**
 * Health
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Health extends SchemaAbstract
{
    public function getDefinition()
    {
        $sb = $this->getSchemaBuilder('Export Health Check');
        $sb->boolean('healthy');
        $sb->string('error');
        $check = $sb->getProperty();

        $sb = $this->getSchemaBuilder('Export Health');
        $sb->boolean('healthy');
        $sb->objectType('checks')
            ->setTitle('Export Health Checks')
            ->setAdditionalProperties($check);

        return $sb->getProperty();
    }
}