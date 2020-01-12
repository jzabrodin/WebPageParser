<?php

require_once __DIR__.'/../src/parsers/ImageParser.php';
require_once __DIR__.'/../src/parsers/ParserInterface.php';
require_once __DIR__.'/../src/parsers/LinksParser.php';

class RunnerTest extends PHPUnit\Framework\TestCase
{

    public $data_for_link_parser;
    public $data_for_image_parser;

    /** @test
     * @return void
     * imageParser
     */
    public function imageParserTest()
    {

        $image_parser = new ImageParser($this->data_for_image_parser);
        $parse_result = $image_parser->parse();
        $result_size = count($parse_result);
        $this->assertTrue($result_size > 0);
        $this->assertEquals($result_size, 1);
        $image_parser->showResult();

    }

    /** @test
     * @return void
     * linksParserTest
     */
    public function linksParserTest()
    {
        $linksParser = new LinksParser($this->data_for_link_parser);
        $parseResult = $linksParser->parse();
        $this->assertTrue($parseResult > 0);
        $linksParser->showResult();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->data_for_image_parser = '
        <a class="ltv-calc-button header-sign visible-sm-inline visible-md-inline visible-lg-inline text-center" target="_blank" href="https://netpeak.ua/software/lifetime-value-calculator/">
            <div class="header-ltv-sign transition_03">
              <i style="font-size: 23px" class="netpeak-calculator"></i><br>
              <span>LTV</span>
            </div>
          </a>
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
            <img width="34" height="34" src="https://cdn.netpeak.net/img/new-design/mob-menu.png">
          </button>
          <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
            <img width="34" height="34" src="https://cdn.netpeak.net/img/new-design/mob-menu.js">
            <img width="34" height="34" src="https://cdn.netpeak.net/img/new-design/mob-menu.css">
          </button>
        ';

        $this->data_for_link_parser = '
                    <a href="https://netpeak.ua/services/seo/">
                    <div class="flipper-front">
                        <div>
                            <i class="netpeak-seo"></i>
                        </div>
                    <div class="flipper-back">
                            <span class="mini-description"><p> Действия, направленные на улучшение видимости вашего сайта в тематиках, которым он действительно соответствует </p>
                    </div>
                    </div>
                    </a>
                    </li >
                    <li data - filter - item="1|2|3|4|5">
                    <a
                        class="flip-container"
                        href="https://netpeak.ua/services/ppc/"
                    >
                                        <a href="https://netpeak.ua/services/seo2/">
        ';
    }
}