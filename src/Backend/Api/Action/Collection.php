<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Impl\Backend\Api\Action;

use Fusio\Impl\Backend\Api\BackendApiAbstract;
use Fusio\Impl\Backend\Schema;
use Fusio\Impl\Backend\View;
use PSX\Api\Resource;
use PSX\Framework\Loader\Context;
use PSX\Validate\Filter as PSXFilter;
use PSX\Validate\Validate;

/**
 * Collection
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Collection extends BackendApiAbstract
{
    use ValidatorTrait;

    /**
     * @Inject
     * @var \Fusio\Impl\Service\Action
     */
    protected $actionService;

    /**
     * @param integer $version
     * @return \PSX\Api\Resource
     */
    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addResponse(200, $this->schemaManager->getSchema(Schema\Action\Collection::class))
        );

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema(Schema\Action\Create::class))
            ->addResponse(201, $this->schemaManager->getSchema(Schema\Message::class))
        );

        return $resource;
    }

    /**
     * Returns the GET response
     *
     * @return array|\PSX\Record\RecordInterface
     */
    protected function doGet()
    {
        return $this->tableManager->getTable(View\Action::class)->getCollection(
            $this->getParameter('startIndex', Validate::TYPE_INTEGER) ?: 0,
            $this->getParameter('search', Validate::TYPE_STRING) ?: null,
            $this->getParameter('routeId', Validate::TYPE_INTEGER) ?: null
        );
    }

    /**
     * Returns the POST response
     *
     * @param \PSX\Record\RecordInterface $record
     * @return array|\PSX\Record\RecordInterface
     */
    protected function doPost($record)
    {
        $this->actionService->create(
            $record->name,
            $record->class,
            $record->engine,
            $record->config->getProperties()
        );

        return array(
            'success' => true,
            'message' => 'Action successful created',
        );
    }
}
