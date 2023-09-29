<?php
// File           TabMenu.class.php / ibWebAdmin
// Purpose        generate and display tabfolder menus 
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/04 09:59:20 lb>
// 
// $Id: TabMenu.class.php,v 1.1 2004/03/07 16:12:16 lbrueckner Exp $

/**
* Implements a tabfolder-menu. The rendering of the graphic
* representations for the menu is done on the fly through the
* gd-library functions. Output format is png for the graphics
* and html for the belonging image map.
*
* @author   Lutz Brueckner <lb@knuut.de>
* @version  1.0
* @package  TabMenu
* 
*/
class TabMenu {

    /**
    * String on the tabfolder that becomes the active one..
    * This must be one of $this->items[]["text"].
    *
    * @var     string   $active
    * @see     items
    * @access  public
    */
    var $active = '';

    /**
    * Fontname for the ttf-font to use on the tabfolders.
    * May including path if nessesary.
    *
    * @var     string   $font
    * @see     fontsize
    * @access  public
    */
    var $font = '';

    /**
    * Size of the ttf-font
    *
    * @var     integer  $fontsize
    * @see     font
    * @access  public
    */
    var $fontsize = 18;

    /**
    * Name with path for the png-file
    *
    * @var     string  $pngname
    * @access  public
    */
    var $pngname = 'tabmenu.png';       // name for the png

    /**
    * Horizontal space in pixel before and after the menutexts
    *
    * @var     integer  $xpadding
    * @access  public
    */
    var $xpadding = 5;

    /**
    * Vertical space in pixel above and under the menutexts
    *
    * @var     integer  $ypadding
    * @access  public
    */
    var $ypadding = 10;

    /**
    * Overall width of the generated png-graphic
    *
    * @var     integer  $forcewidth
    * @access  public
    */
    var $forcewidth= 600;

    /**
    * Background color of the active item and of the background
    * outside of the tabfolders
    *
    * @var     integer  $bg1
    * @access  public
    */
    var $bg1 = 0xd8d8d8;

    /**
    * Background color of the passive menuitems
    *
    * @var     integer  $bg2
    * @access  public
    */
    var $bg2 = 0xcaea62;

    /**
    * Foreground color, used for the menutext
    *
    * @var     integer  $fg
    * @access  public
    */
    var $fg = 0x000000;

    /**
    * Shade color, used for the line below of the active item
    *
    * @var     integer  $s1
    * @access  public
    */
    var $s1 = 0xf4f4f4;

    /**
    * Shade color, used for the outlines of all items
    *
    * @var     integer  $s2
    * @access  public
    */
    var $s2 = 0x686868;

    /**
    * X-offset where the first item starts
    *
    * @var     integer  $xoffset
    * @access  public
    */
    var $xoffset = 8;

    /**
    * Array that is holding the menu-items
    *   $items['text']     : string on this item               
    *   $items['link']     : url to call, if it is selected by the user
    *   $items['ixsize']   : horizontal size                   
    *   $items['ixstart']  : x coordinate where the item starts
    *
    * @var     array    $items
    * @access  private
    */
    var $items = array();

    /**
    * Count of items in the menu
    *
    * @var     integer  $count
    * @access  private
    */
    var $count = 0;

    /**
    * Handle for the image, needed by the gd-functions
    *
    * @var     integer  $img
    * @access  private
    */
    var $img = 0;

    /**
    * Horizontal size of the image
    *
    * @var     integer  $xsize
    * @see     ysize
    * @access  private
    */
    var $xsize = 0;

    /**
    * Vertical size of the image
    *
    * @var     integer  $ysize
    * @see     xsize
    * @access  private
    */
    var $ysize = 0;

    /**
    * Handle for the color bg1, needed by the gd-functions
    *
    * @var     integer  $bg1col
    * @see     bg1
    * @access  private
    */
    var $bg1col = 0;

    /**
    * Handle for the color bg2, needed by the gd-functions
    *
    * @var     integer  $bg2col
    * @see     bg2
    * @access  private
    */
    var $bg2col = 0;

    /**
    * Handle for the color fg, needed by the gd-functions
    *
    * @var     integer  $fgcol
    * @see     fg
    * @access  private
    */
    var $fgcol = 0;

    /**
    * Handle for the color s1, needed by the gd-functions
    *
    * @var     integer  $a1col
    * @see     s1
    * @access  private
    */
    var $s1col = 0;

    /**
    * Handle for the color s2, needed by the gd-functions
    *
    * @var     integer  $s2col
    * @see     s2
    * @access  private
    */
    var $s2col = 0;


    /**
     * Add another item to the menu
     *
     * @param   string   text that shall appear on the tabfolder-item
     * @param   string   url to call if the user select this item
     * @return  bool     $ok
     * @see     calc_item_size
     * @see     items, count, xoffset
     * @access  public
     */                                                                                
    function addItem ($itemtext, $itemlink) {
        $x = ($this->count == 0) ? 0 + $this->xoffset
                                 : $this->items[$this->count-1]['ixstart']
                                   + $this->items[$this->count-1]['ixsize'] +1;

        $this->items[] = array('text'    => $itemtext,
                               'link'    => $itemlink,
                               'ixsize'  => $this->calc_item_size($itemtext),
                               'ixstart' => $x);
        $this->count += 1;
        return true;
    }


    /**
     * Create the png-File containing the menu
     *
     * @return  bool     $ok
     * @see     calc_png_size, color_allocate, draw_item_box, write_item, draw_border
     * @see     img, pngname, xsize, ysize, forcewidth, count
     * @see     bg1col, bg2col, fgcol, s1col, s2col, bg1, bg2, fg, s1, s2
     * @access  public
     */                                                                                
    function buildPNG() {
        // calculate $xsize and $ysize
        $this->calc_png_size();
        $this->img = ImageCreate($this->xsize, $this->ysize);

        $this->bg1col = $this->color_allocate($this->bg1);
        $this->bg2col = $this->color_allocate($this->bg2);
        $this->fgcol = $this->color_allocate($this->fg);
        $this->s1col = $this->color_allocate($this->s1);
        $this->s2col = $this->color_allocate($this->s2);

        ImageFill($this->img, 1, 1, $this->bg1col);

        for ($i=0; $i<$this->count; $i++){
            $this->draw_item_box($i);
            $this->write_item($i);
        }

	if ($this->forcewidth == $this->xsize)
	  $this->draw_border();

        ImagePNG($this->img, $this->pngname);
        ImageDestroy($this->img);
        return true;
    }


    /**
     * generate html source for the image map representing the
     * menu on the png-graphic
     *
     * @return  string   $map
     * @see     items, count, ysize, pngname
     * @access  public
     */                                                                                
    function buildMap() {
        $map = "<map name=\"TabMenu\">\n";
        for ($i=0; $i<$this->count; $i++) {
            $x1 = $this->items[$i]['ixstart'];
            $x2 = $this->items[$i]['ixstart'] + $this->items[$i]['ixsize'] - 1;
            $y2 = $this->ysize;
            $href = $this->items[$i]['link'];
            $map .= "<area shape=\"rect\" coords=\"$x1,0,$x2,$y2\" href=\"$href\">\n";
        }
        $map .= "</map>\n";
        $map .= '<img src="'.$this->pngname."\" usemap=\"#TabMenu\" border=0>\n";
        return $map;
    }


    /**
     * generate html source for a pagewidth fitting table
     * containing an image map with our png file
     *
     * @return  string   $menutable
     * @see     buildMap
     * @access  public
     */                                                                                
    function buildMenuTable() {
        $menutable = "<table>\n<tr>\n<td>";
        $menutable .= $this->buildMap();
        $menutable .= "</td>\n";
	$menutable .= "<td width=\"100%\">&nbsp;</td>\n</tr>\n</table>\n";
        return $menutable;
    }


    /**
     * Returns a color index for the submitted color
     *
     * @param   integer   $color
     * @return  integer   $colorindex
     * @access  private
     */                                                                                
    function color_allocate($color){
        $red   = ($color & 0xff0000) / 0x10000;
        $green = ($color & 0x00ff00) / 0x100;
        $blue  = $color & 0x0000ff;
        $colorindex = ImageColorAllocate($this->img, $red, $green, $blue);
        return $colorindex;
    }


    /**
     * setup xsize and ysize with the dimensions
     * of the image that we want to generate
     *
     * @see     count, xsize, ysize, forcewidth, xpadding, ypadding, font, fontsize
     * @access  private
     */                                                                                
    function calc_png_size() {
        $str = '';
        for ($i=0; $i<$this->count; $i++) 
            $str .= $this->items[$i]['text'];
        $arr = ImageTTFBBox($this->fontsize, 0, $this->font, $str);
        $x = $arr[2] - $arr[0] + 2 * $this->count * $this->xpadding;
        $this->xsize = ($x > $this->forcewidth) ? $x : $this->forcewidth;
        $this->ysize = $arr[1] - $arr[7] + 2 * $this->ypadding;
    }


    /**
     * calculate the width of an item with the content of the given string
     *
     * @return  $width
     * @see     font, fontsize, xpadding
     * @access  private
     */                                                                                
    function calc_item_size($text) {
        $arr = ImageTTFBBox($this->fontsize, 0, $this->font, $text);
        $width = $arr[2] - $arr[0] + 2 * $this->xpadding;
        return ($width);
    }


    /**
     * Write the text for the item into the image
     *
     * @param   $idx
     * @see     items, active, img, font, fontsize, fgcol, ysize, ypadding, xpadding
     * @access  private
     */
    function write_item($idx){
        $xpos = $this->items[$idx]['ixstart']+$this->xpadding;
        if ($this->items[$idx]['text'] == $this->active)
            $ypos = $this->ysize-$this->ypadding-3;
        else
            $ypos = $this->ysize-$this->ypadding-2;
        ImageTTFText($this->img, $this->fontsize, 0, $xpos, $ypos, $this->fgcol, $this->font, $this->items[$idx]['text']);
    }


    /**
     * Draws a border around a menu-item
     *
     * @param   $idx
     * @see     draw_active_item_box, draw_passive_item_box
     * @see     items, active
     * @access  private
     */
    function draw_item_box($idx) {
        if ($this->items[$idx]['text'] == $this->active)
            $this->draw_active_item_box($idx);
        else
            $this->draw_passive_item_box($idx);
    }


    /**
     * Draws a border around the active menu-item
     *
     * @param   $idx
     * @see     items, img, ysize, s1col, s2col
     * @access  private
     */
    function draw_active_item_box($idx) {
        $x1 = $this->items[$idx]['ixstart'];
        $y1 = 0;
        $x2 = $x1 + $this->items[$idx]['ixsize'];
        $y2 = $this->ysize-1;
        ImageFilledRectangle($this->img, $x1 +7, $y1, $x2 -7, $y1+1, $this->s2col);    // above
        ImageFilledRectangle($this->img, $x1, $y1 +12, $x1+1, $y2, $this->s2col);      // left
        ImageLine($this->img, $x2-1, $y1+1 +12, $x2-1, $y2, $this->s2col);             // right
        ImageLine($this->img, $x2, $y1 +12, $x2, $y2, $this->s2col);                   //   "
        ImageFilledRectangle($this->img, $x1+10, $y2-1, $x2-10, $y2, $this->s1col);    // below

        imagearc ($this->img, $x1+7, $y1+14, 14, 24, 180, 270, $this->s2col);          // left bend
        imagearc ($this->img, $x1+6, $y1+13, 14, 24, 183, 270, $this->s2col);

        imagearc ($this->img, $x2-7, $y1+14, 14, 24, 270, 0, $this->s2col);            // right bend
        imagearc ($this->img, $x2-6, $y1+13, 14, 24, 270, 356, $this->s2col);

    }


    /**
     * Draws a border around a passive menu-item
     *
     * @param   $idx
     * @see     items, img, ysize, s2col, bg2col
     * @access  private
     */
    function draw_passive_item_box($idx) {
        $x1 = $this->items[$idx]['ixstart'];
        $y1 = 0;
        $x2 = $x1 + $this->items[$idx]['ixsize'];
        $y2 = $this->ysize-1;

        ImageFilledRectangle($this->img, $x1 +7, $y1+2, $x2 -7, $y1+3, $this->s2col);   // above
        ImageFilledRectangle($this->img, $x1, $y2-1, $x2, $y2, $this->s2col);           // beow
        ImageFilledRectangle($this->img, $x1, $y1+2 +12, $x1+1, $y2, $this->s2col);     // left
        ImageFilledRectangle($this->img, $x2-1, $y1+2 +12, $x2, $y2-2, $this->s2col);   // right
        
        imagearc ($this->img, $x1+7, $y1+14, 14, 24, 180, 270, $this->s2col);           // left bow
        imagearc ($this->img, $x1+6, $y1+13, 14, 24, 183, 265, $this->s2col);

        imagearc ($this->img, $x2-7, $y1+14, 14, 24, 270, 0, $this->s2col);             // right bow
        imagearc ($this->img, $x2-6, $y1+13, 14, 24, 275, 356, $this->s2col);

        ImageFill($this->img, $x1+5, $y1+5, $this->bg2col);
    }


    /**
     * Draws the edge from the rightmost item to the right margin of the image
     *
     * @see     items, img, xoffset, ysize, forcewidth, s2col
     * @access  private
     */
    function draw_border() {
  	$idx = $this->count - 1;
  	$x1 = $this->items[$idx]['ixstart'] + $this->items[$idx]['ixsize'];
  	$x2 = $this->forcewidth;
  	$y1 = $this->ysize - 2;
  	$y2 = $this->ysize - 1;
  	ImageFilledRectangle($this->img, $x1, $y1, $x2, $y2, $this->s2col);
  	ImageFilledRectangle($this->img, 0, $y1, $this->xoffset, $y2, $this->s2col);
    }

}
?>
