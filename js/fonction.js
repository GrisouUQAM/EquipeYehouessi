function sendForm() {

	var username = document.getElementById("txt_user").value;
	var url_elem = document.getElementById("ddl_url");
	var wikiurl = url_elem.options[url_elem.selectedIndex].value;
	var loader = document.getElementById("ico_loading");
	
	$("#ico_loading").fadeIn("fast");
	$("#box_result").fadeOut("fast");
	
	$.get("wiki_interogatorUserOnClick.php", { user:username, wiki:wikiurl }, function(data) {
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
    alert(y);
    //MaintenAnt je vais recuperer l'elemeent surlequel il a clicke et esnsuite faire un XMLREQUEST'et l<envooye rsur le serveur avec un fichier de traitement specifique
  // var z = y.find(".resTd"); 
 //  alert (z);
   
   
  // var clickedValue = y.find('td:first').next().text();

//alert(clickedValue );
   //     alert(y.find('td.resTd').text());

  // alert (z.text());
    
    
}
/*function essai(){
	var lignes=document.getElementById('mytable').getElementsByTagName('tr'); 
	var i=1; 
	var t = new Array(); 
	while(lignes[i]) 
	{ 
	var cells=lignes[i].getElementsByTagName('td'); 
	t.push(cells[1].innerHTML); 
	alert(cells[1].innerHTML);
	i++; 
	} 

	var cells=lignes[2].getElementsByTagName('td'); 
	//t.push(cells[1].innerHTML); 
	alert(cells[1].innerHTML);
	
	}
	
function sendForm1() {

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
	