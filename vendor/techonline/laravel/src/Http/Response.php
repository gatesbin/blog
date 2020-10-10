<?php

namespace TechOnline\Laravel\Http;


class Response
{
    public static function generate($code, $msg, $data = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        if (null === $data) {
            unset($response['data']);
        }
        return $response;
    }

    public static function jsonSuccessData($data)
    {
        return self::json(0, 'ok', $data);
    }

    public static function jsonSuccess($msg = 'ok')
    {
        return self::json(0, $msg);
    }

    public static function jsonError($msg = 'error')
    {
        return self::json(-1, $msg);
    }

    public static function json($code, $msg, $data = null, $redirect = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'redirect' => $redirect,
        ];
        if (null === $redirect) {
            unset($response['redirect']);
        }
        return \Illuminate\Support\Facades\Response::json($response);
    }

    public static function jsonFromGenerate($ret)
    {
        if ($ret['code']) {
            return self::json($ret['code'], $ret['msg']);
        }
        if (!isset($ret['msg'])) {
            $ret['msg'] = 'ok';
        }
        if (isset($ret['data'])) {
            return self::json($ret['code'], $ret['msg'], $ret['data']);
        }
        return self::json($ret['code'], $ret['msg']);
    }

    public static function jsonRaw($data)
    {
        return \Illuminate\Support\Facades\Response::json($data);
    }

    public static function jsonp($data, $callback = null)
    {
        if (empty($callback)) {
            $callback = \Illuminate\Support\Facades\Input::get('callback', null);
        }
        if (empty($callback)) {
            return \Illuminate\Support\Facades\Response::json($data);
        }
        return \Illuminate\Support\Facades\Response::jsonp($callback, $data);
    }

    public static function send($code, $msg, $data = null, $redirect = null)
    {

        if (\Illuminate\Support\Facades\Request::ajax()) {
            return self::json($code, $msg, $data, $redirect);
        } else {
            if (empty($msg) && $redirect) {
                return redirect($redirect);
            }
            $response = [
                'code' => $code,
                'msg' => $msg,
                'redirect' => $redirect,
                'data' => $data
            ];
            if (null === $redirect) {
                unset($response['redirect']);
            }
            return view('base::msg', $response);
        }
    }

    public static function download($filename, $content, $headers = [])
    {
        $response = new \Illuminate\Http\Response($content);
        $disposition = $response->headers->makeDisposition(
            \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        foreach ($headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        return $response;
    }

    public static function raw($content, $headers = [])
    {
        $response = new \Illuminate\Http\Response($content);
        foreach ($headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        return $response;
    }
}