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

class Response
{
    static public $isValidate = false;

    static public $_TSPEC = array(
        1 => array(
            'var' => 'statusCode',
            'isRequired' => false,
            'type' => TType::I32,
        ),
        2 => array(
            'var' => 'headers',
            'isRequired' => false,
            'type' => TType::MAP,
            'ktype' => TType::STRING,
            'vtype' => TType::STRING,
            'key' => array(
                'type' => TType::STRING,
            ),
            'val' => array(
                'type' => TType::STRING,
                ),
        ),
        3 => array(
            'var' => 'body',
            'isRequired' => false,
            'type' => TType::STRING,
        ),
    );

    /**
     * @var int
     */
    public $statusCode = null;
    /**
     * @var array
     */
    public $headers = null;
    /**
     * @var string
     */
    public $body = null;

    public function __construct($vals = null)
    {
        if (is_array($vals)) {
            if (isset($vals['statusCode'])) {
                $this->statusCode = $vals['statusCode'];
            }
            if (isset($vals['headers'])) {
                $this->headers = $vals['headers'];
            }
            if (isset($vals['body'])) {
                $this->body = $vals['body'];
            }
        }
    }

    public function getName()
    {
        return 'Response';
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
                    if ($ftype == TType::I32) {
                        $xfer += $input->readI32($this->statusCode);
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                case 2:
                    if ($ftype == TType::MAP) {
                        $this->headers = array();
                        $_size55 = 0;
                        $_ktype56 = 0;
                        $_vtype57 = 0;
                        $xfer += $input->readMapBegin($_ktype56, $_vtype57, $_size55);
                        for ($_i59 = 0; $_i59 < $_size55; ++$_i59) {
                            $key60 = '';
                            $val61 = '';
                            $xfer += $input->readString($key60);
                            $xfer += $input->readString($val61);
                            $this->headers[$key60] = $val61;
                        }
                        $xfer += $input->readMapEnd();
                    } else {
                        $xfer += $input->skip($ftype);
                    }
                    break;
                case 3:
                    if ($ftype == TType::STRING) {
                        $xfer += $input->readString($this->body);
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
        $xfer += $output->writeStructBegin('Response');
        if ($this->statusCode !== null) {
            $xfer += $output->writeFieldBegin('statusCode', TType::I32, 1);
            $xfer += $output->writeI32($this->statusCode);
            $xfer += $output->writeFieldEnd();
        }
        if ($this->headers !== null) {
            if (!is_array($this->headers)) {
                throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
            }
            $xfer += $output->writeFieldBegin('headers', TType::MAP, 2);
            $output->writeMapBegin(TType::STRING, TType::STRING, count($this->headers));
            foreach ($this->headers as $kiter62 => $viter63) {
                $xfer += $output->writeString($kiter62);
                $xfer += $output->writeString($viter63);
            }
            $output->writeMapEnd();
            $xfer += $output->writeFieldEnd();
        }
        if ($this->body !== null) {
            $xfer += $output->writeFieldBegin('body', TType::STRING, 3);
            $xfer += $output->writeString($this->body);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }
}
