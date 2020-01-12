<?php

require_once __DIR__.'/ParserInterface.php';
require_once __DIR__.'/BaseParser.php';

class LinksParser extends BaseParser
{
    public $content;

    public function __construct($content)
    {
        parent::__construct($content);
        $this->tag_regex = '/<a .{0,1000}>/';
        $this->src_regex = '/(href=".{0,}">)/';
        $this->tag_replace_string = ['href=', '"', '/>'];
        $this->name = 'Links';
    }
}