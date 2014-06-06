<?php

/*
__PocketMine Plugin__
name=Suicidal
version=1.0
description=Allows people to commit suicide
author=A+Comics
class=Kill
apiversion=10
*/

class Kill implements Plugin{

private $api;

public function __construct(ServerAPI $api, $server = false){
$this->api = $api;
}

public function init(){
$this->api->console->register("suicide","Commit suicide!",array($this, "Suicide"));
$this->api->ban->cmdWhitelist("suicide");
}

public function Suicide($cmd, $issuer){
$username = $issuer->username;
$this->api->console->run("kill " . $username);
$this->api->broadcast("".$username." was killed by his own hand")
$this->api->chat->sendto("Goodbye cruel world!", ".$username.")
}

public function __destruct(){
}
}
?>
