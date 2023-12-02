<?php
	//require file for database connection
	require_once("db/db_connect.php");
	
	//function call to connect to db
	$db_connect = db_connect();
	
	$employee_list_select = mysql_query("SELECT * FROM employee ORDER by full_name ASC");
	$i = 0;
	while($employee_list = mysql_fetch_array($employee_list_select))
	{
		$employee_names[$i]['id'] = $employee_list['id'];
		$employee_names[$i]['employee_id'] = $employee_list['employee_id'];
		$employee_names[$i]['full_name'] = $employee_list['full_name'];
		$employee_names[$i]['department_name'] = $employee_list['department_name'];
		$employee_names[$i]['current_timelog_id'] = $employee_list['current_timelog_id'];
		$i++;
	}
	 
if(strlen($_GET['str']) > 0){
	for($n = 0; $n < count($employee_names); $n++){
		$pos = stripos($employee_names[$n]['full_name'], $_GET['str']);
		
		if($pos !== FALSE){
			//echo str_replace($_GET['str'], '<b style="color: blue; font-family: Trebuchet MS">'.$_GET['str'].'</b>', $employee_names[$n]) . '<br>';
			?> 
				<div style="background: #D8D8D8; border-width: 1px; border-color: #808080; color: #27408b">
					<?php
						//echo str_replace($_GET['str'], '<b style="color: blue; font-family: Trebuchet MS">'.$_GET['str'].'</b>', $employee_names[$n]) . '<br>';
					?>
					<div class="eid" style="padding: 3px; height: 14px;" onmouseover="this.style.background = '#BCC6CC'" onmouseout="this.style.background = '#D8D8D8'" onclick="setvalue ('<?php echo ucwords($employee_names[$n]['full_name']); ?>'); setid ('<?php echo $employee_names[$n]['employee_id']; ?>'); setcurrentlogid ('<?php echo $employee_names[$n]['current_timelog_id']; ?>');"> 
						<?php 
							echo str_replace($_GET['str'], '<span style="color: blue; font-family: Trebuchet MS">'.$_GET['str'].'</span>', ucwords($employee_names[$n]['full_name'])) . '<br>'; 
						?>
					</div>
				</div>
   <?php 
		} 
	}
}

?>