<?php

require_once __DIR__ . '/../Runner.php';
require_once __DIR__ . '/../src/Files.php';


class RunnerTest extends PHPUnit\Framework\TestCase
{

    public $runner;
    public $urls;
    public $correct_urls;

    /** @test
     * @return void
     * connection test
     */
    public function connectionTest()
    {
        foreach ($this->urls as $url) {
            $this->runner = new Runner($url);
            $connection = $this->runner->getConnection();
            $this->assertNotFalse($connection);
        }
    }

    /** @test
     * @return void
     * @depends connectionTest
     */
    public function contentTest()
    {
        foreach ($this->correct_urls as $url) {
            $runner = new Runner($url);
            $content = $runner->getPageContent();
            $this->assertNotEmpty($content);
        }
    }

    /** @test
     * @return void
     * file_write_test
     * @depends contentTest
     */
    public function fileWriteTest()
    {
        foreach ($this->urls as $url) {
            $runner = new Files($url);
            $result = $runner->savePageContent("\n hello!!! \n" . $url);
            $this->assertNotFalse($result);
        }
    }

    /** @test
     * @return void
     * completeTest
     */
    public function complete_test()
    {
        foreach ($this->correct_urls as $url) {
            $runner = new Runner($url);
            $runner->run();
            $this->assertFalse(count($runner->errors) > 0);
        }
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->urls = [
            "www.netpeak.ua",
            "http.netpeak.ua",
            "http://www.netpeak.ua",
            "wwwnetpeak.ua",
        ];

        $this->correct_urls = [
                "https://netpeak.ua/",
        ];
    }
}