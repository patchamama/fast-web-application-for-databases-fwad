<?php
// File           panel_elements.inc.php / ibWebAdmin
// Purpose        functions for generating html-code that is needed in various panels
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/11 21:45:25 lb>
//
// $Id: panel_elements.inc.php,v 1.62.2.1 2005/01/17 21:20:52 lbrueckner Exp $


//
// output the html for the part of a table/form where the definitions
// for a <datatype> statement can be entered; used for columns and domains
// 
// Variables:     $i        index in $s_coldefs[] for this coldef
//                $title    headline string
//                $rowspan  span the name field over so much rows
//                $collate  display the collation element if TRUE
//
function get_datatype_definition($idx, $title, $rowspan = 1, $collate=FALSE) {
    global $s_coldefs, $tb_strings;

    // preselect values for the form elements
    $name_value     = isset($s_coldefs[$idx]['name'])        ? $s_coldefs[$idx]['name']         : '';
    $colpos_value   = isset($HTTP_POST_VARS['tb_modcol_pos'])? $HTTP_POST_VARS['tb_modcol_pos'] : '';
    $datatype_pre   = isset($s_coldefs[$idx]['type'])        ? $s_coldefs[$idx]['type']         : NULL;
    $size_value     = isset($s_coldefs[$idx]['size'])        ? $s_coldefs[$idx]['size']         : '';
    $charset_pre    = isset($s_coldefs[$idx]['charset'])     ? $s_coldefs[$idx]['charset']      : NULL;
    $collate_pre    = isset($s_coldefs[$idx]['collate'])     ? $s_coldefs[$idx]['collate']      : NULL;
    $prec_value     = isset($s_coldefs[$idx]['prec'])        ? $s_coldefs[$idx]['prec']         : '';
    $scale_value    = isset($s_coldefs[$idx]['scale'])       ? $s_coldefs[$idx]['scale']        : '';
    $stype_value    = isset($s_coldefs[$idx]['stype'])       ? $s_coldefs[$idx]['stype']        : '';
    $segsize_value  = isset($s_coldefs[$idx]['segsize'])     ? $s_coldefs[$idx]['segsize']      : '';

    // colspan attribute for the charset cell
    $charspan = ($collate == FALSE) ? 2 : 1;

    // javascript event-handler to adjust the collation accordingly to the selected charset
    $charset_tags = array();
    if (USE_DHTML == TRUE  &&  $collate == TRUE) {
        $form_name = get_form_name($idx);
        $charset_tags = array('onChange' => 'adjustCollation(document.'.$form_name.'.cd_def_charset'.$idx.', document.'.$form_name.'.cd_def_collate'.$idx.')');
    }

    $html = "  <tr>\n"
          . '    <th colspan="9" align="left"><b>'.$title."</b></th>\n"
          . "  </tr>\n"

          . "  <tr>\n"
          . '    <td rowspan="'.$rowspan."\" valign=\"top\" height=\"100%\">\n"
          . "      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
          . "        <tr>\n"
          . "          <td>\n"
          . '            <b>'.$tb_strings['Name']."</b><br>\n"
          . '            '.get_textfield('cd_def_name'.$idx, 20, 31, $name_value)
          . "          </td>\n"
          . "        </tr>\n"
          . "      </table>\n";

    if ($idx === 'mod') {
        $html .= "      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" valign=\"bottom\" height=\"100%\">\n"
               . "        <tr>\n"
               . "          <td>\n"
               . '            <b>'.$tb_strings['NewColPos']."</b><br>\n"
               . '            '.get_textfield('tb_modcol_pos', 4, 4, $colpos_value)
               . "          </td>\n"
               . "        </tr>\n"
               . "      </table>\n";
    }

    $html .= "    </td>\n"
           ."     <td>\n"
           . '      <b>'.$tb_strings['Datatype']."</b><br>\n"
           . '      '.get_selectlist('cd_def_type'.$idx, get_datatypes(SERVER_FAMILY, SERVER_VERSION), $datatype_pre, TRUE)
           . "    </td>\n"
           . "    <td>\n"
           . '      <b>'.$tb_strings['Size']."</b><br>\n"
           . '      '.get_textfield('cd_def_size'.$idx, 5, 5, $size_value)
           . "    </td>\n"
           . '    <td colspan="'.$charspan."\">\n"
           . '      <b>'.$tb_strings['Charset']."</b><br>\n"
           . '      '.get_charset_select('cd_def_charset'.$idx, $charset_pre, TRUE, $charset_tags)
           . "    </td>\n";

    if ($collate == TRUE) {            
        $html .= "    <td>\n"
               . '      <b>'.$tb_strings['Collation']."</b><br>\n"
               . '      '.get_collation_select('cd_def_collate'.$idx, $collate_pre, TRUE)
               . "    </td>\n";
    }

    $html .= "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['PrecShort']."</b><br>\n"
           . '      '.get_textfield('cd_def_prec'.$idx, 2, 2, $prec_value)
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['Scale']."</b><br>\n"
           . '      '.get_textfield('cd_def_scale'.$idx, 2, 2, $scale_value)
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['Subtype']."</b><br>\n"
           . '      '.get_textfield('cd_def_stype'.$idx, 3, 3, $stype_value)
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['SegSiShort']."</b><br>\n"
           . '      '.get_textfield('cd_def_segsize'.$idx, 5, 5, $segsize_value)
           . "    </td>\n"
           . "  </tr>\n";

    echo $html;
}


//
// html sequence for part of a table/form to define a <col_def> statement
//
// Variables:     $idx      index in $s_coldefs[] for this coldef
//                $title  headline string
//                $rowspan  span the name field over so much rows
//
function get_coldef_definition($idx, $title, $rowspan, $collate=FALSE) {
    global $s_coldefs, $tb_strings, $s_domains;

    $coldefs = isset($s_coldefs[$idx]) ? $s_coldefs[$idx] : array();

    $domain_names = array_keys($s_domains);
    $rowspan = !empty($domain_names) ? $rowspan -1 : $rowspan;

    // preselect values for the form elements
    $domain_pre    = isset($coldefs['domain'])  ? $coldefs['domain']  : NULL;
    $comp_value    = isset($coldefs['comp'])    ? $coldefs['comp']    : '';
    $default_value = isset($coldefs['default']) ? $coldefs['default'] : '';
    $check_value   = isset($coldefs['check'])   ? $coldefs['check']   : '';

    $ehandler_str = '';
    if (USE_DHTML == TRUE) {
        $form = 'document.'.get_form_name($idx);
        $ehandler_str = ' onClick="checkColConstraint('.$form.", this.name, '".$idx."')";
    }

    $html = get_datatype_definition($idx, $title, $rowspan, $collate)
          . "  <tr>\n"
          . "    <td colspan=\"4\">\n";

    if (!empty($domain_names)) {
        $html .= '      <b>'.$tb_strings['Domain']."</b><br>\n"
               . '      '.get_selectlist('cd_def_domain'.$idx, $domain_names, $domain_pre, TRUE);
    }
    else {
        $html .= "&nbsp;\n";
    }

    $html .= "    </td>\n"
           . "    <td colspan=\"2\" align=\"center\">\n"
           . '      <b>'.$tb_strings['NotNull']."</b><br>\n"
           . '      <input type="checkbox" name="cd_def_notnull'.$idx.'"'.$ehandler_str.'"'.(!empty($coldefs['notnull']) ? ' checked' : '').">\n"
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['Unique']."</b><br>\n"
           . '      <input type="checkbox" name="cd_def_unique'.$idx.'"'.$ehandler_str.'"'.(!empty($coldefs['unique']) ? ' checked' : '').">\n"
           . "    </td>\n"
           . "    <td align=\"center\">\n"
           . '      <b>'.$tb_strings['Primary']."</b><br>\n"
           . '      <input type="checkbox" name="cd_def_primary'.$idx.'"'.$ehandler_str.'"'.(!empty($coldefs['primary']) ? ' checked' : '').">\n"
           . "    </td>\n"
           . "  </tr>\n"

           . "  <tr>\n"
           . "    <td colspan=\"2\">\n"
           . '      <b>'. $tb_strings['CompBy']."</b><br>\n"
           . '      '.get_textfield('cd_def_comp'.$idx, 27, 512, $comp_value)
           . "    </td>\n"
           . "    <td colspan=\"2\">\n"
           . '      <b>'.$tb_strings['Default']."</b><br>\n"
           . '      '.get_textfield('cd_def_default'.$idx, 27, 256, $default_value)
           . "    </td>\n"
           . "    <td colspan=\"4\">\n"
           . '      <b>'.$tb_strings['Check']."</b><br>\n"
           . '      '.get_textfield('cd_def_check'.$idx, 27, 256, $check_value)
           . "    </td>\n"
           . "  </tr>\n"

           . get_column_constraint_definition($coldefs, $idx);

    return $html;
}


//
// html for foreign key definitions and dropping column constraints
//
function get_column_constraint_definition($coldefs, $idx) {
    global $fk_actions, $tb_strings;

    $fk_name   = isset($coldefs['fk_name'])   ? $coldefs['fk_name']   : '';
    $fk_table  = isset($coldefs['fk_table'])  ? $coldefs['fk_table']  : '';
    $fk_column = isset($coldefs['fk_column']) ? $coldefs['fk_column'] : '';
    $on_update = isset($coldefs['on_update']) ? $coldefs['on_update'] : '';
    $on_delete = isset($coldefs['on_delete']) ? $coldefs['on_delete'] : '';

    $table_element = get_table_selectlist('cd_def_fk_table_'.$idx,
                                          array('no_views', 'references'),
                                          $fk_table,
                                          TRUE,
                                          array('onChange' => "requestTableColumns(selectedElement(this), 'cd_def_fk_col_".$idx."', 'fk');")
                                          );

    $drop_element = '';
    if ($idx == 'mod' &&
        ((isset($coldefs['primary'])  &&  $coldefs['primary_cols'] == 1) ||
         (isset($coldefs['unique'])   &&  $coldefs['unique_cols']  == 1) ||
         (isset($coldefs['foreign'])  &&  $coldefs['foreign_cols'] == 1)
         )
        ) {
        $checked_str = $coldefs['fk_del'] == TRUE ? ' checked' : '';
        $drop_element =  "        <tr>\n"
                        ."          <td colspan=\"7\">\n"
                        ."            <table style=\"border-bottom-width: 1px; border-bottom-style: solid;\" width=\"100%\">\n"
                        ."              <tr>\n";

        if (isset($coldefs['primary'])  &&  $coldefs['primary_cols'] == 1) {
            $checked_str = $coldefs['pk_del'] == TRUE ? ' checked' : '';
            $drop_element .= "                <td>\n"
                            .'                  <input type="checkbox" name="cd_def_pk_del_'.$idx.'"'.$checked_str.'> <b>'.$tb_strings['DropPK']."</b>\n" 
                            ."                </td>\n";
        }
        if (isset($coldefs['unique'])  &&  $coldefs['unique_cols'] == 1) {
            $checked_str = $coldefs['uq_del'] == TRUE ? ' checked' : '';
            $drop_element .= "                <td>\n"
                            .'                  <input type="checkbox" name="cd_def_uq_del_'.$idx.'"'.$checked_str.'> <b>'.$tb_strings['DropUq']."</b>\n" 
                            ."                </td>\n";
        }
        if (isset($coldefs['foreign'])  &&  $coldefs['foreign_cols'] == 1) {
            $checked_str = $coldefs['fk_del'] == TRUE ? ' checked' : '';
            $drop_element .= "                <td>\n"
                            .'                  <input type="checkbox" name="cd_def_fk_del_'.$idx.'"'.$checked_str.'> <b>'.$tb_strings['DropFK']."</b>\n" 
                            ."                </td>\n";
        }

        $drop_element .= "              </tr>\n"
                        ."            </table>\n"
                        ."          <td>\n"
                        ."        </tr>\n";
    }

    $html = "  <tr>\n"
           ."    <td colspan=\"8\">\n"
           ."      <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n"
                    .$drop_element
           ."        <tr>\n"
           ."          <td colspan=\"7\">\n"
           .'            <b>'.$tb_strings['FKName']."</b><br>\n"
           .'            <input type="text" size="27" maxlength="31" name="cd_def_fk_name_'.$idx.'" value="'.$fk_name."\">\n"
           ."          </td>\n"
           ."        </tr>\n"
           ."        <tr>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['Table1']."</b><br>\n"
           .'            '.$table_element."\n"
           ."          </td>\n"
           ."          <td>&nbsp;&nbsp;</td>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['Column1']."</b><br>\n"
           .'            <span id="cd_def_fk_col_'.$idx."\">\n"
           .'              <input type="text" size="20" maxlength="31" name="cd_def_fk_col_'.$idx.'" value="'.$fk_column."\">\n"
           ."            </span>\n"
           ."          </td>\n"
           ."          <td>&nbsp;&nbsp;</td>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['OnUpdate']."</b><br>\n"
           ."            ".get_selectlist('cd_def_ou_'.$idx, $fk_actions, $on_update, TRUE)."\n"
           ."          </td>\n"
           ."          <td>&nbsp;&nbsp;</td>\n"
           ."          <td>\n"
           .'            <b>'.$tb_strings['OnDelete']."</b><br>\n"
           ."            ".get_selectlist('cd_def_od_'.$idx, $fk_actions, $on_delete, TRUE)."\n"
           ."          </td>\n"
           ."        </tr>\n"
           ."      </table>\n"
           ."    </td>\n"
           ."  </tr>\n";

    return $html;
}


//
// find out the name of a datatype definition form
//
// Parameter: $idx    suffix, used for the form elements
//
function get_form_name($idx) {

    // yes, its ugly,
    // but I need the form name for the adjustCollation() javascript
    $idx = (string)$idx;
    switch ($idx) {
        case 'add':
            // modify table / add column
            $form_name = 'tb_modadd_form';
            break;
        case 'dom':
            // create domain
            $form_name = 'acc_domain_form';
            break;
        default:
            // create table
            $form_name = 'tb_create_col_form';
    }

    return $form_name;
}


//
// asks for SYSDBAs password
//
function sysdba_pw_textfield($caption, $explain, $pw) {

?>
<table border cellpadding="3" cellspacing="0">
<tr>
  <th align="left"><?php echo $caption; ?></th>
     <td><input type="password"  size="20" maxlength="32" name="sysdba_pw" value="<?php echo password_stars($pw); ?>">&nbsp;
        <?php echo $explain; ?>
     </td>
</tr>
</table>
<?php

}


//
// determine SYSDBAs password from the sysdba_pw_textfield()
//
function get_sysdba_pw() {
    global $HTTP_POST_VARS;

    if ($GLOBALS['s_login']['user'] == 'SYSDBA') {

        return $GLOBALS['s_login']['password'];
    }

    if (isset($HTTP_POST_VARS['sysdba_pw'])  
    &&  strlen(trim($HTTP_POST_VARS['sysdba_pw'])) != 0) {
        $pw = trim($HTTP_POST_VARS['sysdba_pw']);
        if (strspn($pw, '*') != strlen($GLOBALS['s_sysdba_pw'])
        ||  strlen($GLOBALS['s_sysdba_pw']) == 0) {

            return $pw;
        }
    }

    return $GLOBALS['s_sysdba_pw'];
}


//
// return the html for a selectlist of the  FireBird/Interbase character sets.
//
function get_charset_select($name, $sel=NULL, $empty=FALSE, $tags=array()) {
    
    $charset_names = array();
    if (!empty($GLOBALS['s_charsets'])) {
        foreach($GLOBALS['s_charsets'] as $cs) {
            $charset_names[] = $cs['name'];
        }
    }
    else {
        $charset_names = get_preset_charsets(SERVER_FAMILY, SERVER_VERSION);
    }

    return get_selectlist($name, $charset_names, $sel, $empty, $tags);
}


//
// return the html for a selectlist of the available collation orders
//
function get_collation_select($name, $sel=NULL, $empty=FALSE, $tags=array()) {

    $collation_names = array();
    foreach ($GLOBALS['s_charsets'] as $charset) {
        foreach ($charset['collations'] as $collation) {
            $collation_names[] = $collation;
        }
    }
    sort($collation_names);

    return get_selectlist($name, $collation_names, $sel, $empty, $tags);
}


//
// return the html for a selectlist for the tables of the selected database
//
function get_table_selectlist($name, $restrictions=array(), $sel=NULL, $empty=FALSE, $tags=array()) {
    global $s_tables, $s_login;

    $rights = array('S' => 'select',
                    'I' => 'insert',
                    'U' => 'update',
                    'D' => 'delete',
                    'R' => 'reference');

    $tables = array();
    foreach($s_tables as $tablename => $tarr) {

        if (in_array('noviews', $restrictions)
        &&  $tarr['is_view'] == TRUE) {
            continue;
        }

        if (in_array('views', $restrictions)
        &&  $tarr['is_view'] == FALSE) {
            continue;
        }

        if ($s_login['user'] != 'SYSDBA') {
            if (in_array('owner', $restrictions)
            &&  $s_login['user'] != $tarr['owner']) {
                continue;
            }

            foreach ($rights as $code => $val) {
                if (in_array($val, $restrictions)
                &&  !in_array($code, $tarr['privileges'])
                &&  $s_login['user'] != $tarr['owner']) {
                    continue 2;
                }
            }
        }

        $tables[] = $tablename;
    }
    
    return get_selectlist($name, $tables, $sel, $empty, $tags);
}


//
// output the <option> list for a selectlist for the columns
// of the table $name
//
function build_column_options($table) {
    global $s_fields;

    echo "<option>\n";
    foreach($s_fields as $field) {
	if ($field['table'] == $table) {
	    echo '<option> '.$field['name']."\n";
	}
    }
}


//
// output the <option> list for a selectlist for the indices
// of the selected database
//
function build_index_options() {
    global $indices;

    echo "<option>\n";
    if (is_array($indices)) {
        $inames = array_keys($indices);
        foreach($inames as $name) {
            echo '<option> '.$name."\n";
        }
    }
}


//
// output the <option> list for a selectlist
// for the interbase users in isc4.gdb
// of the selected database
//
function build_user_options($with_sysdba=TRUE) {
    global $users;

    echo "<option>\n";
    if (is_array($users)) {
        $unames = array_keys($users);
        if ($with_sysdba == FALSE) {
            unset($unames[array_search('SYSDBA', $unames)]);
        }
        foreach($unames as $uname) {
            echo '<option> '.$uname."\n";
        }
    }
}


//
// return the html for a selectlist
//
function get_selectlist($name, $arr, $sel=NULL, $empty=FALSE, $tags=array()) {

    $tags_str = '';
    foreach ($tags as $tag => $val) {
        $tags_str .= sprintf(' %s="%s"', $tag, $val);
    }

    $html = '<select name="'.$name.'" size="1"'.$tags_str.">\n";
    if ($empty == TRUE) {
        $html .= "<option />\n";
    }
    foreach ($arr as $opt) {
        $html .= '<option value="'.$opt.'"';
        if ($opt == $sel) {
            $html .= ' selected';
        }
        $html .= ">$opt</option>\n";
    }
    $html .= "</select>\n";

    return $html;
}


//
// output a form textfield
//
function get_textfield($name, $size, $maxlength=NULL, $value=NULL) {

    $html = '<input type="text" name="'.$name.'" size="'.$size.'"';
    if ($maxlength !== NULL) {
        $html .= ' maxlength="'.$maxlength.'"';
    }
    if ($value !== NULL) {
        $html .= ' value="'.$value.'"';
    }
    $html .= ">\n";

    return $html;
}


//
// echo the html for hidden field
//
function hidden_field($name, $value) {

    echo '<input type="hidden" name="'.$name.'" value="'.$value."\">\n";
}


function get_panel_navigation($active, $index) {
    global $ptitle_strings;

    $panel_navigation = DATAPATH . (BG_TRANSPARENT == TRUE ? 'transparent/' : 'opaque/') . strtolower(ICON_SIZE) . '/panel_navigation.png';

    $coords = array('big'   => array('up'     => '0,0,15,11',
                                     'top'    => '16,0,32,11',
                                     'bottom' => '33,0,49,11',
                                     'down'   => '50,0,64,11'
                                     ),
                    'small' => array('up'     => '0,0,12,9',
                                     'top'    => '13,0,25,9',
                                     'bottom' => '26,0,38,9',
                                     'down'   => '39,0,52,9'
                                    )
                   );

    $up_url     = url_session("move_panel.php?a=$active&p=$index&d=up");
    $top_url    = url_session("move_panel.php?a=$active&p=$index&d=top");
    $bottom_url = url_session("move_panel.php?a=$active&p=$index&d=bottom");
    $down_url   = url_session("move_panel.php?a=$active&p=$index&d=down");

    return         '      <map name="Panel_Navi_'.$index."\">\n"
          .sprintf('        <area shape="rect" coords="%1$s" href="%2$s" alt="%3$s" title="%3$s">'."\n", $coords[ICON_SIZE]['up'],     $up_url,     $ptitle_strings['Up'])
          .sprintf('        <area shape="rect" coords="%1$s" href="%2$s" alt="%3$s" title="%3$s">'."\n", $coords[ICON_SIZE]['top'],    $top_url,    $ptitle_strings['Top'])
          .sprintf('        <area shape="rect" coords="%1$s" href="%2$s" alt="%3$s" title="%3$s">'."\n", $coords[ICON_SIZE]['bottom'], $bottom_url, $ptitle_strings['Bottom'])
          .sprintf('        <area shape="rect" coords="%1$s" href="%2$s" alt="%3$s" title="%3$s">'."\n", $coords[ICON_SIZE]['down'],   $down_url,   $ptitle_strings['Down'])
                  ."      </map>\n"
                  .'      <img src="'.$panel_navigation.'" align="right" usemap="#Panel_Navi_'.$index."\" border=\"0\" alt=\"\">\n";
}

function panel_navigation($active, $index) {

    echo get_panel_navigation($active, $index);
}

//
// return the html for a closed detail
//
function get_closed_detail($title, $url) {
    global $green_triangle_icon, $ptitle_strings;

return <<<EOT
      <a href="$url" class="dtitle"><img src="$green_triangle_icon" alt="${ptitle_strings['Open']}" title="${ptitle_strings['Open']}" border="0" hspace="7">$title</a>

EOT;
}

//
// build the url for a link to open/close a detail 
//
function fold_detail_url($type, $status, $name, $title) {
    global $s_use_jsrs;

    switch ($type) {
    case 'table':
        $div_prefix = 't';
        $fold_script = 'toggle_fold_table.php?t='.$name;
        break;
    case 'view':
        $div_prefix = 'v';
        $fold_script = 'toggle_fold_table.php?t='.$name;
        break;
    case 'trigger':
        $div_prefix = 'r';
        $fold_script = 'toggle_fold_trigger.php?n='.$name;
        break;
    case 'procedure':
        $div_prefix = 'p';
        $fold_script = 'toggle_fold_procedure.php?n='.$name;
        break;
    }

    if ($s_use_jsrs == TRUE) {

        if ($status == 'close') {
            $url = sprintf("javascript:requestDetail('%s', '%s', '%s')", $type, $name, $title);
        }
        else {
            $url = sprintf("javascript:closeDetail('%s', '%s_%s', '%s', '%s')", $type, $div_prefix, $name, $name, $title);
        }
    }

    else {
        $url = url_session($fold_script);
    }

    return $url;
}


//
// deliver the html for an opened table on the tb_show panel
//
function get_opened_table($name, $title, $url) {
    global $s_fields, $red_triangle_icon, $tb_strings, $ptitle_strings;

    $html = <<<EOT
        <nobr>
          <a href="$url" class="dtitle"><img src="$red_triangle_icon" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" border="0" hspace="7">$title</a>
        </nobr>
        <table>
          <tr>
            <td width="26">
            </td>
            <td>
              <table border cellpadding="0" cellspacing="0">

EOT;
    $cols = array('Name', 'Type', 'Charset', 'Collate',
                  'NotNull', 'Unique', 'Computed', 'Default', 'Check', 'Primary', 'Foreign');
    $html .= "                <tr align=\"left\">\n";
    foreach ($cols as $idx) {
        $html .= '                  <th class="detail"><nobr>'.$tb_strings[$idx]."</nobr></th>\n";
    }
    $html .= "                </tr>\n";

    foreach($s_fields as $field) {
        if ($field['table'] <> $name) {
            continue;
        }
 
        $type_str = isset($field['domain']) ? $field['type'] : get_type_string($field);
        $type_str .=isset($field['lower_bound']) ? '['.$field['lower_bound'].':'.$field['upper_bound'].']' : ''; 
        $char_str = isset($field['charset']) ? $field['charset']  : '&nbsp;';
        $coll_str = isset($field['collate']) ? $field['collate']  : '&nbsp;';
        $nn_str   = isset($field['notnull']) ? $tb_strings['Yes'] : '&nbsp;';
        $uniq_str = isset($field['unique'])  ? $tb_strings['Yes'] : '&nbsp;';
        $comp_str = isset($field['comp'])    ? $tb_strings['Yes'] : '&nbsp;';
        $def_str  = isset($field['default']) ? $tb_strings['Yes'] : '&nbsp;';
        $check_str= isset($field['check'])   ? $tb_strings['Yes'] : '&nbsp;';
        $prim_str = isset($field['primary']) ? $tb_strings['Yes'] : '&nbsp;';
        $fk_str   = isset($field['foreign']) ? $tb_strings['Yes'] : '&nbsp;';

        $html .= "                <tr>
                  <td class=\"detail\">${field['name']}</td>
	          <td class=\"detail\">$type_str</td>
    	          <td class=\"detail\">$char_str</td>
                  <td align=\"right\" class=\"detail\">$coll_str</td>
                  <td align=\"center\" class=\"detail\">$nn_str</td>
                  <td align=\"center\" class=\"detail\">$uniq_str</td>
                  <td align=\"center\" class=\"detail\">$comp_str</td>
                  <td align=\"center\" class=\"detail\">$def_str</td>
                  <td align=\"center\" class=\"detail\">$check_str</td>
                  <td align=\"center\" class=\"detail\">$prim_str</td>
                  <td align=\"center\" class=\"detail\">$fk_str</td>
                </tr>\n";
    }

    $html .= "              </table>\n"
            ."            </td>\n"
            ."          </tr>\n"
            ."        </table>\n"
            ."      </nobr>\n";

    return $html;
}


//
// return the html for a closed panel
//
function get_closed_panel($title, $active, $nr, $icon) {
    global $ptitle_strings;

    $fold_url = url_session('toggle_fold_panel.php?a='.$active.'&p='.$nr.'&d=open');

    return "<table class=\"panel\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">\n"
           ."  <tr>\n"
           ."    <td width=\"25\" align=\"center\" cellspacing=\"5\">\n"
           .'      '.sprintf('<a href="%1$s"><img src="%2$s" alt="%3$s" title="%3$s" border="0"></a>'."\n", $fold_url, $icon, $ptitle_strings['Open'])
           .'    <td width="100%"><a class="ptitle" href="'.$fold_url.'">'.$title."</b></td>\n"
           ."    <td width=\"65\">\n"
           .'      '.get_panel_navigation($active, $nr)."\n"
           ."    </td>\n"
           ."  </tr>\n"
           ."</table>\n";
}

?>
