function sendForm() {

	var username = document.getElementById("txt_user").value;
	var url_elem = document.getElementById("ddl_url");
	var wikiurl = url_elem.options[url_elem.selectedIndex].value;
	var loader = document.getElementById("ico_loading");
	
	$("#ico_loading").fadeIn("fast");
	$("#box_result").fadeOut("fast");
	
	$.get("wiki_interogator.php", { user:username, wiki:wikiurl }, function(data) {
		$("#result").html(data);
		var values = [], labels = [];

		$("tr").each(function() {
			values.push(parseInt($("td", this).text(), 10));
			labels.push($("th", this).text());
		});

		$("#box_result").fadeIn("slow");
		$("#ico_loading").fadeOut("slow");
	}); 
	

	return false;
}

function envoiDiscussion(element) {
    var y = element.innerHTML;
    var String=y.substring((y.indexOf(">")+1),(y.indexOf("/")-1));       
    var discussion = "nomDiscussion="+String;
    //var discussion = "fname=Henry&lname=Ford";
    var url = "actionDiscussion.php";   
    $("#icoCentraliteLoading").fadeIn("fast");
   // $("#box_result").fadeOut("fast");
        
if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
} else {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}


xmlhttp.open("POST",url,true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
//http.setRequestHeader("Content-length", params.length);
//http.setRequestHeader("Connection", "close");
 xmlhttp.send(discussion);
 xmlhttp.onreadystatechange=function() {
   if (xmlhttp.readyState==4 && xmlhttp.status==200){
     document.getElementById("box-section-content-bottom").innerHTML=xmlhttp.responseText;
     alert(http.responseText);
  //   $("#box_result").fadeIn("slow");
     $("#icoCentraliteLoading").fadeOut("slow");
     }
   }
 

 /* $.get("actionDiscussion.php", { nomDiscussion:y},function(data){
     alert("Data: " + data );
   });*/
 
      
}
	
/*function sendForm1() {

	var username = document.getElementByName("paiement").value;
	
	var wikiurl = username.value;
	
	
	var radios = document.getElementsByName("paiement");

for (var i = 0, length = radios.length; i < length; i++) {
    if (radios[i].checked) {
        // do whatever you want with the checked radio 
alert(radios[i].checked);
$.get("wiki_interogatortransmettre.php", { discussion:radios[i].value}, function(data) {
		$("#result").html(data);
		var values = [], labels = [];

		$("tr").each(function() {
			values.push(parseInt($("td", this).text(), 10));
			labels.push($("th", this).text());
		});

		
	}); 	   
        break;
    }
}*/
	