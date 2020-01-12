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
    protected $url;

    public function __construct($url, $content)
    {
        $this->content = $content;
        $this->url = $url;
        $this->result = array();
    }

    public function parse()
    {
        foreach ($this->src_regex as $regex) {
            $this->parseTagData($regex);
        }

        return $this->result;
    }

    /**
     * @param array $regex
     *
     * @return array
     */
    public function parseTagData(string $regex)
    {

        if (!$regex) {
            throw new RuntimeException('empty src regex!');
        }

        preg_match_all(
                $regex,
                $this->content,
                $matches,
                PREG_UNMATCHED_AS_NULL
        );

        $this->getLinksFromTag($matches[0] ?? []);


        return $matches;
    }

    /**
     * @param array $matches
     */
    public
    function getLinksFromTag(
            array $matches
    ): void {
        foreach ($matches as $match) {

            $url = $this->cleanUrlString($match);

            if (empty($url)) {
                continue;
            }

            $this->processTagData($url);

        }
    }

    /**
     * @param $match
     *
     * @return string
     */
    private
    function cleanUrlString(
            $match
    ): string {
        foreach ($this->tag_replace_string as $value) {
            $match = str_replace($value, '', $match);
        }

        return $match;
    }

    /**
     * @param array $exploded
     */
    private
    function processTagData(
            string $data
    ): void {
        $result = $this->validate($data);

        if (!$result) {
            return;
        }

        if (in_array($result, $this->result)) {
            return;
        }

        $this->result[] = $result;

    }

    public
    function validate(
            $data
    ) {

        $data = filter_var($data, FILTER_SANITIZE_URL);
        $validating_result = filter_var($data, FILTER_VALIDATE_URL);

        if ($validating_result) {
            return $validating_result;
        }

        $quotes_escaped = str_replace('//', '', $data);

        $quotes_escaped_result = filter_var($quotes_escaped, FILTER_VALIDATE_URL);
        if ($quotes_escaped_result) {
            return $quotes_escaped_result;
        }

        $validating_result = $quotes_escaped;

        return $validating_result;
    }

    public
    function showResult()
    {
        echo "\n--- Result of ".self::NAME." parsing --- \n";
        foreach ($this->result as $value) {
            echo "$value \n";
        }
    }

    public
    function getResult()
    {
        return $this->result;
    }

    public
    function getName()
    {
        return $this->name;
    }

}