<?php

use Foxkit\Application as App;

if (!function_exists('__')) {

    /**
     * 翻译给定的消息，是方法 trans() 的别名
     * @param $id
     * @param array $parameters
     * @param string $domain
     * @param null $locale
     * @return
     */
    function __($id, array $parameters = [], $domain = 'messages', $locale = null) {
        return App::translator()->trans($id, $parameters, $domain, $locale);
    }
}

if (!function_exists('_c')) {

    /**
     * 根据数字选择翻译给定的选择信息，是方法 transChoice() 的别名
     * @param $id
     * @param $number
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     * @return
     */
    function _c($id, $number, array $parameters = [], $domain = null, $locale = null) {
        return App::translator()->transChoice($id, $number, $parameters, $domain, $locale);
    }
}
