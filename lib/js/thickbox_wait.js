function TB_Wait() {

	try {
		if (document.getElementById("TB_HideSelect") == null) {
		$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
		$("#TB_overlay").click(TB_remove);
		}
		
		$(window).scroll(TB_position);
 		
		TB_overlaySize();
		
		$("body").append("<div id='TB_load'><img src='images/loadingAnimation.gif' /></div>");
		TB_load_position();
			
	} catch(e) {
		alert( e );
	}
}

function TB_Wait_Stop() {
	TB_remove();
}

