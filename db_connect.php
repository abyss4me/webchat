<?php
/* 
			***Description of MySQL tables:***
	1th table user (id int PK, nickname varchar(64), login varchar(64), gender varchar(1))
	2th table users_chat (id int FK, messg TEXT, time int, status int(1))
	3th table public_messages (id int PK, message)
	4th table ban_table (id int FK, time int, isbanned int(1))
	5th table private_chat (id_from int FK, id_to int FK, message TEXT, id int PK);
*/
class ChatClass
{
	protected $pass = "Fg6n%3";
	protected $user = "root";
	protected $database = "chat_database";
	protected $host = "localhost";
	public $user_id;
    public $session_id;
	public $con = null;
	
	function __construct()
	{
		$this->con = mysqli_connect($this->host, $this->user, $this->pass, $this->database);
		if (mysqli_connect_errno())
			echo "Failed to connect to MySQL: " . $mysqli_connect_error();
	}
	function __destruct()
	{
		//print "Destructor is done!";	
	}
	
	function UserRegister()
	{
		if (isset($_GET['nickname']) && isset($_GET['login']) && isset($_GET['gender'] )) {
			session_start();		/* initiate session */
			$_SESSION["name"] = trim($_GET['nickname']); /* store nickname in Global */
			$nickname = trim($_GET['nickname']);

			$q = mysqli_query($this->con,"SELECT isbanned FROM ban_table JOIN user ON user.id=ban_table.id WHERE user.name='$nickname';"); /* check for ban */
			//if (mysqli_connect_errno())
		    //+echo "Failed to connect to MySQL 1: " . $mysqli_connect_error;
			$result = mysqli_fetch_assoc($q);
			if ($result['isbanned'] == 1) {
				echo "userisbanned";
				exit();
			}
			$time = time();
			$status = 1;
			$pass = md5($_GET['login']);
			$q = mysqli_query($this->con,"SELECT * FROM user;");
			while(($result = mysqli_fetch_assoc($q))) {	
				if (($result['name'] === $nickname) && ($result['sex'] === $_GET['gender'])) {
					if ($result['pass'] === md5($_GET['login']))  {
					 /* in case of success we uptade users_chat table (time stamp and status) */
						$user_id = $result['id'];
						$q = mysqli_query($this->con,"UPDATE users_chat SET time='$time', status='$status' WHERE id='$user_id';") 
									or die ("Invalid query_1: " . mysqli_errno($this->con));
						mysqli_close($this->con);			
						exit();
					} else { 
						echo "err_user_exists";
						mysqli_close($this->con);
						exit();					 				   	
					}		
				} elseif (($result['name'] === $nickname) && ($result['sex'] !== $_GET['gender'])) {
					echo "err_user_exists";
					mysqli_close($this->con);
					exit();	
				}
			}
		/* if username is new - add it to database! */	
			$q = mysqli_query($this->con,"INSERT INTO user (name, pass, sex) VALUES ('$nickname', '$pass', '$_GET[gender]');") 
								or die ("Invalid query_2: " . mysqli_errno($this->con));	
			$q = mysqli_query($this->con,"SELECT * FROM user;");					
			while(($result = mysqli_fetch_assoc($q))) {
					if ($result['name'] == $_GET['nickname'])					
							$user_id = $result['id'];		
			}				
			$message = "";			
			$q = mysqli_query($this->con,"INSERT INTO users_chat (id, messg, time, status) VALUES ('$user_id', '$message', '$time', '$status');") 
								or die ("Invalid query_3: " . mysqli_errno($this->con));						
			mysqli_close($this->con);	
		}
	}
	
	function PrintOnlineUsers() {	
		if ((isset($_GET['getonlineusers']) == "get" )) {
		$username = $_GET['username'];
      	$time = time();
      	$q = mysqli_query($this->con,"UPDATE users_chat JOIN user ON user.id=users_chat.id SET users_chat.time='$time' 
								 WHERE user.name = '$username';"); /* updating timestamp */
		$q = mysqli_query($this->con,"UPDATE users_chat SET status=0 WHERE ('$time' - time) > 60;");	/* if user has exited closing browser's tab, timeout=60sec */		 
		$list = "[";
		$q = mysqli_query($this->con,"SELECT name, sex FROM user JOIN users_chat ON user.id=users_chat.id WHERE users_chat.status=1 ORDER BY user.name;")
								or die ("Invalid query_4: " . mysqli_errno($this->con));
		$n = mysqli_num_rows($q);
		while($n) { 								/* while cycle to patch result into JSON array a = ["res1", "res2"...];*/
			$result = mysqli_fetch_assoc($q);
			$list = $list . "{" . "\"name\"" . ":" . "\"" . $result['name'] ."\"" . "," . "\"sex\"" . ":" . "\"" . $result['sex'] ."\"" . "}";
			if ($n > 1) {
				$list = $list . ",";
			} else {
				$n--;
				continue;
			}
			$n--;
		}
		$list = $list . "]";	
/* [{"name":"nick1","sex":"m"},{"name":"nick2","sex":"f"}]	*/
		echo $list; 		/*send JSON string to the client*/
		mysqli_close($this->con);
		}
	}
    function SmileReplace($message) {
		$message= str_replace("*angel*","<img src = './smiles/aa.gif'>",$message);
		$message = str_replace("*smile*","<img src = './smiles/ab.gif'>",$message);
		$message = str_replace("*sad*","<img src = './smiles/ac.gif'>",$message);
		$message = str_replace("*wink*","<img src = './smiles/ad.gif'>",$message);
		$message = str_replace("*tangue*","<img src = './smiles/ae.gif'>",$message);
		$message = str_replace("*glass*","<img src = './smiles/af.gif'>",$message);
		$message = str_replace("*ROFL*","<img src = './smiles/ag.gif'>",$message);
		$message = str_replace("*shy*","<img src = './smiles/ah.gif'>",$message);
		$message = str_replace("*surprised*","<img src = './smiles/ai.gif'>",$message);
		$message = str_replace("*kiss*","<img src = './smiles/aj.gif'>",$message);
		$message = str_replace("*cry*","<img src = './smiles/ak.gif'>",$message);
		$message = str_replace("*silent*","<img src = './smiles/al.gif'>",$message);
		$message = str_replace("*evil*","<img src = './smiles/am.gif'>",$message);
		$message = str_replace("*dummy*","<img src = './smiles/an.gif'>",$message);
		$message = str_replace("*proud*","<img src = './smiles/ao.gif'>",$message);
		$message = str_replace("*chuckl*","<img src = './smiles/ap.gif'>",$message);
		$message = str_replace("*devil*","<img src = './smiles/aq.gif'>",$message);
		$message = str_replace("*music*","<img src = './smiles/ar.gif'>",$message);
		$message = str_replace("*flirt*","<img src = './smiles/as.gif'>",$message);
		$message = str_replace("*vomit*","<img src = './smiles/at.gif'>",$message);
		$message = str_replace("*yawn*","<img src = './smiles/au.gif'>",$message);
		$message = str_replace("*stop*","<img src = './smiles/av.gif'>",$message);
		$message = str_replace("*love*","<img src = './smiles/aw.gif'>",$message);
		$message = str_replace("*flower*","<img src = './smiles/ax.gif'>",$message);
		$message = str_replace("*thumbsup*","<img src = './smiles/ay.gif'>",$message);
		$message = str_replace("*booze*","<img src = './smiles/az.gif'>",$message);
		return $message;
	}
	function PutMessage() {
		if (isset($_GET['message'])) {//echo $_GET['message'];
		$msg = ChatClass::SmileReplace($_GET['message']);
		//$msg = $_GET['message'];
		$str = mysqli_real_escape_string($this->con, $msg);
		$q = mysqli_query($this->con,"INSERT INTO public_messages (message) VALUES ('$str');")
						or die ("Invalid query_5: " . mysqli_error($this->con));
		mysqli_close($this->con);
		}
	}

	function MakeJSONstring($n, $q) {
		$list = "[";
		while($n) { 								
			$result = mysqli_fetch_assoc($q);
			$list = $list . "{" . "\"id\"" . ":" . "\"" . $result['id'] ."\"" . "," . "\"message\"" . ":" . "\"" . $result['message'] ."\"" . "}";
			if ($n > 1) {
				$list = $list . ",";
			} else {
				$n--;
				continue;
			}
		$n--;
		}
		$list = $list . "]";
		return $list;
	}

	function FetchMessage() {
		if (isset($_GET['id'])) {
			if ($_GET['id'] == 0) {		   
				$q = mysqli_query($this->con,"SELECT id, message FROM public_messages ORDER BY id DESC LIMIT 15;")
					or die ("Invalid query_6: " . mysqli_errno($this->con));
				$n = mysqli_num_rows($q);
				if ($n == 0) {
					echo "[0]";
					exit();
				}	
				echo ChatClass::MakeJSONstring($n, $q);
			}
			else {  /* if ID  != 0 */
				$id = $_GET['id']; 
				$q = mysqli_query($this->con,"SELECT id, message FROM public_messages WHERE id>'$id';") //LIMIT $delta OFFSET $offset;")
					or die ("Invalid query_7_2: " . mysqli_error($this->con));
				$n = mysqli_num_rows($q);
				if ($n == 0) {
					echo "[0]";
					mysqli_close($this->con);
					exit();
			}
			echo ChatClass::MakeJSONstring($n, $q);			
			}
		}
		mysqli_close($this->con);		
	}
	
	function OnExit() {
		if (isset($_GET['session_id'])) { 
			$users_id = $_GET['session_id'];
			$q = mysqli_query($this->con,"UPDATE users_chat JOIN user ON user.id=users_chat.id SET users_chat.status=0
									 WHERE user.name = '$users_id';")
					or die ("Invalid query_7: " . mysqli_error($this->con));
			mysqli_close($this->con);
		}
	}

	function RetrieveUsername() {
		session_start();
		if (isset($_SESSION["name"])) {
			echo $_SESSION["name"];
			session_unset();
			session_destroy();
		}		
		else { 
			echo "unset";
		}	
	}

	function BanUser($login, $time) {
		$q = mysqli_query($this->con,"SELECT id FROM user WHERE name='$login'");
		$result = mysqli_fetch_assoc($q);
		$id = $result['id'];
		$q = mysqli_query($this->con,"INSERT INTO ban_table VALUES ('$id', '$time', 1);");
	    mysqli_close($this->con);
	}

	function IsOnline() {
		if (isset($_GET['name'])) {
			$name =  $_GET['name'];
			$q = mysqli_query($this->con,"SELECT status FROM users_chat JOIN user ON user.id=users_chat.id WHERE user.name='$name';")
									or die ("Invalid query_4: " . mysqli_errno($this->con));
			$result = mysqli_fetch_assoc($q);
			$result['status'] ? ($f = "online") : ($f = "offline");
			echo $f;
		}
	}
}


//$wc = new ChatClass;
//$str = $wc->SmileReplace("hello world *angel*");
//echo $str; 
//$wc->UserRegister();

//phpinfo();
//exit();

class AssocArr {
		public $id;
		public $name;
		public $message;
}
class AssocMessageArr {
		public $id;
		public $message;
}
			
class PrivateChat extends ChatClass {
	function __construct() {
		parent::__construct();
	}
	
	function MakeJSONstring($n, $q) {
	$list = "[";
	while($n) { 								
		$result = mysqli_fetch_assoc($q);
		$list = $list . "{" . "\"id\"" . ":" . "\"" . $result['id'] ."\"" . "," . "\"message\"" . ":" . "\"" . $result['message'] ."\"" . "}";
		if ($n > 1) {
			$list = $list . ",";
			} else {
				$n--;
				continue;
			}
		$n--;
		}
		$list = $list . "]";
		return $list;
	}	
	function GetUsersId() {
		$q = mysqli_query($this->con,"SELECT user.id, name FROM user RIGHT JOIN users_chat ON user.id=users_chat.id
		 WHERE status=1;")
			or die ("Invalid query_5: " . mysqli_error($this->con));
		$i=0;	
		while (($result = mysqli_fetch_assoc($q)) != null) { /* making array of object */
			$cl =  new AssocArr();
			$c[] = $cl;
			$c[$i]->id = $result['id'];
			$c[$i]->name = $result['name'];
			$i++;
		}
		mysqli_close($this->con);
		echo json_encode($c);
	}
	
	function PutPrivateMessage() {
		$id_to = $_GET['id_to'];	
		$message = mysqli_real_escape_string($this->con, $_GET['message']);
		$name_from = $_GET['name_from'];
		$q = mysqli_query($this->con,"SELECT id FROM user WHERE name='$name_from'")
				or die ("Invalid query_8: " . mysqli_error($this->con));
		$result = mysqli_fetch_assoc($q)	;
		$id_from = $result['id'];
		$q = mysqli_query($this->con,"INSERT INTO private_chat (id_from, id_to, message) VALUES ('$id_from','$id_to','$message')")
				or die ("Invalid query_8_1: " . mysqli_error($this->con));
		if ($id_to == $id_from) {
			return false;
		}	
		else
			$q = mysqli_query($this->con,"INSERT INTO private_chat (id_from, id_to, message) VALUES ('$id_to','$id_from','$message')")
				or die ("Invalid query_8_1: " . mysqli_error($this->con));	
		mysqli_close($this->con);
	}
	
	function FetchPrivateMessage() {
		$name_from = $_GET['name_from'];
		$id = $_GET['msg_id'];
		$q = mysqli_query($this->con,"SELECT id FROM user WHERE name='$name_from'")
				or die ("Invalid query_9_1: " . mysqli_error($this->con));
		$result = mysqli_fetch_assoc($q)	;
		$id_from = $result['id'];
		if ($id == 0) {
			$q = mysqli_query($this->con,"SELECT id, message FROM private_chat WHERE id_to='$id_from';") /* select all messages if id = 0 */
				or die ("Invalid query_9_2: " . mysqli_error($this->con));
			$n = mysqli_num_rows($q);
			if ($n == 0) {
				echo "[0]";
				exit();
			}
			echo PrivateChat::MakeJSONstring($n, $q);
		}
			/* if id > 0 */
		else {	
			$q = mysqli_query($this->con,"SELECT id, id_to, id_from, message FROM private_chat WHERE id_to='$id_from' AND id>'$id';")
				or die ("Invalid query_7_2: " . mysqli_error($this->con));		
			$n = mysqli_num_rows($q);
		   	if ($n == 0) {
				echo "[0]";
				mysqli_close($this->con);
				exit();
			}
			echo PrivateChat::MakeJSONstring($n, $q);
		}
	}

}

?>
