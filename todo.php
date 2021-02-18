<?php

	class Todo
	{
		private $servername = "localhost";
		private $username 	= "root";
		private $password 	= "";
		private $database 	= "webdev_task";
		public  $con;


		public function __construct()
		{
			$this->con = new mysqli($this->servername, $this->username,$this->password,$this->database);
			if(mysqli_connect_error()) {
				trigger_error("Failed to connect to MySQL: " . mysqli_connect_error());
			}else{
				return $this->con;
			}
		}

		public function insertData($todo)
		{
			$todo = $this->con->real_escape_string($todo);
			$query = "INSERT INTO todos(todo,createDateTime) VALUES('$todo', NOW())";
			$sql = $this->con->query($query);
			if ($sql==true) {
				return $this->con->insert_id;;
			}else{
				return 0;
			}
		}

		public function displayData($recordStatus)
		{
			$query = "SELECT * FROM todos";
			if ($recordStatus != "") {
				$query .= " WHERE completed_status = '".$recordStatus."'";
			}

			$query .= " ORDER BY id ASC";
			$result = $this->con->query($query);
			if ($result->num_rows > 0) {
				$data = array();
				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
				return $data;
			}else{
				return array();
			}
		}

		public function displyaRecordById($id)
		{
			$query = "SELECT * FROM todos WHERE id = '$id'";
			$result = $this->con->query($query);
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return $row;
			}else{
				return array();
			}
		}



		public function updateRecord($id, $todo)
		{
			if (trim($todo) == "") {
				return 0;
			}
			$todo = $this->con->real_escape_string($todo);
			$query = "UPDATE todos SET todo = '".$todo."' WHERE id = '".$id."'";
			$sql = $this->con->query($query);
			if ($sql==true) {
				return 1;
			}else{
				return 0;
			}
			
		}

		public function deleteRecord($id)
		{
			$query = "DELETE FROM todos WHERE id = '$id'";
			$sql = $this->con->query($query);
			if ($sql==true) {
				return 1;
			}else{
				return 0;
			}
		}

		public function deleteCompletedRecord()
		{
			$query = "DELETE FROM todos WHERE completed_status = 1";
			$sql = $this->con->query($query);
			if ($sql==true) {
				return 1;
			}else{
				return 0;
			}
		}
		

		public function completeRecord($id)
		{
			$query = "UPDATE todos SET completed_status = 1 WHERE id = '$id'";
			$sql = $this->con->query($query);
			if ($sql==true) {
				return 1;
			}else{
				return 0;
			}
		}

		public function getTotalTodo()
		{
			$query = "SELECT COUNT(id) as cnt FROM todos";
			$result = $this->con->query($query);
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return $row["cnt"];
			}else{
				return 0;
			}
		}

		public function getTotalCompleteTodo()
		{
			$query = "SELECT COUNT(id) as cnt FROM todos WHERE completed_status = 1";
			$result = $this->con->query($query);
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return $row["cnt"];
			}else{
				return 0;
			}
		}

	}
?>