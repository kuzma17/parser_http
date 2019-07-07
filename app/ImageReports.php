<?php

namespace App;


class ImageReports extends Reports
{
    /**
     * @param $fp
     */
    protected function getItems($fp)
    {
        $items = [];

        while (($rows = fgetcsv($fp, 1000, "\t")) !== FALSE) {
            foreach($rows as $row){
                $data = explode($this->delimiter_csv, $row);
                $key = array_search($data[0], array_column($items, 'url'));
                if ($key === 0 || $key > 0){
                    $items[$key]['img'][] = $data[1];
                }else{
                    $items[] = [
                        'url' => $data[0],
                        'img' => [
                            $data[1]
                        ]
                    ];
                }
            }
        }

        $all_page = 0;
        $all_img = 0;
        foreach($items as $item){
            echo "\e[1m {$item['url']}\e[0m".' ('.count($item['img']).')'.PHP_EOL;
            $images = $item['img'];
            $all_page++;
            foreach ($images as $image){
                echo $image.PHP_EOL;
                $all_img++;
            }
        }

        echo "\e[1m Total page: {$all_page} Total images: {$all_img}\e[0m".PHP_EOL;
    }


}