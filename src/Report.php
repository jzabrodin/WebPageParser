<?php

require_once __DIR__.'/Files.php';

class Report
{
    public $domain;

    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    public function printReport()
    {
        $index_data_dir = dirname(__DIR__).'/storage/index_data';

        if (!file_exists($index_data_dir)) {
            echo "\ndirectory $index_data_dir didnt exist\n";

            return;
        }

        echo "\n Trying to open files in : $index_data_dir \n";

        if (empty($index_data_dir)) {
            var_dump(__file__);
            var_dump($index_data_dir);
            exit;
        }

        $files = scandir($index_data_dir);
        echo "---- data for $this->domain ---- ";
        print_r($files);

        foreach ($files as $file) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            $current_file = $index_data_dir.'/'.$file;

            if (!file_exists($current_file)) {
                echo "\n$current_file didnt exist\n";
                continue;
            }

            echo "\nreading $current_file\n";
            $data = $this->getFileData($current_file);
            try {
                $object = json_decode($data);
            } catch (Exception $exception) {
                echo $exception->getMessage();
                continue;
            }

            if (!property_exists($object, 'url')) {
                echo "\nproperty url didnt exist\n";
                continue;
            }

            $domain = $this->getDomain($object);

            if ($domain === $this->domain) {
                $this->printData($object);
            } else {
                echo "\nsomething wrong $this->domain didnt equal $domain\n";
            }

        }
    }

    /**
     * @param string $current_file
     *
     * @return string
     */
    private function getFileData(string $current_file)
    {
        return file_get_contents($current_file);
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    private function getDomain($object)
    {
        $url = (string)$object->url;
        $filtered = str_replace('/', '.', $url);
        var_dump($filtered);
        $url_parts_array = explode('.', $filtered);
        if (strpos($filtered[0], 'http') === false) {
            $domain = $url_parts_array[2] ?? '';
            $country = $url_parts_array[3] ?? '';
        } else {
            $domain = $url_parts_array[3] ?? '';
            $country = $url_parts_array[4] ?? '';
        }

        return $domain.($country ? '.'.$country : '');
    }

    private function printData($object)
    {
        if (!file_exists($object->filename)) {
            echo "\n file $object->filename doesn't exist \n";
        }


        $file_data = $this->getFileData($object->filename);
        $data = (object)json_decode(
                $file_data
        );

        echo "\nData for url : $object->url";

        foreach ($data as $key => $value) {
            echo "\n $key count is ".count($value);
        }

        echo "\n ---- Done $this->domain . -----";
    }

}