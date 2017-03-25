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

namespace Fusio\Impl\Consumer\Api\User;

use Fusio\Impl\Consumer\Schema;
use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Http\Exception as StatusCode;
use PSX\Validate\Filter as PSXFilter;

/**
 * Provider
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Provider extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \Fusio\Impl\Service\User\Provider
     */
    protected $userProviderService;

    /**
     * @param integer $version
     * @return \PSX\Api\Resource
     */
    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema(Schema\User\Provider::class))
            ->addResponse(200, $this->schemaManager->getSchema(Schema\User\JWT::class))
        );

        return $resource;
    }

    /**
     * Returns the POST response
     *
     * @param \PSX\Record\RecordInterface $record
     * @return array|\PSX\Record\RecordInterface
     */
    protected function doPost($record)
    {
        $provider    = $this->getUriFragment('provider');
        $code        = $record->code;
        $clientId    = $record->clientId;
        $redirectUri = $record->redirectUri;

        $token = $this->userProviderService->provider($provider, $code, $clientId, $redirectUri);

        if (!empty($token)) {
            return [
                'token' => $token,
            ];
        } else {
            throw new StatusCode\UnauthorizedException('Invalid data', 'Basic');
        }
    }
}