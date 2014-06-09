<?php

/*
__PocketMine Plugin__
name=UltraChat
description=UltraChat
version=1.0
author=Exxarion
class=UltraChat
apiversion=12
*/


class UltraChat implements Plugin{
	private $api, $prefix, $path, $user;
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->addHandler("player.join", array($this, "handler"), 5);
		$this->api->addHandler("player.chat", array($this, "handler"), 5);
		$this->api->addHandler("player.chat", array($this, "eventHandle"), 50);
                $this->api->addHandler("player.chat", array($this, "eventHandle"), 100);
		$this->api->addHandler("player.quit", array($this, "handler"), 5);
		$this->api->schedule(1200, array($this, "minuteSchedule"), array(), true);
		$this->readConfig();
		$this->api->console->register("prefix", "Add/change a user's prefix", array($this, "Pref"));
		$this->api->console->register("defprefix", "Set the default player Prefix", array($this, "Pref"));
		$this->api->console->register("delprefix", "Delete a user's prefix", array($this, "Pref"));
		$this->api->console->register("nick", "Add/change a user's nickname", array($this, "Pref"));
		$this->api->console->register("delnick", "Remove user's nickname", array($this, "Pref"));
		$this->api->console->register("mute", "Shut a player up", array($this, "Pref"));
		$this->api->console->register("unmute", "Allow a player to use chat again", array($this, "Pref"));
		$this->api->console->register("chaton", "Allow users to chat", array($this, "Pref"));
		$this->api->console->register("chatoff", "Turn off the chat", array($this, "Pref"));
                $this->api->console->register("profanefilter", "UltraChat profane filter", array($this, "commandHandler")
		console(FORMAT_GREEN."[UltraChat] Loaded and ready to go!");
		
	}
	
	public function __destruct(){
	}
	
	public function readConfig(){
		$this->path = $this->api->plugin->createConfig($this, array(
			"chat-format" => "[{prefix}]<{DISPLAYNAME}> {MESSAGE}",
			"default" => "Player",
			"chat" => "enable",
		));
	}

	
	public function Pref($cmd, $args){
	switch($cmd){
	    case "prefix":
	      $player = $args[0];
	      $pref = $args[1];
	      
	      $this->config['player'][$player]['pref'] =$pref;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "Changed ".$player."'s prefix to ".$pref.".\n";
	      $this->api->chat->sendTo(false, "Your prefix has been changed to ".$pref." !", $player);
      break;
	    case "defprefix":
	      $def = $args[0];
	       
	      $this->config['default']=$def;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	       
	      $output .= "The default player prefix has been changed to ".$def.".\n";
	    break;
	    case "delprefix":
	      $player = $args[0];
	       
	      unset($this->config['player'][$player]['pref']);
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	       
	      $output .= "[UChat] Deleted".$player."'s  prefix.\n";
	      $this->api->chat->sendTo(false, "Your prefix is now ".$def."!", $player);
	    break;
	    case "nick":
	      $player = $args[0];
	      $nick = $args[1];
	      
	      $this->config['player'][$player]['nick'] = .$nick;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player." Is now nicknamed ".$nick.".\n";
	      $this->api->chat->sendTo(false, "Your nickname is now ".$nick." !", $player);
      break;
      case "delnick":
	      $player = $args[0];
	      
	      unset($this->config['player'][$player]['nick']);
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player."'s nickname has been removed.\n";
	      $this->api->chat->sendTo(false, "Your nickname has been removed.", $player);
      break;
      case "mute":
	      $player = $args[0];
	      
	      $this->config['player'][$player]['mute'] = true;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player." has been muted.\n";
	      $this->api->chat->sendTo(false, "You have been muted from the chat.", $player);
      break;
      case "unmute":
	      $player = $args[0];
	      
	      unset($this->config['player'][$player]['mute']);
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player." is has been unmuted\n";
	      $this->api->chat->sendTo(false, "You are no longer muted from chat.", $player);
      break;
	  case "chaton":
	      $this->config['chat']="enable";
		  $output .= "Chat has been enabled\n";
	  break;
	  case "chatoff":
	      $this->config['chat']="disable";
		  $output .= "Chat has been disabled\n";
	  break;
      default:		$output .= 'UltraChat Plugin by Exxarion';
      break;
	  }
	  return $output;
	  }

			case "player.chat":
          $player = $data["player"]->username;
		  If(!isset($this->config['player'][$player]['mute']) && $this->config['chat']=="enable")
		  {
		     If(!isset($this->config['player'][$player]['pref'])){
		     $prefix=$this->config['default'];
		     }
		     else{
		     $prefix= $this->config['player'][$player]['pref'];
		     }
		     If(!isset($this->config['player'][$player]['nick'])){
		     $nickname=$player;
		     }
		     else{
		     $nickname=$this->config['player'][$player]['nick'];
		     }
			 }
		    
          $data = array("player" => $data["player"], "message" => str_replace(array("{DISPLAYNAME}", "{MESSAGE}", "{prefix}"), array($nickname, $data["message"], $data["player"]->level->getName(), $prefix, $kills), $this->config["chat-format"]));
          if($this->api->handle("UltraChat.".$event, $data) !== false){
					  $this->api->chat->broadcast($data["message"]);
				 }
				 return false;
		  }
		   elseif(isset($this->config['player'][$player]['mute']))
		   {
		   $this->api->chat->sendTo(false, "You cannot use the chat.", $player);
		   return false;
		   }
		   else
		   {
		   $this->api->chat->sendTo(false, "Chat is disabled", $player);
		   return false;
		   }
}

public function __destruct(){}

public function eventHandle($data, $event) {
$message = $data['message'];
$player = $data['player'];
$username = $player->username;
if (!isset($this->lastmessage[$username])) {
	$this->lastmessage[$username] = "";
}
if (strtolower($message) == $this->lastmessage[$username]) {
	$this->chatblock[$username] = strtotime("now") + 60;
	$player->sendChat("No spamming. You are now blocked from the chat");
	return false;
}
elseif (isset($this->chatblock[$username])) {
	
if (strtotime("now") > $this->chatblock[$username]) {
	unset($this->chatblock[$username]);
	$player->sendChat('You may chat again!');
	return true;
}
else {
 $player->sendChat('Spamming is not allowed!');
  return false;
	
}
}
else {
	$this->lastmessage[$username] = strtolower($message);
	return true;
}
{
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$server = ServerAPI::request();
	}
	
	public function init(){	

		$this->config['config'] = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array(
			"mode" => "comprehensive",
		));
 
		$trim = explode("\n", Utils::curl_get("http://www.bannedwordlist.com/lists/swearWords.txt"));
		$this->config['blockwords'] = array();
		foreach($trim as $p)
		{
			$this->config['blockwords'][] = trim($p);
		}
	}
	
	public function eventHandler($data, $event)
	{
		switch($event)
		{
			case "player.chat":
				//$this->api->chat->send($data['player'], $this->attatchChatPrefix($data['player'], $data['message']));
				if($this->checkSpam($data['message'], "comprehensive") === true)
				{
					$this->api->chat->send($data['player'], $data['message']);
				}
				else
				{
					$data['player']->sendChat("[ChatGuard] Your chat message has been blocked.");
				}
				return false;
				break;
		}
	}	
	
	private function checkSpam($string, $mode)
	{
		if($mode == 'comprehensive')
		{
			$string = preg_replace('/[^\da-z]/i', '', $string);
			$string = preg_replace('/\s/', '', $string);
 
		}
			
		foreach($this->config['blockwords'] as $badword)
		{
			if(strpos($string, $badword) !== false)
			{
				return false;
			}
		}
		return true;
	}
 
	public function minuteSchedule()
	{
		//TODO:Add Stuff here
	}
	
	public function commandHandler($cmd, $params, $issuer, $alias){
		$output = "";
		if($cmd != "profanefilter")
		{
			$output .= "Invalid command.";
			return $output;
		}
			
		if($issuer instanceof Player)
		{
			$output .= "Command can only be run by console.";
			return $output;
		}
			
		switch(array_shift($params)){
 
		}
		return $output;
	}
	
	public function __destruct()
	{
		
}
		}
	}	
}
