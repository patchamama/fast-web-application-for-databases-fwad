// File           js/miscellaneous.js / ibWebAdmin
// Purpose        collection of javascript functions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/05/01 17:25:01 lb>
//
// $Id: miscellaneous.js,v 1.3 2003/10/13 20:56:41 lbrueckner Exp $


// an implementation of Function.apply() and Function.call() for this browser
// which didn't implement them (i.e. ie5); needed by js/webfx/selectabletable.js;
//
// based on an example from 'Professional JavaScript 2nd Edition', Wrox Press 2001
//
// patch the apply method of the Function class of objects if needed
function _Apply_(thisObj, argArray) {
    var str, i, len, retValue;

    if (thisObj + "" == "null"  ||  thisObj + "" == "undefined")
        thisObj = window;
    if (argArray + "" == "null"  ||  argArray + "" == "undefined")
        argArray = new Array();

    var index = 0;
    while (thisObj["temp" + index] + "" != "undefined")
        index++;
    thisObj["temp" + index] = this;

    str = "thisObj.temp" + index + "(";
    len = argArray.length;
    for (i=0; i<len; i++) {
        str += "argArray[" + i + "]";
        if (i + 1 < len)
            str += ", ";
    }
    str += ");";

    retValue = eval(str);

    thisObj["temp" + index] = undefined;

    return retValue;
}
if (!Function.prototype.apply)
    Function.prototype.apply = _Apply_;

// patch the call method of the Function class of objects if needed
function _Call_(thisObj, arg1, arg2, argN) {
    return this.apply(thisObj, Array.apply(null, arguments).slice(1));
}
if (!Function.prototype.call)
    Function.prototype.call = _Call_;

// patch the undefined value for compatability for older browsers
var undefined;



// find and return the value of the selected element in a selectlist
function selectedElement(source) {

    return source.options[source.selectedIndex].value;
}
