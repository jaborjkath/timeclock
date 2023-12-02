//Load autocomplete from external file/page
$(document).ready(function(){
  $("#name_input").keyup(function(){
	var name_input = $("#name_input").val();
	
	$("#container").load("name_autocomplete.php?str="+name_input,function(responseTxt,statusTxt,xhr){
		/*if(statusTxt=="success")
		  alert("External content loaded successfully!");
		if(statusTxt=="error")
		  alert("Error: "+xhr.status+": "+xhr.statusText);*/
		$('#container').css({"visibility":"visible","height":"0px","width":"164px"});
	});
  });
});
	
//Set value for name input text box
function setvalue (thevalue){
	$('#container').css({"visibility":"hidden","height":"0px","width":"0px"});
	$('#name_input').val(thevalue);
}
	
//Set employee id for hidden input text box
function setid (eid){
	$('#eid').val(eid);
}
	
//Set current employee timelog id based from db for hidden input text box
function setcurrentlogid (logid){
	$('#ecurrentlogid').val(logid);
}
	
//Login event
$(document).ready(function(){
	$("#login").click(function(e) {
		var buttonId = $("#login").val();
		var name_input = $("#name_input").val();
		var employee_id = $("#eid").val();
		var employee_passwd = $("#employee_passwd").val();
		var employee_currentlogid = $("#ecurrentlogid").val();
		
		if (buttonId  == "Login" && employee_id != '' && employee_passwd != '') 
		{						
			e.preventDefault();
			$.ajax({
				type: "post",
				url: "index.php",
				data: "employee_id="+employee_id+"&name_input="+name_input+"&employee_passwd="+employee_passwd+"&buttonId="+buttonId+"&employee_currentlogid="+employee_currentlogid,
				
				success: function(str){
					$("#displaymsg").html(str);
				},error: function() {
					//alert('There has been an error');
					//e.preventDefault();
				}
			});					
		}else{
			//alert('Empty username/password'+employee_passwd);
			e.preventDefault();
			return false;
		}
	});
});

//Action: In to Timeclock
$(document).ready(function(){
	$("#in").click(function(e){
		var inId = $("#in").val();
		var notes_txt = $("#notes_txt").val();
		var employee_id = $("#session_eid").val();
		
		$.ajax({
			type: "post",
			url: "index.php",
			data: "in="+inId+"&notes_txt="+notes_txt+"&employee_id="+employee_id,
			
			success: function(str){
				$("#displaymsg").load("index.php?in="+inId+"&notes_txt="+notes_txt+"&employee_id="+employee_id);
				
			}
		});	
	});
});

//Action: Break from Timeclock
$(document).ready(function(){
	$("#break").click(function(e){
		var breakId = $("#break").val();
		var notes_txt = $("#notes_txt").val();
		var employee_id = $("#session_eid").val();
		
		$.ajax({
			type: "post",
			url: "index.php",
			data: "break="+breakId+"&notes_txt="+notes_txt+"&employee_id="+employee_id,
			
			success: function(str){
				$("#displaymsg").load("index.php?break="+breakId+"&notes_txt="+notes_txt+"&employee_id="+employee_id);
			}
		});	
	});
});

//Action: Back in Timeclock
$(document).ready(function(){
	$("#back").click(function(e){
		var backId = $("#back").val();
		var notes_txt = $("#notes_txt").val();
		var employee_id = $("#session_eid").val();
		
		$.ajax({
			type: "post",
			url: "index.php",
			data: "back="+backId+"&notes_txt="+notes_txt+"&employee_id="+employee_id,
			
			success: function(str){
				$("#displaymsg").load("index.php?back="+backId+"&notes_txt="+notes_txt+"&employee_id="+employee_id);
			}
		});	
	});
});

//Action: Out from Timeclock
$(document).ready(function(){
	$("#out").click(function(e){
		var outId = $("#out").val();
		var notes_txt = $("#notes_txt").val();
		var employee_id = $("#session_eid").val();
		
		$.ajax({
			type: "post",
			url: "index.php",
			data: "out="+outId+"&notes_txt="+notes_txt+"&employee_id="+employee_id,
			
			success: function(str){
				$("#displaymsg").load("index.php?out="+outId+"&notes_txt="+notes_txt+"&employee_id="+employee_id);
			}
		});	
	});
});

//Log out from Timeclock
$(document).on("click", "a", function(e){
	var anchorId = $(this).attr("id");
	var employee_currentlogid = $("#session_ecurrentlogid").val();
	
	if(anchorId == 'save_new_notes'){
		var save_notesId = anchorId;
		var new_notes = $("#data_notes_"+employee_currentlogid).val();
		
		$.ajax({
			type: "post",
			url: "index.php",
			data: "is_save=1&new_notes="+new_notes+"&ecurrentlogid="+employee_currentlogid,
			
			success: function(str){		
				$("#displaymsg").load("index.php?is_save=1&new_notes="+new_notes+"&ecurrentlogid="+employee_currentlogid);
				$("#save_notes_msg").text("Notes saved!").delay(2000).fadeOut();
			}
		});	
	}
	else if(anchorId == 'logout'){
		$("#displaymsg").load("logout.php",function(responseTxt,statusTxt,xhr){
			/*if(statusTxt=="success")
			  alert("External content loaded successfully!");
			if(statusTxt=="error")
			  alert("Error: "+xhr.status+": "+xhr.statusText);
			*/
		});
	}
});
