<?php
//生产环境或开发环境
define('IS_ENV_PRODUCTION',true);

//设置错误高爆选项
error_reporting(E_ALL|E_STRICT);
ini_set('dislay_errors',!IS_ENV_PRODUCTION);
ini_set('error_log','log/phperror.txt');

date_default_timezone_set('America/New_York');

