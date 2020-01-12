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
        $files_operations = new Files($this->pageUrl);

        $page_content = $this->getPageContent();
        $files_operations->savePageContent($page_content);

        if ($page_content === false) {
            $this->showErrors();
        }
        $this->getDataFromParsers($page_content, $this->pageUrl);

        $files_operations->save($this->result);

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
        list($page_content, $connection) = $this->handleRedirection($connection);


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
        curl_setopt($connection, CURLOPT_HEADER, true);
        return $connection;
    }

    /**
     * @param $page_content
     */
    private function getDataFromParsers(string $page_content, string $page_url): bool
    {
        $parsed = true;
        $parsers = [];
        $parsers[] = new ImageParser($page_url, $page_content);
        $parsers[] = new LinksParser($page_url, $page_content);

        foreach ($parsers as $parser) {

            $parser->parse();
            $parse_result = $parser->getResult();

            if (!empty($parse_result)) {
                $parser_name = $parser->getName();
                $this->result[$parser_name] = $parse_result;
            }

        }

        if (count($parse_result) === 0) {
            $this->errors[] = "parse result for $this->pageUrl is empty";
            $this->showErrors();
            $parsed = false;
        }
        return $parsed;

    }

    /**
     * @param $connection
     *
     * @return array
     */
    private function handleRedirection($connection): array
    {
        $page_content = curl_exec($connection);

        $code = curl_getinfo($connection, CURLINFO_HTTP_CODE);

        $http_redirect_codes = [301, 302, 303, 307];
        if (in_array($code, $http_redirect_codes, true)) {
            preg_match('/\wocation:(.*?)\n/', $page_content, $matches);
            curl_close($connection);
            $first_url = array_pop($matches);
            $new_url = trim($first_url);
            $connection = $this->getConnection($new_url);
            $page_content = curl_exec($connection);
        }

        return array($page_content, $connection);
    }

}