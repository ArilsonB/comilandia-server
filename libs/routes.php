<?php

    include realpath('./class/router.class.php');

    $pages = include realpath('./pages/.php');
    $receitas = include realpath('./libs/receitas.php');
    $sendrecipe = include realpath('./libs/sendrecipe.php');

    Router::get('(|\/)',function($req,$res) use($pages){
        $res['sendFile']('./layout/header.php');
        $pages['home']();
        $res['sendFile']('./layout/footer.php');
    });

    Router::get('/about', function($req,$res) use($pages){
        $res['sendFile']('./layout/header.php');
        $pages['about']();
        $res['sendFile']('./layout/footer.php');
    });

    Router::get('/test', function(){
        Router::go('/new-recipe');
    });

    Router::get('/new-recipe', function($req,$res) use($pages){
        $res['sendFile']('./layout/header.php');
        $pages['new-recipe']();
        $res['sendFile']('./layout/footer.php');
    });

    Router::post('/send-recipe', function($req,$res) use($sendrecipe){
        return $sendrecipe['send']();
    });

    Router::static('/static','./pages');

    $api = [
        'receitas' => array(function() use($receitas){
            return $receitas['json']();
        })
    ];
    Router::api('1',$api);
    Router::init();
?>