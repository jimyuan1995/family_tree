<?php
    require ("config.php");

    class Respond {
        public $id;
        public $exist;
        function Respond(string $id, bool $exist) {
            $this->id = $id;
            $this->exist = $exist;
        }
    }

    function member_exist(string $raw) {
		$crsid = mysqli_real_escape_string($GLOBALS['db'], $raw);

		$sql_statement = "SELECT crsid FROM member WHERE crsid='{$crsid}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);

		if ($row = mysqli_fetch_array($retval)) {
			return true;
		} else {
			return false;
		}
    }
    
    $id = $_GET['q'];
    $respond = new Respond($id, member_exist($id));
    echo json_encode($respond);
