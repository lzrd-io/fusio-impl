<?php
/*
 * Fusio is an open source API management platform which helps to create innovative API solutions.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fusio\Impl\Webhook;

/**
 * SenderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org
 */
interface SenderInterface
{
    /**
     * Returns whether the sender supports this dispatcher instance
     * 
     * @param object $dispatcher
     * @return boolean
     */
    public function accept(object $dispatcher): bool;

    /**
     * Sends an event using the dispatcher. The dispatcher is by default an http client but it also possible to
     * configure another dispatcher by using the name of an connection. Through this it would be possible to dispatch
     * events using different message queue systems. By default the sender can handle
     */
    public function send(object $dispatcher, Message $message): int;
}
