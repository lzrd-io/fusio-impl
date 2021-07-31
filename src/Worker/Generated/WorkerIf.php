<?php
namespace Fusio\Impl\Worker\Generated;

/**
 * Autogenerated by Thrift Compiler (0.14.2)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TApplicationException;

interface WorkerIf
{
    /**
     * @param \Fusio\Impl\Worker\Generated\Connection $connection
     * @return \Fusio\Impl\Worker\Generated\Message
     */
    public function setConnection($connection);
    /**
     * @param \Fusio\Impl\Worker\Generated\Action $action
     * @return \Fusio\Impl\Worker\Generated\Message
     */
    public function setAction($action);
    /**
     * @param \Fusio\Impl\Worker\Generated\Execute $execute
     * @return \Fusio\Impl\Worker\Generated\Result
     */
    public function executeAction($execute);
}
