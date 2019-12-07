<?php

require_once __DIR__ . '/ParserInterface.php';

class ImageParser implements ParserInterface
{
    const name = 'Images';
    public $content;
    private $result;

    public function __construct($content)
    {
        $this->content = $content;
        $this->result = array();
    }

    public function parse()
    {
        // отсечем все картинки
        $exploded = explode('<img', $this->content);

        foreach ($exploded as $string) {
            // теперь справа
            $tag_data = explode('>', $string);
            // с помощью регулярки доберемся до содержимого
            foreach ($tag_data as $tag_description) {
                $matches = [];
                preg_match('/src=.{1,}/', $tag_description, $matches);
                $this->getLinksFromTag($matches);
            }
        }

        return count($this->result);
    }

    /**
     * @param array $matches
     */
    public function getLinksFromTag(array $matches): void
    {
        foreach ($matches as $match) {
            $url = str_replace('src=', '', $match);
            if (!empty($url)) {
                $explode = explode(' ', $url);
                if (count($explode)) {
                    $this->result[] = $explode[0];
                }
            }
        }
    }

    public function showResult()
    {
        echo "\n--- Result of " . self::name . " parsing --- \n";
        foreach ($this->result as $value) {
            echo "$value \n";
        }
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getName(): string
    {
        return self::name;
    }
}