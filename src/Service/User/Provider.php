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

namespace Fusio\Impl\Service\User;

use Fusio\Impl\Service;
use Fusio\Impl\Service\User\Model\User as UserModel;
use PSX\Framework\Config\Config;
use PSX\Http;
use PSX\Http\Exception as StatusCode;

/**
 * Provider
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Provider
{
    /**
     * @var \Fusio\Impl\Service\User
     */
    protected $userService;

    /**
     * @var \Fusio\Impl\Service\App
     */
    protected $appService;

    /**
     * @var \Fusio\Impl\Service\Config
     */
    protected $configService;

    /**
     * @var \Fusio\Impl\Service\User\TokenIssuer
     */
    protected $tokenIssuer;

    /**
     * @var \PSX\Http\Client
     */
    protected $httpClient;

    /**
     * @var \Fusio\Impl\Mail\MailerInterface
     */
    protected $mailer;

    /**
     * @var \PSX\Framework\Config\Config
     */
    protected $psxConfig;

    public function __construct(Service\User $userService, Service\Config $configService, TokenIssuer $tokenIssuer, Http\Client $httpClient, Config $psxConfig)
    {
        $this->userService   = $userService;
        $this->configService = $configService;
        $this->tokenIssuer   = $tokenIssuer;
        $this->httpClient    = $httpClient;
        $this->psxConfig     = $psxConfig;
    }

    public function provider($providerName, $code, $clientId, $redirectUri)
    {
        $providerName = strtolower($providerName);
        $provider     = $this->getProvider($providerName);

        if ($provider instanceof ProviderInterface) {
            $user = $provider->requestUser($code, $clientId, $redirectUri);

            if ($user instanceof UserModel) {
                $scopes = $this->getDefaultScopes();
                $userId = $this->userService->createRemote(
                    $provider->getId(),
                    $user->getId(),
                    $user->getName(),
                    $user->getEmail(),
                    $scopes
                );

                return $this->tokenIssuer->createToken($userId, $scopes);
            } else {
                throw new StatusCode\BadRequestException('Could not request user information');
            }
        } else {
            throw new StatusCode\BadRequestException('Not supported provider');
        }
    }

    protected function getProvider($provider)
    {
        switch ($provider) {
            case 'facebook':
                $secret = $this->configService->getValue('provider_facebook_secret');
                if (!empty($secret)) {
                    return new Provider\Facebook($this->httpClient, $secret);
                }
                break;
            case 'github':
                $secret = $this->configService->getValue('provider_github_secret');
                if (!empty($secret)) {
                    return new Provider\Github($this->httpClient, $secret);
                }
                break;
            case 'google':
                $secret = $this->configService->getValue('provider_google_secret');
                if (!empty($secret)) {
                    return new Provider\Google($this->httpClient, $secret);
                }
                break;
        }

        return null;
    }

    protected function getDefaultScopes()
    {
        $scopes = $this->configService->getValue('scopes_default');

        return array_filter(array_map('trim', explode(',', $scopes)), function ($scope) {
            // we filter out the backend scope since this would be a major
            // security issue
            return !empty($scope) && $scope != 'backend';
        });
    }
}