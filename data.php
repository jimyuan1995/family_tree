<?php
	require('config.php');
	
	function crsid_to_name(string $raw) {
		$crsid = mysqli_real_escape_string($GLOBALS['db'], $raw);

		$sql_statement = "SELECT name FROM member WHERE crsid='{$crsid}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);

		if ($row = mysqli_fetch_array($retval)) {
			return $row['name'];
		} else {
			return null;
		}
	}

	function name_to_crsid(string $raw) {
		$name = mysqli_real_escape_string($GLOBALS['db'], $raw);

		$sql_statement = "SELECT crsid FROM member WHERE name='{$name}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);

		if ($row = mysqli_fetch_array($retval)) {
			return $row['crsid'];
		} else {
			return null;
		}
	}
	
	function get_parent_family(string $raw) {
		$crsid = mysqli_real_escape_string($GLOBALS['db'], $raw);
		$sql_statement = "SELECT * FROM family JOIN children ON family.fid=children.fid WHERE cid='{$crsid}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);

		if ($row = mysqli_fetch_array($retval)) {
			return $row;
		} else {
			return null;
		}
	}


	function get_father(string $crsid) {
		$family = get_parent_family($crsid);
		if ($family) {
			return $family['hid'];
		} else {
			return null;
		}
	}

	function get_children(string $raw) {
		$crsid = mysqli_real_escape_string($GLOBALS['db'], $raw);
		$sql_statement = "SELECT * FROM family JOIN children ON family.fid=children.fid WHERE hid='{$crsid}' OR wid='{$crsid}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);

		$cids = array();
		while ($row = mysqli_fetch_array($retval)) {
			array_push($cids, $row['cid']);
		}
		
		return $cids;
	}

	function get_partner(string $raw) {
		$crsid = mysqli_real_escape_string($GLOBALS['db'], $raw);
		$sql_statement = "SELECT * FROM family WHERE hid='{$crsid}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);
		if ($row = mysqli_fetch_array($retval)) {
			return $row['wid'];
		}

		$crsid = mysqli_real_escape_string($GLOBALS['db'], $raw);
		$sql_statement = "SELECT * FROM family WHERE wid='{$crsid}';";
		$retval = mysqli_query($GLOBALS['db'], $sql_statement);
		if ($row = mysqli_fetch_array($retval)) {
			return $row['hid'];
		}
		
		return null;
	}

    class Node {
		public $id;
		public $title;

		function Node(string $_id, string $_title) {
			$this->id = $_id;
			$this->title = $_title;
		}
	}

	class Tree {
		public $id;
		public $root;

		function Tree(int $_id, Node $_root) {
			$this->id = $_id;
			$this->root = $_root;
		}
	}

	function find_ancestor($id) {
		$f = get_father($id);
		while ($f) {
			$id = $f;
			$f = get_father($id);
		}
		return $id;
	}

	function gen_family_tree($id) {
		$node = new Node($id, crsid_to_name($id));
		$cids = get_children($id);
		
		$partner_id = get_partner($id);
		if ($partner_id) {
			$name = crsid_to_name($partner_id);
			$node->subtitle = "Married with $name";
		}

		if (count($cids) > 0) {
			$children = array();
			foreach ($cids as $cid) {
				$child_node = gen_family_tree($cid);
				$child_node->type = "subordinate";
				array_push($children, $child_node);
			}
			$node->children = $children;
		}
		return $node;
	}

	$crsid = $_GET['q'];
	$ancestor_id = find_ancestor($crsid);
	$root = gen_family_tree($ancestor_id);

	$data = new Tree(1, $root);

	echo (json_encode($data));
?>