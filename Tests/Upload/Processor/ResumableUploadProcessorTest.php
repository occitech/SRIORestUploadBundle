<?php
namespace SRIO\RestUploadBundle\Tests\Upload\Processor;

use SRIO\RestUploadBundle\Exception\UploadProcessorException;
use SRIO\RestUploadBundle\Upload\Processor\ResumableUploadProcessor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResumableUploadProcessorTest extends AbstractProcessorTestCase
{
    /**
     * @dataProvider contentSuccessRangeDataProvider
     */
    public function testSuccessComputeContentRange ($string, $start, $end, $length)
    {
        $result = $this->callParseContentRange($string);
        $this->assertTrue(is_array($result));
        $this->assertEquals($start, $result['start']);
        $this->assertEquals($end, $result['end']);
        $this->assertEquals($length, $result['total']);
    }

    /**
     * @dataProvider contentErrorRangeDataProvider
     * @expectedException \SRIO\RestUploadBundle\Exception\UploadProcessorException
     * @param $string
     */
    public function testErrorComputeContentRange ($string)
    {
        $this->callParseContentRange($string);
    }

    /**
     * Call parseContentRange function.
     *
     * @param $string
     * @return mixed
     */
    protected function callParseContentRange ($string)
    {
        $method = $this->getMethod('\SRIO\RestUploadBundle\Upload\Processor\ResumableUploadProcessor', 'parseContentRange');
        $em = $this->getMockEntityManager();
        $uploadProcessor = new ResumableUploadProcessor($em);
        return $method->invokeArgs($uploadProcessor, array($string));
    }

    /**
     * Data Provider for success Content-Range test.
     *
     */
    public function contentSuccessRangeDataProvider ()
    {
        return array(
            array('bytes 1-2/12', 1, 2, 12),
            array('bytes */1000', '*', null, 1000),
            array('bytes 0-1000/1000', 0, 1000, 1000)
        );
    }

    /**
     * Data Provider for error Content-Range test.
     *
     */
    public function contentErrorRangeDataProvider ()
    {
        return array(
            array('bytes 2-1/12'),
            array('bytes 12/12'),
            array('bytes 0-13/12'),
            array('1-2/12')
        );
    }
} 