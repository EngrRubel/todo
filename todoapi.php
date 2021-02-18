<?php 

include 'todo.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todoObj = new Todo();

    if (!isset($_POST['action'])) {
    	echo 'ERROR CODE';
    	exit();
    }

    $action = $_POST['action'];

    if ($action == "GETTODOLIST") {
    	$data = array(
			"todos" => $todoObj->displayData($_POST['recordStatus']),
			"totalTodo" => $todoObj->getTotalTodo(),
			"totalCompletedTodo" => $todoObj->getTotalCompleteTodo()
		);
		echo json_encode($data);
    }

    else if ($action == "CREATETODO") {
    	$status = "";
    	$todo = array();
    	$insertedId =  $todoObj->insertData($_POST['todo']);
    	
    	if ($insertedId == 0) {
    		$status = "ERROR";
    	}
    	else{
    		$status = "OK";
    		$todo = $todoObj->displyaRecordById($insertedId);
    	}
    	$data = array(
			"status" => $status,
			"todo" => $todo,
			"totalTodo" => $todoObj->getTotalTodo(),
			"totalCompletedTodo" => $todoObj->getTotalCompleteTodo()
		);
		echo json_encode($data);
    }

    else if ($action == "UPDATETODO") {
    	$sts = $todoObj->updateRecord($_POST['to_do_id'], $_POST['todo']);
    	if ($sts == 0) {
    		$status = "ERROR";
    		$todo = $todoObj->displyaRecordById($_POST['to_do_id']);
    	}
    	else{
    		$status = "OK";
    		$todo = $todoObj->displyaRecordById($_POST['to_do_id']);
    	}
    	$data = array(
			"status" => $status,
			"todo" => $todo
		);
		echo json_encode($data);
    }

    else if ($action == "DELETETODO") {
    	$status = "";
    	$isDelete = $todoObj->deleteRecord($_POST['to_do_id']);
    	
    	if ($isDelete == 1) {
    		$status = "OK";
    	}
    	else{
    		$status = "ERROR";
    	}
    	$data = array(
			"status" => $status,
			"totalTodo" => $todoObj->getTotalTodo(),
			"totalCompletedTodo" => $todoObj->getTotalCompleteTodo()
		);
		echo json_encode($data);
    }

    else if ($action == "COMPLETETODO") {
    	$status = "";
    	$isDelete = $todoObj->completeRecord($_POST['to_do_id']);
    	
    	if ($isDelete == 1) {
    		$status = "OK";
    	}
    	else{
    		$status = "ERROR";
    	}
    	$data = array(
			"status" => $status,
			"totalTodo" => $todoObj->getTotalTodo(),
			"todo" => $todoObj->displyaRecordById($_POST['to_do_id']),
			"totalCompletedTodo" => $todoObj->getTotalCompleteTodo()
		);
		echo json_encode($data);
    }
    else if ($action == "CLEARCOMPLETED") {
    	$status = "";
    	$isDelete = $todoObj->deleteCompletedRecord();
    	
    	if ($isDelete == 1) {
    		$status = "OK";
    	}
    	else{
    		$status = "ERROR";
    	}
    	$data = array(
			"status" => $status,
			"totalTodo" => $todoObj->getTotalTodo(),
			"totalCompletedTodo" => $todoObj->getTotalCompleteTodo()
		);
		echo json_encode($data);
    }


    else{
    	$data = array(
			"todos" => array(),
			"totalTodo" => 0,
			"totalCompletedTodo" => 0
		);
		echo json_encode($data);
    }

    
    
}
else{
	echo "ERROR!";
}
?>