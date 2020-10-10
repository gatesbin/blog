<?php

namespace TechSoft\Laravel\Controllers;


use EasyWeChat\Message\Text;
use EasyWeChat\Server\BadRequestException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function PHPSTORM_META\type;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Wechat\Events\LocationEvent;
use TechSoft\Laravel\Wechat\Events\MenuClickEvent;
use TechSoft\Laravel\Wechat\Events\ScanEvent;
use TechSoft\Laravel\Wechat\Events\SubscribeEvent;
use TechSoft\Laravel\Wechat\Events\TextRecvEvent;
use TechSoft\Laravel\Wechat\Facades\WechatAuthorizationServerFacade;
use TechSoft\Laravel\Wechat\Types\WechatAuthType;
use TechSoft\Laravel\Wechat\WechatServiceUtil;
use TechSoft\Laravel\Wechat\WechatUtil;


class HandleController extends Controller
{
    const TEST_APP_ID = 'wx570bc396a51b8ff8';

    public function index($appId = null,
                          $alias = null)
    {
        if ($appId == self::TEST_APP_ID) {
            $m = WechatServiceUtil::loadAccountByAppIdAndAuthType(self::TEST_APP_ID, WechatAuthType::OAUTH);
            if (!$m) {
                WechatServiceUtil::add([
                    'authType' => WechatAuthType::OAUTH,
                    'authStatus' => 1,
                    'name' => '发布测试',
                    'enable' => 1,
                    'appId' => self::TEST_APP_ID,
                    'alias' => '6orafl8dcfpt9pyflok2z66y8p39co8t',
                    'username' => 'gh_3c884a361561',
                ]);
            }
        }

        if ($appId == self::TEST_APP_ID . '_refresh') {
            $openId = ConfigUtil::get('wechatAuthorizationPublishTestOpenId');
            $queryAuthCode = ConfigUtil::get('wechatAuthorizationPublishTestQueryAuthCode');
            if ($openId && $queryAuthCode) {
                $account = WechatServiceUtil::loadAccountByAppIdAndAuthType(self::TEST_APP_ID, WechatAuthType::OAUTH);
                $app = WechatUtil::app($account['id'], $account);
                $app->staff->message(new Text(['content' => $queryAuthCode . '_from_api']))->to($openId)->send();
                ConfigUtil::set('wechatAuthorizationPublishTestOpenId', null);
                ConfigUtil::set('wechatAuthorizationPublishTestQueryAuthCode', null);
                return 'OK';
            }
            return '<script>setTimeout(function(){window.location.reload();},1000);</script>Waiting...';
        }

        if ($alias) {
            $account = WechatServiceUtil::loadAccountByAppIdAndAuthType($appId, WechatAuthType::CONFIG);
            if (empty($account)) {
                return 'success';
            }
        } else {
            $account = WechatServiceUtil::loadAccountByAppIdAndAuthType($appId, WechatAuthType::OAUTH);
            if (empty($account)) {
                return 'success';
            }
        }
        $app = WechatUtil::app($account['id']);
        if (empty($app)) {
            return 'success';
        }

        $app->server->setMessageHandler(function ($message) use (&$app, &$wechatService) {
            switch ($message->MsgType) {
                case 'event':
                                        if ($app->account['appId'] == self::TEST_APP_ID) {
                        $app->setReply(new Text(['content' => $message->Event . 'from_callback']));
                    }
                    switch ($message->Event) {
                        case 'CLICK':
                                                        $eventKey = $message->EventKey;
                            if ($eventKey) {
                                                                $event = new MenuClickEvent();
                                $event->app = &$app;
                                $event->data = &$message;
                                $event->key = &$eventKey;
                                Event::fire($event);
                            }
                            break;
                        case 'SCAN':
                                                                                    $eventKey = $message->EventKey;
                            if ($eventKey) {
                                                                $event = new ScanEvent();
                                $event->app = &$app;
                                $event->data = &$message;
                                $event->scene = $eventKey;
                                $event->isSubscribe = false;
                                Event::fire($event);
                            }
                            break;
                        case 'LOCATION':
                                                                                                                $latitude = $message->Latitude;
                            $longitude = $message->Longitude;
                            $precision = $message->Precision;
                            if ($latitude && $longitude && $precision) {
                                                                $event = new LocationEvent();
                                $event->app = &$app;
                                $event->data = &$message;
                                $event->latitude = $latitude;
                                $event->longitude = $longitude;
                                $event->precision = $precision;
                                Event::fire($event);
                            }
                            break;
                        case 'subscribe':
                                                        
                            $subscribeEvent = new SubscribeEvent();
                            $subscribeEvent->app = &$app;
                            $subscribeEvent->data = &$message;

                            $eventKey = $message->EventKey;
                            if ($eventKey && Str::startsWith($eventKey, 'qrscene_')) {

                                $scene = substr($eventKey, strlen('qrscene_'));

                                                                $event = new ScanEvent();
                                $event->app = &$app;
                                $event->data = &$message;
                                $event->scene = $scene;
                                $event->isSubscribe = true;
                                Event::fire($event);

                                $event->scene = $scene;
                            }

                            Event::fire($subscribeEvent);

                            break;
                    }
                    break;
                case 'text':
                    
                    if ($app->account['appId'] == self::TEST_APP_ID) {
                        if ($message->Content == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
                            $app->setReply(new Text(['content' => 'TESTCOMPONENT_MSG_TYPE_TEXT_callback']));
                        } else if (Str::startsWith($message->Content, 'QUERY_AUTH_CODE:')) {
                            $queryAuthCode = substr($message->Content, strlen('QUERY_AUTH_CODE:'));
                            $ret = WechatAuthorizationServerFacade::getQueryAuth($queryAuthCode);
                            WechatServiceUtil::update($app->account['id'], ['authorizerRefreshToken' => $ret['authorization_info']['authorizer_refresh_token']]);
                            ConfigUtil::set('wechatAuthorizationPublishTestOpenId', $message->FromUserName);
                            ConfigUtil::set('wechatAuthorizationPublishTestQueryAuthCode', $queryAuthCode);
                        }
                    }

                    $event = new TextRecvEvent();
                    $event->app = &$app;
                    $event->data = &$message;
                    Event::fire($event);
                    break;
            }
            $reply = $app->getReply();

            if (null === $reply) {
                $reply = 'success';
            }
            return $reply;
        });

        try {
            $app->server->serve()->send();
        } catch (\Exception $e) {
            if ($e instanceof BadRequestException) {
                            } else {
                throw $e;
            }
        }

    }
}