<?php
/**
 * wechat php test
 */
//define your token
// 最后测试网址：
// http://hhr.yiopay.com/backend/wxcheck/test.php?echostr=8085854468487076604&timestamp=1530682104&nonce=1130401568&signature=f2050e0f5a8c2a3c64ca49cde6232f714634282d
// 返回 f2050e0f5a8c2a3c64ca49cde6232f714634282d
define("TOKEN", "easywechat");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
  public function valid()
  {
    $echoStr = $_GET["echostr"];

    //valid signature , option
    if($this->checkSignature()) {
        echo $echoStr;
        exit;
    } else {
        echo '校验失败！';
    }
  }

  public function responseMsg()
  {
    //get post data, May be due to the different environments
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

     //extract post data
    if (!empty($postStr)) {
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = "<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType>
              <Content><![CDATA[%s]]></Content>
              <FuncFlag>0</FuncFlag>
              </xml>";      
        if(!empty( $keyword ))
        {
           $msgType = "text";
          $contentStr = "Welcome to wechat world!";
          $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
          echo $resultStr;
        }else{
          echo "Input something...";
        }

    }else {
      echo "";
      exit;
    }
  }
    
  private function checkSignature()
  {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"]; 
        
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    
    if( $tmpStr == $signature ){
      return true;
    } else {
      return false;
    }
  }
}
