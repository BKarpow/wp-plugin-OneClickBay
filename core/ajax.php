<?php


add_action('wp_ajax_ok_send_order', 'ok_ajax_send_order');
add_action('wp_ajax_ok_recaptcha_verify', 'ok_recaptcha_verify');
add_action('wp_ajax_nopriv_ok_recaptcha_verify', 'ok_recaptcha_verify');
add_action('wp_ajax_nopriv_ok_send_order', 'ok_ajax_send_order');

function ok_ajax_send_order()
{
    $request = json_decode( file_get_contents('php://input'), true);
    $res_log = OneClick::send($request);
    die(json_encode(['log' => $res_log]));
}

function ok_recaptcha_verify(){
    $request = json_decode( file_get_contents('php://input'), true);
    OneClick::setRecaptchaSecret(get_option('ok_recaptcha_secret_key')['input']);
    $res = OneClick::reVerify($request['token']);
    echo json_encode(['result' => $res]);
    die();
}
