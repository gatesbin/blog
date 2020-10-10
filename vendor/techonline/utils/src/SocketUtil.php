<?php

namespace TechOnline\Utils;


class SocketUtil
{
    public static function isTCPConnectable($ip, $port, $timeout = 3)
    {
        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $connect_timeval = array("sec" => $timeout, "usec" => 0);
            socket_set_option(
                $socket,
                SOL_SOCKET,
                SO_SNDTIMEO,
                $connect_timeval
            );
            socket_set_option(
                $socket,
                SOL_SOCKET,
                SO_RCVTIMEO,
                $connect_timeval
            );
            if (socket_connect($socket, $ip, $port)) {
                @socket_close($socket);
                return true;
            }
        } catch (\Exception $e) {
            @socket_close($socket);
        }
        return false;
    }

    public static function isTCPConnectableBySocks5($socks5ip, $socks5port, $ip, $port, $timeout = 3)
    {
        $socket = false;
        try {
            $socket = stream_socket_client("tcp://$socks5ip:$socks5port", $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
            stream_set_timeout($socket, $timeout);
            fwrite($socket, pack("C3", 0x05, 0x01, 0x00));
            $server_status = fread($socket, 2048);
            if ($server_status == pack("C2", 0x05, 0x00)) {
                            } else {
                @stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
                @fclose($socket);
                                return false;
            }
            fwrite($socket, pack("C5", 0x05, 0x01, 0x00, 0x03, strlen($ip)) . $ip . pack("n", $port));
            $server_buffer = fread($socket, 10);
            if (ord($server_buffer[0]) == 5 && ord($server_buffer[1]) == 0 && ord($server_buffer[2]) == 0) {
                            } else {
                @stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
                @fclose($socket);
                                return false;
            }
                                                @stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
            @fclose($socket);
            return true;
        } catch (\Exception $e) {
            @stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
            @fclose($socket);
        }
        return false;
    }
}
