<?php


class Files
{

    public $pageUrl;

    public function __construct($url)
    {
        $this->pageUrl = $url;
    }


    public function savePageContent($page_content)
    {
        $file = fopen($this->getFileName(), 'wb');
        $fwrite = fwrite($file, $page_content);
        fclose($file);
        return $fwrite;
    }

    /**
     * @param $filename
     * @return string
     */
    private function getFileName(): string
    {
        $filename = $this->getBaseFilename();
        return __DIR__ . '/storage/index_data/' . $filename;
    }

    /**
     * @return string|string[]|null
     */
    private function getBaseFilename()
    {
        return preg_replace("/[^a-zA-Z0-9\s]/", "", $this->pageUrl);
    }

    public function save($result)
    {
        $this->saveDataToCSV($result);
        $this->saveSerializedData($result);
    }

    /**
     * @param $result
     * @return string
     */
    private function saveDataToCSV($result)
    {
        $filename = $this->getFilenameForResult() . '.csv';
        $file = fopen($filename, 'wb');

        foreach ($result as $key => $value) {

            foreach ($value as $row) {
                fputcsv($file, [
                    $this->pageUrl,
                    $key,
                    $row
                ]);
            }
        }

        fclose($file);
        echo "\n\n CSV data saved in : \n  $filename \n\n";

    }

    /**
     * @param $filename
     * @return string
     */
    private function getFileNameForResult(): string
    {
        $filename = $this->getBaseFilename();
        return __DIR__ . '/storage/site_data/' . $filename . '_analyzed';
    }

    /**
     * @param $result
     */
    private function saveSerializedData($result): void
    {
        $name_for_serialized = $this->getFileNameForSerialized();
        $file = fopen($name_for_serialized, 'wb');
        $data = serialize($result);
        fwrite($file, $data);
        fclose($file);
        echo "\n\n serialized data saved in : \n $name_for_serialized \n\n";

        $name_for_index = $this->getFileNameForIndex();
        $file = fopen($name_for_index, 'wb');
        $data = serialize(
            [
                'url' => $this->pageUrl,
                'filename' => $name_for_serialized
            ]
        );
        fwrite($file, $data);
        fclose($file);

    }

    /**
     * @param $filename
     * @return string
     */
    private function getFileNameForSerialized(): string
    {
        $filename = $this->getBaseFilename();
        return self::getSerializedDataDirectory() . '/' . $filename;
    }

    /**
     * @return false|string
     */
    public static function getSerializedDataDirectory()
    {
        return realpath(__DIR__ . '/storage/serialized_data/');
    }

    private function getFileNameForIndex()
    {
        $filename = $this->getBaseFilename();
        return self::getIndexDataDirectory() . '/' . $filename;
    }

    /**
     * @return false|string
     */
    public static function getIndexDataDirectory()
    {
        return realpath(__DIR__ . '/storage/index_data/');
    }

}