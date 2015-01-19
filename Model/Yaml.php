<?php
namespace Stc\CheckTranslationBundle\Model;

use Symfony\Component\Yaml\Parser;

class Yaml
{
    const EXTENSION = 'yml';

    private $file;
    private $locale;
    private $content;
    private $explode;

    /**
     * @param $file
     * @param $locale
     */
    public function __construct($file, $locale)
    {
        $this
            ->setFile($file)
            ->setLocale($locale)
            ->prepare()
        ;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if ($this->content === null) {
            $this->setContent(file_get_contents($this->getRelativePath()));
        }

        return $this->content;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @param $key
     * @return array
     */
    public function getExplode($key = null)
    {
        if ($this->explode === null) {
            $this->explode = explode(PHP_EOL, $this->getContent());
        }

        if ($key !== null) {
            return (isset($this->explode[$key]) ? $this->explode[$key] : false);
        }

        return $this->explode;
    }


    /**
     * @param $explode
     * @return $this
     */
    public function setExplode($explode)
    {
        $this->explode = $explode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getFile() . '.' . $this->getLocale() . '.' . self::EXTENSION;
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return __DIR__ . '/../../../../../../src/' . $this->getPath();
    }

    /**
     * @return $this
     */
    public function prepare()
    {
        $yaml = new Parser();
        $arr = $this->getExplode();
        foreach ($arr as $key => $string) {
            if (!isset($string) || '' == trim($string, ' ') || 0 === strpos(ltrim($string, ' '), '#')) {
                continue;
            }
            $response = $yaml->parse($string);

            if (is_array($response)) {
                $arr[$key] = current(array_flip($yaml->parse($string)));

            }
        }

        $this->setExplode($arr);

        return $this;
    }

}