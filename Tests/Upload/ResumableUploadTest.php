<?php
namespace SRIO\RestUploadBundle\Tests\Upload;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ResumableUploadTest extends AbstractUploadTestCase
{
    public function testCompleteUpload()
    {
        $client = $this->startSession();

        $response = $client->getResponse();
        $location = $response->headers->get('Location');
        $content = $this->getResource($client, 'apple.gif');
        $client->request('PUT', $location, array(), array(), array(
            'CONTENT_TYPE' => 'image/gif',
            'CONTENT_LENGTH' => strlen($content)
        ), $content);

        $response = $client->getResponse();
        $this->assertSuccessful($response, $content);
    }

    protected function startSession ()
    {
        $client = static::createClient();
        $content = $this->getResource($client, 'apple.gif');
        $parameters = array('name' => 'test');
        $json = json_encode($parameters);

        $client->request('POST', '/upload?uploadType=resumable', array(), array(), array(
            'CONTENT_TYPE' => 'application/json',
            'CONTENT_LENGTH' => strlen($json),
            'HTTP_X-Upload-Content-Type' => 'image/gif',
            'HTTP_X-Upload-Content-Length' => strlen($content)
        ), $json);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertEquals(0, $response->headers->get('Content-Length', 0));

        return $client;
    }

    protected function assertSuccessful (Response $response, $content)
    {
        $jsonContent = json_decode($response->getContent(), true);
        $this->assertNotEmpty($jsonContent);
        $this->assertFalse(array_key_exists('errors', $jsonContent));
        $this->assertTrue(array_key_exists('path', $jsonContent));
        $this->assertTrue(array_key_exists('size', $jsonContent));
        $this->assertTrue(array_key_exists('name', $jsonContent));
        $this->assertEquals('test', $jsonContent['name']);
        $this->assertEquals(strlen($content), $jsonContent['size']);
        $this->assertTrue(file_exists($jsonContent['path']));
        $this->assertEquals($content, file_get_contents($jsonContent['path']));
    }
}
