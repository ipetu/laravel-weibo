<?php

namespace App\Http\Controllers;

use DefaultProfile;
use DefaultAcsClient;
use Sts\Request\V20150401\AssumeRoleRequest as AssumeRoleRequest;

define("REGION_ID", "cn-shanghai");
define("ENDPOINT", "sts.cn-shanghai.aliyuncs.com");
class AliyunSTSController extends Controller
{
    //
    public function index()
    {
        $call = $this->feachAliyunStsCallBack();
        return response(json_encode($call), 200)->header('Content-Type', 'text/plain');
    }

    public function feachAliyunStsCallBack()
    {
        // 只允许子用户使用角色
        DefaultProfile::addEndpoint(REGION_ID, REGION_ID, "Sts", ENDPOINT);
        $iClientProfile = DefaultProfile::getProfile(REGION_ID, "LTAIzATj6jbeNNTd", "eq2jb5oo4yvtII1PrGfEnjqqWgjAIL");
        $client = new DefaultAcsClient($iClientProfile);
        // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        $roleArn = "acs:ram::1427937646054230:role/osssts";
        $request = new AssumeRoleRequest();
        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName("client_name");
        $request->setRoleArn($roleArn);
        $request->setDurationSeconds(1200);
        try {
            return $response = $client->getAcsResponse($request);
//            print_r($response);
        } catch (ServerException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        } catch (ClientException $e) {
            print "Error: " . $e->getErrorCode() . " Message: " . $e->getMessage() . "\n";
        }
    }

    public function feachStsCallBack()
    {
        $iClientProfile = DefaultProfile::getProfile(REGION_ID, "LTAIzATj6jbeNNTd", "eq2jb5oo4yvtII1PrGfEnjqqWgjAIL");
        $client = new \Sts\Core\DefaultAcsClient($iClientProfile);
        $request = new \Sts\AssumeRoleRequest();
        $request->setRoleArn("acs:ram::1427937646054230:role/osssts");
        $request->setRoleSessionName('osssts');
        $request->setDurationSeconds(3600);
        $client->doAction($request);
        $response = $client->getAcsResponse($request);
        return $response;
    }
}
