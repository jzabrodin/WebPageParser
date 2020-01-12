<?php

require_once __DIR__.'/ParserInterface.php';
require_once __DIR__.'/BaseParser.php';

class LinksParser extends BaseParser
{
    public $content;

    public function __construct($url, $content)
    {
        parent::__construct($url, $content);
        $this->src_regex = ["/href=\".*?\"/"];
        $this->tag_replace_string = ['href=', '"', '////'];
        $this->name = 'Links';
    }
}