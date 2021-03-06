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
        return file_put_contents($this->getFileName(), $page_content);
    }

    /**
     * @param $filename
     * @return string
     */
    public function getFileName(): string
    {
        $filename = $this->getBaseFilename();

        return __DIR__.'/../storage/index_data/'.$filename;
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

        return __DIR__.'/../storage/site_data/'.$filename.'_analyzed';
    }

    /**
     * @param $result
     */
    private function saveSerializedData($result): void
    {
        $name_for_serialized = $this->getFileNameForSerialized();
        $data = json_encode($result);
        file_put_contents($name_for_serialized, $data);
        echo "\n\n serialized data saved in : \n $name_for_serialized \n\n";

        $name_for_index = $this->getFileNameForIndex();
        $data = json_encode(
            [
                'url' => $this->pageUrl,
                'filename' => $name_for_serialized
            ]
        );
        file_put_contents($name_for_index, $data);

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
        return dirname(__DIR__).'/storage/serialized_data/';
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
        return dirname(__DIR__).'/storage/index_data/';
    }

}