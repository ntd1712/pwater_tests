<?php

/**
 * Created by PhpStorm.
 * User: dai_duong
 * Date: 9/19/2016
 * Time: 4:43 PM
 */
require_once ( __DIR__.'/../../../../../pwater/application/widgets/_admin/controllers/cash_back_manager.php');
class cash_back_managerTest extends \Ci_Framework_TestCase
{
    protected $_object;

    protected $_data;

    public function setUp() {
        parent::setUp();
        $this->_object = new cash_back_manager();
    }
    /**
     * test get data from csv file and check status
     * @dataProvider empty_contract_id_data_provider
     */
    /*public function test_get_cash_back_table_by_csv ($files) {
        foreach ($files as $file) {
            $temp_dir = __DIR__.'\testUpload'.'\\'.$file['file_name'];
            $file_handle =  file($temp_dir, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            $csv_data = array();
            unset($file_handle[0]);
            if (!empty($file_handle)) {
                foreach ($file_handle as $line) {
                    $csv_data[] =  $this->_object->format_cash_back_row(str_getcsv($line));
                }
            }
            foreach ($csv_data as $item) {
                $this->assertEquals($file['expected_result'], $item['status']);
            } 
        }
    }*/
    public function empty_contract_id_data_provider (){
        $files =array(
            array (
                array (
                    /*array(
                        'file_name' => 'contract_id_outside.csv',
                        'file_type' => 'text/plain',
                        'file_path' => '/media/sf_www/pwater/temp/',
                        'full_path' => '/media/sf_www/pwater/temp/wrong_contract_id.csv',
                        'raw_name' => 'wrong_contract_id',
                        'orig_name' => 'wrong_contract_id.csv',
                        'client_name' => 'wrong_contract_id.csv',
                        'file_ext' => '.csv',
                        'file_size' => '0.13',
                        'is_image' => '',
                        'image_width' => '',
                        'image_height' => '',
                        'image_type' => '',
                        'image_size_str' => '',
                    ),*/
                   /* array(
                        'file_name'         => 'contract_id_disabled.csv',
                        'expected_result'   => 'Not_Update',
                    ),
                    array(
                        'file_name' => 'contract_id_outside.csv',
                        'expected_result'   => 'Not_Update',
                    ),*/
                    array(
                        'file_name' => 'empty_cash.csv',
                        'expected_result'   => 'NG',
                    ),
                    /*array(
                        'file_name' => 'empty_contract_id.csv',
                        'expected_result'   => 'NG',
                    ),
                    array(
                        'file_name' => 'empty_date.csv',
                        'expected_result'   => 'NG',
                    ),
                    array(
                        'file_name' => 'rigth_structure.csv',
                        'expected_result'   => 'Update',
                    ),*/
                ),
            ),
        );
        return $files;
    }

    /**
     * test contract id
     * @dataProvider data_contract_id_data_provider
     */
    public function test_contract_id ($data) {
        $result = $this->_object->valid_contract_id ($data);
        $this->assertEquals($data, $result);
    }
    public function data_contract_id_data_provider () {
        return array(
            array(
                array(
                    array(
                        'contract_id'   => '',
                        'cash_back_amount' => '10000',
                        'cash_back_date'   => '2016-10-11',
                        'status'        => 'NG',
                        'content'       => '',
                    ),
                    array(
                        'contract_id'   => 'abc',
                        'cash_back_amount' => '',
                        'cash_back_date'   => '2016-10-11',
                        'status'        => 'NG',
                        'content'       => '',
                    ),
                    array(
                        'contract_id'   => '2222222222',
                        'cash_back_amount' => '',
                        'cash_back_date'   => '',
                        'status'        => 'Not_Update',
                        'content'       => '',
                    ),
                    array(
                        'contract_id'   => '241827',
                        'cash_back_amount' => '10000',
                        'cash_back_date'   => '2016.10.11',
                        'status'        => 'Not_Update',
                        'content'       => '',
                    ),
                    array(
                        'contract_id'   => '242256',
                        'cash_back_amount' => '10000',
                        'cash_back_date'   => '20161011',
                        'status'        => 'Insert',
                        'content'       => '',
                    ),
                    array(
                        'contract_id'   => '242.256',
                        'cash_back_amount' => '10000',
                        'cash_back_date'   => '',
                        'status'        => 'NG',
                        'content'       => '',
                    ),
                ),
            )
        );
    }
}
