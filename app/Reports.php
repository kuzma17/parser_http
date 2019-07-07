<?php

namespace App;


use Exception;

abstract class Reports
{
    /**
     * @var string
     */
    protected $patch_csv;
    /**
     * @var string
     */
    protected $delimiter_csv = ',';

    /**
     * Reports constructor.
     * @param $settings
     */
    function __construct($settings)
    {
        $this->patch_csv = $settings['dir'].'/'.$settings['patch_csv'];
        $this->delimiter_csv = $settings['delimiter_csv'];
    }

    /**
     * @param $string
     * @return mixed|string
     */
    protected function cleanDomain($string)
    {
        $string = trim($string);
        $string = str_ireplace('https://','',$string);
        $string = str_ireplace('http://','',$string);
        $string = str_ireplace('www','',$string);

        return $string;
    }

    /**
     *
     */
    public function listDomains()
    {
        $dir = $this->patch_csv;

        $files = scandir($dir, 0);
        foreach ($files as $file){
            if ($file == '.' || $file == '..') {
                continue;
            }
            echo substr($file, 0, -4 ).PHP_EOL;
        }
    }

    /**
     * @param $domain
     */
    public function getDomainFile($domain)
    {
        $domain = $this->cleanDomain($domain);
        $file = $this->patch_csv.'/'.$domain.'.csv';

        try{
            if (!file_exists($file)){
                throw new Exception('not found file');
            }
        } catch (Exception $e ) {
            echo "\e[41m Error: {$e->getMessage()}\e[0m".PHP_EOL;
            exit();
        }

        $fp = @fopen($file, 'r');
        $this->getItems($fp);

        fclose($fp);
    }

    /**
     * @param $fp
     * @return mixed
     */
    protected abstract function getItems($fp);

}