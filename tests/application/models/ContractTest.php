<?php

/**
 * Class ContractTest
 */
class ContractTest extends \Model_TestCase
{
    /**
     * [11318][12408][12410][CRE] - [tuan_dung] - [07/07/2016] - Create cron request purchase on 06:00 AM - UT Test
     *
     * @dataProvider get_sbps_purchase_delivery_order_data_provider
     */
    public function test_get_sbps_purchase_delivery_order($carriers, $codes, $from, $to, $negative)
    {
        /** @var \CI_DB_result $query */
        $sql = sprintf(<<<EOF
SELECT  order_detail.id
FROM    order_detail JOIN
        contract_order ON contract_order.id = order_detail.contract_order_id JOIN
        contract ON contract.id = contract_order.contract_id
WHERE   contract.`disable` = %3\$d AND
        contract_order.`disable` = %3\$d AND
        order_detail.`disable` = %3\$d AND
        contract.contract_status_id <> %6\$d AND
        contract_order.order_type_code %1\$s AND
        order_detail.settlement_type_id %2\$s AND
        order_detail.shipping_status_code = %4\$d AND
        order_detail.authorize_status = %5\$d
EOF
            , $this->transformInEqual($codes, $negative)
            , $this->transformInEqual($carriers)
            , self::STATUS_ENABLE
            , fetch_code('shipment_status_waiting')
            , Sbps::AUTHORIZE_STATUS_BEFORE_CHECK_CREDIT_LIMIT
            , $this->getContractStatus());

        $sql .= $this->transformDate('order_detail.instruction_plan_date', array('from' => $from, 'to' => $to));
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_purchase_delivery_order($carriers, $codes, $from, $to, $negative);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_purchase_delivery_order_data_provider()
    {
        return array(
            array(
                $carriers = array(12, '13', 14),
                $codes = 7,
                $from = '2016-06-30',
                $to = '',
                $negative = true
            ),
            array(
                13,
                $codes,
                '2016-07-14',
                $to,
                $negative
            ),
            array(
                $carriers,
                $codes,
                $from,
                '2016-06-30 23:59:59',
                $negative
            ),
            array(
                $carriers,
                array(1, '2', 3),
                $from,
                $to,
                false
            )
        );
    }

    /**
     * [11318][11879][11882][CRE] - [tuan_dung] - [15/06/2016] - CS06_04 Export csv to send mail - UT Test
     *
     * @dataProvider get_sbps_notify_cod_data_provider
     */
    public function test_get_sbps_notify_cod($carriers, $codes, $from, $to, $negative)
    {
        $shipment_status_waiting = fetch_code('shipment_status_waiting');
        $this->CI->db
            ->select('order_detail.id, order_detail.delivery_date, customer.last_name, customer.first_name, customer.mail_address_pc, customer.mail_address_mb')
            ->from('order_detail')
            ->join('contract_order', 'contract_order.id = order_detail.contract_order_id')
            ->join_subquery_start('contract', 'contract.id = contract_order.contract_id')
                ->select('id, customer_id')
                ->from('contract')
                ->where('contract.disable', self::STATUS_ENABLE)
                ->{is_array($carriers) ? 'where_in' : 'where'}('settlement_type_id', $carriers)
            ->subquery_close()
            ->join('customer', 'customer.id = contract.customer_id')
            ->join('settlement_type', 'settlement_type.id = order_detail.settlement_type_id')
            ->where('customer.disable', self::STATUS_ENABLE)
            ->where('settlement_type.disable', self::STATUS_ENABLE)
            ->where('settlement_type.column_name', Settlement_type::PAYMENT_COD)
            ->where('order_detail.disable', self::STATUS_ENABLE)
            ->where('order_detail.shipping_status_code', $shipment_status_waiting)
            ->where('contract_order.disable', self::STATUS_ENABLE);

        if ($negative)
        {
            if (is_array($codes))
            {
                $this->CI->db->where_not_in('contract_order.order_type_code', $codes);
            }
            else
            {
                $this->CI->db->where(sprintf("contract_order.order_type_code <> %d", $codes));
            }
        }
        else
        {
            $this->CI->db->{is_array($codes) ? 'where_in' : 'where'}('contract_order.order_type_code', $codes);
        }

        if (empty($to))
        {
            if (!empty($from))
            {
                $this->CI->db->where(sprintf("order_detail.instruction_plan_date = '%s'", addslashes($from)));
            }
        }
        else
        {
            if (!empty($from))
            {
                $this->CI->db->where(sprintf("order_detail.instruction_plan_date >= '%s'", addslashes($from)));
            }

            $this->CI->db->where(sprintf("order_detail.instruction_plan_date <= '%s'", addslashes($to)));
        }

        $query = $this->CI->db->get();
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_notify_cod($carriers, $codes, $from, $to, $negative);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_notify_cod_data_provider()
    {
        return array(
            array(
                $carriers = array(12, '13', 14),
                $codes = 7,
                $from = '2016-06-09',
                $to = '',
                $negative = true
            ),
            array(
                12,
                $codes,
                '2016-06-09',
                $to,
                $negative
            ),
            array(
                $carriers,
                $codes,
                $from,
                '2016-06-30 23:59:59',
                $negative
            ),
            array(
                $carriers,
                array(1, '2', 3),
                $from,
                $to,
                false
            )
        );
    }

    /**
     * [11318][11846][11878][CRE] - [tuan_dung] - [14/06/2016] - CS06_02 Request purchase for add fee - UT Test
     *
     * @dataProvider get_sbps_purchase_fee_and_payment_request_data_provider
     */
    public function test_get_sbps_purchase_fee_and_payment_request($carriers, $codes, $from, $to)
    {
        /** @var \CI_DB_result $query */
        $sql = sprintf(<<<EOF
SELECT  order_detail.id
FROM    order_detail JOIN
        order_settlement ON order_detail.id = order_settlement.order_detail_id JOIN
        contract_order ON contract_order.id = order_detail.contract_order_id JOIN
        contract ON contract.id = contract_order.contract_id
WHERE   contract.`disable` = %3\$d AND
        contract_order.`disable` = %3\$d AND
        order_detail.`disable` = %3\$d AND
        order_settlement.`disable` = %3\$d AND
        contract.contract_status_id <> %5\$d AND
        contract_order.order_type_code %1\$s AND
        order_settlement.settlement_type_id %2\$s AND
        order_detail.authorize_status = %4\$d
EOF
            , $this->transformInEqual($codes)
            , $this->transformInEqual($carriers)
            , self::STATUS_ENABLE
            , Sbps::AUTHORIZE_STATUS_BEFORE_CHECK_CREDIT_LIMIT
            , $this->getContractStatus());

        $sql .= $this->transformDate('order_settlement.settlement_plan_date', array('from' => $from, 'to' => $to));
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_purchase_fee_and_payment_request($carriers, $codes, $from, $to);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_purchase_fee_and_payment_request_data_provider()
    {
        return array(
            array(
                $carriers = array(12, '13', 14),
                $codes = 7,
                $from = '',
                $to = '2016-07-01'
            ),
            array(
                12,
                '7',
                $from,
                'now'
            ),
            array(
                $carriers,
                $codes,
                $from = '2016-05-04',
                $to
            )
        );
    }

    /**
     * [11318][11846][11878][CRE] - [tuan_dung] - [14/06/2016] - CS06_02 Request purchase for add fee - UT Test
     *
     * @dataProvider get_sbps_payment_data_provider
     */
    public function test_get_sbps_payment($carriers, $from, $to)
    {
        /** @var \CI_DB_result $query */
        $sql = sprintf(<<<EOF
SELECT  order_detail.id
FROM    order_detail JOIN
        order_settlement ON order_detail.id = order_settlement.order_detail_id
WHERE   order_detail.`disable` = %2\$d AND
        order_settlement.`disable` = %2\$d AND
        order_settlement.settlement_type_id %1\$s AND
        order_settlement.settlement_status_code = %3\$d AND
        order_detail.authorize_status = %4\$d
EOF
            , $this->transformInEqual($carriers)
            , self::STATUS_ENABLE
            , fetch_code('settlement_status_charging_request_before')
            , Sbps::AUTHORIZE_STATUS_CHECK_CREDIT_LIMIT_OK);

        $sql .= $this->transformDate('order_settlement.settlement_plan_date', array('from' => $from, 'to' => $to));
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_payment($carriers, 7, $from, $to);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_payment_data_provider()
    {
        return array(
            array(
                $carriers = array('13', 14),
                $from = '',
                $to = '2016-07-01'
            ),
            array(
                12,
                $from,
                'now'
            ),
            array(
                $carriers,
                $from = '2016-05-04',
                $to
            )
        );
    }

    /**
     * [11318][12179][12191][CRE] - [tuan_dung] - [28/06/2016] - Cron JOB after cron create cycle order - UT Test
     *
     * @dataProvider get_sbps_purchase_cyclic_delivery_data_provider
     */
    public function test_get_sbps_purchase_cyclic_delivery($carriers, $codes, $from, $to)
    {
        /** @var \CI_DB_result $query */
        $sql = sprintf(<<<EOF
SELECT  order_detail.id
FROM    order_detail JOIN
        contract_order ON contract_order.id = order_detail.contract_order_id JOIN
        contract ON contract.id = contract_order.contract_id
WHERE   contract.`disable` = %3\$d AND
        contract_order.`disable` = %3\$d AND
        order_detail.`disable` = %3\$d AND
        contract.contract_status_id <> %6\$d AND
        contract_order.order_type_code %1\$s AND
        order_detail.settlement_type_id %2\$s AND
        order_detail.shipping_status_code = %4\$d AND
        order_detail.authorize_status = %5\$d
EOF
            , $this->transformInEqual($codes)
            , $this->transformInEqual($carriers)
            , self::STATUS_ENABLE
            , fetch_code('shipment_status_waiting')
            , Sbps::AUTHORIZE_STATUS_BEFORE_CHECK_CREDIT_LIMIT
            , $this->getContractStatus());

        $sql .= $this->transformDate('order_detail.instruction_plan_date', array('from' => $from, 'to' => $to), true);
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_purchase_cyclic_delivery($carriers, $codes, $from, $to);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_purchase_cyclic_delivery_data_provider()
    {
        return array(
            array(
                $carriers = array('13', 14),
                $codes = array(1, '2'),
                $from = '2016-05-01',
                ''
            ),
            array(
                13,
                2,
                $from,
                ''
            ),
            array(
                $carriers,
                $codes,
                $from,
                '2016-07-28 23:59:59'
            )
        );
    }

    /**
     * [11318][12119][12121][CRE] - [tuan_dung] - [23/06/2016] - Cron - CS06_05 Request cancel - refunded and re- request purchase - UT Test
     *
     * @dataProvider get_sbps_recheck_carrier_credit_limit_data_provider
     */
    public function test_get_sbps_recheck_carrier_credit_limit($carriers, $codes, $from, $to, $negative, $trails)
    {
        /** @var \CI_DB_result $query */
        if (false !== $trails)
        {
            $sql = sprintf(<<<EOF
 JOIN (
    SELECT  order_detail_id
    FROM    settlement_sbps_detail_log_purchase
    WHERE   `disable` = %2\$d AND `status` %1\$s
 ) settlement_sbps_detail_log_purchase ON order_detail.id = settlement_sbps_detail_log_purchase.order_detail_id
EOF
                , $this->transformInEqual($trails)
                , self::STATUS_ENABLE);
        }

        $sql = sprintf(<<<EOF
SELECT  order_detail.id
FROM    order_detail JOIN
        contract_order ON contract_order.id = order_detail.contract_order_id JOIN
        contract ON contract.id = contract_order.contract_id %7\$s
WHERE   contract.`disable` = %3\$d AND
        contract_order.`disable` = %3\$d AND
        order_detail.`disable` = %3\$d AND
        contract.contract_status_id <> %6\$d AND
        contract_order.order_type_code %1\$s AND
        order_detail.settlement_type_id %2\$s AND
        order_detail.shipping_status_code = %4\$d AND
        order_detail.authorize_status = %5\$d
EOF
            , $this->transformInEqual($codes, $negative)
            , $this->transformInEqual($carriers)
            , self::STATUS_ENABLE
            , fetch_code('shipment_status_waiting')
            , Sbps::AUTHORIZE_STATUS_CHECK_CREDIT_LIMIT_OK
            , $this->getContractStatus()
            , isset($sql) ? $sql : '');

        $sql .= $this->transformDate('order_detail.instruction_plan_date', array('from' => $from, 'to' => $to), true);
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_recheck_carrier_credit_limit($carriers, $codes, $from, $to, $negative, $trails);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_recheck_carrier_credit_limit_data_provider()
    {
        return array(
            array(
                $carriers = array('13', 14),
                $codes = 7,
                $from = '2016-06-30',
                $to = '',
                $negative = true,
                $trails = false
            ),
            array(
                14,
                $codes,
                '2016-07-14',
                $to,
                $negative,
                $trails
            ),
            array(
                $carriers,
                $codes,
                $from,
                '2016-06-30 23:59:59',
                $negative,
                $trails
            ),
            array(
                $carriers,
                array(1, '2', 3),
                $from,
                $to,
                false,
                $trails
            ),
            array(
                $carriers,
                $codes,
                $from,
                $to,
                $negative,
                array(1, '2', 3)
            )
        );
    }

    /**
     * [11318][11831][11832][CRE] - [tuan_dung] - [13/06/2016] - CS06_01 Cron - Request purchase - UT Test
     *
     * @dataProvider get_sbps_purchase_data_provider
     */
    public function test_get_sbps_purchase($carriers, $codes, $from, $to, $negative)
    {
        /** @var \CI_DB_result $query */
        $sql = sprintf(<<<EOF
SELECT  order_detail.id
FROM    order_detail JOIN
        contract_order ON contract_order.id = order_detail.contract_order_id
WHERE   contract_order.`disable` = %3\$d AND
        order_detail.`disable` = %3\$d AND
        contract_order.order_type_code %1\$s AND
        order_detail.settlement_type_id %2\$s AND
        order_detail.shipping_status_code = %4\$d AND
        order_detail.authorize_status = %5\$d
EOF
            , $this->transformInEqual($codes, $negative)
            , $this->transformInEqual($carriers)
            , self::STATUS_ENABLE
            , fetch_code('shipment_status_waiting')
            , Sbps::AUTHORIZE_STATUS_BEFORE_CHECK_CREDIT_LIMIT);

        $sql .= $this->transformDate('order_detail.instruction_plan_date', array('from' => $from, 'to' => $to), true);
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->contract->get_sbps_purchase($carriers, $codes, $from, $to, $negative);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_sbps_purchase_data_provider()
    {
        return array(
            array(
                $carriers = array('13', 14),
                $codes = 7,
                $from = '2016-05-01',
                $to = '',
                $negative = true
            ),
            array(
                13,
                $codes,
                $from,
                '',
                $negative
            ),
            array(
                $carriers,
                $codes,
                $from,
                '2016-07-28 23:59:59',
                $negative
            ),
            array(
                $carriers,
                array(1, '2', 3),
                $from,
                $to,
                false
            )
        );
    }

    /**
     * [11318][11795][11827][CRE] - [tuan_dung] - [13/06/2016] - Admin - CS05_05 Report - UT Test
     *
     * @dataProvider get_carrier_ng_credits_data_provider
     */
    public function test_get_carrier_ng_credits($params, $carriers, $codes)
    {
        $this->markTestSkipped();

        // $query = $this->CI->contract->get_carrier_ng_credits($params, $carriers, $codes);
        // $this->assertInstanceOf('CI_DB_result', $query);
        //
        // /** @var array $actual */
        // $actual = $query->row_array();
        // $query->free_result();
        //
        // if (!empty($actual))
        // {
        //     $this->assertArrayHasKey('id', $actual);
        //     $this->assertArrayHasKey('contract_id', $actual);
        //     $this->assertArrayHasKey('settlement_type_name', $actual);
        // }
        // else
        // {
        //     echo __FUNCTION__ . ' : no data' . PHP_EOL . PHP_EOL;
        // }
    }

    public function get_carrier_ng_credits_data_provider()
    {
        return array(
            array(
                array(),
                $carriers = array(12, '13', 14),
                $codes = array(3, 4)
            ),
            array(
                array(
                    'from_date' => $from = '2016-05-01'
                ),
                array(13, '14'),
                array(3, '4')
            ),
            array(
                array(
                    'to_date' => $to = date('Y-m-d', strtotime('last day of')) . ' 23:59:59'
                ),
                array(14),
                array('3')
            ),
            array(
                array(
                    'from_date' => $from,
                    'to_date' => $to
                ),
                $carriers,
                $codes
            ),
            array(
                array(
                    'from_date' => $from,
                    'to_date' => $to
                ),
                '14',
                '3'
            )
        );
    }

    /**
     * @param   string $status
     * @return  int
     */
    protected function getContractStatus($status = Contract_status::CSTS_TM_CARRIER_DEFICIENCIES)
    {
        $contract_status = $this->CI->contract_status->get_status_by_column_name($status);
        return is_array($contract_status) && isset($contract_status['id']) ? (int)$contract_status['id'] : 0;
    }

    /**
     * @param   string $left
     * @param   array $right
     * @param   bool $negative
     * @return  string
     */
    protected function transformDate($left, array $right, $negative = false)
    {
        $sql = '';

        if (empty($right['to']))
        {
            if (!empty($right['from']))
            {
                $sql .= PHP_EOL . sprintf($negative ? " AND %s >= '%s'" : " AND %s = '%s'", $left, addslashes($right['from']));
            }
        }
        else
        {
            if (!empty($right['from']))
            {
                $sql .= PHP_EOL . sprintf(" AND %s >= '%s'", $left, addslashes($right['from']));
            }

            $sql .= PHP_EOL . sprintf(" AND %s <= '%s'", $left, addslashes($right['to']));
        }

        return str_replace("'now'", "'" . date('Y-m-d') . "'", $sql);
    }

    /**
     * @param   int|int[] $var
     * @param   bool $negative
     * @return  string
     */
    protected function transformInEqual($var, $negative = false)
    {
        if (!is_array($var))
        {
            $var = array($var);
        }

        return 1 < count($var)
            ? sprintf($negative ? "NOT IN (%s)" : "IN (%s)", implode(',', $var))
            : sprintf($negative ? '<> %d' : '= %d', reset($var));
    }

    /** @var int */
    const STATUS_ENABLE = 0;
}