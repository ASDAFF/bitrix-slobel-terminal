<?
class SlobelTerminal
{
	const realm="SLOBEL SSH Terminal";
	static $mycommabd=array('myhistory'=>'');
	private $mydir;
	private $output;
	private $prompt;
	private $cwd;
	private $fullcwd;
	private $history;
	private $os;
	private $command;
	private $users;
	private $realm;
	private $httpUsername;
	private $arUser;

function __construct()
	{
		global $USER;
		$rsUser = CUser::GetByID($USER->GetID());
		$this->arUser = $rsUser->Fetch();
		//$this->users=array('' =>'');
		$this->mydir=$_SERVER['DOCUMENT_ROOT'];
		$this->output=$_SESSION['sl_output'];
		$this->prompt=$_SESSION['sl_prompt'];
		$this->cwd=$_SESSION['sl_cwd'];
		$this->history=$_SESSION['sl_history'];
		$this->os=$_SESSION['sl_os'];
		$this->command=$_REQUEST['command'];
		$this->init();
	}
	
	function init()
	{
		$this->auth();
		
		if(@empty($_POST))
			$this->clear();
		
		if(@!empty($_GET['reset']))
			$this->clear(true);
		
		$this->initVars();
		
		if(empty($this->prompt))
			$this->formatPrompt();
		
		$this->buildCommandHistory();
		$this->outputHandle();
		$this->save();
	}
	
	function auth()
	{
		if (!empty($this->users)) {
			if(empty($_SERVER['PHP_AUTH_DIGEST'])){
				header('HTTP/1.1 401 Unauthorized');
        		header('WWW-Authenticate: Digest realm="'.self::realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5(self::realm).'"');
        		die("Bye-bye!\n");
			}
			
			if (!($data = $this->httpDigestParse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($this->users[$data['username']])) {
				die("Wrong Credentials!\n");
			}
			
			$A1 = md5($data['username'] . ':' . self::realm . ':' . $this->users[$data['username']]);
			$A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
			$valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);
			
			if ($data['response'] != $valid_response) {
				die("Wrong Credentials!\n");
			}
			
			$this->httpUsername=$data['username'];
		}
	}
	
	function httpDigestParse($txt)
	{
		$needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));
	
		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
	
		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}
	
		return $needed_parts ? false : $data;
	}
	
	function clear($flag)
	{
		unset($this->cwd);
		unset($this->output);
		unset($this->prompt);
		if($flag)
			unset($this->history);
	}
	
	function save()
	{
		$_SESSION['sl_output']=$this->output;
		$_SESSION['sl_prompt']=$this->prompt;
		$_SESSION['sl_cwd']=$this->cwd;
		$_SESSION['sl_history']=$this->history;
		$_SESSION['sl_os']=$this->os;
	}
	
	function draw($value)
	{
			switch ($value) {
				case 'fullcwd':
					return $this->fullcwd;
					break;
				case 'output':
					return $this->output;
					break;
				case 'history':
					return $this->history;
					break;
				case 'os':
					return $this->os;
					break;
			}
	}

	function formatPrompt()
	{
		$user=shell_exec("whoami");
		$host=explode(".", shell_exec("uname -n"));
		$this->prompt = rtrim($user)."@".rtrim($host[0]);
	}

	function initVars()
	{
		if (empty($this->os)){
			$os=explode(" \\", shell_exec("cat /etc/issue"." 2>&1"));
			$this->os=$os[0];
		}
		if (empty($this->cwd) || @!empty($_GET['reset']))
		{
			chdir($this->mydir);
			$this->cwd = getcwd();
			$this->output = '';
			$this->command ='';
		}
		if(empty($this->history) || @!empty($_GET['reset']))
			$this->history = array();
	}

	function buildCommandHistory()
	{
		$user=(!empty($this->arUser['NAME']))?$this->arUser['NAME']:$this->arUser['LOGIN'];
		$this->fullcwd = $this->prompt."/".str_replace('/', '', strrchr($this->cwd, '/')).">";
		
		if(!empty($this->command))
		{
			if(get_magic_quotes_gpc())
				$this->command = stripslashes($this->command);
			
			if(count($this->history)>=20) 
				array_shift($this->history);
			
			$this->history[]=$this->command;
			$this->output = $this->fullcwd.$this->command."\n\n";
			
		}
		else $this->output = $this->fullcwd."\n\nHello ".$user."! Enter your command!";
	}

	function outputHandle()
	{
		if (preg_match('|^[[:blank:]]*cd[[:blank:]]*$|', @$this->command))
		{
			$this->cwd = getcwd();
			$this->buildCommandHistory();
		}
		elseif(preg_match('|^[[:blank:]]*cd[[:blank:]]+([^;]+)$|', @$this->command, $regs))
		{
			($regs[1][0] == '/') ? $new_dir = $regs[1] : $new_dir = $this->cwd . '/' . $regs[1];

			while (strpos($new_dir, '/./') !== false)
				$new_dir = str_replace('/./', '/', $new_dir);
			
			while (strpos($new_dir, '//') !== false)
				$new_dir = str_replace('//', '/', $new_dir);
			
			while (preg_match('|/\.\.(?!\.)|', $new_dir))
				$new_dir = preg_replace('|/?[^/]+/\.\.(?!\.)|', '', $new_dir);

			if(empty($new_dir)) 
				$new_dir = "/";

			(@chdir($new_dir)) ? $this->cwd = $new_dir : $this->output .= "could not change to: $new_dir\n";
			$this->buildCommandHistory();
		}
		elseif(@$this->command=='myhistory'){
			foreach($this->history as $key => $val)
				$this->output .= htmlspecialchars($val."\n", ENT_COMPAT, 'UTF-8');
		}
		else
		{
			chdir($this->cwd);
			$stdout=shell_exec($this->command." 2>&1");
			$this->output .= htmlspecialchars($stdout, ENT_COMPAT, 'UTF-8');
		}
	}
}
?>