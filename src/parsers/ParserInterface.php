<?php


interface ParserInterface
{
    public function __construct($url, $content);

    public function parse();

    public function showResult();

    public function getResult();

    public function getName();

    public function getLinksFromTag(array $matches);

    public function validate($data);

}