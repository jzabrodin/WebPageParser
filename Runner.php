<?php

require_once __DIR__.'/src/parsers/ImageParser.php';
require_once __DIR__.'/src/parsers/LinksParser.php';
require_once __DIR__.'/src/Files.php';

class Runner
{

    public $errors;
    private $pageUrl;
    private $result;

    public function __construct(string $page_url)
    {

        $page_url = trim($page_url);
        $index_of_https = (strpos($page_url, 'https://'));
        $index_of_http = (strpos($page_url, 'http://'));

        if ($index_of_http === false && $index_of_https === false) {
            $page_url = 'https://' . $page_url;
        }

        $this->pageUrl = trim($page_url);
        $this->result = [];
        $this->errors = [];
    }

    public function run()
    {

        $this->checkURL();
        $filesOperations = new Files($this->pageUrl);

        $page_content = $this->getPageContent();
        $filesOperations->savePageContent($page_content);

        if ($page_content === false) {
            $this->showErrors();
        }
        $this->getDataFromParsers($page_content);

        $filesOperations->save($this->result);

    }

    public function checkURL(): void
    {

        $is_correct_url = filter_var($this->pageUrl, FILTER_VALIDATE_URL);

        if (!$is_correct_url) {
            $this->errors[] = 'This url doesnt correct ' . $this->pageUrl;
            $this->showErrors();
            exit;
        }
    }

    private function showErrors()
    {
        echo " ----- ERRORS ------- \n";
        foreach ($this->errors as $error) {
            echo "$error \n";
        }
    }

    public function getPageContent()
    {
        $connection = $this->getConnection($this->pageUrl);
        $page_content = curl_exec($connection);

        if ($page_content === false) {
            $this->errors[] = curl_error($connection);
            curl_close($connection);
            return '';
        }

        curl_close($connection);
        return $page_content;
    }

    public function getConnection()
    {
        $connection = curl_init($this->pageUrl);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        return $connection;
    }

    /**
     * @param $page_content
     */
    private function getDataFromParsers($page_content): bool
    {
        $parsed = true;
        $parsers = [];
        $parsers[] = new ImageParser($page_content);
        $parsers[] = new LinksParser($page_content);

        foreach ($parsers as $parser) {

            $parser->parse();
            $parse_result = $parser->getResult();

            if (!empty($parse_result)) {
                $parser_name = $parser->getName();
                $this->result[$parser_name] = $parse_result;
            }

        }

        if (count($parse_result) === 0) {
            $this->errors[] = $page_content;
            $this->showErrors();
            $parsed = false;
        }
        return $parsed;

    }

}