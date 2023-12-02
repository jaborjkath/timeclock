<?php
	//start session for Timeclock
	session_start();
	
	//require file for database connection
	require_once("db/db_connect.php");
	
	//function call to connect to db
	$db_connect = db_connect();
	
	$employee_list_id = array();
	$employee_list_select = mysql_query("SELECT * FROM employee ORDER by full_name ASC");
	$emp_counter = 0;
	while($result_list_select = mysql_fetch_array($employee_list_select))
	{
		$employee_list_id[$emp_counter]['employee_id'] = $result_list_select['employee_id'];
		$employee_list_id[$emp_counter]['full_name']  = $result_list_select['full_name'];
		$employee_list_id[$emp_counter]['department_name']  = $result_list_select['department_name'];
		$employee_list_id[$emp_counter]['email_address']  = $result_list_select['email_address'];
		$employee_list_id[$emp_counter]['timelog_id']  = $result_list_select['current_timelog_id'];
		
		$ctlid = $result_list_select['current_timelog_id'];
		$eid = $result_list_select['employee_id'];
		$employee_logs_select = mysql_query("SELECT * FROM employee_timelogs WHERE employee_timelogs.id='$ctlid' AND employee_timelogs.fk_employee_id='$eid'");
		while($result_employee_logs = mysql_fetch_array($employee_logs_select))
		{
			$employee_list_id[$emp_counter]['status']  = $result_employee_logs['status'];
			$employee_list_id[$emp_counter]['location']  = $result_employee_logs['location'];
			$employee_list_id[$emp_counter]['time']  = $result_employee_logs['time'];
			$employee_list_id[$emp_counter]['date']  = $result_employee_logs['date'];
			$employee_list_id[$emp_counter]['notes']  = $result_employee_logs['notes'];
		}
		$emp_counter++;
	}
	
	if(isset($_POST['buttonId']) == "login"){
		$employee_id=$_POST["employee_id"];
		$employee_password=$_POST["employee_passwd"];
		$employee_name_input=$_POST["name_input"];
		if(isset($_POST["employee_currentlogid"])) $employee_currentlogid=$_POST["employee_currentlogid"]; else	$employee_currentlogid = "";
		
		$user_list_select = mysql_query("SELECT * FROM users, employee WHERE users.fk_employee_id='$employee_id' AND users.password='$employee_password' 
										 AND employee.employee_id='$employee_id'");
		$user_row = mysql_fetch_assoc($user_list_select);
		
		if(mysql_num_rows($user_list_select) == 0){
			$message = 'error';
		}else{			
			$_SESSION['is_user_logged_in'] = true;
			$_SESSION['empid'] = $employee_id;
			$_SESSION['username'] = $employee_name_input;
			$_SESSION['epasswd'] = $employee_password;
			$_SESSION['emailaddress'] = $user_row['email_address'];
			$_SESSION['ecurrentlogid'] = $employee_currentlogid;
			
			$user_log_select = mysql_query("SELECT * FROM employee_timelogs WHERE employee_timelogs.id='{$_SESSION['ecurrentlogid']}' AND 
											employee_timelogs.fk_employee_id='{$_SESSION['empid']}'");
			$user_log_row = mysql_fetch_assoc($user_log_select);
			
			switch($user_log_row['status']){					
				case "break": 
						$_SESSION['in_is_disabled'] = " disabled";
						$_SESSION['break_is_disabled'] = " disabled";
						$_SESSION['out_is_disabled'] = " disabled";
						break;
				case "in":
				case "back":
						$_SESSION['in_is_disabled'] = " disabled";
						$_SESSION['back_is_disabled'] = " disabled";
						break;
				case "out":
						$_SESSION['break_is_disabled'] = " disabled";
						$_SESSION['back_is_disabled'] = " disabled";
						$_SESSION['out_is_disabled'] = " disabled";
						break;
				default:
						$_SESSION['break_is_disabled'] = " disabled";
						$_SESSION['back_is_disabled'] = " disabled";
						$_SESSION['out_is_disabled'] = " disabled";
						break;
			}
		}
	}
	
	if(isset($_SESSION['is_user_logged_in']))
	{
		if(isset($_POST['employee_id']))
		{
			$_SESSION['empid'] = $_POST['employee_id'];
			
		}
		
		if(isset($_SESSION['empid'])) $employee_id = $_SESSION['empid'];
		
		$user_active_select = mysql_query("SELECT * FROM employee, employee_timelogs WHERE employee.employee_id='$employee_id' AND employee.current_timelog_id=employee_timelogs.id");
		
		while($result_active_select = mysql_fetch_array($user_active_select)){
			$active_full_name = $result_active_select['full_name'];
			
			if(!empty($result_active_select['status'])) $_SESSION['active_status'] = $result_active_select['status']; else $_SESSION['active_status']="";
		}
		
		$set_current_date = date('Y-m-d');
		$set_current_time = date('h:i:s');
		
		$session_empid = $_SESSION['empid'];
		
		if(isset($_POST['notes_txt'])) $notes_txt = $_POST['notes_txt']; else $notes_txt = "";
		if(isset($_SESSION['active_status'])) $active_status = $_SESSION['active_status']; else $active_status = "";
		
		if(isset($_POST['in']) || $active_status == "in"){
			if(isset($_POST['in'])) $_SESSION['in'] = 'in';
			
			if(isset($_SESSION['in']) || $_SESSION['active_status'] == "in"){
				$_SESSION['in_is_disabled'] = " disabled";
				$_SESSION['break_is_disabled'] = " ";
				$_SESSION['back_is_disabled'] = " disabled";
				$_SESSION['out_is_disabled'] = " ";
			}else $_SESSION['in_is_disabled'] = " disabled";
			
			if(isset($_POST['in']))
			{
				$is_in_timelog_inserted = mysql_query("INSERT INTO employee_timelogs(id,fk_employee_id, status, location, time, date, notes) VALUES('', '$session_empid', 'in', 'home', '$set_current_time', '$set_current_date','$notes_txt')", $db_connect);
				if($is_in_timelog_inserted != true)
				{
					//$message = 'Error Action-in<br/>' . mysql_error($db_connect);
				}
				
				$maxid_select = mysql_query("SELECT MAX(id) as 'maxid' from employee_timelogs", $db_connect);
				while($maxid_result = mysql_fetch_array($maxid_select)){
					$maxid = $maxid_result['maxid'];
				}
				$_SESSION['mid'] = $maxid;
				
				$is_in_timelog_current = mysql_query("UPDATE employee SET current_timelog_id='$maxid' WHERE employee_id='$session_empid'", $db_connect);
			}
		}
		
		if(isset($_POST['break']) || $active_status == "break"){
			if(isset($_POST['break'])) $_SESSION['break'] = 'break';
			
			if(isset($_SESSION['break']) || $_SESSION['active_status'] == "break"){
				$_SESSION['break_is_disabled'] = " disabled";
				$_SESSION['in_is_disabled'] = " disabled";
				$_SESSION['back_is_disabled'] = " ";
				$_SESSION['out_is_disabled'] = " disabled";
			}else $_SESSION['break_is_disabled'] = " disabled";
			
			if(isset($_POST['break']))
			{
				$is_break_timelog_inserted = mysql_query("INSERT INTO employee_timelogs(id,fk_employee_id, status, location, time, date, notes) VALUES('','$session_empid', 'break', 'home', '$set_current_time', '$set_current_date','$notes_txt')", $db_connect);
				if($is_break_timelog_inserted != true)
				{
					//$message = 'Error Action-break<br/>' . mysql_error($db_connect);
				}
				
				$maxid_select = mysql_query("SELECT MAX(id) as 'maxid' from employee_timelogs", $db_connect);
				while($maxid_result = mysql_fetch_array($maxid_select)){
					$maxid = $maxid_result['maxid'];
				}
				$_SESSION['mid'] = $maxid;
				
				$is_break_timelog_current = mysql_query("UPDATE employee SET current_timelog_id='$maxid' WHERE employee_id='$session_empid'", $db_connect);
			}
		}
		
		if(isset($_POST['back']) || $active_status == "back"){
			if(isset($_POST['back'])) $_SESSION['back'] = 'back';
			
			if(isset($_SESSION['back']) || $_SESSION['active_status'] == "back"){
				$_SESSION['back_is_disabled'] = " disabled";
				$_SESSION['in_is_disabled'] = " disabled";
				$_SESSION['break_is_disabled'] = " ";
				$_SESSION['out_is_disabled'] = " ";				
			}else $_SESSION['back_is_disabled'] = " disabled";
			
			if(isset($_POST['back']))
			{
				$is_back_timelog_inserted = mysql_query("INSERT INTO employee_timelogs(id,fk_employee_id, status, location, time, date, notes) VALUES('','$session_empid', 'back', 'home', '$set_current_time', '$set_current_date','$notes_txt')", $db_connect);
				if($is_back_timelog_inserted != true)
				{
					//$message = 'Error Action-back<br/>' . mysql_error($db_connect);
				}
				
				$maxid_select = mysql_query("SELECT MAX(id) as 'maxid' from employee_timelogs", $db_connect);
				while($maxid_result = mysql_fetch_array($maxid_select)){
					$maxid = $maxid_result['maxid'];
				}
				$_SESSION['mid'] = $maxid;
				
				$is_back_timelog_current = mysql_query("UPDATE employee SET current_timelog_id='$maxid' WHERE employee_id='$session_empid'", $db_connect);
			}
		}
		
		if(isset($_POST['out']) || $active_status == "out"){
			if(isset($_POST['out'])) $_SESSION['out'] = 'out';
			
			if(isset($_SESSION['out']) || $_SESSION['active_status'] == "out"){
				$_SESSION['out_is_disabled'] = " disabled";
				$_SESSION['in_is_disabled'] = " ";
				$_SESSION['break_is_disabled'] = " disabled";
				$_SESSION['back_is_disabled'] = " disabled";
			}else $_SESSION['out_is_disabled'] = " disabled";
			
			if(isset($_POST['out']))
			{
				$is_out_timelog_inserted = mysql_query("INSERT INTO employee_timelogs(id,fk_employee_id, status, location, time, date, notes) VALUES('','$session_empid', 'out', 'home', '$set_current_time', '$set_current_date','$notes_txt')", $db_connect);
				if($is_out_timelog_inserted != true)
				{
					//$message = 'Error Action-out<br/>' . mysql_error($db_connect);
				}
				
				$maxid_select = mysql_query("SELECT MAX(id) as 'maxid' from employee_timelogs", $db_connect);
				while($maxid_result = mysql_fetch_array($maxid_select)){
					$maxid = $maxid_result['maxid'];
				}
				$_SESSION['mid'] = $maxid;
				
				$is_out_timelog_current = mysql_query("UPDATE employee SET current_timelog_id='$maxid' WHERE employee_id='$session_empid'", $db_connect);
			}		
		}

		if(isset($_POST['ecurrentlogid'])) $ecurrentlogid = $_POST['ecurrentlogid'];
		if(isset($_POST['new_notes']))  $new_notes = $_POST['new_notes'];
		
		if(isset($_POST['is_save'])){
			$is_saved = mysql_query("UPDATE employee_timelogs SET notes='$new_notes' WHERE employee_timelogs.id='$ecurrentlogid'", $db_connect);
		
			if($is_saved != true)
			{
				//$message = 'Error saving notes<br/>' . mysql_error($db_connect);
			}
		}
	}
	
?>

<body id="displaymsg">
	<table class=header width=100% border=0 cellpadding=0 cellspacing=1>
		<tr >
			<td width=5% align=left style='padding-left:10px;'><a href='./index.php'><img border=0 align=middle src='./images/logos/timeclock.png'></a></td>
			<td align=left valign=middle><a href='./index.php' style='text-decoration:none;font-size:16;color:#FFFFFF;font-family:Trebuchet MS;'>DWC TEAM NAGA</a></td>
			<td colspan=2 scope=col align=right valign=middle><a href='http://www.historychannel.com/tdih' style='color:#FFFFFF;font-family:Trebuchet MS;font-size:10pt; text-decoration:none;'><?php echo date("F j, Y h:i A (l)"); ?> &nbsp;&nbsp;</a></td>
		</tr>
	</table>
	
	<table class=topmain_row_color width=100% border=0 cellpadding=0 cellspacing=0>
		<tr height=25>
			<td align=left valign=middle >&nbsp;&nbsp;<img src='./images/icons/house.png' border='0' align=top>&nbsp;&nbsp;<a href='./index.php' style='color:#FFFFFF;font-family:Tahoma;font-size:10pt;text-decoration:none;'>Time Clock&nbsp;&nbsp;</a>
			<?php 
				if(isset($_SESSION['is_user_logged_in'])){
					echo "<img src='./images/icons/report.png' border='0' align=top>&nbsp;&nbsp;<a href='.#' style='color:#FFFFFF;font-family:Tahoma;font-size:10pt;
								text-decoration:none;'>Time Log&nbsp;&nbsp;</a>
						<img src='./images/icons/bricks.png' border='0' align=top>&nbsp;&nbsp;<a href='.#' style='color:#FFFFFF;font-family:Tahoma;font-size:10pt;
								text-decoration:none;'>Daily Report&nbsp;&nbsp;</a>
						<img src='./images/icons/arrow_rotate_clockwise.png' border='0' align=top>&nbsp;&nbsp;<a id='logout' href='#' style='color:#FFFFFF;font-family:Tahoma;font-size:10pt;text-decoration:none;'>
					Logout&nbsp;&nbsp;</a>";
				}
			?>
			</td>
		</tr>
	</table>
	
	<div id='data_row'>
		<table width=100% height=89% border=0 cellpadding=0 cellspacing=1>
		  <tr valign=top>
			<td class=left_main width=170 align=left scope=col>
			  <table class=hide width=100% border=0 cellpadding=1 cellspacing=0>
				<tr><td height=7></td></tr>
				<form name='timeclock' method='post' id='searchform'>
					<tr>
						<td <?php if(!isset($_SESSION['is_user_logged_in'])) echo "class=title_underline"; ?> height=4 align=center valign=middle style='padding-left:10px; <?php if(isset($_SESSION['is_user_logged_in'])) echo "font-size:9pt;"; ?>'>
						<?php
							if(isset($_SESSION['is_user_logged_in']))
								echo "<b>".$_SESSION['username']."</b>";
							else echo "Please sign in below:";
						?>
						</td>
					</tr>
					<tr><td height=7></td></tr>
					<tr><td height=4 align=center valign=middle class=misc_items>
						<?php
							if(isset($_SESSION['is_user_logged_in']))
								echo "<b> &lt;{$_SESSION['emailaddress']}&gt; <br>(Development)</b>";
							else echo "Name:";
						?>
					</td></tr>
					<tr><td height=4 align=center valign=middle class=misc_items>
						<?php
							if(isset($_SESSION['is_user_logged_in'])){
								echo 'Notes:
								<div>
									<textarea rows="3" cols="18" id="notes_txt" value=""></textarea>
									<input type="hidden" id="session_eid" value="'.$_SESSION['empid'].'" />
									<input type="hidden" id="session_ecurrentlogid" value="';
										if(isset($_SESSION['mid'])) echo $_SESSION['mid']; else echo $_SESSION['ecurrentlogid'];
									echo '" >
								  </div>';
							}else echo '<div>
										<input autocomplete="off" type="text" size="24" placeholder="Enter name to search..." value="" name="name_input" id="name_input" />
									  </div>
									  <input type="hidden" id="eid" value="" />
									  <input type="hidden" id="ecurrentlogid" value="" />
									  <input type="hidden" id="loginmsg" value="" />
									  <div id="container" style="display: block; position:absolute; z-index:1; width: 164px;" /></div>';
						?>
					<tr><td height=7></td></tr>
					
					<?php
							if(isset($_SESSION['is_user_logged_in'])){
								echo "<tr><td height=4 align=center valign=middle class=misc_items>Working @ <b>HOME</b></td></tr>
									  <tr><td height=4 align=center valign=middle class=misc_items><input type='button' style='width:160px' id='in' value='in' ";
										if(isset($_SESSION['in_is_disabled']))
											echo $_SESSION['in_is_disabled'];
								echo " /></td></tr>
									<tr><td height=4 align=center valign=middle class=misc_items><input type='button' style='width:160px' id='break' value='break' ";
										if(isset($_SESSION['break_is_disabled']))
											echo $_SESSION['break_is_disabled'];
								echo " /></td></tr>
									<tr><td height=4 align=center valign=middle class=misc_items><input type='button' style='width:160px' id='back' value='back' ";
										if(isset($_SESSION['back_is_disabled']))
											echo $_SESSION['back_is_disabled'];
								echo " /></td></tr>
									<tr><td height=4 align=center valign=middle class=misc_items><input type='button' style='width:160px' id='out' value='out' ";
										if(isset($_SESSION['out_is_disabled']))
											echo $_SESSION['out_is_disabled'];
								echo " /></td></tr>
									<tr><td width=100%><table width=100% border=0 cellpadding=0 cellspacing=0>
									<tr><td colspan='2' nowrap height=4 align=center valign=middle class=misc_items></td></tr>
									<tr><td colspan='2' nowrap height=4 align=center valign=middle class=misc_items></td></tr>
									<tr><td colspan='2' nowrap height=4 align=center valign=middle class=misc_items></td></tr>
									<tr><td colspan='2' nowrap height=4 align=center valign=middle class=misc_items><u><a href='#'>Change Password</u></td></tr>";
									
							}
							else{
								echo "<tr><td height=4 align=center valign=middle class=misc_items>Password:</td></tr>
									<tr><td height=4 align=center valign=middle class=misc_items><input type='password' id='employee_passwd' name='employee_passwd' maxlength='25' size='17' tabindex=2></td></tr>
									<tr><td height=7></td></tr>
									<tr><td height=4 align=center valign=middle class=misc_items><input type='submit' id='login' name='login' value='Login' align='center' tabindex=6 style='width:160px'></td></tr>
									<tr><td width=100%><table width=100% border=0 cellpadding=0 cellspacing=0>
									<tr><td colspan='2' nowrap height=4 align=center valign=middle class=misc_items>Remember&nbsp;Me?
										<input type='checkbox' name='remember_me' value='1'>
									</td></tr>";
							}
					?>
					
				</table></td><tr>
			</form>
				<tr><td class=left_rows height=14 align=center valign=middle><br><font class=admin_headings><b>UPCOMING EVENTS</b></td></tr>
					<tr><td><table width='100%'>
					<tr ><td class=left_rows height=18 align=left valign=middle><font style='font-family: Tahoma; font-size:12px; color:green; font-weight: bold;'>SI Training Testers</font></td><td class=left_rows height=18 align=right valign=middle><font style='font-family: Tahoma; font-size:10px;'>Sep 2 Tue</font></td></tr>
					<tr ><td class=left_rows height=18 align=left valign=middle><font style='font-family: Tahoma; font-size:12px; color:green; font-weight: bold;'>SI Training Devs</font></td><td class=left_rows height=18 align=right valign=middle><font style='font-family: Tahoma; font-size:10px;'>Sep 3 Wed</font></td></tr>
					<tr ><td class=left_rows height=18 align=left valign=middle><font style='font-family: Tahoma; font-size:12px; '>PSG</font></td><td class=left_rows height=18 align=right valign=middle><font style='font-family: Tahoma; font-size:10px;'>Sep 3 Wed</font></td></tr>
					<tr ><td class=left_rows height=18 align=left valign=middle><font style='font-family: Tahoma; font-size:12px; color:red; font-weight: bold;'>Teof's Bday</font></td><td class=left_rows height=18 align=right valign=middle><font style='font-family: Tahoma; font-size:10px;'>Sep 6 Sat</font></td></tr>
					</table></td>
				</tr>
		
				<tr ><td class=left_rows height=18 align=center valign=middle><font style='font-family: Tahoma; font-size:10px;'><a href='#'>Add Event</a></font></td></tr>
				<tr><td height=90%></td></tr>
			</table></td>
			
			<title>Team Naga TimeClock </title>
				<td align=left class=right_main scope=col>
				  <table width=100% height=100% border=0 cellpadding=5 cellspacing=1>
					<tr class=right_main_text>
					  <td valign=top>
						<table width=100% align=center class=misc_items border=0 cellpadding=3 cellspacing=0>
						  <tr class=display_hide>
							<td nowrap style='font-size:9px;color:#000000;padding-left:10px;'>Current Status Report&nbsp;&nbsp;---->&nbsp;&nbsp;As of: 12:46 am, 8/13/2014</td></tr>
						</table>
						  
						<table class=misc_items width=100% border=0 cellpadding=2 cellspacing=0>
						  <tr><td align=right colspan=8><a style='font-size:11px;color:#853d27;'href='#'>printer friendly page</a></td></tr>
						  <tr class=notprint>
							<td nowrap width=2% align=right style='padding-left:1px;padding-right:1px;'>No</td>
							<td nowrap width=20% align=left style='padding-left:10px;padding-right:10px;'><a style='font-size:11px;color:#27408b;' href='#'>Name</a></td>
							<td nowrap width=7% align=left style='padding-left:10px;'><a style='font-size:11px;color:#27408b;' href='#'>Status</a></td>
							<td nowrap width=7% align=center style='padding-left:0px;'>Location</td>
							<td nowrap width=5% align=right style='padding-right:10px;'><a style='font-size:11px;color:#27408b;' href='#'>Time</a></td>
							<td nowrap width=5% align=right style='padding-left:10px;'><a style='font-size:11px;color:#27408b;' href='#'>Date</a></td>
							<td nowrap width=10% align=left style='padding-left:10px;'><a style='font-size:11px;color:#27408b;' href='#'>Department</a></td>
							<td style='padding-left:10px;'><a style='font-size:11px;color:#27408b;' href='#'><u>Notes</u></a></td>
						  </tr>
						  
						   <tr class=notdisplay>
							<td nowrap width=10% align=left style='padding-left:10px;padding-right:10px;font-size:11px;color:#27408b; text-decoration:underline;'>Name</td>
							<td nowrap width=7% align=left style='padding-left:10px;font-size:11px;color:#27408b; text-decoration:underline;'>In/Out</td>
							<td nowrap width=5% align=right style='padding-right:10px;font-size:11px;color:#27408b; text-decoration:underline;'>Time</td>
							<td nowrap width=5% align=right style='padding-left:10px;font-size:11px;color:#27408b; text-decoration:underline;'>Date</td>
							<td nowrap width=10% align=left style='padding-left:10px;font-size:11px;color:#27408b; text-decoration:underline;'>Group</td>
							<td style='padding-left:10px;'><a style='font-size:11px;color:#27408b;text-decoration:underline;'>Notes</td>
						  </tr>
						  
						  <?php
							if(count($employee_list_id) > 0){
								for($i=0; $i<count($employee_list_id); $i++){
								
									if(!empty($employee_list_id[$i]['time'])) $cur_time = date("g:i a",strtotime($employee_list_id[$i]['time'])); else $cur_time='';
									if(!empty($employee_list_id[$i]['date'])) $cur_date = date("M d (D)",strtotime($employee_list_id[$i]['date'])); else $cur_date='';
									if(!empty($employee_list_id[$i]['status'])) $cur_status = $employee_list_id[$i]['status']; else $cur_status='';
									if(!empty($employee_list_id[$i]['location'])) $cur_location = $employee_list_id[$i]['location']; else $cur_location='';
									if(!empty($employee_list_id[$i]['notes'])) $cur_notes = $employee_list_id[$i]['notes']; else $cur_notes='';
									
									if(isset($active_full_name) && (ucwords($active_full_name) == ucwords($employee_list_id[$i]['full_name']))) $cur_fullname = "<b>".strtoupper($employee_list_id[$i]['full_name'])."</b>"; else $cur_fullname = ucwords($employee_list_id[$i]['full_name']);
									if(isset($_SESSION['ecurrentlogid']) == $employee_list_id[$i]['timelog_id']) $elogid = $employee_list_id[$i]['timelog_id']; else $elogid ="";
									
									echo "<tr class=display_row id='data_row'><td nowrap width=1% align=right style='padding-left:1px;padding-right:1px;font-size:9px;' bgcolor='#FBFBFB'>".($i+1)."</td>
											  <td nowrap width=10% bgcolor='#FBFBFB' style='padding-left:15px;
														  padding-right:10px;'><font style='font-size:12px;' face='Trebuchet MS'><font color='#161693'>".$cur_fullname."</font></td>
												<td nowrap align=left width=7% style='background-color:#FBFBFB;color:#0000FF;
														padding-left:10px;' id='data_status'>";
												if($cur_status == 'out') echo "<font color='red'>".$cur_status."</font>";
												else if($cur_status == 'break') echo "<font color='#FF9900'>".$cur_status."</font>";
												else echo $cur_status;
												echo "</td>
												<td nowrap align=center width=5% bgcolor='#FBFBFB' >";
												if($cur_status == 'out') echo "<font color='#83CFEF'>".$cur_location."</font>";
												else echo "<font color='blue'>{$cur_location}</font>";
												echo "</td>
												<td nowrap align=right width=5% bgcolor='#FBFBFB' style='padding-right:10px;'>".$cur_time."</td>
												<td nowrap align=right width=5% bgcolor='#FBFBFB' style='padding-left:10px;'>".$cur_date."</td>
												<td nowrap align=left width=10% bgcolor='#FBFBFB' style='padding-left:10px;'>{$employee_list_id[$i]['department_name']}</td>";
												if(isset($active_full_name) && (ucwords($active_full_name) == ucwords($employee_list_id[$i]['full_name'])))
													echo "<td style='font-size:9px;'> <input type='text' id='data_notes_$elogid' value='{$cur_notes}' style='font-size:11px;'></input>
														  <a id='save_new_notes'><img src='./images/icons/save-icon.png' border='0' align=top height='20px'></img></a>
														  <span id='save_notes_msg'></span>
														  </td>";
												else echo "<td> {$cur_notes} </td>";
									echo "</tr>";
								}
							}
						  ?>
						</table>
						
						<br> &nbsp;&nbsp;&nbsp;&nbsp;  </td></tr>
					<tr class=hide>
						<td height=4% class=misc_items align=right valign=middle scope=row colspan=2>Powered by&nbsp;
							<a class=footer_links href='http://httpd.apache.org/'>Apache</a>&nbsp;&#177
							<a class=footer_links href='http://mysql.org'>&nbsp;MySql</a> &#177
							<a class=footer_links href='http://php.net'>&nbsp;PHP</a>
						</td></tr>
				</table>
		</div>
</body>
