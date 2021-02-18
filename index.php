<!DOCTYPE html>
<html>
<head>
	<title>To Do</title>
	<style type="text/css">
		body{
			margin: 0 auto;
			width: 80%;
			background: #F9F9FA;
		}
		.large-input{
			width: 100%;
			height: 45px;
			border: 0px;
			margin-top:5px;
			margin-bottom: 5px;
			padding-left: 5px;
			padding-right: 5px;
			box-shadow: 2px 2px 10px -2px #000;
		}
		.large-input-edit{
			width: 98%;
			height: 45px;
			border: 0px;
			padding-left: 5px;
			padding-right: 5px;
		}
		.large-input-edit:focus{
			box-shadow: 2px 2px 10px -2px #000;
		}

		h1{
			font-size: 50pt;
			color: #c0c0c0;
			text-align: center;
			margin: 0px;
		}
		table{
			width: 100%;
		}
		td{
			height: 45px;
			background: #fff;
		}
		.underline{
			text-decoration: line-through;
		}
	</style>
</head>
<body style="">
	<div style="margin: 0 auto; width: 60%">
		<h1>todos</h1>
		<input class="large-input" type="text" name="to_do_input" id="to_do_input" placeholder="what need to be done?" onkeydown="onKeyDownOfInput(this)">
		<table id="to_do_list"></table>
		<table id="to_do_action" style="display: none">
			<tr>
				<td><span id="total_count_view">0</span> items left</td>
				<td><a href="#" onclick="showTodos();">All</a></td>
				<td><a href="#" onclick="showTodos(0);">Active</a></td>
				<td><a href="#" onclick="showTodos(1);">Completed</a></td>
				<td align="right"><a id="clearCompleted_btn" href="#" onclick="clearCompleted();">Clear completed</a></td>
			</tr>
		</table>

		<input type="hidden" id="count_total" value="0">
		<input type="hidden" id="completed_status" value="0">
	</div>
</body>
<script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
<script type="text/javascript">
	var to_do_id = 0;
	$(document).ready(function(){
		$("#completed_status").val(0);
		$("#count_total").val(0);
		$("#add_to_do").on("click", function(){
			var to_do_input = $("#to_do_input").val();
			if (to_do_input.trim() != "") {
				createToDo(to_do_input.trim());
			}
		});
		showTodos();
		
	});

	
	function clearTodoView(){
		$("#to_do_list").html("");
		$("#completed_status").val(0);
		$("#count_total").val(0);
		$("#to_do_action").hide();
	}
	function onKeyDownOfInput(element){
		if(event.key === 'Enter') {
			let todo = element.value;
			if (todo.trim() != "") {
				element.value = "";
				createToDo(todo.trim());
			}      
	    }
	}

	function onKeyDownOfInputEdit(todo_id){
		if(event.key === 'Enter') {
			$("#to_do_input_"+todo_id).trigger("blur");
			editTodo(todo_id);
	    }
	}

	function editTodo(todo_id) {
		let todo = $("#to_do_input_"+todo_id).val();
		$.ajax({
            url: '/todo/todoapi.php',
            type: "POST",
            data: { action: "UPDATETODO", todo: todo, to_do_id: todo_id},
            datatype: 'json',
            async: false,
            success: function(response){
            	let response_data = $.parseJSON(response);
            	
            	if(response_data.status != "OK"){
            		let todoAct = response_data.todo;
            		$("#to_do_input_"+todo_id).val(todoAct.todo);
            		alert("Blank todo! todo was not updated!");
            	}
            	
            },
            error: function(model, xhr, options){
                alert('failed');
            }
   		});
	}

	function showTodos(recordStatus = "") {
		clearTodoView();
		$.ajax({
            url: '/todo/todoapi.php',
            type: "POST",
            data: { action: "GETTODOLIST", recordStatus: recordStatus},
            datatype: 'json',
            async: false,
            success: function(response){
            	let response_data = $.parseJSON(response);
            	let todos = response_data.todos;
            	$.each(todos, function(key,val){
            		showCreatedTodoTr(val.id, val.todo, val.completed_status);
            	});

            	showHideActionPane(response_data.totalTodo, response_data.totalCompletedTodo);
            	
				
            	
            },
            error: function(model, xhr, options){
                alert('failed');
            }
   		});
   		return 0;
	}
	function createToDo(to_do) {
		$.ajax({
            url: '/todo/todoapi.php',
            type: "POST",
            data: { action: "CREATETODO", todo: to_do},
            datatype: 'json',
            async: false,
            success: function(response){
            	let response_data = $.parseJSON(response);
            	
            	if(response_data.status == "OK"){
            		let todo = response_data.todo;
            		showCreatedTodoTr(todo.id, todo.todo, todo.completed_status);
            	}
            	
            	showHideActionPane(response_data.totalTodo, response_data.totalCompletedTodo);
            	
            },
            error: function(model, xhr, options){
                alert('failed');
            }
   		});
	}

	function deleteToDo(to_do_id){
		$.ajax({
            url: '/todo/todoapi.php',
            type: "POST",
            data: { action: "DELETETODO", to_do_id: to_do_id},
            datatype: 'json',
            async: false,
            success: function(response){
            	let response_data = $.parseJSON(response);
            	
            	if(response_data.status == "OK"){
            		$("#to_do_"+to_do_id).remove();
            		
            		showHideActionPane(response_data.totalTodo, response_data.totalCompletedTodo);
            	}
            	
            },
            error: function(model, xhr, options){
                alert('failed');
            }
   		});

	}

	function showCreatedTodoTr(to_do_id, to_do, completed_status){

		let html_tr = "";
		html_tr += '<tr id="to_do_'+to_do_id+'">';
		html_tr += '	<td width="5%"><input type="checkbox" id="checkbox_'+to_do_id+'" onclick="completeTodo('+to_do_id+')"></td>';

		if(completed_status == 0){
			html_tr += '	<td id="to_do_text_'+to_do_id+'"><input class="large-input-edit" type="text" name="to_do_input_'+to_do_id+'" id="to_do_input_'+to_do_id+'" value="'+to_do+'" onkeydown="onKeyDownOfInputEdit('+to_do_id+')" onBlur="editTodo('+to_do_id+')"></td>';
		}
		else{
			html_tr += '	<td id="to_do_text_'+to_do_id+'">'+to_do+'</td>';
		}
		html_tr += '	<td width="5%"><img src="image/delete.gif" onclick="deleteToDo('+to_do_id+')"></td>';
		html_tr += '</tr>';
		$("#to_do_list").append(html_tr);
		completeTodoDesign(to_do_id, completed_status);
	}

	function showHideActionPane(totalTodo, totalCompletedTodo) {
		if(totalTodo == 0){
    		$("#to_do_action").hide();
    	}
    	else{
    		$("#total_count_view").html(totalTodo);
    		$("#to_do_action").show();
    	}
    	if (totalCompletedTodo == 0) {
    		$("#clearCompleted_btn").hide();
    	}
    	else{
    		$("#clearCompleted_btn").show();
    	}
	}

	function completeTodo(to_do_id) {
		$.ajax({
            url: '/todo/todoapi.php',
            type: "POST",
            data: { action: "COMPLETETODO", to_do_id: to_do_id},
            datatype: 'json',
            async: false,
            success: function(response){
            	let response_data = $.parseJSON(response);
            	
            	if(response_data.status == "OK"){
            		$("#to_do_text_"+to_do_id).html(response_data.todo.todo);
            		completeTodoDesign(to_do_id, 1);
            		showHideActionPane(response_data.totalTodo, response_data.totalCompletedTodo);
            	}
            	
            },
            error: function(model, xhr, options){
                alert('failed');
            }
   		});
	}

	function clearCompleted() {
		$.ajax({
            url: '/todo/todoapi.php',
            type: "POST",
            data: { action: "CLEARCOMPLETED"},
            datatype: 'json',
            async: false,
            success: function(response){
            	let response_data = $.parseJSON(response);
            	
            	if(response_data.status == "OK"){
            		showTodos();
            	}
            	
            },
            error: function(model, xhr, options){
                alert('failed');
            }
   		});
	}

	
	function completeTodoDesign(to_do_id, sts){
		if (sts == 1) {
			$("#to_do_text_"+to_do_id).addClass("underline");
            $("#checkbox_"+to_do_id).attr("disabled", true);
            $("#checkbox_"+to_do_id).attr("checked", true);
		}
	}
</script>
</html>