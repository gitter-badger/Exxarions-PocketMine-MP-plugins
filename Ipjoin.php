<?php

/*
__Pocketmine Plugin__
name=IpJoin
description=Players must connect with their specified IP addresses, or they will not be able to join. This prevents impersonation and not needing a login system! Note: everytime a player joins, be sure to do /ipjoin add <player's name>
version=1.0
author=A+Comics
class=ipjoin
apiversion=10
*/

class ipjoin implements Plugin{
        private $api, $config, $server;
        public function __construct(ServerAPI $api, $server = false){
                $this->api = $api;
                $this->server = ServerAPI::request();
        }
        public function init(){
                $this->api->console->register("ipjoin", "<add> <username>", array($this, "cmd"));
                $this->config = new Config($this->api->plugin->configPath($this)."IpJoin.yml", CONFIG_YAML, array(
                  "Players" => array(),
                ));
                $this->config = $this->api->plugin->readYAML($this->api->plugin->configPath($this) ."IpJoin.yml");
                $this->api->console->alias("ipa", "ipadmin");
                $this->api->addHandler('player.connect', array($this, "connect"));
        }
        public function cmd($cmd, $params, $issuer){
            $username = $issuer->username;
            switch($params[0]){
                case "add":
                    if(!isset($params[1])){
                        $output = "Usage: /ipjoin add <playername>";
                        break;
                    }
                    $name = strtolower($params[1]);
                    $player = $this->api->player->get($name);
                    if($player instanceof Player){
                        $ip = $player->ip;
                        $this->config['Players'][] = array($name, $ip);
                        $output = "Added ".$name." Ip address! Now they are protected from impersonation!";
                        $this->api->plugin->writeYAML($this->api->plugin->configPath($this) ."IpJoin.yml", $this->config);
                    }else{
                        $output = "That player doesnt exist";
                    }
                    break;
                default:
                    $output = "Usage: /ipjoin <add> <playername>";
                    break;
            }
            return $output;
        }
        public function connect($data){
            $username = $data->iusername;
            $ip = $data->ip;
            foreach($this->config['Players'] as $val){
                if(($val[0] == $username) and ($val[1] != $ip)){
                    return false;
                }
            }
        }
 
        public function __destruct(){}
}
