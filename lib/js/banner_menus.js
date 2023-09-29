function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
// -->

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function P7_autoLayers() { //v1.4 by PVII
 var g,b,k,f,args=P7_autoLayers.arguments;a=parseInt(args[0]);if(isNaN(a))a=0;
 if(!document.p7setc){p7c=new Array();document.p7setc=true;for(var u=0;u<10;u++){
 p7c[u]=new Array();}}for(k=0;k<p7c[a].length;k++){if((g=MM_findObj(p7c[a][k]))!=null){
 b=(document.layers)?g:g.style;b.visibility="hidden";}}for(k=1;k<args.length;k++){
 if((g=MM_findObj(args[k]))!=null){b=(document.layers)?g:g.style;b.visibility="visible";f=false;
 for(var j=0;j<p7c[a].length;j++){if(args[k]==p7c[a][j]) {f=true;}}
 if(!f){p7c[a][p7c[a].length++]=args[k];}}}
}
filename = new String( window.location.href )
rootpath = new String( window.location.href )
rootpathparsed = new String (window.location.href )
levels = new String (" ")
spanlink = new String (" ")
englink = new String (" ")
ptlink = new String (" ")
function getFilename( )
{
filename = filename.substring( filename.lastIndexOf( "/" ) + 1, filename.length );
rootpath = rootpath.substring (rootpath.indexOf("/")+1, rootpath.length );
nn = rootpath.split("/");
levels = nn.length
rootpathparsed = nn.reverse().join()
}
getFilename(filename);
getFilename (rootpath);
getFilename (rootpathparsed);
//make links for Spanish pages
for (i = levels-1; i >= 0; i --)
{
if (nn[i] == 'spanish')
englink = englink + ('/english')
else
englink = englink + ('/' + nn[i])
}
for (i = levels-1; i >= 0; i --)
{
if (nn[i] == 'spanish')
spanlink = spanlink + ('/spanish')
else
spanlink = spanlink + ('/' + nn[i])
}
for (i = levels-1; i >= 0; i --)
{
if (nn[i] == 'spanish')
ptlink = ptlink + ('/portuguese')
else
ptlink = ptlink + ('/' + nn[i])
}
function P7_LH(){ //1.5 by PVII
 P7_autoLayers(0,'banner');
}
function P7_LoadHandler(a){ //1.5 by PVII
 if(a==1||a==3){onload=P7_LH;}if(a>1){onresize=P7_RH;}
}
P7_LoadHandler(1);
