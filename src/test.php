<?php

define("PROJ_DIR", "/home/asmirnov/workspace/pbr-wifc-asm-test/");

require_once __DIR__ . "/include/broadlink.class.php";
require_once __DIR__ . "../..//device/Broadlink_sp2/Broadlink_sp2.php";

//$d = Broadlink::CreateDevice("192.168.1.1", "121231231", 1);
//var_dump($d);

die();

$devs = Broadlink::Discover();

//var_dump($devs);

var_dump($devs[0]->mac());

var_dump($devs[0]->Auth());
var_dump($devs[0]->Set_Power(1));
var_dump($devs[0]->Check_Power());
sleep(2);
var_dump($devs[0]->Set_Power(0));
var_dump($devs[0]->Check_Power());
die();


var_dump($devs[0]->Check_Power());


/*
23:f0:00:00:2a:27:65:00:c2:c3:9f:71:fb:0d:43:b4:00:00:00:00:a1:c3:00:00:45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d
2d:f0:00:00:2a:27:65:00:eb:a4:9f:71:fb:0d:43:b4:00:00:00:00:a1:c3:00:00:45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d:



python
auth packet

payload 00:00:00:00:31:31:31:31:31:31:31:31:31:31:31:31:31:31:31:00:00:00:00:00:00:00:00:00:00:00:01:00:00:00:00:00:00:00:00:00:00:00:00:00:00:01:00:00:54:65:73:74:20:20:31:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00
aes payload 45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d
full packet5a:a5:aa:55:5a:a5:aa:55:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:23:f0:00:00:2a:27:65:00:c2:c3:9f:71:fb:0d:43:b4:00:00:00:00:a1:c3:00:00:45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d


php
string(240) "00:00:00:00:31:31:31:31:31:31:31:31:31:31:31:31:31:31:31:00:00:00:00:00:00:00:00:00:00:00:01:00:00:00:00:00:00:00:00:00:00:00:00:00:00:01:00:00:54:65:73:74:20:20:31:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:"
string(240) "45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d:"
string(408) "5a:a5:aa:55:5a:a5:aa:55:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:2d:f0:00:00:2a:27:65:00:eb:a4:9f:71:fb:0d:43:b4:00:00:00:00:a1:c3:00:00:45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d:"







23:f0:00:00:2a:27:65:00:c2:c3:9f:71:fb:0d:43:b4:00:00:00:00:a1:c3:00:00:45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d
2d:f0:00:00:2a:27:65:00:eb:a4:9f:71:fb:0d:43:b4:00:00:00:00:a1:c3:00:00:45:34:52:e7:f9:2e:da:95:83:44:93:08:35:ef:9a:6d:fb:69:2d:c3:70:b9:04:43:ac:5c:d6:3f:bb:53:ad:fa:08:81:4c:a7:f8:cf:41:71:00:32:8e:57:0c:3b:86:c9:4d:05:70:84:49:a3:89:e2:9a:e1:04:54:36:a0:5b:dd:dc:02:c1:61:af:13:25:e8:7e:19:b0:f7:d1:ce:06:8d:









*/