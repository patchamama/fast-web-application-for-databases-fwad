//AÑADIDO POR DESARROLLOWEB.COM
var empezar = false
var ancla = "ancla"
var capas = new Array("c1","c2","c3","c4","c5","c6","c7","c8","c9")
var retardo 
var ocultar

function muestra(capa){
	xShow(capa);
}
function oculta(capa){
	xHide(capa);
}
function posiciona (){
	posx= xOffsetLeft("ancla")
	posy= xOffsetTop ("ancla")
	for (i=0;i<capas.length;i++){
		xMoveTo(capas[i],posx+5,posy-1)
	}
}
window.onload = function() {
	posiciona()
	empezar = true
}
window.onresize = function() {
	posiciona()
}

function muestra_coloca(capa){
 if (empezar){
	for (i=0;i<capas.length;i++){
		if (capas[i] != capa) xHide(capas[i])
	}
	clearTimeout(retardo)
	xShow(capa)
 }
}

function oculta_retarda(capa){
 if (empezar){
	ocultar =capa
	clearTimeout(retardo)
	retardo = setTimeout("xHide('" + ocultar + "')",1000)
 }
}

function muestra_retarda(ind){
 if (empezar){
	clearTimeout(retardo)
 }
}

//function busca(raiz){
//if (document.f.sitesearch[1].checked){
//	document.f.action= "http://...";
//	document.f.criterio.value=document.f.q.value
//	document.f.method="post";
//}
//}

function abreVentana(sURL){
			sURL = "http://www..." + sURL
			window.open(sURL,"wind","scrollbars,resizable,width=550,height=460,menubar=false,location=false,top=20,left=80")
}

//Extraido de DESARROLLOWEB.COM con modificaciones hechas de Crosss-Browser..

// x_core.js, X v3.15.2, Cross-Browser.com DHTML Library
// Copyright (c) 2004 Michael Foster, Licensed LGPL (gnu.org)

// global vars still duplicated in xlib.js - I still don't know what I'm going to do about this
var xVersion='3.15.2',xNN4,xOp7,xOp5or6,xIE4Up,xIE4,xIE5,xMac,xUA=navigator.userAgent.toLowerCase();
if (window.opera){
  xOp7=(xUA.indexOf('opera 7')!=-1 || xUA.indexOf('opera/7')!=-1);
  if (!xOp7) xOp5or6=(xUA.indexOf('opera 5')!=-1 || xUA.indexOf('opera/5')!=-1 || xUA.indexOf('opera 6')!=-1 || xUA.indexOf('opera/6')!=-1);
}
else if (document.all && xUA.indexOf('msie')!=-1) {
  xIE4Up=parseInt(navigator.appVersion)>=4;
  xIE4=xUA.indexOf('msie 4')!=-1;
  xIE5=xUA.indexOf('msie 5')!=-1;
}
else if (document.layers) {xNN4=true;}
xMac=xUA.indexOf('mac')!=-1;

function xGetElementById(e) {
  if(typeof(e)!='string') return e;
  if(document.getElementById) e=document.getElementById(e);
  else if(document.all) e=document.all[e];
  else e=null;
  return e;
}
function xParent(e,bNode){
  if (!(e=xGetElementById(e))) return null;
  var p=null;
  if (!bNode && xDef(e.offsetParent)) p=e.offsetParent;
  else if (xDef(e.parentNode)) p=e.parentNode;
  else if (xDef(e.parentElement)) p=e.parentElement;
  return p;
}
function xDef() {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])=='undefined') return false;}
  return true;
}
function xStr(s) {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])!='string') return false;}
  return true;
}
function xNum(n) {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])!='number') return false;}
  return true;
}
function xShow(e) {
  if(!(e=xGetElementById(e))) return;
  if(e.style && xDef(e.style.visibility)) e.style.visibility='visible';
}
function xHide(e) {
  if(!(e=xGetElementById(e))) return;
  if(e.style && xDef(e.style.visibility)) e.style.visibility='hidden';
}
function xZIndex(e,uZ) {
  if(!(e=xGetElementById(e))) return 0;
  if(e.style && xDef(e.style.zIndex)) {
    if(xNum(uZ)) e.style.zIndex=uZ;
    uZ=parseInt(e.style.zIndex);
  }
  return uZ;
}
function xMoveTo(e,iX,iY) {
  xLeft(e,iX);
  xTop(e,iY);
}
function xLeft(e,iX) {
  if(!(e=xGetElementById(e))) return 0;
  var css=xDef(e.style);
  if (css && xStr(e.style.left)) {
    if(xNum(iX)) e.style.left=iX+'px';
    else {
      iX=parseInt(e.style.left);
      if(isNaN(iX)) iX=0;
    }
  }
  else if(css && xDef(e.style.pixelLeft)) {
    if(xNum(iX)) e.style.pixelLeft=iX;
    else iX=e.style.pixelLeft;
  }
  return iX;
}
function xTop(e,iY) {
  if(!(e=xGetElementById(e))) return 0;
  var css=xDef(e.style);
  if(css && xStr(e.style.top)) {
    if(xNum(iY)) e.style.top=iY+'px';
    else {
      iY=parseInt(e.style.top);
      if(isNaN(iY)) iY=0;
    }
  }
  else if(css && xDef(e.style.pixelTop)) {
    if(xNum(iY)) e.style.pixelTop=iY;
    else iY=e.style.pixelTop;
  }
  return iY;
}
function xOffsetLeft(e) {
  if (!(e=xGetElementById(e))) return 0;
  if (xDef(e.offsetLeft)) return e.offsetLeft;
  else return 0;
}
function xOffsetTop(e) {
  if (!(e=xGetElementById(e))) return 0;
  if (xDef(e.offsetTop)) return e.offsetTop;
  else return 0;
}
