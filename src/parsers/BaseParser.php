<?php

require_once __DIR__.'/ParserInterface.php';

abstract class BaseParser implements ParserInterface
{

    public $content;
    public $parser;
    protected $result;
    protected $src_regex;
    protected $tag_regex;
    protected $tag_replace_string;
    protected $name;

    public function __construct($content)
    {
        $this->content = $content;
        $this->result = array();
    }

    public function parse()
    {

        if (!$this->tag_regex) {
            throw new \RuntimeException('tag regex is empty!');
        }

        $tags = null;
        preg_match_all($this->tag_regex, $this->content, $tags);

        foreach ($tags as $img_tag) {
            $this->parseTagData($img_tag);
        }

        return $this->result;
    }

    /**
     * @param array $tag_data
     *
     * @return array
     */
    public function parseTagData(array $tag_data)
    {

        if (!$this->src_regex) {
            throw new RuntimeException('empty src regex!');
        }

        $matches = [];

        foreach ($tag_data as $tag_description) {
            $matches = [];
            preg_match(
                    $this->src_regex,
                    $tag_description,
                    $matches,
                    PREG_UNMATCHED_AS_NULL
            );
            $this->getLinksFromTag($matches);
        }

        return $matches;
    }

    /**
     * @param array $matches
     */
    public function getLinksFromTag(array $matches): void
    {
        foreach ($matches as $match) {

            $url = $this->cleanUrlString($match);

            if (empty($url)) {
                continue;
            }

            $exploded = explode(' ', $url);

            if (!count($exploded)) {
                continue;
            }

            $this->processTagData($exploded);

        }
    }

    /**
     * @param $match
     *
     * @return string
     */
    private function cleanUrlString($match): string
    {
        foreach ($this->tag_replace_string as $value) {
            $match = str_replace($value, '', $match);
        }

        return $match;
    }

    /**
     * @param array $exploded
     */
    private function processTagData(array $exploded): void
    {

        $data = $exploded[0];
        $result = $this->validate($data);

        if (!$result) {
            return;
        }

        if (in_array($data, $this->result)) {
            return;
        }

        $this->result[] = $data;

    }

    public function validate($data)
    {
        $data = filter_var($data, FILTER_SANITIZE_URL);
        $res = filter_var($data, FILTER_VALIDATE_URL);

        return $res;
    }

    public function showResult()
    {
        echo "\n--- Result of ".self::NAME." parsing --- \n";
        foreach ($this->result as $value) {
            echo "$value \n";
        }
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getName()
    {
        return $this->name;
    }

}