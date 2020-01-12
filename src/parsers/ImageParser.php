<?php

require_once __DIR__.'/ParserInterface.php';
require_once __DIR__.'/BaseParser.php';

class ImageParser extends BaseParser
{
    public $content;
    public $parser;

    public function __construct($content)
    {
        parent::__construct($content);
        $this->tag_regex = '/<img .{0,1000}>/';
        $this->src_regex = '/((href|src)=".{0,}((png)|(jpg)|(jpeg)))/';
        $this->tag_replace_string = ['src=', 'href=', '"', '/>'];
        $this->name = 'Images';
    }

}