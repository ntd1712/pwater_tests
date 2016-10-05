<?php
/**
 * [11318][11675][11724][CRE] - [tuan_dung] - [06/06/2016] - CS01_02 - Create common function for SBPS Library - UT Test
 */
class Refs11675Test extends \PHPUnit_Framework_TestCase
{
    public function test_create_sps_hashcode()
    {
        $actual1 = $this->refs->_create_sps_hashcode($arr = $this->data->getExpectedReadResponeXml());
        $actual2 = $this->refs->_create_sps_hashcode('merchant_id,', 'service_id,', 'cust_code,', 'order_id,',
            'item_id,', 'item_name,', 'tax,', 'amount,', 'free1,', 'free2,', 'free3,', 'order_rowno,',
            'sps_cust_info_return_flg,', '', $arr['pay_method_info'],
            'pay_option_manage,', 'encrypted_flg,', 'request_date,', 'limit_second,', 'sps_hashcode');

        $this->assertEquals($this->data->getExpectedCreateSpsHashcode(), $actual1);
        $this->assertEquals($this->data->getExpectedCreateSpsHashcode(), $actual2);

        // just to make sure it'll work twice or more without errors
        $actual1 = $this->refs->_create_sps_hashcode($arr = $this->data->getExpectedReadResponeXml());
        $actual2 = $this->refs->_create_sps_hashcode('merchant_id,', 'service_id,', 'cust_code,', 'order_id,',
            'item_id,', 'item_name,', 'tax,', 'amount,', 'free1,', 'free2,', 'free3,', 'order_rowno,',
            'sps_cust_info_return_flg,', '', $arr['pay_method_info'],
            'pay_option_manage,', 'encrypted_flg,', 'request_date,', 'limit_second,', 'sps_hashcode');

        $this->assertEquals($this->data->getExpectedCreateSpsHashcode(), $actual1);
        $this->assertEquals($this->data->getExpectedCreateSpsHashcode(), $actual2);
    }

    public function test_create_request_xml_params()
    {
        $actual1 = $this->refs->_create_request_xml_params($this->data->getExpectedReadResponeXml(), $this->data->getId());
        $actual2 = new SimpleXMLElement($actual1, LIBXML_NOCDATA);

        $this->assertEquals($this->data->getExpectedCreateRequestXmlParams(), $actual1);
        $this->assertInstanceOf('SimpleXMLElement', $actual2);

        // just to make sure it'll work twice or more without errors
        $actual1 = $this->refs->_create_request_xml_params($this->data->getExpectedReadResponeXml(), $this->data->getId());
        $actual2 = new SimpleXMLElement($actual1, LIBXML_NOCDATA);

        $this->assertEquals($this->data->getExpectedCreateRequestXmlParams(), $actual1);
        $this->assertInstanceOf('SimpleXMLElement', $actual2);
    }

    public function test_read_respone_xml()
    {
        $expected = array('@attributes' => array('id' => $this->data->getId())) + $this->data->getExpectedReadResponeXml();
        $actual = $this->refs->_read_respone_xml($this->data->getExpectedCreateRequestXmlParams(), LIBXML_NOCDATA);

        $this->assertEquals($expected, $actual);

        // just to make sure it'll work twice or more without errors
        $expected = array('@attributes' => array('id' => $this->data->getId())) + $this->data->getExpectedReadResponeXml();
        $actual = $this->refs->_read_respone_xml($this->data->getExpectedCreateRequestXmlParams(), LIBXML_NOCDATA);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Exception
     */
    public function test_failure_read_respone_xml()
    {
        $this->refs->_read_respone_xml('');
    }

    protected function setUp()
    {
        $this->refs = new \Refs11675;
        $this->data = new \Refs11675Test_DataProvider;

        parent::setUp();
    }

    /** @var \Refs11675 */
    private $refs;
    /** @var \Refs11675Test_DataProvider */
    private $data;
}

/**
 * Class Refs11675Test_DataProvider
 */
class Refs11675Test_DataProvider
{
    public function getId()
    {
        return 'ST01-00101-101';
    }

    public function getExpectedCreateSpsHashcode()
    {
        return '4e755ea2282b8f4d05c0a16aeb8039a29af525c8';
    }

    public function getExpectedCreateRequestXmlParams()
    {
        return str_replace(PHP_EOL, '', <<<EOF
<?xml version="1.0" encoding="Shift_JIS"?>
<sps-api-request id="ST01-00101-101">
<merchant_id>merchant_id,</merchant_id>
<service_id>service_id,</service_id>
<cust_code>cust_code,</cust_code>
<order_id>order_id,</order_id>
<item_id>item_id,</item_id>
<item_name>item_name,</item_name>
<tax>tax,</tax>
<amount>amount,</amount>
<free1>free1,</free1>
<free2>free2,</free2>
<free3>free3,</free3>
<order_rowno>order_rowno,</order_rowno>
<sps_cust_info_return_flg>sps_cust_info_return_flg,</sps_cust_info_return_flg>
<dtls></dtls>
<pay_method_info>
<cc_number>cc_number,</cc_number>
<cc_expiration>cc_expiration,</cc_expiration>
<security_code>security_code,</security_code>
<cust_manage_flg></cust_manage_flg>
</pay_method_info>
<pay_option_manage>pay_option_manage,</pay_option_manage>
<encrypted_flg>encrypted_flg,</encrypted_flg>
<request_date>request_date,</request_date>
<limit_second>limit_second,</limit_second>
<sps_hashcode>sps_hashcode</sps_hashcode>
</sps-api-request>
EOF
        );
    }

    public function getExpectedReadResponeXml()
    {
        return array(
            'merchant_id' => 'merchant_id,',
            'service_id' => 'service_id,',
            'cust_code' => 'cust_code,',
            'order_id' => 'order_id,',
            'item_id' => 'item_id,',
            'item_name' => 'item_name,',
            'tax' => 'tax,',
            'amount' => 'amount,',
            'free1' => 'free1,',
            'free2' => 'free2,',
            'free3' => 'free3,',
            'order_rowno' => 'order_rowno,',
            'sps_cust_info_return_flg' => 'sps_cust_info_return_flg,',
            'dtls' => '',
            'pay_method_info' => array(
                'cc_number' => 'cc_number,',
                'cc_expiration' => 'cc_expiration,',
                'security_code' => 'security_code,',
                'cust_manage_flg' => ''
            ),
            'pay_option_manage' => 'pay_option_manage,',
            'encrypted_flg' => 'encrypted_flg,',
            'request_date' => 'request_date,',
            'limit_second' => 'limit_second,',
            'sps_hashcode' => 'sps_hashcode'
        );
    }
}

/**
 * [11318][11675][11686][CRE] - [tuan_dung] - [02/06/2016] - CS01_02 - Create common function for SBPS Library - Coding
 */
class Refs11675
{
    /**
     * @param   mixed $params,...
     * @return  string The SHA-1 hash or an empty string
     */
    public function _create_sps_hashcode($params = array())
    {
        if (!is_array($params))
        {
            $params = func_get_args();
        }

        if (empty($params))
        {
            return '';
        }

        $result = '';

        foreach ($params as $v)
        {
            if (is_array($v))
            {
                $result .= $this->_create_sps_hashcode($v);
            }
            elseif (is_scalar($v))
            {
                $result .= trim($v);
            }
        }

        return sha1(trim($result));
    }

    /**
     * @param   array $params
     * @param   string $id
     * @param   string $result
     * @return  string The XML string
     */
    public function _create_request_xml_params(array $params = array(), $id = null, $result = null)
    {
        if (null === $result)
        {
            $result = '<?xml version="1.0" encoding="Shift_JIS"?>'
                . '<sps-api-request' . (empty($id) ? '' : ' id="' . $id . '"') . '>';
        }

        if (!empty($params))
        {
            foreach ($params as $k => $v)
            {
                if (is_array($v))
                {
                    $result .= sprintf('<%s>', $k = trim($k));
                    $result = $this->_create_request_xml_params($v, false, $result);
                    $result .= sprintf('</%s>', $k);
                }
                else
                {
                    $result .= sprintf('<%1$s>%2$s</%1$s>', trim($k), trim($v));
                }
            }
        }

        if (false !== $id)
        {
            $result .= '</sps-api-request>';
        }

        return $result;
    }

    /**
     * @param   mixed $str_xml,...
     * @return  array
     * @throws  \Exception
     * @see     \SimpleXMLElement::__construct
     */
    public function _read_respone_xml($str_xml)
    {
        $simpleXMLElement = new ReflectionClass('SimpleXMLElement');
        $json = str_replace('{}', '""', json_encode((array)$simpleXMLElement->newInstanceArgs(func_get_args())));

        return json_decode($json, true);
    }
}