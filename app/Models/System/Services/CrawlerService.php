<?php

namespace App\Models\System\Services;

use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class CrawlerService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchGoogleMapJson($url)
    {
        $response = $this->client->request('GET', $url, ['headers' => ['accept-encoding' => 'gzip, deflate, br',
                                                                       'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7,ja;q=0.6']]);
        $obj = $this->json_validate($response->getBody());

        $data = ['address'      => $obj->result->formatted_address,
                 'phone'        => $obj->result->formatted_phone_number,
                 'location_lat' => $obj->result->geometry->location->lat,
                 'location_lng' => $obj->result->geometry->location->lng,
                 'name'         => $obj->result->name];
        $data['address'] = str_replace('台灣省', '台灣', $data['address']);
        $data['address'] = str_replace('臺灣省', '台灣', $data['address']);
        $data['address'] = str_replace('臺灣', '台灣', $data['address']);
        $temp = explode('台灣', $data['address']);
        $data['address'] = $temp[1] ?? $data['address'];

        return array_map('trim', $data);
    }

    public function fetchFacebookPage($url)
    {
        $url = str_replace('www.', 'mobile.', $url);
        $response = $this->client->request('GET', $url, ['headers' => ['user-agent'      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                                                                 'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7,ja;q=0.6']]);
        $stream = Psr7\stream_for($response->getBody());
        $text = $stream->getContents();

        $document = new Document($text);
        // $meta = $document->find('meta[property="og:description"]');
        // $content = $meta[0]->getAttribute('content');

        if (empty($document->find('.story_body_container > div'))) {
            $meta = $document->find('meta[property="og:description"]');
            $content = $meta[0]->getAttribute('content');
        } else {
            $content = $document->find('.story_body_container > div')[0]->html();
        }
// var_dump($parentElement);
// die;

        $images = [];
        $meta = $document->find('meta[property="og:image"]');
        foreach($meta as $item) {
        	$images[] = $item->getAttribute('content');
        }

/*
        $document = new Document($text);
        $text = html_entity_decode($text);
        $divs = $document->find('div.hidden_elem');
        foreach($divs as $div) {
            $code = $div->first('code');
            if (empty($code)) continue;
            if (strpos($code, 'userContentWrapper') == false) continue;
            $container = $code->firstChild()->html();
            $container = str_replace('<!-- ', '', $container);
            $container = str_replace(' -->', '', $container);
            break;
        }

        $document = new Document($container);
        $content = $document->find('div.userContent');
        $content = $content[0]->innerHtml();

        $document = new Document($container);
        $images = [];
        $imgs = $document->find('img.scaledImageFitHeight');
        foreach($imgs as $img) {
            $images[] = $img->getAttribute('src');
        }
        $imgs = $document->find('img.scaledImageFitWidth');
        foreach($imgs as $img) {
            $images[] = $img->getAttribute('src');
        }*/

        return ["content" => $content,
                "images"  => $images];
    }

    private function json_validate($string)
    {
        $result = json_decode($string);

        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        return empty($error) ? $result : RTErrorString($error);
    }
}
