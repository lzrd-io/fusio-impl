<?php
namespace Fusio\Impl\Worker\Generated;

/**
 * Autogenerated by Thrift Compiler (0.18.1)
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

class Event
{
    static public $isValidate = false;

    static public $_TSPEC = array(
        1 => array(
            'var' => 'eventName',
            'isRequired' => false,
            'type' => TType::STRING,
        ),
        2 => array(
            'var' => 'data',
            'isRequired' => false,
            'type' => TType::STRING,
        ),
    );

    /**
     * @var string
     */
    public $eventName = null;
    /**
     * @var string
     */
    public $data = null;

    public function __construct($vals = null)
    {
        if (is_array($vals)) {
            if (isset($vals['eventName'])) {
                $this->eventName = $vals['eventName'];
            }
            if (isset($vals['data'])) {
                $this->data = $vals['data'];
            }
        }
    }

    public function getName()
    {
        return 'Event';
    }


    public function read($input)
    {
        $xfer = 0;
        $fname = null;
        $ftype = 0;
        $fid = 0;
        $xfer += $input->readStructBegin($fname);
        while (true) {
            $xfer += $input->readFieldBegin($fname, $ftype, $fid);
            if ($ftype == TType::STOP) {
                break;
            }
            switch ($fid) {
                case 1:
                    if ($ftype == TType::STRING) {
                        $xfer += $input->readString($this->eventName);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                case 2:
                    if ($ftype == TType::STRING) {
                        $xfer += $input->readString($this->data);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                default:
                    $xfer += $input->skip($ftype);
                    break;
            }
            $xfer += $input->readFieldEnd();
        }
        $xfer += $input->readStructEnd();
        return $xfer;
    }

    public function write($output)
    {
        $xfer = 0;
        $xfer += $output->writeStructBegin('Event');
        if ($this->eventName !== null) {
            $xfer += $output->writeFieldBegin('eventName', TType::STRING, 1);
            $xfer += $output->writeString($this->eventName);
            $xfer += $output->writeFieldEnd();
        }
        if ($this->data !== null) {
            $xfer += $output->writeFieldBegin('data', TType::STRING, 2);
            $xfer += $output->writeString($this->data);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }
}
