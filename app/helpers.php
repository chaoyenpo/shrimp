<?php

function RTMsgString($string){
    if (empty($string)) return;
    response()->json(['success' => true, 'msg' => $string], 200,
                     ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE)
              ->send(); exit;
}

function RTErrorString($string){
    if (empty($string)) return;
    response()->json(['success' => false, 'error' => $string], 200,
                     ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE)
              ->send(); exit;
}

function RTIfEntityNotFound($record){
    if (!empty($record)) return;
    response()->json(['success' => false, 'error' => '找不到目標資料'], 200,
                     ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE)
              ->send(); exit;
}

function RTErrorsIfExist($code, $errors){
    if (empty($errors)) return;
    $data = [];
    $errors = $errors->messages();
    foreach($errors as $field=>$array){
        foreach($array as $error){
            $data[] = $field.' '.$error;
        }
    }
    response()->json(['success' => false, 'error' => $data], $code,
                     ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE)
              ->send(); exit;
}