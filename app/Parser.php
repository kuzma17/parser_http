<?php

namespace App;


use Exception;

abstract class Parser
{
    /**
     * @var string
     */
    protected $url;
    /**
     * @var
     */
    protected $domain;
    /**
     * @var array
     */
    protected $links = [];
    /**
     * @var array
     */
    protected $exception_links = [];
    /**
     * @var string
     */
    protected $patch_csv;
    /**
     * @var string
     */
    protected $delimiter_csv = ',';
    /**
     * @var array
     */
    protected $pattern_exceptions;

    /**
     * Parser constructor.
     * @param $settings
     * @param $url
     */
    function __construct($settings, $url)
    {
        $this->patch_csv = $settings['dir'].'/'.$settings['patch_csv'];
        $this->delimiter_csv = $settings['delimiter_csv'];
        $this->pattern_exceptions = $settings['pattern_exceptions'];
        $url = $this->cleanUrl($url);
        $url = $this->normaliseUrl($url);

        if ($this->checkUrl($url) === false){
            exit();
        }

        $this->url = $url;
        $this->domain = parse_url($this->url)['host'];

        $this->hasDir();
    }

    /**
     * @param $string
     * @return mixed|string
     */
    protected function cleanUrl($string)
    {
        $string = trim($string);
        $string = str_ireplace(' ','',$string);
        return $string;
    }

    /**
     * @param $url
     * @return string
     */
    protected function normaliseUrl($url)
    {
        if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
            return $url;
        }

        return 'http://'.$url;
    }

    /**
     * @param $url
     * @return bool
     */
    protected function checkUrl($url)
    {
        try {
            if (!@file_get_contents($url)){
                throw new Exception('Bad url '.$url);
            }

        } catch( Exception $e ) {
            echo "\e[41m Error: {$e->getMessage()}\e[0m".PHP_EOL;
                $this->exception_links[] = $url;
            return false;
        }

        return true;
    }

    /**
     * @param int $link
     * @return array
     */
    public function getLinks($link = 0)
    {
        $url = $link? $link: $this->url;

        echo $url.PHP_EOL;

        $this->links[] = $url;
        $content = @file_get_contents($url);

        preg_match_all('/<a.*?href=["\'](.*?)["\'].*?>/i', $content, $urls, PREG_SET_ORDER);

        foreach ($urls as $url){
            if ($this->isMainUrl($url[1])){
                $link = $this->normaliseLink($url[1]);
                if (array_search($link, $this->links) === false && array_search($link, $this->exception_links) === false){
                    if ($this->checkUrl($link)){
                        $this->getLinks($link);
                    }
                }
            }
        }
    }

    /**
     * @param $url
     * @return bool
     */
    protected function isMainUrl($url)
    {
        $pattern_exceptions = $this->pattern_exceptions;

        foreach ($pattern_exceptions as $exception){
            if (stripos($url, $exception) !== false){
                return false;
            }
        }

        if (substr($url, 0, 4) == 'http' && stripos($url, $this->domain) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param $link
     * @return string
     */
    protected function normaliseLink($link)
    {
        if (!stripos($link, $this->domain)){
            return 'http://'.$this->domain.'/'.ltrim($link, '/');
        }
        return $link;
    }

    /**
     * @param $img
     * @return string
     */
    protected function normaliseImg($img)
    {
        return $this->normaliseLink($img);
    }

    /**
     * @param $file
     * @return resource
     */
    protected function openFile($file)
    {
        try{
            $fp = @fopen($file, 'w');

            if (!$fp){
                throw new Exception('unable to open file');
            }
        } catch (Exception $e ) {
            echo "\e[41m Error: {$e->getMessage()}\e[0m".PHP_EOL;
            exit();
        }
        return $fp;
    }

    /**
     * @write csv
     */
    public function writeCsv()
    {
        $file = $this->patch_csv.'/'.$this->domain.'.csv';
        $fp = $this->openFile($file);
        $links = $this->links;

        foreach ($links as $link){
            $images = $this->parseItems($link);
            foreach ($images as $image){
                fputcsv($fp, $image, $this->delimiter_csv);
            }
        }

        fclose($fp);

        echo "\e[1m  Success. Generated file: {$file}\e[0m".PHP_EOL;
    }

    /**
     *
     */
    public function hasDir()
    {
        $dir = $this->patch_csv;

        if (!file_exists($dir)) {
            mkdir($dir, 0764, true);
        }
    }

    /**
     * @param $link
     * @return mixed
     */
    protected abstract function parseItems($link);
}