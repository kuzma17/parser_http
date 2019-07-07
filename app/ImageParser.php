<?php

namespace App;


class ImageParser extends Parser
{
    /**
     * @param $link
     * @return array
     */
    protected function parseItems($link)
    {
        $content = @file_get_contents($link);

        preg_match_all('/<img.*?src=["\'](.*?)["\'].*?>/i', $content, $images, PREG_SET_ORDER);

        $img = [];
        foreach ($images as $image){

            $img[] = [
                $link,
                $this->normaliseImg($image[1])
            ];
        }

        return $img;
    }

}