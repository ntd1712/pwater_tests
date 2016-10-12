<?php

/**
 * Class ContractTest
 */
class ContractTest extends \Model_TestCase
{
    /**
     * @dataProvider update_contract_option_product_trust_pack_data_provider
     * */
    public function test_update_contract_option_product_trust_pack ($data) {
        foreach ($data as $item) {
            if ($item['cancel_datetime'] == '0000-00-00 00:00:00') {
                $item['cancel_datetime'] = '2016-12-16';
                $this->assertTrue($this->CI->contract->update_contract_option_product_trust_pack($item, $item['id']));
            }
        }
    }
    public function update_contract_option_product_trust_pack_data_provider () {
        return array(
            array (
                array(
                    array(
                        'id'    => '33619',
                        'contract_id'       => '242256',
                        'cancel_datetime'  => '2016-09-22 00:00:00',
                        'product_id'    => '200',
                    ),
                    array(
                        'id'    => '33620',
                        'contract_id'       => '242256',
                        'cancel_datetime'  => '0000-00-00 00:00:00',
                        'product_id'    => '200',
                    ),
                ),
            )
        );
    }
}