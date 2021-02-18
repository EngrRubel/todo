<?php
require_once 'DB.class.php';

class UserTools {

	public function test() {
		$result = mysqli_query("SELECT * FROM todos");
	}
}