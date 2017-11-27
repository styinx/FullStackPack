<?php

/**
 * The Session class controls the visit on a website. If the user opens the website a
 * session object is created. This session is valid until the page gets closed again.
 * After a login the session object can create a user object that holds information about
 * a user account. This object gets destroyed after the logout.
 */

 	//require_once(dirname(__FILE__) . "/../db/database.php");

	$USER_ACCESS = array	(
								0	=> "Blocked",
								10	=> "Guest",
								20	=> "Member",
								30	=> "",
								100	=> "Administrator"
							);

	function start_session()
	{
		$success = session_start();
		if(!isset($_SESSION["session"]))
		{
			$_SESSION["session"] = new Session();
		}
		return $success;
	}

	function start_user_session()
	{
		if(empty($_SESSION["session"]->getUser()))
		{
			$_SESSION["session"]->start();
		}
	}

	function stop_user_session()
	{
		if(!empty($_SESSION["session"]->getUser()))
		{
			$_SESSION["session"]->start();
		}
	}

	function stop_session()
	{
		if(isset($_SESSION["session"]))
		{
			$_SESSION["session"] = new Session();
		}
		session_destroy();
	}

	class Session
	{
		private $id 		= "";
		private $user 		= null;

		public function __construct()
		{
			$this->id = session_id();
		}

		public function start($username = "Guest", $realname = "Guest")
		{
			$this->user	= new User($username, $realname);
		}

		public function getId()
		{
			return $this->id;
		}

		public function getUser()
		{
			return $this->user;
		}

		public function destory()
		{
			$this->user = new User();
		}
	}

	class User
	{
		private $id 			= 0;
		private $username		= "Guest";
		private $realname 		= "Guest";
		private $registerDate 	= null;
		private $access			= 10;
		private $rankTitle 		= "";
		private $rank 			= "";
		private $logintime		= 0;
		private $ontime			= 0;
		private $browser		= null;
		private $device			= null;

		public function __construct($username = "Guest", $realname = "Guest")
		{
			$this->username = $username;
			$this->realname = $realname;
			$dt 			= new DateTime("now");
			$this->setLogintime($dt->getTimestamp());
			$this->setBrowser(getBrowserType());
			$this->setDevice(getDeviceType());
		}

		public function register($pw)
		{
			$crypt = 	[
							"cost" => 10,
							"salt" => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
						];
			return password_hash($pw, PASSWORD_BCRYPT, $crypt);
		}

		public function getUser()
		{
			$user = array();

			$user["id"] 		= $this->id;
			$user["username"] 	= $this->username;
			$user["realname"]	= $this->realname;

			return $user;
		}

		public function setUsername($username)
		{
			$this->username = $username;
		}

		public function getUsername()
		{
			return $this->username;
		}

		public function setRealname($realname)
		{
			$this->realname = $realname;
		}

		public function getRealname()
		{
			return $this->realname;
		}

		public function setRegisterDate($registerDate)
		{
			$this->registerDate = $registerDate;
		}

		public function getRegisterDate()
		{
			return $this->registerDate;
		}

		public function setAccess($access)
		{
			$this->access = $access;
		}

		public function getAccess()
		{
			return $this->access;
		}

		public function setRankTitle($rankTitle)
		{
			$this->rankTitle = $rankTitle;
		}

		public function getRankTitle()
		{
			return $this->rankTitle;
		}

		public function setRank($rank)
		{
			$this->rank = $rank;
		}

		public function getRank()
		{
			return $this->rank;
		}

		/**
		 * Saves the timestamp when a user logs in
		 */
		public function setLogintime($logintime)
		{
			$this->logintime = $logintime;
		}

		public function getLogintime()
		{
			return $this->logintime;
		}

		/**
		 * Returns the time that has passed since login
		 */
		public function getOntime()
		{
			$dt = new DateTime("now");
			$this->ontime = $dt->getTimestamp() - $this->logintime;
			return $this->ontime;
		}

		public function setBrowser($browser)
		{
			$this->browser = $browser;
		}

		public function getBrowser()
		{
			return $this->browser;
		}

		public function setDevice($device)
		{
			$this->device = $device;
		}

		public function getDevice()
		{
			return $this->device;
		}
	}

	function getBrowserType()
	{
		$browser = "unidentified";
		$userAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);

		if(strstr($userAgent, "gecko/"))
		{
			$browser = "Mozilla Firefox";
		}
		else if(strstr($userAgent, "msie") && strstr($userAgent, "opera"))
		{
			$browser = "Internet Explorer";
		}
		else if(strstr($userAgent, "trident"))
		{
			$browser = "Internet Explorer 11";
		}
		else if(strstr($userAgent, "edge"))
		{
			$browser = "Internet Explorer Edge";
		}
		else if(strstr($userAgent, "opera") || strstr($userAgent, "opr"))
		{
			$browser = "Opera";
		}
		else if(strstr($userAgent, "chrome"))
		{
			$browser = "Chrome";
		}
		else if(strstr($userAgent, "chrome") && strstr($userAgent, "safari"))
		{
			$browser = "Safari";
		}
		return $browser;
	}

	function getDeviceType()
	{
		$userAgent 		= $_SERVER["HTTP_USER_AGENT"];
		$deviceName		= "unidentified";
		$devicesTypes 	= array(
								"PC" 			=> array("msie 10", "msie 9", "msie 8", "windows.*firefox", "windows.*chrome", "x11.*chrome", "x11.*firefox", "macintosh.*chrome", "macintosh.*firefox", "opera", "trident"),
								"TABLET-DEVICE" => array("tablet", "android", "ipad", "tablet.*firefox"),
								"MOBILE-DEVICE" => array("mobile ", "android.*mobile", "iphone", "ipod", "opera mobi", "opera mini"),
								"BOT"      		=> array("googlebot", "mediapartners-google", "adsbot-google", "duckduckbot", "msnbot", "bingbot", "ask", "facebook", "yahoo", "addthis")
								);
		foreach($devicesTypes as $deviceType => $devices)
		{
			foreach($devices as $device)
			{
				if(preg_match("/" . $device . "/i", $userAgent))
				{
					$deviceName = ucfirst($deviceType);
				}
			}
		}
		return $deviceName;
 	}
?>