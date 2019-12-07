<?php


interface ParserInterface
{
    public function __construct($content);

    public function parse();

    public function showResult();

    public function getResult();

    public function getName();
}