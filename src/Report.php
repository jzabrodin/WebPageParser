<?php

require_once __DIR__ . '/Files.php';

class Report
{
    public $domain;

    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    public function printReport()
    {
        $index_data_dir = realpath(__DIR__ . '/storage/index_data/');

        if (empty($index_data_dir)) {
            var_dump(__file__);
            var_dump($index_data_dir);
            exit;
        }

        $files = scandir($index_data_dir);
        echo "---- data for $this->domain ---- ";

        foreach ($files as $file) {
            try {
                $current_file = $index_data_dir . '/' . $file;
                $data = $this->getFileData($current_file);
                $object = (object)unserialize($data);

                if (!property_exists($object, 'url')) {
                    continue;
                }

                $domain = $this->getDomain($object);

                if ($domain === $this->domain) {
                    $this->printData($object);
                }


            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**
     * @param string $current_file
     * @return array
     */
    private function getFileData(string $current_file)
    {
        $file = fopen($current_file, 'rb');
        $data = fread($file, filesize($current_file));
        fclose($file);
        return $data;
    }

    /**
     * @param $object
     * @return mixed
     */
    private function getDomain($object)
    {
        $url = (string)$object->url;
        $exploded = explode('.', $url);
        if (strpos($exploded[0], 'http') === false) {
            $domain = explode('/', $exploded[0])[1];
        } else {
            $domain = explode('/', $exploded[0])[2];
        }
        return $domain;
    }

    private function printData($object)
    {
        try {
            if (!file_exists($object->filename)) {
                echo "\n sorry file $object->filename doesnt exist \n";
            }


            $data = (object)unserialize($this->getFileData($object->filename));
            echo "\nData for url : $object->url";

            foreach ($data as $key => $value) {
                echo "\n $key count is " . count($value);
            }

            echo "\n ---- Done $this->domain . -----";
        } catch (Exception $e) {

        }

    }

}