<?php
namespace SRIO\RestUploadBundle\Tests\Upload;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractUploadTestCase extends WebTestCase
{
    protected function getResource (Client $client, $name)
    {
        $filePath = $this->getResourcePath($client, $name);
        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf(
                'File %s do not exists',
                $filePath
            ));
        }

        return file_get_contents($filePath);
    }

    /**
     * Get resource path.
     *
     * @param Client $client
     * @param $name
     * @return string
     */
    protected function getResourcePath (Client $client, $name)
    {
        return $client->getContainer()->getParameter('kernel.root_dir').'/../../Resources/'.$name;
    }
}