<?php

/*
__PocketMine Plugin__
name=UltraChat
description=All you need for complete chat control
version=1.0
author=Various authors and put together by Exxarion
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
		$this->api->addHandler("player.quit", array($this, "handler"), 5);		
		$this->readConfig();
		$this->api->console->register("prefix", "Change a Prefix", array($this, "Pref"));
		$this->api->console->register("defprefix", "Set default Prefix", array($this, "Pref"));
		$this->api->console->register("delprefix", "Delete a prefix", array($this, "Pref"));
		$this->api->console->register("nick", "Add a nickname to you or another user", array($this, "Pref"));
		$this->api->console->register("delnick", "Remove user's nickname", array($this, "Pref"));
		$this->api->console->register("mute", "Shut a player up!", array($this, "Pref"));
		$this->api->console->register("unmute", "Allow the player to speak again", array($this, "Pref"));
		$this->api->console->register("chaton", "Enable chat", array($this, "Pref"));
		$this->api->console->register("chatoff", "Disable chat", array($this, "Pref"));
		console(FORMAT_GREEN."[UltraChat] Loaded and ready to go!");
		
	}
	
	public function __destruct(){
	}
	
	public function readConfig(){
		$this->path = $this->api->plugin->createConfig($this, array(
			"chat-format" => "{WORLDNAME}:[{prefix}]<{DISPLAYNAME}> {MESSAGE}",
			"default" => "Player",
			"chat" => "on",
		));
		$this->config = $this->api->plugin->readYAML($this->path."config.yml");
	}

	
	public function Pref($cmd, $args){
	switch($cmd){
	    case "prefix":
	      $player = $args[0];
	      $pref = $args[1];
	      
	      $this->config['player'][$player]['pref'] =$pref;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "[UChat] Gived ".$pref." to ".$player.".\n";
	      console(FORMAT_GREEN."Added".$pref." prefix to ".$player.".");
	      $this->api->chat->sendTo(false, "Your prefix has been changed to ".$pref." .", $player);
      break;
	    case "defprefix":
	      $def = $args[0];
	       
	      $this->config['default']=$def;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	       
	      $output .= "The Default player prefix is now ".$def.".\n";
	    break;
	    case "delprefix":
	      $player = $args[0];
	       
	      unset($this->config['player'][$player]['pref']);
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	       
	      $output .= "Deleted".$player."'s  prefix.\n";
	      $this->api->chat->sendTo(false, "Your prefix is has been set back to $def", $player);
	    break;
	    case "nick":
	      $player = $args[0];
	      $nick = $args[1];
	      
	      $this->config['player'][$player]['nick'] = .$nick;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player."'s Nickname has changed.\n";
	      console(FORMAT_GREEN."Changed ".$player."'s name to ".$nick." to.");
	      $this->api->chat->sendTo(false, "Your nickname has been changed to ".$nick." .", $player);
      break;
      case "delnick":
	      $player = $args[0];
	      
	      unset($this->config['player'][$player]['nick']);
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "[UChat] ".$player."'s name is now real.\n";
	      console(FORMAT_GREEN."".$player."'s Nickname has been removed.\n");
	      $this->api->chat->sendTo(false, "Your Nickname has been reset", $player);
      break;
      case "mute":
	      $player = $args[0];
	      
	      $this->config['player'][$player]['mute'] = true;
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player." has been silenced.\n";
	      console(FORMAT_GREEN."".$player." was muted.\n");
	      $this->api->chat->sendTo(false, "You are not allowed to chat, ".$player."", $player);
      break;
      case "unmute":
	      $player = $args[0];
	      
	      unset($this->config['player'][$player]['mute']);
	      $this->api->plugin->writeYAML($this->path."config.yml", $this->config);
	      
	      $output .= "".$player." is allowed to chat again\n";
	      console(FORMAT_GREEN."".$player." has been Un-muted\n");
	      $this->api->chat->sendTo(false, "You are now allowed to use the chat", $player);
      break;
	  case "chaton":
	      $this->config['chat']="on";
		  $output .= "The chat has been enabled\n";
	  break;
	  case "chatoff":
	      $this->config['chat']="off";
		  $output .= "Chat has been disabled\n";
	  break;
      default:		$output .= 'Chat plugin put together by Exxarion';
      break;
	  }
	  return $output;
	  }
      break;
			case "player.chat":
          $player = $data["player"]->username;
		  If(!isset($this->config['player'][$player]['mute']) && $this->config['chat']=="on")
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
		    
          $data = array("player" => $data["player"], "message" => str_replace(array("{DISPLAYNAME}", "{MESSAGE}", "{WORLDNAME}", "{prefix}"), array($nickname, $data["message"], $data["player"]->level->getName(), $prefix), $this->config["chat-format"]));
          if($this->api->handle("UltraChat.".$event, $data) !== false){
					  $this->api->chat->broadcast($data["message"]);
				 }
				 return false;
		  }
		   elseif(isset($this->config['player'][$player]['mute']))
		   {
		   $this->api->chat->sendTo(false, "You are not allowed to use chat.", $player);
		   return false;
		   }
		   else
		   {
		   $this->api->chat->sendTo(false, "Chat is disabled", $player);
		   return false;
		   }
{
	private $api, $server;
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$server = ServerAPI::request();
		
		$this->api->plugin->load("http://gist.github.com/sekjun9878/6636915/raw/ChatGuard.php");
	}
	
	public function init()
	{
		
	}
	
	public function __destruct(){

	}
}

public function init(){

$this->api->addHandler("player.chat", array($this,"eventHandle"),50);
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

			break;
		}
	}	
}
