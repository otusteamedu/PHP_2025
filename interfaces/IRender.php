<?php
/**
 * Created by IntelliJ IDEA.
 * User: flashbomb
 * Date: 26.05.2019
 * Time: 7:58
 */

namespace app\interfaces;


interface IRender
{

    public function renderTemplate($template, $params = []);
}