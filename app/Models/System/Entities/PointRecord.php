<?php

namespace App\Models\System\Entities;

use Illuminate\Database\Eloquent\Model;

class PointRecord extends Model
{
    protected $table = 'point_records';

    protected $fillable = [
        'category',
        'user_id', 'point',
        'orderID', 'formData', 'returnData', 'is_confirmed'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'formData'   => 'json',
        'returnData' => 'json'
    ];

    protected $appends = ["text"];

    public function user()
    {
        return $this->belongsTo('App\Models\Profile\Entities\User', 'user_id', 'id');
    }

    public function getTextAttribute() {
        $data = json_decode($this->attributes['formData'], true);
        $returnData = json_decode($this->attributes['returnData'], true);
        switch ($this->attributes['category']) {
        case 'ECPay':
            return "ECPay 儲值";
            break;
        case 'Profile':
            return "修改暱稱";
            break;
        case 'Game':
            return "賽事{$data["identifier"]}{$data["type"]}";
            break;
        case 'Manually':
            return $this->attributes['point'] < 0 ? "退費" : "儲值";
            break;
        case 'Manually':
            return $this->attributes['point'] < 0 ? "退費" : "儲值";
            break;
        case 'RecycleShrimp':
        case 'TransferPoint':
            return isset($returnData['text'])?$returnData['text']:null;
            break;
        }
    }
}
