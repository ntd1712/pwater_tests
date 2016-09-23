<?php

/**
 * Created by PhpStorm.
 * User: dai_duong
 * Date: 9/20/2016
 * Time: 11:41 AM
 */
class Contract_transfer_campaignTest extends \Ci_Framework_TestCase
{
    /**
     * check valid cash back
     * @dataProvider cash_back_amount_data_provider
     */
    public function test_cash_back_amount ($data) {
        foreach ($data as $item) {
            $result = $this->CI->contract_transfer_campaign->valid_cash_back_amount($item['amount']);
            $this->assertEquals($item['status'], $result);
        }
    }
    public function cash_back_amount_data_provider () {
        return array(
                    array (
                        array(
                            array(
                                'amount' => '',    
                                'status' => false,
                            ),
                            array(
                                'amount' => 'abc',
                                'status' => false,
                            ),
                            array(
                                'amount' => '242,210',
                                'status' => true,
                            ),
                            array(
                                'amount' => '242.184',
                                'status' => true,
                            ),
                            array(
                                'amount' => '10000',
                                'status' => true,
                            ),
                        ),
                    )
        );
    }

    /**
     * check valid date
     * @dataProvider cash_back_date_data_provider
     */
    public function test_cash_back_date ($data) {
        foreach ($data as $item) {
            $result = $this->CI->contract_transfer_campaign->valid_cash_back_date($item['date']);
            $this->assertEquals($item['status'], $result);
        }
    }
    public function cash_back_date_data_provider () {
        return array(
            array (
                array(
                    array(
                        'date' => '',
                        'status' => false,
                    ),
                    array(
                        'date' => '2016.10.11',
                        'status' => false,
                    ),
                    array(
                        'date' => '1-10-11',
                        'status' => true,
                    ),
                    array(
                        'date' => '20161011',
                        'status' => true,
                    ),
                    array(
                        'date' => '2016-10-11',
                        'status' => true,
                    ),
                    array(
                        'date' => '2016:10:11',
                        'status' => true,
                    ),
                ),
            )
        );
    }

    /**
     * check insert
     * @dataProvider contract_transfer_campaign_data_provider
     */
    /*public function test_insert_contract_transfer_campaign_by_contract_id ($data) {
        $result = $this->CI->contract_transfer_campaign->_insert_contract_transfer_campaign_by_contract_id($data);
        $this->assertTrue($result);
    }
    public function contract_transfer_campaign_data_provider () {
        return array(
                    array (
                        array(
                            'contract_id'       => '242099',
                            'cash_back_amount'  => '10000',
                            'cash_back_date'    => '2016/10/11',
                        ),
                    )
                );
    }*/

    /**
     * check update
     * @dataProvider update_contract_transfer_campaign_data_provider
     */
    public function test_update_contract_transfer_campaign ($data) {
        $result = $this->CI->contract_transfer_campaign->_update_contract_transfer_campaign_by_contract_id($data);
        $this->assertTrue($result);
    }
    public function update_contract_transfer_campaign_data_provider () {
        return array(
            array (
                array(
                    'contract_id'       => '242099',
                    'cash_back_amount'  => '20000',
                    'cash_back_date'    => '2017/10/11',
                ),
            )
        );
    }
}
