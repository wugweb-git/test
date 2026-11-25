<?php
defined('ABSPATH') || exit;
function wugweb_core_service_all(){static $d=null;if($d===null){$f=__DIR__.'/service-data.json';$d=is_readable($f)?json_decode(file_get_contents($f),true):array();}return is_array($d)?$d:array();}
function wugweb_core_service_data($pillar){$a=wugweb_core_service_all();$k=sanitize_key($pillar);return ($k!=='_toolkit'&&isset($a[$k]))?$a[$k]:null;}
function wugweb_core_service_toolkit(){$a=wugweb_core_service_all();return $a['_toolkit']??null;}
