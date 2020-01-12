<?php

require_once __DIR__.'/ParserInterface.php';
require_once __DIR__.'/BaseParser.php';

class ImageParser extends BaseParser
{
    public $content;
    public $parser;

    public function __construct($url, $content)
    {
        parent::__construct($url, $content);
        $this->src_regex = [
                "/src=\".{0,}?\.png\"/",
                "/src=\".{0,}?\.jpg\"/",
                "/src=\".{0,}?\.jpeg\"/",
        ];
        $this->tag_replace_string = ['src=', '"'];
        $this->name = 'Images';
    }

}