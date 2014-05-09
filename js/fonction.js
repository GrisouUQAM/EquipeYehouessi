function sendForm() {
    var username = document.getElementById("txt_user").value;
    var url_elem = document.getElementById("ddl_url");
    var wikiurl = url_elem.options[url_elem.selectedIndex].value;
    var loader = document.getElementById("ico_loading");
    var effacer = document.getElementById("box-section-content-bottom");

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
        effacer.innerHTML="<div id='icoCentraliteLoading'> </div>";
    }); 
    return false;
}

function envoiDiscussion(element) {    
    document.getElementById("box-section-content-bottom").innerHTML="<div id='icoCentraliteLoading'><img src='images/miniloading.gif' style='height:28px;' /> </div>";
    var y = element.innerHTML;
    var String=y.substring((y.indexOf(">")+1),(y.indexOf("/")-1));       
    var discussion = "nomDiscussion="+String;
    var url = "actionDiscussion.php";   
    $("#icoCentraliteLoading").fadeIn("fast");   
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST",url,true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(discussion);
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200){
            document.getElementById("box-section-content-bottom").innerHTML=xmlhttp.responseText;
            $("#icoCentraliteLoading").fadeOut("slow");
        }
    }
}
	