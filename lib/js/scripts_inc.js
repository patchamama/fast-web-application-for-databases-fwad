//checkBrowser();

function getBrowserType() {
	if (navigator.userAgent.indexOf("Opera")!=-1 && document.getElementById) type="OP";		//Opera
	else if (navigator.userAgent.indexOf("Safari")!=-1) type="SA";							//Safari
	else if (navigator.userAgent.indexOf("iCab")!=-1) type="IC";							//iCab
	else if (document.all) type="IE";														//Internet Explorer e.g. IE4 upwards
	else if (document.layers) type="NN";													//Netscape Communicator 4
	else if (!document.all && document.getElementById) type="MO";							//Mozila e.g. Netscape 6 upwards
	else type = "??";		//I assume it will not get here
	return type ;
}

function getPlatform() {
	browserInfo = navigator.userAgent.toLowerCase() ;
	if (browserInfo.indexOf("win")!=-1) {
		platform = "windows" ;
	} else if (browserInfo.indexOf("mac")!=-1) {
		platform = "macintosh" ;
	} else {
		platform = "??" ;
	}
	return platform ;
}

function checkBrowser() {
	// when entering the site, check if proper browser is used
	var referringpage = unescape(document.referrer) ;
	var thispage = unescape(document.location.href) ;
	entering_the_site = true ;
	if (referringpage == thispage) {
		entering_the_site = false ;
	}
	var lastSlash = thispage.lastIndexOf("/") ;
	if (referringpage.substr(0,7) == "http://") {
		if (referringpage.substr(0,lastSlash) == thispage.substr(0,lastSlash)) {
			entering_the_site = false ;
		}
	}
	
	if (entering_the_site == true) {
		//check browser used
		type = getBrowserType() ;
		platform = getPlatform() ;
		version = (navigator.appVersion).substr(0,1) ;
		if (type =="NN" && version < 7) {
			document.location.href = "browser_warning.php" ;
		} else if (type =="MO" && version < 1) {
			document.location.href = "browser_warning.php" ;
		} else if (type =="IE" && platform == "macintosh") {
			document.location.href = "browser_warning.php" ;
		} else if (type != "IE" && type !="NN" && type !="MO" && type !="SA") {
			document.location.href = "browser_warning.php" ;
		} else if (thispage.indexOf(".index.html") != -1 || lastSlash == (thispage.length)-1) {
			document.location.href = "search.php" ;
		}
	}
}

function ShowLayer(id, action){
	if (document.all)  {
		eval("document.all." + id + ".style.visibility='" + action + "'");
	} else if (document.getElementById) {
		eval("document.getElementById('" + id + "').style.visibility='" + action + "'");
	} else {
		eval("document." + id + ".visibility='" + action + "'");
	}
}

function resizeNamesLayer() {
	type = getBrowserType() ;
	platform = getPlatform() ;
	if (platform == "windows" && type == "IE") {
		document.all.nameslayer.style.width = 260 ;
	} else if (platform == "macintosh" && type == "IE") {
		document.all.nameslayer.style.width = 245 ;
	} else if (platform == "macintosh" && (type == "MO")) {
		document.getElementById('nameslayer').style.width = 326 ;
	}
}

function autoComplete(theTaxonToComplete,theKeyCode,ctrlKeyIsDown,altKeyIsDown) {
	switch (theKeyCode) {
       case 38: //up arrow  
       case 40: //down arrow
       case 37: //left arrow
       case 39: //right arrow
       case 33: //page up  
       case 34: //page down  
       case 36: //home  
       case 35: //end                  
       case 13: //enter  
       case 9: //tab  
       case 27: //esc  
       case 16: //shift  
       case 17: //ctrl  
       case 18: //alt  
       case 20: //caps lock
       case 8: //backspace  
       case 46: //delete
           return true;
           break;
   } 
	var theTaxonShown = document.list_of_names.taxon.value ;
	var browserType = getBrowserType() ;
	if (browserType == "IE") {
		var theField = "document.getElementById('search_" + theTaxonToComplete +"')" ;
	} else if (document.all) {
		var theField = "document.all.search_" + theTaxonToComplete ;
	} else if (document.search_form) {
		var theField = "document.search_form.search_" + theTaxonToComplete ;
	} else  {
		return true;
	}
	theValue = eval (theField +".value") ;
	if ("0123456789".indexOf(theValue.charAt(0)) != -1) {
		//ignoring numeric input
		return true;
	}
	theValue = escape (theValue) ;
	if (theTaxonToComplete == theTaxonShown) {
		var theListOfNames = document.list_of_names.names.value ;
		var theLowerCaseList = theListOfNames.toLowerCase() ;
		var theLowerCaseValue = theValue.toLowerCase() ;
		var theOffSet = theLowerCaseList.indexOf("/" + theLowerCaseValue) ;
		
		if (theOffSet > -1) {
			var thePrecedingList = theListOfNames.substr(0,theOffSet) ;
			var thePrecedingListArray = thePrecedingList.split("/")
			var theRowToShow = thePrecedingListArray.length ;
			
			var theName = theListOfNames.substr(theOffSet+1,50) ;
			var theOffSet = theName.indexOf("/") ;
			if (theOffSet > -1) {
				theName = theName.substr(0,theOffSet) ;
				eval (theField + ".value=unescape(theName)") ;
				var theSelectionStart = unescape(theValue).length ;
				var theSelectionEnd = theName.length ;
				if (document.all) {
					var theRange = eval (theField + ".createTextRange()") ;
               		theRange.moveStart("character", theSelectionStart);
               		theRange.moveEnd("character", theSelectionEnd);
               		theRange.select();
					eval ("var thisRow = document.all.name_" + theRowToShow) ;
					if (thisRow) {
						thisRow.scrollIntoView() ;
					}
				} else {
					eval (theField + ".selectionStart='" + theSelectionStart +"'") ;
					eval (theField + ".selectionEnd='" + theSelectionEnd +"'") ;
					eval ("var thisRow = document.getElementById('name_" + theRowToShow +"')") ;
					if (thisRow) {
						thisRow.scrollIntoView(true) ;
					}
				}
			}
		}
	}
}

function selectMenuRow(theRow) {
   if (document.getElementById) {
      var tr = eval("document.getElementById(\"" + theRow + "\")");
   } else {
      return;
   }
   if (tr.style) {
       tr.style.backgroundColor = "#EAF2F7";
   }
}

function deSelectMenuRow(theRow) {
   if (document.getElementById) {
		var tr = eval("document.getElementById(\"" + theRow + "\")");
		if (tr.style) {
			tr.style.backgroundColor = "";
		}
   }
}

function moveMenu() {
	menuLayer = document.getElementById('menu_layer') ;
	theScroll = 0;
	if (window.pageYOffset) {
		theScroll = window.pageYOffset;
	} else if (window.document.documentElement && window.document.documentElement.scrollTop) {
		theScroll = window.document.body.scrollTop;
	} else if (window.document.body) {
		theScroll = window.document.body.scrollTop;
	}
	var newY = theScroll + "px";
	if (menuLayer) {
		menuLayer.style.top = newY;
		setTimeout("moveMenu()",500);
	}
}

function showStatus(message) {
    window.status = message ;
    return true ;
}

function insertEmailAddress(a,b) {
	document.write("<a href='mailto:" + a + "@" + b + "'>") ;
	document.write(a + "@" + b + "</a>") ;
}

function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function changeImages() {
	if (document.images) {
		for (var i=0; i<changeImages.arguments.length; i+=2) {
			document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
		}
	}
}

function preloadImages() {
	if (document.images) {
		arrow_down_red = newImage("images/arrow_down_red.jpg");
		arrow_up_red = newImage("images/arrow_up_red.jpg");
		arrow_down_mousedown = newImage("images/arrow_down_mousedown.jpg");
		arrow_up_mousedown = newImage("images/arrow_up_mousedown.jpg");
		waitGraphic = newImage("images/wait.gif") ;
	}
}

function showWaitScreen(message) {
	window.status = message;
	TB_Wait();  //need thickbox_wait.js
}