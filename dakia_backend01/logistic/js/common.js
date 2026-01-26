/***********************************************************************

	Rapid Parcel Common javascript file
	Author: Andy Creed

************************************************************************/

/***********************************************************************
	On fully loaded document
************************************************************************/

$(document).ready(function(){
	
	/***********************************************************************
		Standard action buttons
	************************************************************************/
	$("#btnSubmit").click(function(){
	  $("#form_action").val("ok");
	  $("#adminForm").submit();
    });
	
	$("#btnSave").click(function(){
	  $("#form_action").val("save");
	  $("#adminForm").submit();
    });	
    
	$("#btnDelete").click(function(){
		if (confirm("Are you sure you wish to delete?"))
		{
			$("#form_action").val("delete");
			$("#adminForm").submit();
			return true;
		}
		return false;
    });
	
	$("#btnCancel").click(function(){
	  	$("#form_action").val("cancel");
	    $("#adminForm").submit();
    });	
	
	$("#btnSearch").click(function(){
	$("#form_action").val("search");
	$("#adminForm").submit();
    });

	$("#btnLogout").click(function(){
		location.href = "../main/login.php?logout=yes"
    });

});
