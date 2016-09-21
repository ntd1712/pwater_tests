<?php

/**
 * Class SbpsTest
 */
class SbpsTest extends \Model_TestCase
{
    /**
     * [11318][12027][12036][CRE] - [tuan_dung] - [21/06/2016] - CS01_09 - Write log to settlement_sbps_detail_log_purchase table - UT Test
     *
     * @dataProvider update_settlement_sbps_detail_log_purchase_data_provider
     */
    public function test_create_settlement_sbps_detail_log_purchase($params)
    {
        $actual = $this->CI->sbps->create_settlement_sbps_detail_log_purchase($params);
        $this->assertInternalType('int', $actual);

        /** @var \CI_DB_result $query */
        $query = $this->CI->db->where('id', $id = $actual)->get(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE);
        $actual = $query->num_rows();
        $query->free_result();
        $this->CI->db->where('id', $id)->delete(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE);
        $this->assertNotEmpty($actual);
    }

    /**
     * @expectedException \Exception
     */
    public function test_failure_create_settlement_sbps_detail_log_purchase()
    {
        $this->CI->sbps->create_settlement_sbps_detail_log_purchase();
    }

    /**
     * [11318][12027][12036][CRE] - [tuan_dung] - [21/06/2016] - CS01_09 - Write log to settlement_sbps_detail_log_purchase table - UT Test
     *
     * @dataProvider update_settlement_sbps_detail_log_purchase_data_provider
     */
    public function test_update_settlement_sbps_detail_log_purchase($params)
    {
        /** @var \CI_DB_result $query */
        $query = $this->CI->db->select_min('id')->get(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE, 1);
        $model = $query->row();
        $query->free_result();

        $params['request_date'] = date('YmdHis');
        $actual = $this->CI->sbps->update_settlement_sbps_detail_log_purchase($params, $model->id);
        $this->assertInternalType('int', $actual);

        $query = $this->CI->db->where('id', $model->id)->get(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE);
        $actual = $query->row();
        $query->free_result();
        $this->assertEquals($params['request_date'], $actual->request_date);
    }

    public function update_settlement_sbps_detail_log_purchase_data_provider()
    {
        return array(
            array(
                array(
                    'order_detail_id' => 4706260,
                    'settlement_type_id' => 12,
                    'status' => 1,
                    'settlement_amount' => 0,
                    'lastup_account_id' => 0,
                    'create_datetime' => date('Ymd His'),
                    'lastup_datetime' => date('Ymd His'),
                    'disable' => 0,
                    // request
                    'merchant_id' => '71743',
                    'service_id' => '001',
                    'tracking_id' => '32121646963879',
                    'cust_code' => 'SPSTestUser0001',
                    'order_id' => md5(uniqid()),
                    'item_id' => 'L_0001',
                    'item_name' => mb_convert_encoding('商品名称Lac', 'Shift_JIS', 'UTF-8'),
                    'tax' => 0,
                    'amount' => rand(1000, 9999),
                    'free1' => '',
                    'free2' => '',
                    'free3' => '',
                    'order_rowno' => '',
                    'request_date' => date('YmdHis'),
                    'limit_second' => '',
                    'sps_hashcode' => 'c3294c4621f64895b611dc27cd2996672f323d25',
                    // response
                    'res_result' => 'NG',
                    'res_sps_transaction_id' => '',
                    'res_tracking_id' => '',
                    'res_process_date' => '',
                    'res_err_code' => '40503001',
                    'res_date' => date('YmdHis')
                )
            )
        );
    }

    /**
     * [11318][12024][12293][CRE] - [tuan_dung] - [02/07/2016] - CS01_09 - Save payment log to settlement_sbps_detail_log_purchase_transaction table - UT Test
     *
     * @dataProvider get_settlement_sbps_detail_log_purchase_by_tracking_ids_data_provider
     */
    public function test_get_settlement_sbps_detail_log_purchase_by_tracking_ids($res_tracking_ids)
    {
        /** @var \CI_DB_result $query */
        $sql = sprintf(<<<EOF
SELECT  id, order_detail_id, `status`, order_id, res_result
FROM    settlement_sbps_detail_log_purchase
WHERE   `disable` = %d AND res_tracking_id %s
EOF
            , self::STATUS_ENABLE
            , sprintf('IN (%s)', '"' . implode('","', is_array($res_tracking_ids) ? $res_tracking_ids : array($res_tracking_ids)) . '"'));
        $query = $this->CI->db->query($sql);
        $collection = $query->row_array();
        $query->free_result();

        /** @var array $actual */
        $query = $this->CI->sbps->get_settlement_sbps_detail_log_purchase_by_tracking_ids($res_tracking_ids);
        $actual = $query->row_array();
        $query->free_result();

        $this->assertInstanceOf('CI_DB_result', $query);
        $this->assertEquals($collection, $actual);
    }

    public function get_settlement_sbps_detail_log_purchase_by_tracking_ids_data_provider()
    {
        return array(
            array(
                ''
            ),
            array(
                array('', 3577755),
            )
        );
    }

    /**
     * [11318][12024][12293][CRE] - [tuan_dung] - [02/07/2016] - CS01_09 - Save payment log to settlement_sbps_detail_log_purchase_transaction table - UT Test
     *
     * @dataProvider update_log_purchase_transaction_data_provider
     */
    public function test_insert_log_purchase_transaction($params)
    {
        $actual = $this->CI->sbps->insert_log_purchase_transaction($params);
        $this->assertInternalType('int', $actual);

        /** @var \CI_DB_result $query */
        $query = $this->CI->db->where('id', $id = $actual)->get(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE_TRANSACTION);
        $actual = $query->num_rows();
        $query->free_result();
        $this->CI->db->where('id', $id)->delete(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE_TRANSACTION);
        $this->assertNotEmpty($actual);
    }

    /**
     * @expectedException \Exception
     */
    public function test_failure_insert_log_purchase_transaction()
    {
        $this->CI->sbps->insert_log_purchase_transaction(array());
    }

    /**
     * [11318][12024][12293][CRE] - [tuan_dung] - [02/07/2016] - CS01_09 - Save payment log to settlement_sbps_detail_log_purchase_transaction table - UT Test
     *
     * @dataProvider update_log_purchase_transaction_data_provider
     */
    public function test_update_log_purchase_transaction($params)
    {
        /** @var \CI_DB_result $query */
        $query = $this->CI->db->select_min('id')->limit(1)->get(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE_TRANSACTION);
        $model = $query->row();
        $query->free_result();

        $params['request_date'] = date('YmdHis');
        $actual = $this->CI->sbps->update_log_purchase_transaction($model->id, $params);
        $this->assertInternalType('int', $actual);

        $query = $this->CI->db->where('id', $model->id)->get(self::TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE_TRANSACTION);
        $actual = $query->row();
        $query->free_result();
        $this->assertEquals($params['request_date'], $actual->request_date);
    }

    public function update_log_purchase_transaction_data_provider()
    {
        return array(
            array(
                array(
                    'order_detail_id' => 5264413,
                    'settlement_sbps_detail_log_purchase_id' => 1,
                    'type' => 1,
                    'merchant_id' => '',
                    'service_id' => '',
                    'sps_transaction_id' => '',
                    'tracking_id' => '',
                    'amount' => 0,
                    // request
                    'request_date' => date('YmdHis'),
                    'limit_second' => '',
                    'sps_hashcode' => 'c3294c4621f64895b611dc27cd2996672f323d25',
                    // response
                    'res_result' => 'NG',
                    'res_sps_transaction_id' => '',
                    'res_tracking_id' => '',
                    'res_cancel_type' => '',
                    'res_process_date' => '',
                    'res_err_code' => '10131005',
                    'res_date' => date('YmdHis')
                )
            )
        );
    }

    /** @var string */
    const TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE = 'settlement_sbps_detail_log_purchase';
    const TBL_SETTLEMENT_SBPS_DETAIL_LOG_PURCHASE_TRANSACTION = 'settlement_sbps_detail_log_purchase_transaction';
    /** @var int */
    const STATUS_ENABLE = 0;
}