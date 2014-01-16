<?php

/*
__PocketMine Plugin__
name=ColorChat
description=Makes your chat more colorful!
version=1.0
author=A+Comics
class=colorchat
apiversion=11
*/

    class colorchat implements plugin{

    private $api;

    public function __construct(ServerAPI $api, $server = false){

		$this->api = $api;

	}

	public function init(){

    $this->api->console->register("color <message>", "Have a colored chat", array($this, "commandHandler"));

    }
    
    public function commandHandler($cmd, $params, $issuer, $alias){

    $this->api->chat->broadcast("<$issuer> §7$Message");

    }

    public function __destruct(){

    }

}
