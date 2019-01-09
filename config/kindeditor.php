<?php
/**
 * Created by PhpStorm.
 * Author: ChenHua <Http://www.ichenhua.cn>
 * Date: 2018/6/15 11:31
 */

return [
    "default"     => 'local', //默认返回存储位置url
    "dirver"      => ['qiniu'], //存储平台
    "connections" => [
        "local"  => [
            'prefix' => 'uploads',
        ],
        "qiniu"  => [
            'access_key' => 'E5k3Cytf7Wv7qLWWOzBk3fztmKWudFwCJARf55hb',
            'secret_key' => 'QvcLWO02Vgvl92tG48xbYR0t5Jea2qGrFXTcdqDI',
            'bucket'     => 'jinxiaocun',
            'prefix'     => '',
            'domain'     => 'panko03e5.bkt.clouddn.com'
        ],
        "aliyun" => [
            'ak_id'     => '',
            'ak_secret' => '',
            'end_point' => '',
            'bucket'    => '',
            'prefix'    => '',
        ],
    ],
];
