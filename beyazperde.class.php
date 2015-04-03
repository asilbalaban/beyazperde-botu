<?php

/**
 * Class Beyazperde
 * @author Asil Balaban
 * @blog http://www.asil.me
 * @mail mail@asilbalaban.com
 * @date 3.4.2015
 */
class Beyazperde
{

    static $data = array ();


    /**
     * Film detaylarını listeler.
     *
     * @param null $url
     * @return array
     */
    static function Detay( $url = NULL )
    {
        if ( $url != NULL ) {

            $open = self::Curl( $url );

            // film adi
            preg_match_all('/<div id="title" (.*?)><meta itemprop="name" content="(.*?)">(.*?)<\/span>/', $open, $titles);
            $title = $titles[0][0];

            // vizyon tarihi
            preg_match_all('/<span itemprop="datePublished" content="(.*?)">(.*?)<\/span>/', $open, $dates);
            $date = $dates[0][0];

            // yönetmen
            preg_match_all('/<span itemprop="name">(.*?)<\/span>/', $open, $directors);
            $director = $directors[0][0];

            // oyuncular
            preg_match_all('/<p><a itemprop="url" href="(.*?)"><span itemprop="name">(.*?)<\/span><\/a>/', $open, $casts);
            $castsStr = '';
            for ($i = 0; $i<count($casts[0]); $i++) {
                $castsStr .= strip_tags($casts[0][$i]);
                if( $i != (count($casts[0]))-1 ) {
                    $castsStr .= ', ';
                }
            }

            // tür
            preg_match_all('/<span itemprop="genre">(.*?)<\/span>/', $open, $genres);
            $genre = $genres[0][0];

            // açıklama
            preg_match_all('/<p itemprop="description">(.*?)<\/p>/', $open, $descriptions);
            $description = $descriptions[0][0];


            $data = array(
                'title' => strip_tags($title),
                'date' => strip_tags($date),
                'director' => strip_tags($director),
                'casts' => $castsStr,
                'genre' => strip_tags($genre),
                'description' => strip_tags($description)
            );

            return $data;

        }
    }

    /**
     * Gereksiz boşlukları temizler.
     *
     * @param $string
     * @return string
     */
    private function replaceSpace( $string )
    {
        $string = preg_replace( "/\s+/", " ", $string );
        $string = trim( $string );
        return $string;
    }

    /**
     * @param $url
     * @param null $proxy
     * @return mixed
     */
    private function Curl( $url, $proxy = NULL )
    {
        $options = array ( CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err = curl_errno( $ch );
        $errmsg = curl_error( $ch );
        $header = curl_getinfo( $ch );

        curl_close( $ch );

        $header[ 'errno' ] = $err;
        $header[ 'errmsg' ] = $errmsg;
        $header[ 'content' ] = $content;

        return str_replace( array ( "\n", "\r", "\t" ), NULL, $header[ 'content' ] );
    }

}
