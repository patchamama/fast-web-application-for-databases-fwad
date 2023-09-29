<?php
// File           show_menu.inc.php / ibWebAdmin
// Purpose        intiate and display the main menu
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        Thu Aug 31 14:12:47 CEST 2000
//
// $Id: show_menu.inc.php,v 1.15 2004/03/07 16:12:55 lbrueckner Exp $

// Variables:     $active    text ($menu->items[][text]) of the active menu item 


$menuentries = array('Datos generales'    => url_session('database.php'),
                     'Donantes'      => url_session('tables.php'),
                     'Localidades' => url_session('accessories.php'));


// use TABMENU_STYLE == 'HTML' as default setting
if (!defined('TABMENU_STYLE')  
||  !in_array(TABMENU_STYLE, array('IMAGE', 'BUILD'))
||  (TABMENU_STYLE == 'BUILD'  &&  $s_tab_menu == FALSE)) {

?>

<table width="100%" bgcolor="<?php echo $s_cust['color']['background']; ?>" cellpadding="2" cellspacing="0">
   <tr>
      <td width="20" style="border-right:2px solid <?php echo $s_cust['color']['menuborder']; ?>; border-bottom:2px solid <?php echo $s_cust['color']['menuborder']; ?>">
         &nbsp;&nbsp;
      </td>
<?php

    foreach ($menuentries as $item => $script) {
        if ($active == $item) {
?>
      <td nowrap style="border-right:2px solid <?php echo $s_cust['color']['menuborder']; ?>; border-top:2px solid <?php echo $s_cust['color']['menuborder']; ?>">
         <a href="<?php echo $script; ?>" style="color:black; font-weight:bold;">
             &nbsp;&nbsp;<?php echo $menu_strings[$item]; ?>&nbsp;&nbsp;
         </a>
      </td>
<?php
        }
        else {
?>
      <td nowrap style="border-right:2px solid <?php echo $s_cust['color']['menuborder']; ?>; border-top:2px solid <?php echo $s_cust['color']['menuborder']; ?>; border-bottom:2px solid <?php echo $s_cust['color']['menuborder']; ?>;" bgcolor="<?php echo $s_cust['color']['panel']; ?>">
         <a href="<?php echo $script; ?>" style="color:black; font-weight:bold;">
             &nbsp;&nbsp;<?php echo $menu_strings[$item]; ?>&nbsp;&nbsp;
         </a>
      </td>
<?php

        }
    }
?>
      <td height="20" width="100%" style="border-bottom:2px solid <?php echo $s_cust['color']['menuborder']; ?>">
         &nbsp;
      </td>
   </tr>
</table>

<?php

}

// use one of the precalculated menus from the ./data directory
elseif (TABMENU_STYLE == 'IMAGE') {
    $png = DATAPATH.'menu_'.$s_cust['language'].'/'.MENU_WIDTH.'/'.$active.'.png';
?>
<table>
  <tr>
    <td>
      <map name="Menu">
<?php

    foreach ($menuentries as $item => $script) {
        echo '         <area shape="rect" coords="'.$menu_coords[$item].'" href="'.$script."\">\n";
    }
?>
      </map>
      <img src="<?php echo $png; ?>" usemap="#Menu" border=0>
    </td>
    <td width="100%">
      &nbsp;
    </td>
  </tr>
</table>
<?php

}

// TABMENU_STYLE == 'BUILD', create the menu images on the fly 
else {

    include_once('./inc/TabMenu.class.php');
        
   // create the subdir in the ./DATA directory if it is not already there
    if (!is_dir(DATAPATH.'menu_'.$s_cust['language'].'/'.MENU_WIDTH)) {

        if (!is_dir(DATAPATH.'menu_'.$s_cust['language'])) {

            if (!mkdir(DATAPATH.'menu_'.$s_cust['language'], 0777)) {
                die ('Error: can not create directory <b>'.DATAPATH.'menu_'.$s_cust['language']."</b><br>\n"
                     .'Please check the permissions and/or your settings for MENU_WIDTH and TAB_MENU in ./inc/configuration.inc.php.');
            }
        }

        if (!mkdir(DATAPATH.'menu_'.$s_cust['language'].'/'.MENU_WIDTH, 0777)) {
            die ('Error: can not create directory <b>'.DATAPATH.'menu_'.$s_cust['language'].'/'.MENU_WIDTH."</b><br>\n"
                 .'Please check the permissions and/or your settings for MENU_WIDTH and TAB_MENU in ./inc/configuration.inc.php.');
        }
    }

    $menu = new TabMenu;
    $menu->active = $menu_strings[$active];
    $menu->font = TTF_FONT;
    $menu->fontsize = TTF_SIZE;
    $menu->bg1 = 0xf6f7c0;
//    $menu->fg = 0x005000;
    $menu->s2 = 0x008000;
    $menu->s1 = 0xcaea62;
    $menu->forcewidth = MENU_WIDTH;

    foreach ($menuentries as $item => $script) {
        $menu->addItem($menu_strings[$item], $script);
    }

    $png = DATAPATH.'menu_'.$s_cust['language'].'/'.MENU_WIDTH.'/'.$active.'.png';
    $menu->pngname = $png;
    	
    $menu->buildPNG();
    echo $menu->buildMenuTable();

}

?>