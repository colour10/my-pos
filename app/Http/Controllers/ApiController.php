<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    // 默认状态码
    protected $statusCode = '0';

    // 设置状态码
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    // 读取状态码
    public function getStatusCode()
    {
        return $this->statusCode();
    }

    // 返回正确状态码得回调值
    public function responseSuccess($message = '')
    {
        return $this->response([
            'status_name' => 'success',
            'status_code' => $this->getStatusCode(),
            'msg' => $message,
        ]);
    }

    // 返回错误状态码的回调值
    public function responseError($message = '')
    {
        return $this->response([
            'status_name' => 'error',
            'errors' => [
                'status_code' => $this->getStatusCode(),
                'msg' => $message,
            ],
        ]);
    }

    // 高级封装
    public function response($data)
    {
        return \Response::json($data);
    }

}
