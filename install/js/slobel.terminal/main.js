$(document).ready(function(event){
function getParams(command, val, method){
	var params
	params={command: val};
	if(command=='reset') params={reset: val};
	$("#slobel-button>input").each(function(e){
		$(this).attr('disabled', true);
	});
	$("#command").attr('disabled', true);
	$.ajax({url: '/bitrix/admin/slobel.terminal/ajax.php',
		type: method,			
		data: params,
		dataType: "json",
		async: false,
		success: function(data){
				var log;

				$("#history").empty();
				$("#cwd").empty();
				$("#os-version").empty();
				$("#command").val("");
				
				if(command!='reset'){
					if($('#log').val()){log=$('#log').val()+"\n\n"+data.output;}
					else {log=data.output}
				}
				$('#log').val(log);
				$("#cwd").append(data.fullcwd);
				$("#os-version").append(data.os);
				$("#history").append(data.history);
				
				$("#log").scrollTop(999999);
				$("#command").removeAttr('disabled');
				$("#slobel-button>input").each(function(e){
					$(this).removeAttr('disabled');
					$(this).removeClass('adm-btn-load');
				});
			}
	})
}

getParams("command", $('#command').val(), "POST");
$('#go').on('click',function(){$(this).addClass('adm-btn-load'); getParams("command", $('#command').val(), "POST");})
$('#reset').on('click',function(){$(this).addClass('adm-btn-load'); getParams("reset", true, "GET");})

$('#history-command').on('click',function(){
	var history="";
	$('#history>option').each(function(){history=history+$(this).text()+"\n";})
	if(!$('#history>option').text()) history="History missing!";
	$('#log').val($('#log').val()+"\n\n"+$('#cwd').text()+"myhistory"+"\n\n"+history);
	$("#log").scrollTop(999999);
})

$("#command").keydown(function (e) {
	var previd = parseInt($("#history>option[data-hist='this']").val())-1;
	var nextid = parseInt($("#history>option[data-hist='this']").val())+1;
	if(e.keyCode=="38" || e.which=="38"){
		$("#command").val($("#history>option[data-hist='this']").text());
		if($("#history>option[id='item-"+previd+"']").val()){
			$("#history>option[id='item-"+previd+"']").attr('data-hist', 'this').siblings().removeAttr('data-hist');
			if($("#history>option[id='item-"+parseInt(previd-1)+"']") && $("#history>option[id='item-"+parseInt(previd+1)+"']")){
				$("#history>option[id='item-"+parseInt(previd-1)+"']").attr('data-hist', 'prev');
				$("#history>option[id='item-"+parseInt(previd+1)+"']").attr('data-hist', 'next');
			}				
		}
	}
	if(e.keyCode=="40" || e.which=="40"){
		$("#command").val($("#history>option[data-hist='this']").text());
		if($("#history>option[id='item-"+nextid+"']").val()){
			$("#history>option[id='item-"+nextid+"']").attr('data-hist', 'this').siblings().removeAttr('data-hist');
			if($("#history>option[id='item-"+parseInt(nextid-1)+"']") && $("#history>option[id='item-"+parseInt(nextid+1)+"']")){
				$("#history>option[id='item-"+parseInt(nextid-1)+"']").attr('data-hist', 'prev');
				$("#history>option[id='item-"+parseInt(nextid+1)+"']").attr('data-hist', 'next');
			}				
		}
	}
	if(e.keyCode=="13" || e.which=="13"){$("#go").addClass('adm-btn-load');getParams("command", $('#command').val(), "POST");}
});
})