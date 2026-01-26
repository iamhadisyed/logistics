var exporting = false,xhr;
$(document).ready(function(){
	/***********************************************************************
		actions to generate bulk labels progress terminal
	************************************************************************/
	$("#showTerminal").click(function(){
		exporting = true;
		$("#export-terminal").css({"z-index": 9999, "visibility": "visible"}).fadeIn();
		$("#export-terminal-msgs").append("<li>Processing request...</li>");
	});
	$(".cancel_button").on("click", function(event){
		if(exporting){
			return false;
		}
		window.location.reload();
		//window.location = "{{URL::previous()}}";
	});

});
function cancelTerminal(){
	xhr.abort();
	$("#export-terminal").css({"z-index":-9999,"visibility":"hidden"});
	$("#export-terminal-msgs").html("");
	$("body").css("overflow-y","auto");
	exporting = false;
	//window.location.reload();
}