<?php
    #file : class/router.class.php

    class Router {
        const route = true;
        private static $url;
        private static $router;
        private static $routes = array();
        private static $method;

        private static function url(){
            $url = $_SERVER['SCRIPT_NAME'];
            $url = rtrim( dirname($url), '/' );
            $url = '' . trim( str_replace( $url, '', $_SERVER['REQUEST_URI'] ), '' );
            $url = urldecode($url);
            $url = strtok($url,'?');
            self::$url = $url;
            self::$method = $_SERVER['REQUEST_METHOD'];
            return self::$url;
        }

        public static function get($route = "/",$data = null,$action = null){
            return self::create($route,$data,'get',$action);
        }
        public static function post($route = "/",$data = null,$action = null){
            return self::create($route,$data,'post',$action);
        }
        public static function group($route = "/",$groups = array(),$method = ['get','post'],$action = null){
            foreach($groups as $group => $sub){
                $group_url = isset($group) ? $group : '(|\/)';
                $group_url = $route.$group_url;
                $data = isset($sub[0]) ? $sub[0] : null;
                $method = isset($sub[1]) ? $sub[1] : $method;
                $workG = true;
                self::create($group_url,$data,$method,$action);
            }
            if($workG){
                return true;
            }else{
                return false;
            }
        }
        public static function api($version = 1,$groups = array(), $customUrl=null, $method = ['get','post','put','delete','patch'], $action = null){
            $api['rUrl'] = isset($customUrl) ? $customUrl : '/api';
            $api['version'] = isset($version) ? 'v'.$version : '';
            foreach ($groups as $group => $apis){
                $api['url'] = isset($group) ? $group : '(|\/)';
                $api['url'] = "$api[rUrl]/$api[version]/$api[url]";
                $api['data'] = isset($apis[0]) ? $apis[0] : null;
                $api['method'] = isset($apis[1]) ? $apis[1] : $method;
                $work = true;
                self::create($api['url'],$api['data'],$api['method'],$action);
            }
            if($work){
                return true;
            }else{
                return false;
            }
        }

        public static function static($route = '/static', $folder = './public/', $method = 'get', $action = null){
            $router['url'] = $route.'/(.*?)';
            $router['method'] = 'get';
            $router['action'] = $action;
            $router['data'] = (function($req) use($folder){
                $file = $req['params'][0];
                if($ext = pathinfo($file, PATHINFO_EXTENSION)){
                    $file = realpath($folder.DIRECTORY_SEPARATOR.$file);
                    if(!is_file($file)) return self::error(404);
                    $type = mime_content_type($file);
                    if($type === 'text/plain'){
                        $type = require('types.router.php');
                        $type = @$type[$ext];
                    }
                    @ob_start();
                    @require($file);
                    $file = @ob_get_contents();
                    @ob_clean();
                    if($file){
                        if($type !== '') header("Content-Type: $type");
                        return exit($file);
                    }else{
                        return self::error(404);
                    }
                }else{
                    return self::error(404);
                }
            });
            return self::create($router['url'],$router['data'],$router['method'],$router['action']);
        }

        private static function create($route = "/",$data = null,$method = ['get','post'],$action = null){
            if(self::exists($route)) return false;
            $pattern = "/:[a-zA-Z0-9]+/i";
            if(preg_match_all($pattern, $route, $matches, PREG_UNMATCHED_AS_NULL)){
                foreach($matches[0] as $match){
                    $expo = str_replace(':','',$match);
                    $route = str_replace($match,"(?'$expo'[^/]+)",$route);
                }
            }
            $route = array(
                "route" => $route,
                "data" => $data,
                "method" => $method,
                "action" => $action
            );
            return array_push(self::$routes,$route);
        }
        private static function exists($route){
            foreach(self::$routes as $router){
                if(in_array($route, $router)){
                    return true;
                }
                next(self::$routes);
            }
            return false;
        }
        public static function init($options = array()){
            if(!file_exists(dirname(__FILE__) . '/types.router.php')) return exit('Faltal error: Dependencie "types.router.php" doesn`t exists!');
            $options = $options ? $options : false;
            $work = array('route'=>false,'method'=>false);
            foreach(self::$routes as $route){
                if(preg_match('#^'.$route['route'].'$#',self::url(),$params)){
                    $work['route'] = true;
                    foreach((array)$route['method'] as $method){
                        if(strtolower(self::$method) === strtolower($method)){
                            array_shift($params);
                            $work['method'] = true;
                            $req = [
                                "params" => $params,
                                "method" => self::$method,
                                "action" => $route['action'],
                            ];
                            $res = [
                                'send' => function($text){
                                    echo $text;
                                },
                                'sendFile' => function($file){
                                    ob_start();
                                    @require realpath($file);
                                    $file = ob_get_contents();
                                    ob_end_clean();
                                    echo $file;
                                }
                            ];
                            switch($route['action']){
                                default:
                                    if(is_callable($route['data'])){
                                        return call_user_func_array($route['data'],array($req,$res));
                                    }
                                break;
                            }
                            break;
                        }
                    }
                } else {
                    next(self::$routes);
                }
            }

            if(!$work['route']){
                return self::error(404);
            }else{
                if(!$work['method']){
                    return self::error(405);
                }
            }
        }

        public static function go($route){
            if(self::exists($route)){
                return header("Location: $route");
            }else{
                return self::error(404);
            }
            return false;
        }

        private static function error($code){
            return http_response_code($code);
        }

    }
