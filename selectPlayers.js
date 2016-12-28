function moveSelectedOptions(from,to,type) {
	// Unselect matching options, if required
	//from = document.getElementById("developers");
	//to = document.getElementById("selecteddevelopers");
	
	if(type=='select'){
		if(from.value>0){
			++document.getElementById('s').value;
		}
	}
	else if(type=='drop'){
		if(from.value>0){	
			--document.getElementById('s').value;
		}
	}
	
	
	if (arguments.length>3) {
		var regex = arguments[3];
		if (regex != "") {
			unSelectMatchingOptions(from,regex);
			}
		}
	// Move them over
	if (!hasOptions(from)) {  	
		return;
	}
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
			if (!hasOptions(to)) { var index = 0; } else { var index=to.options.length; }
			to.options[index] = new Option( o.text, o.value, false, false);
			}
		}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
			}
		}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
		}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
	writeTextField();
	
	}
function unSelectMatchingOptions(obj,regex) {
	selectUnselectMatchingOptions(obj,regex,"unselect",false);
	}
function selectUnselectMatchingOptions(obj,regex,which,only) {
	if (window.RegExp) {
		if (which == "select") {
			var selected1=true;
			var selected2=false;
			}
		else if (which == "unselect") {
			var selected1=false;
			var selected2=true;
			}
		else {
			return;
			}
		var re = new RegExp(regex);
		if (!hasOptions(obj)) { return; }
		for (var i=0; i<obj.options.length; i++) {
			if (re.test(obj.options[i].text)) {
				obj.options[i].selected = selected1;
				}
			else {
				if (only == true) {
					obj.options[i].selected = selected2;
					}
				}
			}
		}
	}
function hasOptions(obj) {
	if (obj!=null && obj.options!=null) { 

	return true; }
	return false;
	}
function sortSelect(obj) {
	var o = new Array();
	if (!hasOptions(obj)) { return; }
	for (var i=0; i<obj.options.length; i++) {
		o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected) ;
		}
	if (o.length==0) { return; }
	o = o.sort( 
		function(a,b) { 
			if ((a.text+"") < (b.text+"")) { return -1; }
			if ((a.text+"") > (b.text+"")) { return 1; }
			return 0;
			} 
		);

	for (var i=0; i<o.length; i++) {
		obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
		}
	}
function writeTextField() {

	var strValue = new Array();
	var strOrder = new Array();

	for (i = 0; i < document.teamselection.selectedplayers.length; i++) {
		strValue[i] = document.teamselection.selectedplayers[i].value;
	}
	//for (i = 0; i < document.teamselection.selectedplayers.length; i++) {
		//strOrder[i] = i;
	//}
	

	document.teamselection.playerids.value = strValue;
	document.teamselection.clicked.value = 'yes';
	//document.teamselection.order.value = strOrder;

}