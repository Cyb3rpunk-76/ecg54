/* ECG54 API */

var tamanhoimagens = "height=\"20\" width=\"20\"";

function Ctrl_AddField(fieldname) {
	return { field: fieldname,
		     title: fieldname,
			 listed: 1,
		     type: "text",
		     readonly: 0,
	         listgrouped: 0,
		     size: 20,
			 lookup: "",
			 image: "" };
}

function FetchLookup(struct,idd) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			struct[idd].lookup = [];
			var xmlLook = this.responseXML;
			var lk = xmlLook.getElementsByTagName("LOOKUPDATA");
			for (var lku = 0; lku <lk.length; lku++) { 
				struct[idd].lookup.push( {
						ID: (lk[lku].getElementsByTagName("ID")[0].childNodes[0].nodeValue),
						DESC: (lk[lku].getElementsByTagName("DESC")[0].childNodes[0].nodeValue) } );
			}
		}
	};
	xmlhttp.open("GET", struct[idd].lookup, true);
	xmlhttp.send();
}

function FillLookups(struct) {
	for (y = 2; y <struct.length; y++) { 
		if (struct[y].listgrouped==0) {
			if (struct[y].lookup !== "") {
				if (typeof struct[y].lookup === 'string') {
					FetchLookup(struct,y);
				}
			}
		}
	}
}

function ListStru(xml,struct) {
	var i;
	var y;
	var cid;
	var linha = "";
	var xmlDoc = xml.responseXML;
	var lstgroup = -1;
	var lstgroupstr = "";
	var table = "";
	var headerg="<tr><th></th><th>##header##</th></tr>";
	var headert="<tr><th></th>";
	var img_line="<img src=\"ecg54api/img/##AC##.png\" "+tamanhoimagens+"></a>";
	var img_ctrl=0;
	var img_item="";
	var img_s="";
	var x = xmlDoc.getElementsByTagName(struct[0].apictrl_xml);
	for (y = 2; y <struct.length; y++) { 
		if (struct[y].listgrouped) {
			lstgroup = y;
		} else { 
			if (struct[y].listed==1) {
				headert += "<th>" + struct[y].title + "</th>"; 
			}
		}
	}
	
	if (struct[0].apictrl_image[0].trim() == "") {
		if (struct[0].apictrl_image[1].trim() !== "") {
			img_line += " &emsp; <img src=\""+struct[0].apictrl_image[1]+"\" "+tamanhoimagens+">";
			img_ctrl = 1; 
		}
	} else {
		img_line += " &emsp; ";
		img_ctrl = 2;
	}
	
	if (lstgroup == -1) {
		table += headert;
    } else {
		headert = headerg+headert;
	}
	for (i = 0; i <x.length; i++) { 
		if (lstgroup !== -1) {
			if (lstgroupstr !== x[i].getElementsByTagName(struct[lstgroup].field)[0].childNodes[0].nodeValue) {
				lstgroupstr = x[i].getElementsByTagName(struct[lstgroup].field)[0].childNodes[0].nodeValue;
				table += headert.replace(/##header##/i, lstgroupstr );
			}	
		}
		cid = x[i].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue;
		table += "<tr>"; 
		
		img_s = img_line.replace(/##AC##/i,'edit');
		switch(img_ctrl) {
			case 2:
			    img_item = x[i].getElementsByTagName(struct[0].apictrl_image[0])[0].childNodes[0].nodeValue.trim();
				if (img_item == '-') {
					img_item = struct[0].apictrl_image[2].trim();
				}
				img_s += "<img src=\""+struct[0].apictrl_image[1].replace(/##FLD##/i,img_item)+"\" "+tamanhoimagens+">";
				break;
			default:	
				break;
		}
		
		table += "<td style=\"text-align:center\"><a href = '#' onclick = \"Requestform("+cid+",'areacomum',0,"+struct[0].apictrl_self+");\">"+img_s+"</td>";
		for (y = 2; y <struct.length; y++) { 
			if (struct[y].listgrouped == 0) {
				if (struct[y].listed==1) {
					if (struct[y].lookup !== "") {
						linha = "<td></td>";
						if (typeof struct[y].lookup !== 'string') {
							for (lku = 0; lku <struct[y].lookup.length; lku++) { 
								if (struct[y].lookup[lku].ID==x[i].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue) {
									linha = "<td>"+struct[y].lookup[lku].DESC+"</td>";
									break;
								}
							}
						}
						table += linha;
					} else {
						if (struct[y].type == "checkbox") {
							table += "<td>"+InteiroSimNao(x[i].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue)+"</td>";
						} else {
							table += "<td>"+x[i].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue + "</td>";
						} 
					}
				}
			}	
		}
		table += "</tr>";
	}
	
	if (struct[0].apictrl_enableinsert) {
		img_s = img_line.replace(/##AC##/i,'add');
		switch(img_ctrl) {
			case 2:
				img_s += "<img src=\""+struct[0].apictrl_image[1].replace(/##FLD##/i,struct[0].apictrl_image[2])+"\" "+tamanhoimagens+">";
				break;
			default:	
				break;
		}
		table += "<tr><td style=\"text-align:center\"><a href = '#' onclick = \"Requestform(-1,'areacomum',0,"+struct[0].apictrl_self+");\">"+img_s+"</td>";
		table += "</tr>";
	}
	
	document.getElementById("xmlresult").innerHTML = table;
}	
 
function InteiroSimNao(p1) {
  if (p1 == 1) {
    return "<b>Sim</b>";
  } else {
    return "Não";
  }
}
function CheckboxControle(flagx) {
  if (flagx == 1) {
    return "checked";
  } else {
    return "";
  }
}	
function DesabilitarControle(flagx) {
  if (flagx == 1) {
    return "";
  } else {
    return "disabled";
  }
}

function Saveform(target_id,struct) {
	var xmlhttp = new XMLHttpRequest();
	var kvpairs = [];
	var form = document.getElementById("c_form");
	for (var y = 2; y <struct.length; y++) { 
		if (struct[y].type == "checkbox") {
			if (struct[y].readonly == 0) {
				if (document.getElementById(struct[y].field).checked) { 
					document.getElementById(struct[y].field).value = 1;
				} else { document.getElementById(struct[y].field).value = 0; } 
			}
		}
	}
	for (var i = 0; i < form.elements.length; i++ ) {
		var e = form.elements[i];
		if ( encodeURIComponent(e.value) !== "undefined" ) {
			if ( encodeURIComponent(e.value).trim() !== "" ) {
			kvpairs.push(encodeURIComponent(e.name) + "=" + encodeURIComponent(e.value));  }  }  }
	var queryString = kvpairs.join("&");
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			setTimeout( function() { 
				document.getElementById("xmlresult").innerHTML = "";
				if (target_id == -1) { struct[0].apictrl_load(struct); } else { Requestform(target_id,'areacomum',0,struct); }
			}, 300);
		}
	};
	document.getElementById("xmlresult").innerHTML = "<tr><h2>Gravando os dados ...</h2></tr>";
	xmlhttp.open("GET", struct[0].apictrl_updt+"?"+queryString, true);
	xmlhttp.send();
}

function Requestlist( LoadStruct ) {
  FillLookups(LoadStruct);
  var xmlhttp = new XMLHttpRequest();
  document.getElementById("areacomum").innerHTML = "<table id=\"xmlresult\"> </table>";
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
	  document.getElementById("xmlresult").innerHTML = "";	
	  ListStru(this,LoadStruct);
    }
  };
  document.getElementById("xmlresult").innerHTML = "<tr><h2>Carregando dados ...</h2></tr>";
  xmlhttp.open("GET", LoadStruct[0].apictrl_view, true);
  xmlhttp.send();
}

function LoadNetDevices(xml) {
  var i;
  var currentarea = "---";
  var loadedarea = "---";
  var tdstatus = "";
  var table = "";	
  var xmlDoc = xml.responseXML;
  var tabheader = "<tr><th style=\"text-align:center\">##net##</th\"><th  style=\"text-align:center\">Quando</th><th style=\"text-align:center\">IP</th><th style=\"text-align:center\">MAC</th><th style=\"text-align:center\">Quem</th><th style=\"text-align:center\">Tipo</th><th style=\"text-align:center\">Metodo</th><th>Modelo</th><th>Fabricante</th></tr>";
  var x = xmlDoc.getElementsByTagName("DEVICE");
  for (i = 0; i <x.length; i++) { 
	loadedarea = x[i].getElementsByTagName("net_area")[0].childNodes[0].nodeValue ;
	if ( loadedarea != currentarea ) {  
		currentarea = tabheader.replace(/##net##/i, loadedarea );
		table += currentarea;
		currentarea = loadedarea ; }	
	if ( x[i].getElementsByTagName("net_device_status")[0].childNodes[0].nodeValue == "up") {
       tdstatus = "<tr class=\"up\"><td><ul class=\"up\"><li></li></ul> [ UP ] "; } else {
	   tdstatus = "<tr><td style=\"color:red;font-weight:bold\"><ul class=\"down\"><li></li></ul> [ DOWN ] "; }   
	if ( x[i].getElementsByTagName("device_type_id")[0].childNodes[0].nodeValue.trim() !== "-") {
       tdstatus +=	" &emsp; <img src=\"ecg54api/img/device_types/"+x[i].getElementsByTagName("device_type_id")[0].childNodes[0].nodeValue.trim()+".png\" "+tamanhoimagens+">"; 
	}
    table += tdstatus + "</td><td style=\"text-align:center\">";
    table += x[i].getElementsByTagName("net_device_last_check")[0].childNodes[0].nodeValue + "</td><td>" ;
    table += x[i].getElementsByTagName("net_device_ipadd")[0].childNodes[0].nodeValue + "</td><td>" ;
	table += x[i].getElementsByTagName("net_device_macadd")[0].childNodes[0].nodeValue + "</td><td>" ;
	table += x[i].getElementsByTagName("device_owner")[0].childNodes[0].nodeValue + "</td><td>" ;
    table += x[i].getElementsByTagName("device_type")[0].childNodes[0].nodeValue + "</td><td>" ;
    table += x[i].getElementsByTagName("net_method")[0].childNodes[0].nodeValue +  "</td><td>" ;
	table += x[i].getElementsByTagName("net_model")[0].childNodes[0].nodeValue +  "</td><td>" ;
    table += x[i].getElementsByTagName("net_vendor")[0].childNodes[0].nodeValue +  "</td></tr>";
  }
  document.getElementById("xmlresult").innerHTML = table;
}

function pad(num, size) {
    var s = "000000000" + num;
    return s.substr(s.length-size);
}

function loadXMLEvents() {
  var today = new Date();
  var sdate = pad(today.getDate(),2)+'-'+pad((today.getMonth()+1),2)+'-'+today.getFullYear();
  var datepicker = "<input type=\"text\" id=\"datepicker\" value=\""+sdate+"\" /><button type = \"button\" onclick = \"RunEvents()\"> Run </button>";
  document.getElementById("areacomum").innerHTML = datepicker + "<br><br><table id=\"xmlresult\" style=\"height:auto\"></table>" ;
  var foopicker = new FooPicker({  id: 'datepicker',  dateFormat: 'dd-MM-yyyy' });
}

function loadXMLVideos() {
  var today = new Date();
  var sdate = pad(today.getDate(),2)+'-'+pad((today.getMonth()+1),2)+'-'+today.getFullYear();
  var datepicker = "<input type=\"text\" id=\"datepicker\" value=\""+sdate+"\" /><button type = \"button\" onclick = \"RunVideos()\"> Run </button>";
  document.getElementById("areacomum").innerHTML = datepicker + "<br><br><table id=\"xmlresult\" style=\"height:auto\"></table>" ;
  var foopicker = new FooPicker({  id: 'datepicker',  dateFormat: 'dd-MM-yyyy' });
}

function RunEvents() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
	  document.getElementById("xmlresult").innerHTML = "";	
      LoadEvents(this);  }
  };
  document.getElementById("xmlresult").innerHTML = "<tr><h2>Carregando dados ...</h2></tr>";
  xmlhttp.open("GET", "ecg54api/eventdate.php?date="+document.getElementById("datepicker").value, true);
  xmlhttp.send();
}

function RunVideos() {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
	  document.getElementById("xmlresult").innerHTML = "";	
      LoadEvents(this);  }
  };
  document.getElementById("xmlresult").innerHTML = "<tr><h2>Carregando dados ...</h2></tr>";
  xmlhttp.open("GET", "ecg54api/videodate.php?date="+document.getElementById("datepicker").value, true);
  xmlhttp.send();
}

function LoadEvents(xml) {
  var i;
  var descevento = "---";
  var currentdevice = "---";
  var loadeddevice = "---";
  var semi = "";	
  var xmlDoc = xml.responseXML;
  var table = "<tr><th style=\"text-align:center\">Events</th><th style=\"text-align:center\">IP / Mac</th><th style=\"text-align:center\">Tipo</th><th style=\"text-align:center\">Modelo</th><th style=\"text-align:center\">Fabricante</th><th style=\"text-align:center\">Meio</th><th style=\"text-align:center\">Quem</th></tr>";
  var x = xmlDoc.getElementsByTagName("EVENT");
  for (i = 0; i <x.length; i++) { 
	loadeddevice = ( x[i].getElementsByTagName("net_device_macadd")[0].childNodes[0].nodeValue ) ;
	if ( loadeddevice != currentdevice ) {  
		if (currentdevice != "---" ) {
			// table += ( "<tr><td><ul>" + descevento + "</ul></td>" );
			table += ( "<tr style=\"valign:top\"><td>" + descevento + "<br></td>" );
			table += ( semi + "</tr>" ); }	
		currentdevice = loadeddevice;
		descevento = x[i].getElementsByTagName("network_when")[0].childNodes[0].nodeValue + ' - <strong>' + x[i].getElementsByTagName("network_eventtype_desc")[0].childNodes[0].nodeValue + '</strong>';
		semi = ( "<td><strong>" + x[i].getElementsByTagName("net_device_ipadd")[0].childNodes[0].nodeValue + "</strong> [" + x[i].getElementsByTagName("net_device_macadd")[0].childNodes[0].nodeValue + "]</td>" );
		semi += ( "<td>" + x[i].getElementsByTagName("device_type")[0].childNodes[0].nodeValue + "</td>" );
		semi += ( "<td>" + x[i].getElementsByTagName("net_model")[0].childNodes[0].nodeValue + "</td>" );
		semi += ( "<td>" + x[i].getElementsByTagName("net_vendor")[0].childNodes[0].nodeValue + "</td>" );
		semi += ( "<td>" + x[i].getElementsByTagName("net_method")[0].childNodes[0].nodeValue + "</td>" );
		semi += ( "<td><strong>" + x[i].getElementsByTagName("device_owner")[0].childNodes[0].nodeValue + "</strong></td>" ); }
	else { descevento += ( "<br>" + x[i].getElementsByTagName("network_when")[0].childNodes[0].nodeValue + ' - <strong>' + x[i].getElementsByTagName("network_eventtype_desc")[0].childNodes[0].nodeValue + "</strong>" ); }
  }
  table += ( "<tr><td>" + descevento + "<br></td>" );
  table += ( semi + "</tr>" );
  document.getElementById("xmlresult").innerHTML = table;
}

function loadXMLNetDevices() {
  var xmlhttp = new XMLHttpRequest();
  document.getElementById("areacomum").innerHTML = "<table id=\"xmlresult\"> </table>";
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
	  document.getElementById("xmlresult").innerHTML = "";
      LoadNetDevices(this);
    }
  };
  document.getElementById("xmlresult").innerHTML = "<tr><h2>Carregando dados ...</h2></tr>";
  xmlhttp.open("GET", "ecg54api/devicestatus.php", true);
  xmlhttp.send();
}

function Requestform(target_id,target_html,editmode,editstructure) {
  FillLookups(editstructure);
  var xmlhttp = new XMLHttpRequest();
  document.getElementById(target_html).innerHTML = "<div id=\"xmlresult\"> </div>";
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
	  document.getElementById("xmlresult").innerHTML = "";	
      Genform(this,target_id,editmode,target_html,editstructure);
    } };
  document.getElementById("xmlresult").innerHTML = "<tr><h2>Carregando dados ...</h2></tr>";
  xmlhttp.open("GET", editstructure[0].apictrl_view, true);
  xmlhttp.send();
}

function GenLooupCtrl(struct,idx,edit,selected) {
	var lku = 0;
	var ctrlgen = "<select id=\"" + struct[idx].field +"\" name=\"" + struct[idx].field +"\" "+DesabilitarControle(edit)+" >";
	if (typeof struct[idx].lookup !== 'string') {
		for (lku = 0; lku <struct[idx].lookup.length; lku++) { 
			ctrlgen += "<option value=\""+struct[idx].lookup[lku].ID+"\" ";
			if (struct[idx].lookup[lku].ID==selected) { ctrlgen += "selected"; }
			ctrlgen += ">"+struct[idx].lookup[lku].DESC+"</option>";
		}
	} else {
		ctrlgen += "<option value=\"-1\" selected>(Nenhum)</option>";
	}		
	ctrlgen += "</select></P>";
	return ctrlgen;
}	

function Genform(xml,id_target,edit,target_html,struct) {
	var i;
	var xmlDoc = xml.responseXML;
	var new_id = 0;
	var img_ctrl = 0; 
	var img_item = "";	
	var img_s = "";
	var idx = -1;
	var formulario = "<form id=\"c_form\"><fieldset>";
	var x = xmlDoc.getElementsByTagName(struct[0].apictrl_xml);
	
	if (id_target == -1) {
		idx = (x.length-1); 
		new_id = 1;
		edit = 1;
		for (y = 2; y <struct.length; y++) { 
			if (struct[y].type == "checkbox") {
				x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue = "0"; 
			} else {
				x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue = "-";
			} }
		id_target = x[idx].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue; 
	} else {
		for (i = 0; i <x.length; i++) { 
		if (x[i].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue == id_target) {
			idx = i;
			break; } } }	

	formulario += "<legend>"+struct[0].apictrl_title_desc+":</legend>";
	formulario += "<label for=\""+struct[1].field+"\"><STRONG>";

	if (struct[0].apictrl_image[0].trim() == "") {
		if (struct[0].apictrl_image[1].trim() !== "") {
			img_s = "<img src=\""+struct[0].apictrl_image[1]+"\" >";
			img_ctrl = 1; 
		}
	} else {
		img_item = x[i].getElementsByTagName(struct[0].apictrl_image[0])[0].childNodes[0].nodeValue.trim();
		if (img_item == '-') {
			img_item = struct[0].apictrl_image[2].trim();
		}
		img_s += "<img src=\""+struct[0].apictrl_image[1].replace(/##FLD##/i,img_item)+"\" >";
		img_ctrl = 1; 
	}
	if (new_id) { 
		formulario += "[ + "+struct[0].apictrl_title+" ]";
	} else { 
	    formulario += "ID "+x[idx].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue; }
	formulario  += "</STRONG></label>";	
	
	if (img_ctrl == 1) {
		formulario += img_s;
	}
		
	formulario  += "<input type=\"hidden\" name=\""+struct[1].field+"\" value=\"";	
	if (new_id) { 
		formulario += "-1";
	} else {
		formulario += x[idx].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue; }
	formulario += "\"><br><br>";

    for (y = 2; y <struct.length; y++) { 
	    formulario += "<P><label for=\""+struct[y].field+"\"><strong>"+struct[y].title+"</strong></label>";
		if (struct[y].readonly == 1) {
			if (struct[y].type == "checkbox") {	formulario += InteiroSimNao(x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue);
			} else { formulario += x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue;	}
			formulario += "</P>";	
		} else {	
		    if (typeof struct[y].lookup !== 'string') {
				formulario += GenLooupCtrl(struct,y,edit,x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue);
			} else {	
				if (struct[y].type == "checkbox") {	formulario += "<input type=\"checkbox\" id=\""+struct[y].field+"\" name=\""+struct[y].field+"\" value=\"1\" "+CheckboxControle(x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue)+" "+DesabilitarControle(edit)+"></P>";
				} else { formulario += "<input type=\"text\" name=\""+struct[y].field+"\" value=\""+x[idx].getElementsByTagName(struct[y].field)[0].childNodes[0].nodeValue+"\" "+DesabilitarControle(edit)+" size=\""+struct[y].size+"\"></P>";
			} } } }
	formulario += "<br>";
	
	if (edit == 1) {
		if (new_id) {
			formulario += "<P><button type = \"button\" onclick = \"Saveform(-1,"+struct[0].apictrl_self+")\"> Gravar </button>  ";
			formulario += "<button type = \"button\" onclick = \""+struct[0].apictrl_self+"[0].apictrl_load("+struct[0].apictrl_self+")\"> Cancelar </button></P>"; 
		} else {		  
			formulario += "<P><button type = \"button\" onclick = \"Saveform("+x[idx].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue+","+struct[0].apictrl_self+")\"> Gravar </button>  ";
			formulario += "<button type = \"button\" onclick = \"Requestform("+x[idx].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue+",'"+target_html+"',0,"+struct[0].apictrl_self+")\"> Cancelar </button></P>"; 
		}
	} else {
		formulario += "<P><button type = \"button\" onclick = \"Requestform("+x[idx].getElementsByTagName(struct[1].field)[0].childNodes[0].nodeValue+",'"+target_html+"',1,"+struct[0].apictrl_self+")\"> Editar </button>  ";
		formulario += "<button type = \"button\" onclick = \""+struct[0].apictrl_self+"[0].apictrl_load("+struct[0].apictrl_self+")\"> Voltar </button></P>"; 
	}
	
	if (new_id) { id_target = -1; }
	formulario  += "</fieldset></form>";
	document.getElementById("xmlresult").innerHTML = formulario;
	
}
