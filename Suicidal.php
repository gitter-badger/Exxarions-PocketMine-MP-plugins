<?php

/*
__PocketMine Plugin__
name=Suicidal
version=1.0
description=Allows people to commit suicide
author=Exxarion
class=Suicide
apiversion=10,11,12
*/

class Suicide implements Plugin{

private $api;

public function __construct(ServerAPI $api, $server = false){
$this->api = $api;
}

public function init(){
$this->api->addHandler("player.chat", array($this, "handler"), 5));
$this->api->console->register("suicide","Commit suicide!",array($this, "Suicide"));
$this->api->ban->cmdWhitelist("suicide");
}

public function Suicide($cmd, $issuer){
$username = $issuer->username;
$this->api->console->run("kill '.$issuer.'" . $username);
$this->api->broadcast("".$username." died by his own hand")
$this->api->chat->sendTo(false, "Goodbye cruel world!", ".$issuer.")
public function eventHandler($data, $event)
    {
        switch($event)
        {
            case "player.chat":
            $message = strtolower($data['message']);
                if(strpos($message,'".$issuer." died') !== false) { 
				return false;
				}
}

public function __destruct(){
}
}
?>
