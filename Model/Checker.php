<?php
namespace Otc\CheckTranslationBundle\Model;

class Checker
{
    private $output = array();
    private $locales;
    private $files;
    private $hasErrors = false;
    private $largestArray;
    private $translator;

    /**
     * @param $locales
     * @param $translator
     */
    public function __construct($locales, $translator)
    {
        $this->translator = $translator;
        $this->setLocales($locales);
    }

    /**
     * @param $file
     * @return $this
     */
    public function checkTranslationYaml($file)
    {
        foreach ($this->getLocales() as $key => $locale) {
            $this->files[] = new Yaml($file, $locale);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLargestArray()
    {
        if ($this->largestArray === null) {
            $largeArrayCount = 0;

            foreach($this->getFiles() as $file) {
                $count = count($file->getExplode());
                if($count > $largeArrayCount) {
                    $this->setLargestArray($file);
                    $largeArrayCount = $count;
                }
            }
        }

        return $this->largestArray;
    }

    /**
     * @param $largestArray
     * @return $this
     */
    public function setLargestArray($largestArray)
    {
        $this->largestArray = $largestArray;

        return $this;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * @param array $locales
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
    }

    /**
     * process()
     */
    public function process()
    {
        $error = false;

        foreach ($this->getLargestArray()->getExplode() as $key => $value) {
            foreach ($this->getFiles() as $file) {
                if ($error) {
                    break;
                }

                if ($file->getExplode($key) === false) {
                    $error = true;
                    $output = $this->translator->trans('otc.check.translation.not.found', array(
                        '%first_value%' => $value,
                        '%first_line%' => $key + 1,
                        '%first_file%' => $this->getLargestArray()->getPath(),
                        '%second_file%' => $file->getPath(),
                    ));
                    $this->addOutput($output, 'red');
                } else {
                    if ($file->getExplode($key) != $value) {
                        $error = true;
                        $output = $this->translator->trans('otc.check.translation.not.found', array(
                            '%first_value%' => $value,
                            '%first_line%' => $key + 1,
                            '%first_file%' => $this->getLargestArray()->getPath(),
                            '%second_file%' => $file->getPath(),
                            '%second_value%' => $file->getExplode($key),
                        ));
                        $this->addOutput($output, true);
                    }
                }
            }

            if ($error) {
                break;
            }
        }

        if (!$this->getOutput()) {
            $output = $this->translator->trans('otc.check.translation.success', array(
                '%first_file%' => $this->getLargestArray()->getFile(),
            ));

            $this->addOutput($output);

        }
    }

    /**
     * @param $output
     * @param $error
     * @return string
     */
    public function addOutput($output, $error = false)
    {
        $fg = 'green';

        if ($error) {
            $fg = 'red';
            $this->setHasErrors(true);
        }

        $this->output[] = '<fg=' . $fg .  '>' . $output . '</fg=' . $fg . '>';
        $this->output[] = '================================================';

        return $this;
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return mixed
     */
    public function getHasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * @param mixed $hasErrors
     */
    public function setHasErrors($hasErrors)
    {
        $this->hasErrors = $hasErrors;
    }
}