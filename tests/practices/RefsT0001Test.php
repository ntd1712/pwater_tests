<?php

/**
 * @author tuan_dung
 */
class RefsT0001Test extends \Ci_Framework_TestCase
{
    public function test_upload()
    {
        $config = array(
            'upload_path' => FCPATH . 'temp/',
            'allowed_types' => 'jpg',
            'max_size' => '0',
            'file_name' => 'file_name' . date('YmdHis')
        );

        $this->CI->load->library('upload', $config);
        $this->assertInstanceOf('PW_Upload', $this->CI->upload);

        $this->CI->upload->initialize($config);
        $this->assertNotEmpty($this->CI->upload->upload_path);
        $this->assertNotEmpty($this->CI->upload->allowed_types);
        $this->assertNotNull($this->CI->upload->max_size);
        $this->assertNotEmpty($this->CI->upload->file_name);

        // First of all, most of PHP developers fortunately use safe functions to test if a file has been uploaded
        // (is_uploaded_file) or to move an uploaded file (move_uploaded_file).
        // But sadly, in CLI (Command Line Interface) both these methods return FALSE.
        $result = $this->CI->upload->do_upload('userfile');
        $this->assertFalse($result);

        print_r($this->CI->upload->fail_data());
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $_FILES = array(
            'userfile' => array(
                'name' => 'top_logo.jpg',
                'type' => 'image/jpeg',
                'size' => 7238,
                'tmp_name' => FCPATH . 'public/images/logo/top_logo.jpg'
            )
        );
        parent::setUp();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        unset($_FILES);
        parent::tearDown();
    }
}