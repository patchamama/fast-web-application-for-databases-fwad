<?php
// File           javascript.inc.php / ibWebAdmin
// Purpose        inline JavaScript functions
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/20 17:42:18 lb>
//
// $Id: javascript.inc.php,v 1.17 2004/05/20 10:29:07 lbrueckner Exp $



//
// print a JavaScript function that checks the settings for 'Not Null',
// 'Unique' and 'Primary' in a col_def_defination field
// 
// -> only one of 'Unique' and 'Primary' can be selected 
// -> if 'Unique' or 'Primary' is selected, autoselect 'Not Null'
//
// opt   : name of the selected checkbox
// index : index of the col_def_definition
// form  : form object
//
function js_checkColConstraint() {
    static $done = FALSE;

    if (USE_DHTML == FALSE  ||  $done == TRUE) {
        return '';
    }


    echo <<<EOT
<script language="JavaScript">
<!--
function checkColConstraint(form, opt, index) {

    with (form) {

        if ((eval("cd_def_unique" + index).checked == true)
        ||  (eval("cd_def_primary" + index).checked == true)) {
            eval("cd_def_notnull" + index).checked = true;
        }

        if (("cd_def_unique" + index) == opt) {
            if  ((eval("cd_def_primary" + index).checked == false)
            &&   (eval("cd_def_unique" + index).checked == true)) {
                eval("cd_def_unique" + index).checked = true;
            }
            else {
                eval("cd_def_unique" + index).checked = false;
            }
        }

        if (("cd_def_primary" + index) == opt) {
            if ((eval("cd_def_unique" + index).checked == false)
            &&  (eval("cd_def_primary" + index).checked == true)) {
                eval("cd_def_primary" + index).checked = true;
            }
            else {
                  eval("cd_def_primary" + index).checked = false;
            }             
        }
    }
}
//-->
</script>

EOT;

    $done = TRUE;
}


//
// return a string with a javascript to give the focus to $field in $form
// (because ns4.7 fails on js inside of a table, this is written to
//  a string $js_stack, which is printed out in script_end.inc.php)
//
function js_giveFocus($form, $field) {
    $js  = "<script language=\"JavaScript\">\n<!--\n";
    $js .= "    window.document.$form.$field.focus();\n";
    $js .= "//-->\n</script>\n";

    return $js;
}


//
// set width and height of the window
//
function js_window_resize($width, $height) {

    $js  = "<script language=\"JavaScript\">\n<!--\n"
           ."   window.resizeTo($width, $height);\n"
          ."//-->\n</script>\n";

    return $js;
}


//
// builds a javascript array with the collation definitions
// and a function to restrict the collation selectlist according to the selected charset
//
// Parameter: charsets   charset definitions, $_SESSION['s_charsets']
//
//            source     charsets selectlist object
//            target     collations selectlist object
//
function js_collations(&$charsets) {
    static $done = FALSE;

    if (USE_DHTML == FALSE  ||  $done == TRUE) {
        return '';
    }

    $js  = "<script language=\"JavaScript\">\n<!--\n"
          ."    var collations = new Array();\n";

    foreach ($charsets as $cs) {
        $js .= '    collations["'.$cs['name']."\"] = new Array();\n";
        $n = 0;
        foreach ($cs['collations'] as $coll) {
            $js .= '    collations["'.$cs['name'].'"]['.$n.'] = "'.$coll."\";\n";
            $n++;
        }
    }
    $js .= "\n";

    $js .=<<<EOT
    function adjustCollation(source, target) {
        var i, charset;
        for(i=0; i<source.length; i++) {
            if(source.options[i].selected == true) {
                charset = source.options[i].value;
            }
        }
        cnt = target.options.length;
        for (i=0; i<cnt; i++){
            target.options[0] = null;
        }
        target.options[0] = new Option("", "");
        if (typeof(collations[charset]) == "object") {
            for (i=0; i<collations[charset].length; i++){
                target.options[i+1] = new Option(collations[charset][i], collations[charset][i]);
            }
        }
    }
//-->
</script>

EOT;

    $done = TRUE;

    return $js;
}


//
// include the jsrsClient code
//
function js_jsrs_client() {
    static $done = FALSE;

    if (USE_DHTML == FALSE  ||  $done == TRUE) {
        return '';
    }
    $done = TRUE;

    return '    <script src="jsrs/jsrsClient.js" type="text/javascript"></script>'."\n";
}


//
// functions to request, display and hide the values for a foreign key on the tb_watch panel
//
function js_jsrs_fk() {

    $js =<<<EOT
    <script language="javascript">
    <!--
    function requestFKValues(table, column, value) {
        jsrsPOST = true;
        jsrsExecute("%s", displayFKValues, "get_fk_values", Array(table, column, value));
    }

    function displayFKValues(returnstring) {
        var target = document.getElementById("fk");
        target.innerHTML = returnstring;
        target.style.display = "block";
    }

    function closeFK() {
        var target = document.getElementById("fk");
        target.style.display = "none";
    }
    //-->
    </script>

EOT;

    $js = sprintf($js, url_session('jsrs/fk_request.php'));

    return $js;
}


//
// functions to request, display and hide the details for a database object
//
function js_jsrs_detail() {
    static $done = FALSE;

    if ($done == TRUE) {
        return '';
    }

    $js =<<<EOT
    <script language="javascript">
    <!--
    function requestDetail(type, name, title) {
        jsrsPOST = true;
        jsrsExecute("%1\$s", displayDetail, "get_detail", Array(type, name, title));
    }

    function displayDetail(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        var target = document.getElementById(result[0]);
        target.innerHTML = result[1];
        target.style.display = "block";
    }

    function closeDetail(type, id, name, title) {
        var target = document.getElementById(id);
        target.innerHTML = "%2\$s"  + "\\n";
        jsrsPOST = true;
        jsrsExecute("%3\$s", null, "close_detail", Array(type, name));
    }
    //-->
    </script>

EOT;
    $closed_html = addslashes(trim(get_closed_detail('{TITLE}', fold_detail_url('{TYPE}', 'close', '{NAME}', '{TITLE}'))));
    if (ini_get('magic_quotes_sybase') == 1) {
        $closed_html = str_replace(array('"', "''"), array('\"',"\\'"), $closed_html);
    }
    $closed_html = str_replace(array('{TITLE}', '{TYPE}', '{NAME}'),
                               array('" + title + "', '" + type + "', '" + name + "'),
                               $closed_html);
    $js = sprintf($js,
                  url_session('jsrs/detail_request.php'),
                  $closed_html,
                  url_session('jsrs/detail_close.php')
                  );
    $done = TRUE;

    return $js;
}


//
// functions to request and display a closed panel
//
function js_jsrs_close_panel() {

    $js =<<<EOT
    <script language="javascript">
    <!--
    function requestClosedPanel(idx, active) {
        jsrsPOST = true;
        jsrsExecute("%1\$s", displayClosedPanel, "closed_panel", Array(idx, active));
    }

    function displayClosedPanel(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        var target = document.getElementById("p" + result[0]);
        target.innerHTML = result[1];
    }
    //-->
    </script>

EOT;

    return sprintf($js, url_session('jsrs/closed_panel_request.php')); 
}


//
// functions to get the content of a sql buffer and to put it into the textarea on the sql-enter panel
//
function js_jsrs_sql_buffer() {

    $js =<<<EOT
    <script language="javascript">
    <!--
    function requestSqlBuffer(idx) {
        jsrsPOST = true;
        jsrsExecute("%1\$s", putSqlBuffer, "sql_buffer", Array(idx));
    }

    function putSqlBuffer(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        document.getElementById("sql_script").value = result[0];
        document.getElementById("sql_pointer").options[result[1]].selected = true;

        var next = document.getElementById("sql_next").href;
        var nidx = parseInt(result[1]) == %2\$d -1 ? 0 : parseInt(result[1]) +1;
        document.getElementById("sql_next").href = next.replace(/\d+/, nidx);

        var prev = document.getElementById("sql_prev").href;
        var pidx = parseInt(result[1]) == 0 ?  %2\$d -1 : parseInt(result[1]) -1;
        document.getElementById("sql_prev").href = next.replace(/\d+/, pidx);
    }
    //-->
    </script>

EOT;

    return sprintf($js, url_session('jsrs/sql_buffer_request.php'), SQL_HISTORY_SIZE); 
}


//
// functions used for the system table filters
//
// don't set jsrsPOST = true! this will lead to requests for jsrs/jsrs/systable_filter_request.php ?!?
function js_jsrs_filter_fields() {

    $js = <<<EOT
    <script language="javascript">
    <!--
    function getFilterFields(table) {
        jsrsPOST = true;
        jsrsExecute("%1\$s", setFilterFields, "filter_fields", Array(table));
    }

    function setFilterFields(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        var target = document.getElementById("systable_field");
        target.innerHTML = result[0];
    }

    function getFilterValues(table, field) {
        jsrsPOST = true;
        jsrsExecute("%1\$s", setFilterValues, "filter_values", Array(table, field));
    }

    function setFilterValues(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        var target = document.getElementById("systable_value");
        target.innerHTML = result[0];
    }
    //-->
    </script>

EOT;

    return sprintf($js, url_session('jsrs/systable_filter_request.php'));
}


//
// auto-refresh feature on thy systables panel for IB7 temporary system tables
//
function js_jsrs_refresh_systable() {

    $js = <<<EOT
    <script language="javascript">
    var sttimer;
    function refresh_systable(seconds) {
        if (sttimer) {
            window.clearInterval(sttimer);
        }
        if (seconds != 0) {
            sttimer = window.setInterval('requestSystable()', seconds*1000);
        }
        else {
            requestSystable(0);
        }
    }

    function requestSystable() {
        jsrsPOST = true;
        jsrsExecute("%1\$s", displaySystable, "systable", Array(document.db_systable_form.db_refresh.value));
    }

    function displaySystable(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        if (result[0].length > 0) {
            var target = document.getElementById("st");
            target.innerHTML = result[0];
            if (str) delete str;
            str = new SelectableTableRows(document.getElementById("systable"), true)
        }
    }
    </script>

EOT;

    return sprintf($js, url_session('jsrs/systable_request.php'));
}


//
// request a selectlist filled with the columns of a table;
//
function js_jsrs_table_columns() {

    $js = <<<EOT
    <script language="javascript">
    function requestTableColumns(table, target, restriction) {
        jsrsPOST = true;
        jsrsExecute("%1\$s", displayTableColumns, "table_columns_selectlist", Array(table, target, restriction));
    }
 
    function displayTableColumns(returnstring) {
        var result = jsrsArrayFromString(returnstring, "~");
        var target = document.getElementById(result[0]);
        target.innerHTML = result[1];
    }
    </script>

EOT;

    return sprintf($js, url_session('jsrs/table_columns_request.php'));
}


//
// include webfx's selectableeleements library
//
function js_selectableelements() {
    static $done = FALSE;

    if (USE_DHTML == FALSE  ||  $done == TRUE) {
        return '';
    }
    $done = TRUE;

    return '    <script src="lib/js/webfx/selectableelements.js" type="text/javascript"></script>'."\n"
          .'    <script src="lib/js/webfx/selectabletablerows.js" type="text/javascript"></script>'."\n";
}

//
// javascript to install an eventlistener to mark tablerows on mouse clicks
//
function js_selectabletablerows_listener($tableid) {

    return "    <script language=\"JavaScript\">\n"
          ."    <!--\n"
          .'        var str = new SelectableTableRows(document.getElementById("'.$tableid."\"), true);\n"
          ."    //-->\n"
         ."    </script>\n";
}

?>
