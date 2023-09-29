<?php
// ************************************************************************
// |       -- P R O F I L E  Generator ProfileAble.php --                 |
// ************************************************************************
// | 2 ways to get it running:
// | A) Used this profile code instead of the original class
// |
// | B) Class hijacking
// |     a) Renaming the original Class file from Foo to Foo_
// |     b) Name this class to Foo and extend it from Foo_
// |   
// |   This Class is then between the caller and the original Class and
// |   captures every call, thus collecting info.
// |           CALLER <--> Profiler <--> original Class
// |   NOTE: The performance will drop to 30% of original because of the 
// |         overhead.
// |
// | How to use:
// |   Run your test as normal and then call profileToHtml() to see 
// |   the profile info.
// +----------------------------------------------------------------------+
// | Main Authors:                                                        |
// |   Sam Blum <bs_php@infeer.com>                                       |
// +----------------------------------------------------------------------+
// | Requires PHP version  4.0.5 and up                                   |
// +----------------------------------------------------------------------+

require_once(dirname(__FILE__).'/XPath.class.php');

class XPathBase_Prof extends XPathBase {

    /******************************************************************************************************
    //-----------------------------------------------------------------------------------------
    //                          -- P R O F I L E  Methods BEGIN --                             
    //-----------------------------------------------------------------------------------------
    */
    var $callStack = array();
      
    /**
     * Profile begin call
     */
    function _profBegin($sonFuncName) {
      static $entryTmpl = array (
               'start'  => array(),
               'recursiveCount' => 0,
               'totTime' => 0,
               'callCount' => 0
             );
      $now  = explode(' ', microtime());
      
      if (empty($this->callStack)) {
        $fatherFuncName = '';
      } else {
        $fatherFuncName = $this->callStack[sizeOf($this->callStack)-1];
        $fatherEntry = &$this->profile[$fatherFuncName];
      }
      $this->callStack[] = $sonFuncName;
      
      if (!isSet($this->profile[$sonFuncName])) {
        $this->profile[$sonFuncName] = $entryTmpl;
      }
      
      $sonEntry = &$this->profile[$sonFuncName];
      $sonEntry['callCount']++;
      // if we call the t's the same function let the time run, otherwise sum up
      if ($fatherFuncName == $sonFuncName) {
        $sonEntry['recursiveCount']++;
      }
      if (!empty($fatherFuncName)) {
        $last = $fatherEntry['start'];
        $fatherEntry['totTime'] += round( (($now[1] - $last[1]) + ($now[0] - $last[0]))*10000 );
        $fatherEntry['start'] = 0;
      }
      $sonEntry['start'] = explode(' ', microtime());
    }
    
    /**
     * Profile end call
     */
    function _profEnd($sonFuncName) {
      $now  = explode(' ', microtime());
      
      array_pop($this->callStack);
      if (empty($this->callStack)) {
        $fatherFuncName = '';
      } else {
        $fatherFuncName = $this->callStack[sizeOf($this->callStack)-1];
        $fatherEntry = &$this->profile[$fatherFuncName];
      }
      $sonEntry = &$this->profile[$sonFuncName];
      if (empty($sonEntry)) {
        echo "ERROR in profEnd(): '$funcNam' not in list. Seams it was never started ;o)";
      }
      
      $last = $sonEntry['start'];
      $sonEntry['totTime'] += round( (($now[1] - $last[1]) + ($now[0] - $last[0]))*10000 );
      $sonEntry['start'] = 0;
      if (!empty($fatherEntry)) $fatherEntry['start'] = explode(' ', microtime());
    }
    
    /**
     * Show profile gathered so far as HTML table
     */
    function profileToHtml() {
      $sortArr = array();
      if (empty($this->profile)) return '';
      reset($this->profile);
      while (list($funcName) = each($this->profile)) {
        $sortArrKey[] = $this->profile[$funcName]['totTime'];
        $sortArrVal[] = $funcName;
      }
      //echo '<pre>';var_dump($sortArrVal);echo '</pre>';
      array_multisort ($sortArrKey, SORT_DESC, $sortArrVal );
      //echo '<pre>';var_dump($sortArrVal);echo '</pre>';
      
      $totTime = 0;
      $size = sizeOf($sortArrVal);
      for ($i=0; $i<$size; $i++) {
        $funcName = &$sortArrVal[$i];
        $totTime += $this->profile[$funcName]['totTime'];
      }
      $out = '<table border="1">';
      $out .='<tr align="center" bgcolor="#bcd6f1"><th>Function</th><th> % </th><th>Total [ms]</th><th># Call</th><th>[ms] per Call</th><th># Recursive</th></tr>';
      for ($i=0; $i<$size; $i++) {
        $funcName = &$sortArrVal[$i];
        $row = &$this->profile[$funcName];
        $procent = round($row['totTime']*100/$totTime);
        if ($procent>20)
          $bgc = '#ff8080';
        elseif ($procent>15) 
          $bgc = '#ff9999';
        elseif ($procent>10) 
          $bgc = '#ffcccc';
        elseif ($procent>5) 
          $bgc = '#ffffcc';
        else 
          $bgc = '#66ff99';
          
        $out .="<tr align='center' bgcolor='{$bgc}'>";
        $out .='<td>'. $funcName  .'</td><td>'.  $procent .'% '.'</td><td>'.  $row['totTime']/10 .'</td><td>'. $row['callCount'] .'</td><td>'. round($row['totTime']/10/$row['callCount'],2) .'</td><td>'. $row['recursiveCount'].'</td>';
        $out .='</tr>';
      }
      $out .= '</table> Total Time [' . $totTime/10 .'ms]' ;
      return $out;
    }
    /*
    //-----------------------------------------------------------------------------------------
    //                          -- P R O F I L E  Methods END --                               
    //-----------------------------------------------------------------------------------------
    ******************************************************************************************************/

  # XPathBase_Prof from line 196
  function XPathBase_Prof() {
    $this->_profBegin('XPathBase');
    $ret = parent::XPathBase();
    $this->_profEnd('XPathBase');
    return $ret;
  }

  # reset from line 235
  function reset() {
    $this->_profBegin('reset');
    $ret = parent::reset();
    $this->_profEnd('reset');
    return $ret;
  }

  # _bracketsCheck from line 249
  function _bracketsCheck($term) {
    $this->_profBegin('_bracketsCheck');
    $ret = parent::_bracketsCheck($term);
    $this->_profEnd('_bracketsCheck');
    return $ret;
  }

  # _searchString from line 306
  function _searchString($term, $expression) {
    $this->_profBegin('_searchString');
    $ret = parent::_searchString($term, $expression);
    $this->_profEnd('_searchString');
    return $ret;
  }

  # _bracketExplode from line 338
  function _bracketExplode($separator, $term) {
    $this->_profBegin('_bracketExplode');
    $ret = parent::_bracketExplode($separator, $term);
    $this->_profEnd('_bracketExplode');
    return $ret;
  }

  # _getEndGroups from line 416
  function _getEndGroups($string, $open='[', $close=']') {
    $this->_profBegin('_getEndGroups');
    $ret = parent::_getEndGroups($string, $open, $close);
    $this->_profEnd('_getEndGroups');
    return $ret;
  }

  # _prestr from line 502
  function _prestr(&$string, $delimiter, $offset=0) {
    $this->_profBegin('_prestr');
    $ret = parent::_prestr(&$string, $delimiter, $offset);
    $this->_profEnd('_prestr');
    return $ret;
  }

  # _afterstr from line 520
  function _afterstr($string, $delimiter, $offset=0) {
    $this->_profBegin('_afterstr');
    $ret = parent::_afterstr($string, $delimiter, $offset);
    $this->_profEnd('_afterstr');
    return $ret;
  }

  # setVerbose from line 539
  function setVerbose($levelOfVerbosity = 1) {
    $this->_profBegin('setVerbose');
    $ret = parent::setVerbose($levelOfVerbosity);
    $this->_profEnd('setVerbose');
    return $ret;
  }

  # getLastError from line 558
  function getLastError() {
    $this->_profBegin('getLastError');
    $ret = parent::getLastError();
    $this->_profEnd('getLastError');
    return $ret;
  }

  # _setLastError from line 577
  function _setLastError($message='', $line='-', $file='-') {
    $this->_profBegin('_setLastError');
    $ret = parent::_setLastError($message, $line, $file);
    $this->_profEnd('_setLastError');
    return $ret;
  }

  # _displayError from line 594
  function _displayError($message, $lineNumber='-', $file='-', $terminate=TRUE) {
    $this->_profBegin('_displayError');
    $ret = parent::_displayError($message, $lineNumber, $file, $terminate);
    $this->_profEnd('_displayError');
    return $ret;
  }

  # _displayMessage from line 611
  function _displayMessage($message, $lineNumber='-', $file='-') {
    $this->_profBegin('_displayMessage');
    $ret = parent::_displayMessage($message, $lineNumber, $file);
    $this->_profEnd('_displayMessage');
    return $ret;
  }

  # _beginDebugFunction from line 630
  function _beginDebugFunction($functionName) {
    $this->_profBegin('_beginDebugFunction');
    $ret = parent::_beginDebugFunction($functionName);
    $this->_profEnd('_beginDebugFunction');
    return $ret;
  }

  # _closeDebugFunction from line 656
  function _closeDebugFunction($aStartTime, $returnValue = "") {
    $this->_profBegin('_closeDebugFunction');
    $ret = parent::_closeDebugFunction($aStartTime, $returnValue);
    $this->_profEnd('_closeDebugFunction');
    return $ret;
  }

  # _profileFunction from line 681
  function _profileFunction($aStartTime, $alertString) {
    $this->_profBegin('_profileFunction');
    $ret = parent::_profileFunction($aStartTime, $alertString);
    $this->_profEnd('_profileFunction');
    return $ret;
  }

  # _printContext from line 694
  function _printContext($context) {
    $this->_profBegin('_printContext');
    $ret = parent::_printContext($context);
    $this->_profEnd('_printContext');
    return $ret;
  }

  # _treeDump from line 706
  function _treeDump($node, $indent = '') {
    $this->_profBegin('_treeDump');
    $ret = parent::_treeDump($node, $indent);
    $this->_profEnd('_treeDump');
    return $ret;
  }

} // END Class 
class XPathEngine_Prof extends XPathEngine {

    /******************************************************************************************************
    //-----------------------------------------------------------------------------------------
    //                          -- P R O F I L E  Methods BEGIN --                             
    //-----------------------------------------------------------------------------------------
    */
    var $callStack = array();
      
    /**
     * Profile begin call
     */
    function _profBegin($sonFuncName) {
      static $entryTmpl = array (
               'start'  => array(),
               'recursiveCount' => 0,
               'totTime' => 0,
               'callCount' => 0
             );
      $now  = explode(' ', microtime());
      
      if (empty($this->callStack)) {
        $fatherFuncName = '';
      } else {
        $fatherFuncName = $this->callStack[sizeOf($this->callStack)-1];
        $fatherEntry = &$this->profile[$fatherFuncName];
      }
      $this->callStack[] = $sonFuncName;
      
      if (!isSet($this->profile[$sonFuncName])) {
        $this->profile[$sonFuncName] = $entryTmpl;
      }
      
      $sonEntry = &$this->profile[$sonFuncName];
      $sonEntry['callCount']++;
      // if we call the t's the same function let the time run, otherwise sum up
      if ($fatherFuncName == $sonFuncName) {
        $sonEntry['recursiveCount']++;
      }
      if (!empty($fatherFuncName)) {
        $last = $fatherEntry['start'];
        $fatherEntry['totTime'] += round( (($now[1] - $last[1]) + ($now[0] - $last[0]))*10000 );
        $fatherEntry['start'] = 0;
      }
      $sonEntry['start'] = explode(' ', microtime());
    }
    
    /**
     * Profile end call
     */
    function _profEnd($sonFuncName) {
      $now  = explode(' ', microtime());
      
      array_pop($this->callStack);
      if (empty($this->callStack)) {
        $fatherFuncName = '';
      } else {
        $fatherFuncName = $this->callStack[sizeOf($this->callStack)-1];
        $fatherEntry = &$this->profile[$fatherFuncName];
      }
      $sonEntry = &$this->profile[$sonFuncName];
      if (empty($sonEntry)) {
        echo "ERROR in profEnd(): '$funcNam' not in list. Seams it was never started ;o)";
      }
      
      $last = $sonEntry['start'];
      $sonEntry['totTime'] += round( (($now[1] - $last[1]) + ($now[0] - $last[0]))*10000 );
      $sonEntry['start'] = 0;
      if (!empty($fatherEntry)) $fatherEntry['start'] = explode(' ', microtime());
    }
    
    /**
     * Show profile gathered so far as HTML table
     */
    function profileToHtml() {
      $sortArr = array();
      if (empty($this->profile)) return '';
      reset($this->profile);
      while (list($funcName) = each($this->profile)) {
        $sortArrKey[] = $this->profile[$funcName]['totTime'];
        $sortArrVal[] = $funcName;
      }
      //echo '<pre>';var_dump($sortArrVal);echo '</pre>';
      array_multisort ($sortArrKey, SORT_DESC, $sortArrVal );
      //echo '<pre>';var_dump($sortArrVal);echo '</pre>';
      
      $totTime = 0;
      $size = sizeOf($sortArrVal);
      for ($i=0; $i<$size; $i++) {
        $funcName = &$sortArrVal[$i];
        $totTime += $this->profile[$funcName]['totTime'];
      }
      $out = '<table border="1">';
      $out .='<tr align="center" bgcolor="#bcd6f1"><th>Function</th><th> % </th><th>Total [ms]</th><th># Call</th><th>[ms] per Call</th><th># Recursive</th></tr>';
      for ($i=0; $i<$size; $i++) {
        $funcName = &$sortArrVal[$i];
        $row = &$this->profile[$funcName];
        $procent = round($row['totTime']*100/$totTime);
        if ($procent>20)
          $bgc = '#ff8080';
        elseif ($procent>15) 
          $bgc = '#ff9999';
        elseif ($procent>10) 
          $bgc = '#ffcccc';
        elseif ($procent>5) 
          $bgc = '#ffffcc';
        else 
          $bgc = '#66ff99';
          
        $out .="<tr align='center' bgcolor='{$bgc}'>";
        $out .='<td>'. $funcName  .'</td><td>'.  $procent .'% '.'</td><td>'.  $row['totTime']/10 .'</td><td>'. $row['callCount'] .'</td><td>'. round($row['totTime']/10/$row['callCount'],2) .'</td><td>'. $row['recursiveCount'].'</td>';
        $out .='</tr>';
      }
      $out .= '</table> Total Time [' . $totTime/10 .'ms]' ;
      return $out;
    }
    /*
    //-----------------------------------------------------------------------------------------
    //                          -- P R O F I L E  Methods END --                               
    //-----------------------------------------------------------------------------------------
    ******************************************************************************************************/

  # XPathEngine_Prof from line 842
  function XPathEngine_Prof($userXmlOptions=array()) {
    $this->_profBegin('XPathEngine');
    $ret = parent::XPathEngine($userXmlOptions);
    $this->_profEnd('XPathEngine');
    return $ret;
  }

  # getProperties from line 888
  function getProperties($param=NULL) {
    $this->_profBegin('getProperties');
    $ret = parent::getProperties($param);
    $this->_profEnd('getProperties');
    return $ret;
  }

  # setXmlOption from line 909
  function setXmlOption($optionID, $value) {
    $this->_profBegin('setXmlOption');
    $ret = parent::setXmlOption($optionID, $value);
    $this->_profEnd('setXmlOption');
    return $ret;
  }

  # setXmlOptions from line 920
  function setXmlOptions($userXmlOptions=array()) {
    $this->_profBegin('setXmlOptions');
    $ret = parent::setXmlOptions($userXmlOptions);
    $this->_profEnd('setXmlOptions');
    return $ret;
  }

  # setCaseFolding from line 940
  function setCaseFolding($onOff=TRUE) {
    $this->_profBegin('setCaseFolding');
    $ret = parent::setCaseFolding($onOff);
    $this->_profEnd('setCaseFolding');
    return $ret;
  }

  # setSkipWhiteSpaces from line 961
  function setSkipWhiteSpaces($onOff=TRUE) {
    $this->_profBegin('setSkipWhiteSpaces');
    $ret = parent::setSkipWhiteSpaces($onOff);
    $this->_profEnd('setSkipWhiteSpaces');
    return $ret;
  }

  # &getNode from line 971
  function &getNode($absoluteXPath='') {
    $this->_profBegin('&getNode');
    $ret = &parent::getNode($absoluteXPath);
    $this->_profEnd('&getNode');
    return $ret;
  }

  # &wholeText from line 1007
  function &wholeText($absoluteXPath, $textPartNr=NULL) {
    $this->_profBegin('&wholeText');
    $ret = &parent::wholeText($absoluteXPath, $textPartNr);
    $this->_profEnd('&wholeText');
    return $ret;
  }

  # _stringValue from line 1097
  function _stringValue($node) {
    $this->_profBegin('_stringValue');
    $ret = parent::_stringValue($node);
    $this->_profEnd('_stringValue');
    return $ret;
  }

  # exportAsHtml from line 1117
  function exportAsHtml($absoluteXPath='', $hilightXpathList=array()) {
    $this->_profBegin('exportAsHtml');
    $ret = parent::exportAsHtml($absoluteXPath, $hilightXpathList);
    $this->_profEnd('exportAsHtml');
    return $ret;
  }

  # exportAsXml from line 1137
  function exportAsXml($absoluteXPath='', $xmlHeader=NULL) {
    $this->_profBegin('exportAsXml');
    $ret = parent::exportAsXml($absoluteXPath, $xmlHeader);
    $this->_profEnd('exportAsXml');
    return $ret;
  }

  # exportToFile from line 1157
  function exportToFile($fileName, $absoluteXPath='', $xmlHeader=NULL) {
    $this->_profBegin('exportToFile');
    $ret = parent::exportToFile($fileName, $absoluteXPath, $xmlHeader);
    $this->_profEnd('exportToFile');
    return $ret;
  }

  # _export from line 1228
  function _export($absoluteXPath='', $xmlHeader=NULL, $hilightXpathList='') {
    $this->_profBegin('_export');
    $ret = parent::_export($absoluteXPath, $xmlHeader, $hilightXpathList);
    $this->_profEnd('_export');
    return $ret;
  }

  # _InternalExport from line 1302
  function _InternalExport($node) {
    $this->_profBegin('_InternalExport');
    $ret = parent::_InternalExport($node);
    $this->_profEnd('_InternalExport');
    return $ret;
  }

  # importFromFile from line 1553
  function importFromFile($fileName) {
    $this->_profBegin('importFromFile');
    $ret = parent::importFromFile($fileName);
    $this->_profEnd('importFromFile');
    return $ret;
  }

  # importFromString from line 1618
  function importFromString($xmlString, $absoluteParentPath = '') {
    $this->_profBegin('importFromString');
    $ret = parent::importFromString($xmlString, $absoluteParentPath);
    $this->_profEnd('importFromString');
    return $ret;
  }

  # _handleStartElement from line 1758
  function _handleStartElement($parser, $nodeName, $attributes) {
    $this->_profBegin('_handleStartElement');
    $ret = parent::_handleStartElement($parser, $nodeName, $attributes);
    $this->_profEnd('_handleStartElement');
    return $ret;
  }

  # _handleEndElement from line 1816
  function _handleEndElement($parser, $name) {
    $this->_profBegin('_handleEndElement');
    $ret = parent::_handleEndElement($parser, $name);
    $this->_profEnd('_handleEndElement');
    return $ret;
  }

  # _handleCharacterData from line 1866
  function _handleCharacterData($parser, $text) {
    $this->_profBegin('_handleCharacterData');
    $ret = parent::_handleCharacterData($parser, $text);
    $this->_profEnd('_handleCharacterData');
    return $ret;
  }

  # _handleDefaultData from line 1899
  function _handleDefaultData($parser, $text) {
    $this->_profBegin('_handleDefaultData');
    $ret = parent::_handleDefaultData($parser, $text);
    $this->_profEnd('_handleDefaultData');
    return $ret;
  }

  # _handlePI from line 1927
  function _handlePI($parser, $target, $data) {
    $this->_profBegin('_handlePI');
    $ret = parent::_handlePI($parser, $target, $data);
    $this->_profEnd('_handlePI');
    return $ret;
  }

  # _createSuperRoot from line 1941
  function _createSuperRoot() {
    $this->_profBegin('_createSuperRoot');
    $ret = parent::_createSuperRoot();
    $this->_profEnd('_createSuperRoot');
    return $ret;
  }

  # _internalAppendChild from line 1973
  function _internalAppendChild($stackParentIndex, $nodeName) {
    $this->_profBegin('_internalAppendChild');
    $ret = parent::_internalAppendChild($stackParentIndex, $nodeName);
    $this->_profEnd('_internalAppendChild');
    return $ret;
  }

  # reindexNodeTree from line 2063
  function reindexNodeTree() {
    $this->_profBegin('reindexNodeTree');
    $ret = parent::reindexNodeTree();
    $this->_profEnd('reindexNodeTree');
    return $ret;
  }

  # _generate_ids from line 2077
  function _generate_ids() {
    $this->_profBegin('_generate_ids');
    $ret = parent::_generate_ids();
    $this->_profEnd('_generate_ids');
    return $ret;
  }

  # _recursiveReindexNodeTree from line 2104
  function _recursiveReindexNodeTree($absoluteParentPath) {
    $this->_profBegin('_recursiveReindexNodeTree');
    $ret = parent::_recursiveReindexNodeTree($absoluteParentPath);
    $this->_profEnd('_recursiveReindexNodeTree');
    return $ret;
  }

  # &cloneNode from line 2197
  function &cloneNode($node, $recursive=FALSE) {
    $this->_profBegin('&cloneNode');
    $ret = &parent::cloneNode($node, $recursive);
    $this->_profEnd('&cloneNode');
    return $ret;
  }

  # __sleep from line 2232
  function __sleep() {
    $this->_profBegin('__sleep');
    $ret = parent::__sleep();
    $this->_profEnd('__sleep');
    return $ret;
  }

  # __wakeup from line 2247
  function __wakeup() {
    $this->_profBegin('__wakeup');
    $ret = parent::__wakeup();
    $this->_profEnd('__wakeup');
    return $ret;
  }

  # match from line 2272
  function match($xPathQuery, $baseXPath='') {
    $this->_profBegin('match');
    $ret = parent::match($xPathQuery, $baseXPath);
    $this->_profEnd('match');
    return $ret;
  }

  # evaluate from line 2336
  function evaluate($xPathQuery, $baseXPath='') {
    $this->_profBegin('evaluate');
    $ret = parent::evaluate($xPathQuery, $baseXPath);
    $this->_profEnd('evaluate');
    return $ret;
  }

  # _removeLiterals from line 2361
  function _removeLiterals($xPathQuery) {
    $this->_profBegin('_removeLiterals');
    $ret = parent::_removeLiterals($xPathQuery);
    $this->_profEnd('_removeLiterals');
    return $ret;
  }

  # _asLiteral from line 2396
  function _asLiteral($string) {
    $this->_profBegin('_asLiteral');
    $ret = parent::_asLiteral($string);
    $this->_profEnd('_asLiteral');
    return $ret;
  }

  # _addLiteral from line 2425
  function _addLiteral($string) {
    $this->_profBegin('_addLiteral');
    $ret = parent::_addLiteral($string);
    $this->_profEnd('_addLiteral');
    return $ret;
  }

  # _GetOperator from line 2452
  function _GetOperator($xPathQuery) {
    $this->_profBegin('_GetOperator');
    $ret = parent::_GetOperator($xPathQuery);
    $this->_profEnd('_GetOperator');
    return $ret;
  }

  # _evaluatePrimaryExpr from line 2601
  function _evaluatePrimaryExpr($xPathQuery, $context, &$result) {
    $this->_profBegin('_evaluatePrimaryExpr');
    $ret = parent::_evaluatePrimaryExpr($xPathQuery, $context, &$result);
    $this->_profEnd('_evaluatePrimaryExpr');
    return $ret;
  }

  # _evaluateExpr from line 2765
  function _evaluateExpr($xPathQuery, $context) {
    $this->_profBegin('_evaluateExpr');
    $ret = parent::_evaluateExpr($xPathQuery, $context);
    $this->_profEnd('_evaluateExpr');
    return $ret;
  }

  # _evaluateOperator from line 3019
  function _evaluateOperator($left, $operator, $right, $operatorType, $context) {
    $this->_profBegin('_evaluateOperator');
    $ret = parent::_evaluateOperator($left, $operator, $right, $operatorType, $context);
    $this->_profEnd('_evaluateOperator');
    return $ret;
  }

  # _evaluatePathExpr from line 3208
  function _evaluatePathExpr($PathExpr, $context) {
    $this->_profBegin('_evaluatePathExpr');
    $ret = parent::_evaluatePathExpr($PathExpr, $context);
    $this->_profEnd('_evaluatePathExpr');
    return $ret;
  }

  # _sortByDocOrder from line 3280
  function _sortByDocOrder($xPathSet) {
    $this->_profBegin('_sortByDocOrder');
    $ret = parent::_sortByDocOrder($xPathSet);
    $this->_profEnd('_sortByDocOrder');
    return $ret;
  }

  # _evaluateStep from line 3379
  function _evaluateStep($steps, $context) {
    $this->_profBegin('_evaluateStep');
    $ret = parent::_evaluateStep($steps, $context);
    $this->_profEnd('_evaluateStep');
    return $ret;
  }

  # _checkPredicates from line 3478
  function _checkPredicates($xPathSet, $predicates) {
    $this->_profBegin('_checkPredicates');
    $ret = parent::_checkPredicates($xPathSet, $predicates);
    $this->_profEnd('_checkPredicates');
    return $ret;
  }

  # _evaluateFunction from line 3575
  function _evaluateFunction($function, $arguments, $context) {
    $this->_profBegin('_evaluateFunction');
    $ret = parent::_evaluateFunction($function, $arguments, $context);
    $this->_profEnd('_evaluateFunction');
    return $ret;
  }

  # _checkNodeTest from line 3633
  function _checkNodeTest($contextPath, $nodeTest) {
    $this->_profBegin('_checkNodeTest');
    $ret = parent::_checkNodeTest($contextPath, $nodeTest);
    $this->_profEnd('_checkNodeTest');
    return $ret;
  }

  # _getAxis from line 3735
  function _getAxis($step) {
    $this->_profBegin('_getAxis');
    $ret = parent::_getAxis($step);
    $this->_profEnd('_getAxis');
    return $ret;
  }

  # _handleAxis_child from line 3931
  function _handleAxis_child($axis, $contextPath) {
    $this->_profBegin('_handleAxis_child');
    $ret = parent::_handleAxis_child($axis, $contextPath);
    $this->_profEnd('_handleAxis_child');
    return $ret;
  }

  # _handleAxis_parent from line 3978
  function _handleAxis_parent($axis, $contextPath) {
    $this->_profBegin('_handleAxis_parent');
    $ret = parent::_handleAxis_parent($axis, $contextPath);
    $this->_profEnd('_handleAxis_parent');
    return $ret;
  }

  # _handleAxis_attribute from line 3997
  function _handleAxis_attribute($axis, $contextPath) {
    $this->_profBegin('_handleAxis_attribute');
    $ret = parent::_handleAxis_attribute($axis, $contextPath);
    $this->_profEnd('_handleAxis_attribute');
    return $ret;
  }

  # _handleAxis_self from line 4022
  function _handleAxis_self($axis, $contextPath) {
    $this->_profBegin('_handleAxis_self');
    $ret = parent::_handleAxis_self($axis, $contextPath);
    $this->_profEnd('_handleAxis_self');
    return $ret;
  }

  # _handleAxis_descendant from line 4040
  function _handleAxis_descendant($axis, $contextPath) {
    $this->_profBegin('_handleAxis_descendant');
    $ret = parent::_handleAxis_descendant($axis, $contextPath);
    $this->_profEnd('_handleAxis_descendant');
    return $ret;
  }

  # _handleAxis_ancestor from line 4068
  function _handleAxis_ancestor($axis, $contextPath) {
    $this->_profBegin('_handleAxis_ancestor');
    $ret = parent::_handleAxis_ancestor($axis, $contextPath);
    $this->_profEnd('_handleAxis_ancestor');
    return $ret;
  }

  # _handleAxis_namespace from line 4093
  function _handleAxis_namespace($axis, $contextPath) {
    $this->_profBegin('_handleAxis_namespace');
    $ret = parent::_handleAxis_namespace($axis, $contextPath);
    $this->_profEnd('_handleAxis_namespace');
    return $ret;
  }

  # _handleAxis_following from line 4105
  function _handleAxis_following($axis, $contextPath) {
    $this->_profBegin('_handleAxis_following');
    $ret = parent::_handleAxis_following($axis, $contextPath);
    $this->_profEnd('_handleAxis_following');
    return $ret;
  }

  # _handleAxis_preceding from line 4140
  function _handleAxis_preceding($axis, $contextPath) {
    $this->_profBegin('_handleAxis_preceding');
    $ret = parent::_handleAxis_preceding($axis, $contextPath);
    $this->_profEnd('_handleAxis_preceding');
    return $ret;
  }

  # _handleAxis_following_sibling from line 4170
  function _handleAxis_following_sibling($axis, $contextPath) {
    $this->_profBegin('_handleAxis_following_sibling');
    $ret = parent::_handleAxis_following_sibling($axis, $contextPath);
    $this->_profEnd('_handleAxis_following_sibling');
    return $ret;
  }

  # _handleAxis_preceding_sibling from line 4206
  function _handleAxis_preceding_sibling($axis, $contextPath) {
    $this->_profBegin('_handleAxis_preceding_sibling');
    $ret = parent::_handleAxis_preceding_sibling($axis, $contextPath);
    $this->_profEnd('_handleAxis_preceding_sibling');
    return $ret;
  }

  # _handleAxis_descendant_or_self from line 4236
  function _handleAxis_descendant_or_self($axis, $contextPath) {
    $this->_profBegin('_handleAxis_descendant_or_self');
    $ret = parent::_handleAxis_descendant_or_self($axis, $contextPath);
    $this->_profEnd('_handleAxis_descendant_or_self');
    return $ret;
  }

  # _handleAxis_ancestor_or_self from line 4257
  function _handleAxis_ancestor_or_self( $axis, $contextPath) {
    $this->_profBegin('_handleAxis_ancestor_or_self');
    $ret = parent::_handleAxis_ancestor_or_self($axis, $contextPath);
    $this->_profEnd('_handleAxis_ancestor_or_self');
    return $ret;
  }

  # _handleFunction_last from line 4281
  function _handleFunction_last($arguments, $context) {
    $this->_profBegin('_handleFunction_last');
    $ret = parent::_handleFunction_last($arguments, $context);
    $this->_profEnd('_handleFunction_last');
    return $ret;
  }

  # _handleFunction_position from line 4293
  function _handleFunction_position($arguments, $context) {
    $this->_profBegin('_handleFunction_position');
    $ret = parent::_handleFunction_position($arguments, $context);
    $this->_profEnd('_handleFunction_position');
    return $ret;
  }

  # _handleFunction_count from line 4305
  function _handleFunction_count($arguments, $context) {
    $this->_profBegin('_handleFunction_count');
    $ret = parent::_handleFunction_count($arguments, $context);
    $this->_profEnd('_handleFunction_count');
    return $ret;
  }

  # _handleFunction_id from line 4318
  function _handleFunction_id($arguments, $context) {
    $this->_profBegin('_handleFunction_id');
    $ret = parent::_handleFunction_id($arguments, $context);
    $this->_profEnd('_handleFunction_id');
    return $ret;
  }

  # _handleFunction_name from line 4343
  function _handleFunction_name($arguments, $context) {
    $this->_profBegin('_handleFunction_name');
    $ret = parent::_handleFunction_name($arguments, $context);
    $this->_profEnd('_handleFunction_name');
    return $ret;
  }

  # _handleFunction_string from line 4368
  function _handleFunction_string($arguments, $context) {
    $this->_profBegin('_handleFunction_string');
    $ret = parent::_handleFunction_string($arguments, $context);
    $this->_profEnd('_handleFunction_string');
    return $ret;
  }

  # _handleFunction_concat from line 4425
  function _handleFunction_concat($arguments, $context) {
    $this->_profBegin('_handleFunction_concat');
    $ret = parent::_handleFunction_concat($arguments, $context);
    $this->_profEnd('_handleFunction_concat');
    return $ret;
  }

  # _handleFunction_starts_with from line 4447
  function _handleFunction_starts_with($arguments, $context) {
    $this->_profBegin('_handleFunction_starts_with');
    $ret = parent::_handleFunction_starts_with($arguments, $context);
    $this->_profEnd('_handleFunction_starts_with');
    return $ret;
  }

  # _handleFunction_contains from line 4466
  function _handleFunction_contains($arguments, $context) {
    $this->_profBegin('_handleFunction_contains');
    $ret = parent::_handleFunction_contains($arguments, $context);
    $this->_profEnd('_handleFunction_contains');
    return $ret;
  }

  # _handleFunction_substring_before from line 4494
  function _handleFunction_substring_before($arguments, $context) {
    $this->_profBegin('_handleFunction_substring_before');
    $ret = parent::_handleFunction_substring_before($arguments, $context);
    $this->_profEnd('_handleFunction_substring_before');
    return $ret;
  }

  # _handleFunction_substring_after from line 4513
  function _handleFunction_substring_after($arguments, $context) {
    $this->_profBegin('_handleFunction_substring_after');
    $ret = parent::_handleFunction_substring_after($arguments, $context);
    $this->_profEnd('_handleFunction_substring_after');
    return $ret;
  }

  # _handleFunction_substring from line 4532
  function _handleFunction_substring($arguments, $context) {
    $this->_profBegin('_handleFunction_substring');
    $ret = parent::_handleFunction_substring($arguments, $context);
    $this->_profEnd('_handleFunction_substring');
    return $ret;
  }

  # _handleFunction_string_length from line 4557
  function _handleFunction_string_length($arguments, $context) {
    $this->_profBegin('_handleFunction_string_length');
    $ret = parent::_handleFunction_string_length($arguments, $context);
    $this->_profEnd('_handleFunction_string_length');
    return $ret;
  }

  # _handleFunction_normalize_space from line 4578
  function _handleFunction_normalize_space($arguments, $context) {
    $this->_profBegin('_handleFunction_normalize_space');
    $ret = parent::_handleFunction_normalize_space($arguments, $context);
    $this->_profEnd('_handleFunction_normalize_space');
    return $ret;
  }

  # _handleFunction_translate from line 4596
  function _handleFunction_translate($arguments, $context) {
    $this->_profBegin('_handleFunction_translate');
    $ret = parent::_handleFunction_translate($arguments, $context);
    $this->_profEnd('_handleFunction_translate');
    return $ret;
  }

  # _handleFunction_boolean from line 4618
  function _handleFunction_boolean($arguments, $context) {
    $this->_profBegin('_handleFunction_boolean');
    $ret = parent::_handleFunction_boolean($arguments, $context);
    $this->_profEnd('_handleFunction_boolean');
    return $ret;
  }

  # _handleFunction_not from line 4666
  function _handleFunction_not($arguments, $context) {
    $this->_profBegin('_handleFunction_not');
    $ret = parent::_handleFunction_not($arguments, $context);
    $this->_profEnd('_handleFunction_not');
    return $ret;
  }

  # _handleFunction_true from line 4681
  function _handleFunction_true($arguments, $context) {
    $this->_profBegin('_handleFunction_true');
    $ret = parent::_handleFunction_true($arguments, $context);
    $this->_profEnd('_handleFunction_true');
    return $ret;
  }

  # _handleFunction_false from line 4693
  function _handleFunction_false($arguments, $context) {
    $this->_profBegin('_handleFunction_false');
    $ret = parent::_handleFunction_false($arguments, $context);
    $this->_profEnd('_handleFunction_false');
    return $ret;
  }

  # _handleFunction_lang from line 4705
  function _handleFunction_lang($arguments, $context) {
    $this->_profBegin('_handleFunction_lang');
    $ret = parent::_handleFunction_lang($arguments, $context);
    $this->_profEnd('_handleFunction_lang');
    return $ret;
  }

  # _handleFunction_number from line 4729
  function _handleFunction_number($arguments, $context) {
    $this->_profBegin('_handleFunction_number');
    $ret = parent::_handleFunction_number($arguments, $context);
    $this->_profEnd('_handleFunction_number');
    return $ret;
  }

  # _handleFunction_sum from line 4779
  function _handleFunction_sum($arguments, $context) {
    $this->_profBegin('_handleFunction_sum');
    $ret = parent::_handleFunction_sum($arguments, $context);
    $this->_profEnd('_handleFunction_sum');
    return $ret;
  }

  # _handleFunction_floor from line 4807
  function _handleFunction_floor($arguments, $context) {
    $this->_profBegin('_handleFunction_floor');
    $ret = parent::_handleFunction_floor($arguments, $context);
    $this->_profEnd('_handleFunction_floor');
    return $ret;
  }

  # _handleFunction_ceiling from line 4823
  function _handleFunction_ceiling($arguments, $context) {
    $this->_profBegin('_handleFunction_ceiling');
    $ret = parent::_handleFunction_ceiling($arguments, $context);
    $this->_profEnd('_handleFunction_ceiling');
    return $ret;
  }

  # _handleFunction_round from line 4839
  function _handleFunction_round($arguments, $context) {
    $this->_profBegin('_handleFunction_round');
    $ret = parent::_handleFunction_round($arguments, $context);
    $this->_profEnd('_handleFunction_round');
    return $ret;
  }

  # _handleFunction_x_lower from line 4862
  function _handleFunction_x_lower($arguments, $context) {
    $this->_profBegin('_handleFunction_x_lower');
    $ret = parent::_handleFunction_x_lower($arguments, $context);
    $this->_profEnd('_handleFunction_x_lower');
    return $ret;
  }

  # _handleFunction_x_upper from line 4880
  function _handleFunction_x_upper($arguments, $context) {
    $this->_profBegin('_handleFunction_x_upper');
    $ret = parent::_handleFunction_x_upper($arguments, $context);
    $this->_profEnd('_handleFunction_x_upper');
    return $ret;
  }

  # _handleFunction_generate_id from line 4914
  function _handleFunction_generate_id($arguments, $context) {
    $this->_profBegin('_handleFunction_generate_id');
    $ret = parent::_handleFunction_generate_id($arguments, $context);
    $this->_profEnd('_handleFunction_generate_id');
    return $ret;
  }

  # decodeEntities from line 4956
  function decodeEntities($encodedData, $reverse=FALSE) {
    $this->_profBegin('decodeEntities');
    $ret = parent::decodeEntities($encodedData, $reverse);
    $this->_profEnd('decodeEntities');
    return $ret;
  }

  # equalNodes from line 4996
  function equalNodes($node1, $node2) {
    $this->_profBegin('equalNodes');
    $ret = parent::equalNodes($node1, $node2);
    $this->_profEnd('equalNodes');
    return $ret;
  }

  # getNodePath from line 5008
  function getNodePath($node) {
    $this->_profBegin('getNodePath');
    $ret = parent::getNodePath($node);
    $this->_profEnd('getNodePath');
    return $ret;
  }

  # getParentXPath from line 5035
  function getParentXPath($absoluteXPath) {
    $this->_profBegin('getParentXPath');
    $ret = parent::getParentXPath($absoluteXPath);
    $this->_profEnd('getParentXPath');
    return $ret;
  }

  # hasChildNodes from line 5050
  function hasChildNodes($absoluteXPath) {
    $this->_profBegin('hasChildNodes');
    $ret = parent::hasChildNodes($absoluteXPath);
    $this->_profEnd('hasChildNodes');
    return $ret;
  }

  # _translateAmpersand from line 5096
  function _translateAmpersand($xmlSource, $reverse=FALSE) {
    $this->_profBegin('_translateAmpersand');
    $ret = parent::_translateAmpersand($xmlSource, $reverse);
    $this->_profEnd('_translateAmpersand');
    return $ret;
  }

  #############################################
  # Adding functions for base class XPathBase

  # XPathBase_Prof from line 196
  function XPathBase_Prof() {
    $this->_profBegin('XPathBase');
    $ret = parent::XPathBase();
    $this->_profEnd('XPathBase');
    return $ret;
  }

  # reset from line 235
  function reset() {
    $this->_profBegin('reset');
    $ret = parent::reset();
    $this->_profEnd('reset');
    return $ret;
  }

  # _bracketsCheck from line 249
  function _bracketsCheck($term) {
    $this->_profBegin('_bracketsCheck');
    $ret = parent::_bracketsCheck($term);
    $this->_profEnd('_bracketsCheck');
    return $ret;
  }

  # _searchString from line 306
  function _searchString($term, $expression) {
    $this->_profBegin('_searchString');
    $ret = parent::_searchString($term, $expression);
    $this->_profEnd('_searchString');
    return $ret;
  }

  # _bracketExplode from line 338
  function _bracketExplode($separator, $term) {
    $this->_profBegin('_bracketExplode');
    $ret = parent::_bracketExplode($separator, $term);
    $this->_profEnd('_bracketExplode');
    return $ret;
  }

  # _getEndGroups from line 416
  function _getEndGroups($string, $open='[', $close=']') {
    $this->_profBegin('_getEndGroups');
    $ret = parent::_getEndGroups($string, $open, $close);
    $this->_profEnd('_getEndGroups');
    return $ret;
  }

  # _prestr from line 502
  function _prestr(&$string, $delimiter, $offset=0) {
    $this->_profBegin('_prestr');
    $ret = parent::_prestr(&$string, $delimiter, $offset);
    $this->_profEnd('_prestr');
    return $ret;
  }

  # _afterstr from line 520
  function _afterstr($string, $delimiter, $offset=0) {
    $this->_profBegin('_afterstr');
    $ret = parent::_afterstr($string, $delimiter, $offset);
    $this->_profEnd('_afterstr');
    return $ret;
  }

  # setVerbose from line 539
  function setVerbose($levelOfVerbosity = 1) {
    $this->_profBegin('setVerbose');
    $ret = parent::setVerbose($levelOfVerbosity);
    $this->_profEnd('setVerbose');
    return $ret;
  }

  # getLastError from line 558
  function getLastError() {
    $this->_profBegin('getLastError');
    $ret = parent::getLastError();
    $this->_profEnd('getLastError');
    return $ret;
  }

  # _setLastError from line 577
  function _setLastError($message='', $line='-', $file='-') {
    $this->_profBegin('_setLastError');
    $ret = parent::_setLastError($message, $line, $file);
    $this->_profEnd('_setLastError');
    return $ret;
  }

  # _displayError from line 594
  function _displayError($message, $lineNumber='-', $file='-', $terminate=TRUE) {
    $this->_profBegin('_displayError');
    $ret = parent::_displayError($message, $lineNumber, $file, $terminate);
    $this->_profEnd('_displayError');
    return $ret;
  }

  # _displayMessage from line 611
  function _displayMessage($message, $lineNumber='-', $file='-') {
    $this->_profBegin('_displayMessage');
    $ret = parent::_displayMessage($message, $lineNumber, $file);
    $this->_profEnd('_displayMessage');
    return $ret;
  }

  # _beginDebugFunction from line 630
  function _beginDebugFunction($functionName) {
    $this->_profBegin('_beginDebugFunction');
    $ret = parent::_beginDebugFunction($functionName);
    $this->_profEnd('_beginDebugFunction');
    return $ret;
  }

  # _closeDebugFunction from line 656
  function _closeDebugFunction($aStartTime, $returnValue = "") {
    $this->_profBegin('_closeDebugFunction');
    $ret = parent::_closeDebugFunction($aStartTime, $returnValue);
    $this->_profEnd('_closeDebugFunction');
    return $ret;
  }

  # _profileFunction from line 681
  function _profileFunction($aStartTime, $alertString) {
    $this->_profBegin('_profileFunction');
    $ret = parent::_profileFunction($aStartTime, $alertString);
    $this->_profEnd('_profileFunction');
    return $ret;
  }

  # _printContext from line 694
  function _printContext($context) {
    $this->_profBegin('_printContext');
    $ret = parent::_printContext($context);
    $this->_profEnd('_printContext');
    return $ret;
  }

  # _treeDump from line 706
  function _treeDump($node, $indent = '') {
    $this->_profBegin('_treeDump');
    $ret = parent::_treeDump($node, $indent);
    $this->_profEnd('_treeDump');
    return $ret;
  }

} // END Class 
class XPath_Prof extends XPath {

    /******************************************************************************************************
    //-----------------------------------------------------------------------------------------
    //                          -- P R O F I L E  Methods BEGIN --                             
    //-----------------------------------------------------------------------------------------
    */
    var $callStack = array();
      
    /**
     * Profile begin call
     */
    function _profBegin($sonFuncName) {
      static $entryTmpl = array (
               'start'  => array(),
               'recursiveCount' => 0,
               'totTime' => 0,
               'callCount' => 0
             );
      $now  = explode(' ', microtime());
      
      if (empty($this->callStack)) {
        $fatherFuncName = '';
      } else {
        $fatherFuncName = $this->callStack[sizeOf($this->callStack)-1];
        $fatherEntry = &$this->profile[$fatherFuncName];
      }
      $this->callStack[] = $sonFuncName;
      
      if (!isSet($this->profile[$sonFuncName])) {
        $this->profile[$sonFuncName] = $entryTmpl;
      }
      
      $sonEntry = &$this->profile[$sonFuncName];
      $sonEntry['callCount']++;
      // if we call the t's the same function let the time run, otherwise sum up
      if ($fatherFuncName == $sonFuncName) {
        $sonEntry['recursiveCount']++;
      }
      if (!empty($fatherFuncName)) {
        $last = $fatherEntry['start'];
        $fatherEntry['totTime'] += round( (($now[1] - $last[1]) + ($now[0] - $last[0]))*10000 );
        $fatherEntry['start'] = 0;
      }
      $sonEntry['start'] = explode(' ', microtime());
    }
    
    /**
     * Profile end call
     */
    function _profEnd($sonFuncName) {
      $now  = explode(' ', microtime());
      
      array_pop($this->callStack);
      if (empty($this->callStack)) {
        $fatherFuncName = '';
      } else {
        $fatherFuncName = $this->callStack[sizeOf($this->callStack)-1];
        $fatherEntry = &$this->profile[$fatherFuncName];
      }
      $sonEntry = &$this->profile[$sonFuncName];
      if (empty($sonEntry)) {
        echo "ERROR in profEnd(): '$funcNam' not in list. Seams it was never started ;o)";
      }
      
      $last = $sonEntry['start'];
      $sonEntry['totTime'] += round( (($now[1] - $last[1]) + ($now[0] - $last[0]))*10000 );
      $sonEntry['start'] = 0;
      if (!empty($fatherEntry)) $fatherEntry['start'] = explode(' ', microtime());
    }
    
    /**
     * Show profile gathered so far as HTML table
     */
    function profileToHtml() {
      $sortArr = array();
      if (empty($this->profile)) return '';
      reset($this->profile);
      while (list($funcName) = each($this->profile)) {
        $sortArrKey[] = $this->profile[$funcName]['totTime'];
        $sortArrVal[] = $funcName;
      }
      //echo '<pre>';var_dump($sortArrVal);echo '</pre>';
      array_multisort ($sortArrKey, SORT_DESC, $sortArrVal );
      //echo '<pre>';var_dump($sortArrVal);echo '</pre>';
      
      $totTime = 0;
      $size = sizeOf($sortArrVal);
      for ($i=0; $i<$size; $i++) {
        $funcName = &$sortArrVal[$i];
        $totTime += $this->profile[$funcName]['totTime'];
      }
      $out = '<table border="1">';
      $out .='<tr align="center" bgcolor="#bcd6f1"><th>Function</th><th> % </th><th>Total [ms]</th><th># Call</th><th>[ms] per Call</th><th># Recursive</th></tr>';
      for ($i=0; $i<$size; $i++) {
        $funcName = &$sortArrVal[$i];
        $row = &$this->profile[$funcName];
        $procent = round($row['totTime']*100/$totTime);
        if ($procent>20)
          $bgc = '#ff8080';
        elseif ($procent>15) 
          $bgc = '#ff9999';
        elseif ($procent>10) 
          $bgc = '#ffcccc';
        elseif ($procent>5) 
          $bgc = '#ffffcc';
        else 
          $bgc = '#66ff99';
          
        $out .="<tr align='center' bgcolor='{$bgc}'>";
        $out .='<td>'. $funcName  .'</td><td>'.  $procent .'% '.'</td><td>'.  $row['totTime']/10 .'</td><td>'. $row['callCount'] .'</td><td>'. round($row['totTime']/10/$row['callCount'],2) .'</td><td>'. $row['recursiveCount'].'</td>';
        $out .='</tr>';
      }
      $out .= '</table> Total Time [' . $totTime/10 .'ms]' ;
      return $out;
    }
    /*
    //-----------------------------------------------------------------------------------------
    //                          -- P R O F I L E  Methods END --                               
    //-----------------------------------------------------------------------------------------
    ******************************************************************************************************/

  # XPath_Prof from line 5135
  function XPath_Prof($fileName='', $userXmlOptions=array()) {
    $this->_profBegin('XPath');
    $ret = parent::XPath($fileName, $userXmlOptions);
    $this->_profEnd('XPath');
    return $ret;
  }

  # setModMatch from line 5177
  function setModMatch($modMatch = XPATH_QUERYHIT_ALL) {
    $this->_profBegin('setModMatch');
    $ret = parent::setModMatch($modMatch);
    $this->_profEnd('setModMatch');
    return $ret;
  }

  # nodeName from line 5206
  function nodeName($xPathQuery) {
    $this->_profBegin('nodeName');
    $ret = parent::nodeName($xPathQuery);
    $this->_profEnd('nodeName');
    return $ret;
  }

  # removeChild from line 5247
  function removeChild($xPathQuery, $autoReindex=TRUE) {
    $this->_profBegin('removeChild');
    $ret = parent::removeChild($xPathQuery, $autoReindex);
    $this->_profEnd('removeChild');
    return $ret;
  }

  # replaceChildByData from line 5312
  function replaceChildByData($xPathQuery, $data, $autoReindex=TRUE) {
    $this->_profBegin('replaceChildByData');
    $ret = parent::replaceChildByData($xPathQuery, $data, $autoReindex);
    $this->_profEnd('replaceChildByData');
    return $ret;
  }

  # &replaceChild from line 5367
  function &replaceChild($xPathQuery, $node, $autoReindex=TRUE) {
    $this->_profBegin('&replaceChild');
    $ret = &parent::replaceChild($xPathQuery, $node, $autoReindex);
    $this->_profEnd('&replaceChild');
    return $ret;
  }

  # insertChild from line 5452
  function insertChild($xPathQuery, $node, $shiftRight=TRUE, $afterText=TRUE, $autoReindex=TRUE) {
    $this->_profBegin('insertChild');
    $ret = parent::insertChild($xPathQuery, $node, $shiftRight, $afterText, $autoReindex);
    $this->_profEnd('insertChild');
    return $ret;
  }

  # appendChild from line 5550
  function appendChild($xPathQuery, $node, $afterText=FALSE, $autoReindex=TRUE) {
    $this->_profBegin('appendChild');
    $ret = parent::appendChild($xPathQuery, $node, $afterText, $autoReindex);
    $this->_profEnd('appendChild');
    return $ret;
  }

  # insertBefore from line 5630
  function insertBefore($xPathQuery, $node, $afterText=TRUE, $autoReindex=TRUE) {
    $this->_profBegin('insertBefore');
    $ret = parent::insertBefore($xPathQuery, $node, $afterText, $autoReindex);
    $this->_profEnd('insertBefore');
    return $ret;
  }

  # getAttributes from line 5656
  function getAttributes($absoluteXPath, $attrName=NULL) {
    $this->_profBegin('getAttributes');
    $ret = parent::getAttributes($absoluteXPath, $attrName);
    $this->_profEnd('getAttributes');
    return $ret;
  }

  # setAttribute from line 5689
  function setAttribute($xPathQuery, $name, $value, $overwrite=TRUE) {
    $this->_profBegin('setAttribute');
    $ret = parent::setAttribute($xPathQuery, $name, $value, $overwrite);
    $this->_profEnd('setAttribute');
    return $ret;
  }

  # setAttributes from line 5707
  function setAttributes($xPathQuery, $attributes, $overwrite=TRUE) {
    $this->_profBegin('setAttributes');
    $ret = parent::setAttributes($xPathQuery, $attributes, $overwrite);
    $this->_profEnd('setAttributes');
    return $ret;
  }

  # removeAttribute from line 5747
  function removeAttribute($xPathQuery, $attrList=NULL) {
    $this->_profBegin('removeAttribute');
    $ret = parent::removeAttribute($xPathQuery, $attrList);
    $this->_profEnd('removeAttribute');
    return $ret;
  }

  # getData from line 5786
  function getData($xPathQuery) {
    $this->_profBegin('getData');
    $ret = parent::getData($xPathQuery);
    $this->_profEnd('getData');
    return $ret;
  }

  # getDataParts from line 5808
  function getDataParts($xPathQuery) {
    $this->_profBegin('getDataParts');
    $ret = parent::getDataParts($xPathQuery);
    $this->_profEnd('getDataParts');
    return $ret;
  }

  # substringData from line 5848
  function substringData($absoluteXPath, $offset = 0, $count = NULL) {
    $this->_profBegin('substringData');
    $ret = parent::substringData($absoluteXPath, $offset, $count);
    $this->_profEnd('substringData');
    return $ret;
  }

  # replaceData from line 5871
  function replaceData($xPathQuery, $replacement, $offset = 0, $count = 0, $textPartNr=1) {
    $this->_profBegin('replaceData');
    $ret = parent::replaceData($xPathQuery, $replacement, $offset, $count, $textPartNr);
    $this->_profEnd('replaceData');
    return $ret;
  }

  # insertData from line 5896
  function insertData($xPathQuery, $data, $offset=0) {
    $this->_profBegin('insertData');
    $ret = parent::insertData($xPathQuery, $data, $offset);
    $this->_profEnd('insertData');
    return $ret;
  }

  # appendData from line 5916
  function appendData($xPathQuery, $data, $textPartNr=1) {
    $this->_profBegin('appendData');
    $ret = parent::appendData($xPathQuery, $data, $textPartNr);
    $this->_profEnd('appendData');
    return $ret;
  }

  # deleteData from line 5942
  function deleteData($xPathQuery, $offset=0, $count=0, $textPartNr=1) {
    $this->_profBegin('deleteData');
    $ret = parent::deleteData($xPathQuery, $offset, $count, $textPartNr);
    $this->_profEnd('deleteData');
    return $ret;
  }

  # &_xml2Document from line 5964
  function &_xml2Document($xmlString) {
    $this->_profBegin('&_xml2Document');
    $ret = &parent::_xml2Document($xmlString);
    $this->_profEnd('&_xml2Document');
    return $ret;
  }

  # _getTextSet from line 6007
  function _getTextSet($xPathQuery, $textPartNr=1) {
    $this->_profBegin('_getTextSet');
    $ret = parent::_getTextSet($xPathQuery, $textPartNr);
    $this->_profEnd('_getTextSet');
    return $ret;
  }

  # _resolveXPathQueryForNodeMod from line 6119
  function _resolveXPathQueryForNodeMod($xPathQuery, $functionName) {
    $this->_profBegin('_resolveXPathQueryForNodeMod');
    $ret = parent::_resolveXPathQueryForNodeMod($xPathQuery, $functionName);
    $this->_profEnd('_resolveXPathQueryForNodeMod');
    return $ret;
  }

  # _resolveXPathQuery from line 6156
  function _resolveXPathQuery($xPathQuery, $function) {
    $this->_profBegin('_resolveXPathQuery');
    $ret = parent::_resolveXPathQuery($xPathQuery, $function);
    $this->_profEnd('_resolveXPathQuery');
    return $ret;
  }

  # _title from line 6207
  function _title($title) {
    $this->_profBegin('_title');
    $ret = parent::_title($title);
    $this->_profEnd('_title');
    return $ret;
  }

  #############################################
  # Adding functions for base class XPathEngine

  # XPathEngine_Prof from line 842
  function XPathEngine_Prof($userXmlOptions=array()) {
    $this->_profBegin('XPathEngine');
    $ret = parent::XPathEngine($userXmlOptions);
    $this->_profEnd('XPathEngine');
    return $ret;
  }

  # getProperties from line 888
  function getProperties($param=NULL) {
    $this->_profBegin('getProperties');
    $ret = parent::getProperties($param);
    $this->_profEnd('getProperties');
    return $ret;
  }

  # setXmlOption from line 909
  function setXmlOption($optionID, $value) {
    $this->_profBegin('setXmlOption');
    $ret = parent::setXmlOption($optionID, $value);
    $this->_profEnd('setXmlOption');
    return $ret;
  }

  # setXmlOptions from line 920
  function setXmlOptions($userXmlOptions=array()) {
    $this->_profBegin('setXmlOptions');
    $ret = parent::setXmlOptions($userXmlOptions);
    $this->_profEnd('setXmlOptions');
    return $ret;
  }

  # setCaseFolding from line 940
  function setCaseFolding($onOff=TRUE) {
    $this->_profBegin('setCaseFolding');
    $ret = parent::setCaseFolding($onOff);
    $this->_profEnd('setCaseFolding');
    return $ret;
  }

  # setSkipWhiteSpaces from line 961
  function setSkipWhiteSpaces($onOff=TRUE) {
    $this->_profBegin('setSkipWhiteSpaces');
    $ret = parent::setSkipWhiteSpaces($onOff);
    $this->_profEnd('setSkipWhiteSpaces');
    return $ret;
  }

  # &getNode from line 971
  function &getNode($absoluteXPath='') {
    $this->_profBegin('&getNode');
    $ret = &parent::getNode($absoluteXPath);
    $this->_profEnd('&getNode');
    return $ret;
  }

  # &wholeText from line 1007
  function &wholeText($absoluteXPath, $textPartNr=NULL) {
    $this->_profBegin('&wholeText');
    $ret = &parent::wholeText($absoluteXPath, $textPartNr);
    $this->_profEnd('&wholeText');
    return $ret;
  }

  # _stringValue from line 1097
  function _stringValue($node) {
    $this->_profBegin('_stringValue');
    $ret = parent::_stringValue($node);
    $this->_profEnd('_stringValue');
    return $ret;
  }

  # exportAsHtml from line 1117
  function exportAsHtml($absoluteXPath='', $hilightXpathList=array()) {
    $this->_profBegin('exportAsHtml');
    $ret = parent::exportAsHtml($absoluteXPath, $hilightXpathList);
    $this->_profEnd('exportAsHtml');
    return $ret;
  }

  # exportAsXml from line 1137
  function exportAsXml($absoluteXPath='', $xmlHeader=NULL) {
    $this->_profBegin('exportAsXml');
    $ret = parent::exportAsXml($absoluteXPath, $xmlHeader);
    $this->_profEnd('exportAsXml');
    return $ret;
  }

  # exportToFile from line 1157
  function exportToFile($fileName, $absoluteXPath='', $xmlHeader=NULL) {
    $this->_profBegin('exportToFile');
    $ret = parent::exportToFile($fileName, $absoluteXPath, $xmlHeader);
    $this->_profEnd('exportToFile');
    return $ret;
  }

  # _export from line 1228
  function _export($absoluteXPath='', $xmlHeader=NULL, $hilightXpathList='') {
    $this->_profBegin('_export');
    $ret = parent::_export($absoluteXPath, $xmlHeader, $hilightXpathList);
    $this->_profEnd('_export');
    return $ret;
  }

  # _InternalExport from line 1302
  function _InternalExport($node) {
    $this->_profBegin('_InternalExport');
    $ret = parent::_InternalExport($node);
    $this->_profEnd('_InternalExport');
    return $ret;
  }

  # importFromFile from line 1553
  function importFromFile($fileName) {
    $this->_profBegin('importFromFile');
    $ret = parent::importFromFile($fileName);
    $this->_profEnd('importFromFile');
    return $ret;
  }

  # importFromString from line 1618
  function importFromString($xmlString, $absoluteParentPath = '') {
    $this->_profBegin('importFromString');
    $ret = parent::importFromString($xmlString, $absoluteParentPath);
    $this->_profEnd('importFromString');
    return $ret;
  }

  # _handleStartElement from line 1758
  function _handleStartElement($parser, $nodeName, $attributes) {
    $this->_profBegin('_handleStartElement');
    $ret = parent::_handleStartElement($parser, $nodeName, $attributes);
    $this->_profEnd('_handleStartElement');
    return $ret;
  }

  # _handleEndElement from line 1816
  function _handleEndElement($parser, $name) {
    $this->_profBegin('_handleEndElement');
    $ret = parent::_handleEndElement($parser, $name);
    $this->_profEnd('_handleEndElement');
    return $ret;
  }

  # _handleCharacterData from line 1866
  function _handleCharacterData($parser, $text) {
    $this->_profBegin('_handleCharacterData');
    $ret = parent::_handleCharacterData($parser, $text);
    $this->_profEnd('_handleCharacterData');
    return $ret;
  }

  # _handleDefaultData from line 1899
  function _handleDefaultData($parser, $text) {
    $this->_profBegin('_handleDefaultData');
    $ret = parent::_handleDefaultData($parser, $text);
    $this->_profEnd('_handleDefaultData');
    return $ret;
  }

  # _handlePI from line 1927
  function _handlePI($parser, $target, $data) {
    $this->_profBegin('_handlePI');
    $ret = parent::_handlePI($parser, $target, $data);
    $this->_profEnd('_handlePI');
    return $ret;
  }

  # _createSuperRoot from line 1941
  function _createSuperRoot() {
    $this->_profBegin('_createSuperRoot');
    $ret = parent::_createSuperRoot();
    $this->_profEnd('_createSuperRoot');
    return $ret;
  }

  # _internalAppendChild from line 1973
  function _internalAppendChild($stackParentIndex, $nodeName) {
    $this->_profBegin('_internalAppendChild');
    $ret = parent::_internalAppendChild($stackParentIndex, $nodeName);
    $this->_profEnd('_internalAppendChild');
    return $ret;
  }

  # reindexNodeTree from line 2063
  function reindexNodeTree() {
    $this->_profBegin('reindexNodeTree');
    $ret = parent::reindexNodeTree();
    $this->_profEnd('reindexNodeTree');
    return $ret;
  }

  # _generate_ids from line 2077
  function _generate_ids() {
    $this->_profBegin('_generate_ids');
    $ret = parent::_generate_ids();
    $this->_profEnd('_generate_ids');
    return $ret;
  }

  # _recursiveReindexNodeTree from line 2104
  function _recursiveReindexNodeTree($absoluteParentPath) {
    $this->_profBegin('_recursiveReindexNodeTree');
    $ret = parent::_recursiveReindexNodeTree($absoluteParentPath);
    $this->_profEnd('_recursiveReindexNodeTree');
    return $ret;
  }

  # &cloneNode from line 2197
  function &cloneNode($node, $recursive=FALSE) {
    $this->_profBegin('&cloneNode');
    $ret = &parent::cloneNode($node, $recursive);
    $this->_profEnd('&cloneNode');
    return $ret;
  }

  # __sleep from line 2232
  function __sleep() {
    $this->_profBegin('__sleep');
    $ret = parent::__sleep();
    $this->_profEnd('__sleep');
    return $ret;
  }

  # __wakeup from line 2247
  function __wakeup() {
    $this->_profBegin('__wakeup');
    $ret = parent::__wakeup();
    $this->_profEnd('__wakeup');
    return $ret;
  }

  # match from line 2272
  function match($xPathQuery, $baseXPath='') {
    $this->_profBegin('match');
    $ret = parent::match($xPathQuery, $baseXPath);
    $this->_profEnd('match');
    return $ret;
  }

  # evaluate from line 2336
  function evaluate($xPathQuery, $baseXPath='') {
    $this->_profBegin('evaluate');
    $ret = parent::evaluate($xPathQuery, $baseXPath);
    $this->_profEnd('evaluate');
    return $ret;
  }

  # _removeLiterals from line 2361
  function _removeLiterals($xPathQuery) {
    $this->_profBegin('_removeLiterals');
    $ret = parent::_removeLiterals($xPathQuery);
    $this->_profEnd('_removeLiterals');
    return $ret;
  }

  # _asLiteral from line 2396
  function _asLiteral($string) {
    $this->_profBegin('_asLiteral');
    $ret = parent::_asLiteral($string);
    $this->_profEnd('_asLiteral');
    return $ret;
  }

  # _addLiteral from line 2425
  function _addLiteral($string) {
    $this->_profBegin('_addLiteral');
    $ret = parent::_addLiteral($string);
    $this->_profEnd('_addLiteral');
    return $ret;
  }

  # _GetOperator from line 2452
  function _GetOperator($xPathQuery) {
    $this->_profBegin('_GetOperator');
    $ret = parent::_GetOperator($xPathQuery);
    $this->_profEnd('_GetOperator');
    return $ret;
  }

  # _evaluatePrimaryExpr from line 2601
  function _evaluatePrimaryExpr($xPathQuery, $context, &$result) {
    $this->_profBegin('_evaluatePrimaryExpr');
    $ret = parent::_evaluatePrimaryExpr($xPathQuery, $context, &$result);
    $this->_profEnd('_evaluatePrimaryExpr');
    return $ret;
  }

  # _evaluateExpr from line 2765
  function _evaluateExpr($xPathQuery, $context) {
    $this->_profBegin('_evaluateExpr');
    $ret = parent::_evaluateExpr($xPathQuery, $context);
    $this->_profEnd('_evaluateExpr');
    return $ret;
  }

  # _evaluateOperator from line 3019
  function _evaluateOperator($left, $operator, $right, $operatorType, $context) {
    $this->_profBegin('_evaluateOperator');
    $ret = parent::_evaluateOperator($left, $operator, $right, $operatorType, $context);
    $this->_profEnd('_evaluateOperator');
    return $ret;
  }

  # _evaluatePathExpr from line 3208
  function _evaluatePathExpr($PathExpr, $context) {
    $this->_profBegin('_evaluatePathExpr');
    $ret = parent::_evaluatePathExpr($PathExpr, $context);
    $this->_profEnd('_evaluatePathExpr');
    return $ret;
  }

  # _sortByDocOrder from line 3280
  function _sortByDocOrder($xPathSet) {
    $this->_profBegin('_sortByDocOrder');
    $ret = parent::_sortByDocOrder($xPathSet);
    $this->_profEnd('_sortByDocOrder');
    return $ret;
  }

  # _evaluateStep from line 3379
  function _evaluateStep($steps, $context) {
    $this->_profBegin('_evaluateStep');
    $ret = parent::_evaluateStep($steps, $context);
    $this->_profEnd('_evaluateStep');
    return $ret;
  }

  # _checkPredicates from line 3478
  function _checkPredicates($xPathSet, $predicates) {
    $this->_profBegin('_checkPredicates');
    $ret = parent::_checkPredicates($xPathSet, $predicates);
    $this->_profEnd('_checkPredicates');
    return $ret;
  }

  # _evaluateFunction from line 3575
  function _evaluateFunction($function, $arguments, $context) {
    $this->_profBegin('_evaluateFunction');
    $ret = parent::_evaluateFunction($function, $arguments, $context);
    $this->_profEnd('_evaluateFunction');
    return $ret;
  }

  # _checkNodeTest from line 3633
  function _checkNodeTest($contextPath, $nodeTest) {
    $this->_profBegin('_checkNodeTest');
    $ret = parent::_checkNodeTest($contextPath, $nodeTest);
    $this->_profEnd('_checkNodeTest');
    return $ret;
  }

  # _getAxis from line 3735
  function _getAxis($step) {
    $this->_profBegin('_getAxis');
    $ret = parent::_getAxis($step);
    $this->_profEnd('_getAxis');
    return $ret;
  }

  # _handleAxis_child from line 3931
  function _handleAxis_child($axis, $contextPath) {
    $this->_profBegin('_handleAxis_child');
    $ret = parent::_handleAxis_child($axis, $contextPath);
    $this->_profEnd('_handleAxis_child');
    return $ret;
  }

  # _handleAxis_parent from line 3978
  function _handleAxis_parent($axis, $contextPath) {
    $this->_profBegin('_handleAxis_parent');
    $ret = parent::_handleAxis_parent($axis, $contextPath);
    $this->_profEnd('_handleAxis_parent');
    return $ret;
  }

  # _handleAxis_attribute from line 3997
  function _handleAxis_attribute($axis, $contextPath) {
    $this->_profBegin('_handleAxis_attribute');
    $ret = parent::_handleAxis_attribute($axis, $contextPath);
    $this->_profEnd('_handleAxis_attribute');
    return $ret;
  }

  # _handleAxis_self from line 4022
  function _handleAxis_self($axis, $contextPath) {
    $this->_profBegin('_handleAxis_self');
    $ret = parent::_handleAxis_self($axis, $contextPath);
    $this->_profEnd('_handleAxis_self');
    return $ret;
  }

  # _handleAxis_descendant from line 4040
  function _handleAxis_descendant($axis, $contextPath) {
    $this->_profBegin('_handleAxis_descendant');
    $ret = parent::_handleAxis_descendant($axis, $contextPath);
    $this->_profEnd('_handleAxis_descendant');
    return $ret;
  }

  # _handleAxis_ancestor from line 4068
  function _handleAxis_ancestor($axis, $contextPath) {
    $this->_profBegin('_handleAxis_ancestor');
    $ret = parent::_handleAxis_ancestor($axis, $contextPath);
    $this->_profEnd('_handleAxis_ancestor');
    return $ret;
  }

  # _handleAxis_namespace from line 4093
  function _handleAxis_namespace($axis, $contextPath) {
    $this->_profBegin('_handleAxis_namespace');
    $ret = parent::_handleAxis_namespace($axis, $contextPath);
    $this->_profEnd('_handleAxis_namespace');
    return $ret;
  }

  # _handleAxis_following from line 4105
  function _handleAxis_following($axis, $contextPath) {
    $this->_profBegin('_handleAxis_following');
    $ret = parent::_handleAxis_following($axis, $contextPath);
    $this->_profEnd('_handleAxis_following');
    return $ret;
  }

  # _handleAxis_preceding from line 4140
  function _handleAxis_preceding($axis, $contextPath) {
    $this->_profBegin('_handleAxis_preceding');
    $ret = parent::_handleAxis_preceding($axis, $contextPath);
    $this->_profEnd('_handleAxis_preceding');
    return $ret;
  }

  # _handleAxis_following_sibling from line 4170
  function _handleAxis_following_sibling($axis, $contextPath) {
    $this->_profBegin('_handleAxis_following_sibling');
    $ret = parent::_handleAxis_following_sibling($axis, $contextPath);
    $this->_profEnd('_handleAxis_following_sibling');
    return $ret;
  }

  # _handleAxis_preceding_sibling from line 4206
  function _handleAxis_preceding_sibling($axis, $contextPath) {
    $this->_profBegin('_handleAxis_preceding_sibling');
    $ret = parent::_handleAxis_preceding_sibling($axis, $contextPath);
    $this->_profEnd('_handleAxis_preceding_sibling');
    return $ret;
  }

  # _handleAxis_descendant_or_self from line 4236
  function _handleAxis_descendant_or_self($axis, $contextPath) {
    $this->_profBegin('_handleAxis_descendant_or_self');
    $ret = parent::_handleAxis_descendant_or_self($axis, $contextPath);
    $this->_profEnd('_handleAxis_descendant_or_self');
    return $ret;
  }

  # _handleAxis_ancestor_or_self from line 4257
  function _handleAxis_ancestor_or_self( $axis, $contextPath) {
    $this->_profBegin('_handleAxis_ancestor_or_self');
    $ret = parent::_handleAxis_ancestor_or_self($axis, $contextPath);
    $this->_profEnd('_handleAxis_ancestor_or_self');
    return $ret;
  }

  # _handleFunction_last from line 4281
  function _handleFunction_last($arguments, $context) {
    $this->_profBegin('_handleFunction_last');
    $ret = parent::_handleFunction_last($arguments, $context);
    $this->_profEnd('_handleFunction_last');
    return $ret;
  }

  # _handleFunction_position from line 4293
  function _handleFunction_position($arguments, $context) {
    $this->_profBegin('_handleFunction_position');
    $ret = parent::_handleFunction_position($arguments, $context);
    $this->_profEnd('_handleFunction_position');
    return $ret;
  }

  # _handleFunction_count from line 4305
  function _handleFunction_count($arguments, $context) {
    $this->_profBegin('_handleFunction_count');
    $ret = parent::_handleFunction_count($arguments, $context);
    $this->_profEnd('_handleFunction_count');
    return $ret;
  }

  # _handleFunction_id from line 4318
  function _handleFunction_id($arguments, $context) {
    $this->_profBegin('_handleFunction_id');
    $ret = parent::_handleFunction_id($arguments, $context);
    $this->_profEnd('_handleFunction_id');
    return $ret;
  }

  # _handleFunction_name from line 4343
  function _handleFunction_name($arguments, $context) {
    $this->_profBegin('_handleFunction_name');
    $ret = parent::_handleFunction_name($arguments, $context);
    $this->_profEnd('_handleFunction_name');
    return $ret;
  }

  # _handleFunction_string from line 4368
  function _handleFunction_string($arguments, $context) {
    $this->_profBegin('_handleFunction_string');
    $ret = parent::_handleFunction_string($arguments, $context);
    $this->_profEnd('_handleFunction_string');
    return $ret;
  }

  # _handleFunction_concat from line 4425
  function _handleFunction_concat($arguments, $context) {
    $this->_profBegin('_handleFunction_concat');
    $ret = parent::_handleFunction_concat($arguments, $context);
    $this->_profEnd('_handleFunction_concat');
    return $ret;
  }

  # _handleFunction_starts_with from line 4447
  function _handleFunction_starts_with($arguments, $context) {
    $this->_profBegin('_handleFunction_starts_with');
    $ret = parent::_handleFunction_starts_with($arguments, $context);
    $this->_profEnd('_handleFunction_starts_with');
    return $ret;
  }

  # _handleFunction_contains from line 4466
  function _handleFunction_contains($arguments, $context) {
    $this->_profBegin('_handleFunction_contains');
    $ret = parent::_handleFunction_contains($arguments, $context);
    $this->_profEnd('_handleFunction_contains');
    return $ret;
  }

  # _handleFunction_substring_before from line 4494
  function _handleFunction_substring_before($arguments, $context) {
    $this->_profBegin('_handleFunction_substring_before');
    $ret = parent::_handleFunction_substring_before($arguments, $context);
    $this->_profEnd('_handleFunction_substring_before');
    return $ret;
  }

  # _handleFunction_substring_after from line 4513
  function _handleFunction_substring_after($arguments, $context) {
    $this->_profBegin('_handleFunction_substring_after');
    $ret = parent::_handleFunction_substring_after($arguments, $context);
    $this->_profEnd('_handleFunction_substring_after');
    return $ret;
  }

  # _handleFunction_substring from line 4532
  function _handleFunction_substring($arguments, $context) {
    $this->_profBegin('_handleFunction_substring');
    $ret = parent::_handleFunction_substring($arguments, $context);
    $this->_profEnd('_handleFunction_substring');
    return $ret;
  }

  # _handleFunction_string_length from line 4557
  function _handleFunction_string_length($arguments, $context) {
    $this->_profBegin('_handleFunction_string_length');
    $ret = parent::_handleFunction_string_length($arguments, $context);
    $this->_profEnd('_handleFunction_string_length');
    return $ret;
  }

  # _handleFunction_normalize_space from line 4578
  function _handleFunction_normalize_space($arguments, $context) {
    $this->_profBegin('_handleFunction_normalize_space');
    $ret = parent::_handleFunction_normalize_space($arguments, $context);
    $this->_profEnd('_handleFunction_normalize_space');
    return $ret;
  }

  # _handleFunction_translate from line 4596
  function _handleFunction_translate($arguments, $context) {
    $this->_profBegin('_handleFunction_translate');
    $ret = parent::_handleFunction_translate($arguments, $context);
    $this->_profEnd('_handleFunction_translate');
    return $ret;
  }

  # _handleFunction_boolean from line 4618
  function _handleFunction_boolean($arguments, $context) {
    $this->_profBegin('_handleFunction_boolean');
    $ret = parent::_handleFunction_boolean($arguments, $context);
    $this->_profEnd('_handleFunction_boolean');
    return $ret;
  }

  # _handleFunction_not from line 4666
  function _handleFunction_not($arguments, $context) {
    $this->_profBegin('_handleFunction_not');
    $ret = parent::_handleFunction_not($arguments, $context);
    $this->_profEnd('_handleFunction_not');
    return $ret;
  }

  # _handleFunction_true from line 4681
  function _handleFunction_true($arguments, $context) {
    $this->_profBegin('_handleFunction_true');
    $ret = parent::_handleFunction_true($arguments, $context);
    $this->_profEnd('_handleFunction_true');
    return $ret;
  }

  # _handleFunction_false from line 4693
  function _handleFunction_false($arguments, $context) {
    $this->_profBegin('_handleFunction_false');
    $ret = parent::_handleFunction_false($arguments, $context);
    $this->_profEnd('_handleFunction_false');
    return $ret;
  }

  # _handleFunction_lang from line 4705
  function _handleFunction_lang($arguments, $context) {
    $this->_profBegin('_handleFunction_lang');
    $ret = parent::_handleFunction_lang($arguments, $context);
    $this->_profEnd('_handleFunction_lang');
    return $ret;
  }

  # _handleFunction_number from line 4729
  function _handleFunction_number($arguments, $context) {
    $this->_profBegin('_handleFunction_number');
    $ret = parent::_handleFunction_number($arguments, $context);
    $this->_profEnd('_handleFunction_number');
    return $ret;
  }

  # _handleFunction_sum from line 4779
  function _handleFunction_sum($arguments, $context) {
    $this->_profBegin('_handleFunction_sum');
    $ret = parent::_handleFunction_sum($arguments, $context);
    $this->_profEnd('_handleFunction_sum');
    return $ret;
  }

  # _handleFunction_floor from line 4807
  function _handleFunction_floor($arguments, $context) {
    $this->_profBegin('_handleFunction_floor');
    $ret = parent::_handleFunction_floor($arguments, $context);
    $this->_profEnd('_handleFunction_floor');
    return $ret;
  }

  # _handleFunction_ceiling from line 4823
  function _handleFunction_ceiling($arguments, $context) {
    $this->_profBegin('_handleFunction_ceiling');
    $ret = parent::_handleFunction_ceiling($arguments, $context);
    $this->_profEnd('_handleFunction_ceiling');
    return $ret;
  }

  # _handleFunction_round from line 4839
  function _handleFunction_round($arguments, $context) {
    $this->_profBegin('_handleFunction_round');
    $ret = parent::_handleFunction_round($arguments, $context);
    $this->_profEnd('_handleFunction_round');
    return $ret;
  }

  # _handleFunction_x_lower from line 4862
  function _handleFunction_x_lower($arguments, $context) {
    $this->_profBegin('_handleFunction_x_lower');
    $ret = parent::_handleFunction_x_lower($arguments, $context);
    $this->_profEnd('_handleFunction_x_lower');
    return $ret;
  }

  # _handleFunction_x_upper from line 4880
  function _handleFunction_x_upper($arguments, $context) {
    $this->_profBegin('_handleFunction_x_upper');
    $ret = parent::_handleFunction_x_upper($arguments, $context);
    $this->_profEnd('_handleFunction_x_upper');
    return $ret;
  }

  # _handleFunction_generate_id from line 4914
  function _handleFunction_generate_id($arguments, $context) {
    $this->_profBegin('_handleFunction_generate_id');
    $ret = parent::_handleFunction_generate_id($arguments, $context);
    $this->_profEnd('_handleFunction_generate_id');
    return $ret;
  }

  # decodeEntities from line 4956
  function decodeEntities($encodedData, $reverse=FALSE) {
    $this->_profBegin('decodeEntities');
    $ret = parent::decodeEntities($encodedData, $reverse);
    $this->_profEnd('decodeEntities');
    return $ret;
  }

  # equalNodes from line 4996
  function equalNodes($node1, $node2) {
    $this->_profBegin('equalNodes');
    $ret = parent::equalNodes($node1, $node2);
    $this->_profEnd('equalNodes');
    return $ret;
  }

  # getNodePath from line 5008
  function getNodePath($node) {
    $this->_profBegin('getNodePath');
    $ret = parent::getNodePath($node);
    $this->_profEnd('getNodePath');
    return $ret;
  }

  # getParentXPath from line 5035
  function getParentXPath($absoluteXPath) {
    $this->_profBegin('getParentXPath');
    $ret = parent::getParentXPath($absoluteXPath);
    $this->_profEnd('getParentXPath');
    return $ret;
  }

  # hasChildNodes from line 5050
  function hasChildNodes($absoluteXPath) {
    $this->_profBegin('hasChildNodes');
    $ret = parent::hasChildNodes($absoluteXPath);
    $this->_profEnd('hasChildNodes');
    return $ret;
  }

  # _translateAmpersand from line 5096
  function _translateAmpersand($xmlSource, $reverse=FALSE) {
    $this->_profBegin('_translateAmpersand');
    $ret = parent::_translateAmpersand($xmlSource, $reverse);
    $this->_profEnd('_translateAmpersand');
    return $ret;
  }

  #############################################
  # Adding functions for base class XPathBase

  # XPathBase_Prof from line 196
  function XPathBase_Prof() {
    $this->_profBegin('XPathBase');
    $ret = parent::XPathBase();
    $this->_profEnd('XPathBase');
    return $ret;
  }

  # reset from line 235
  function reset() {
    $this->_profBegin('reset');
    $ret = parent::reset();
    $this->_profEnd('reset');
    return $ret;
  }

  # _bracketsCheck from line 249
  function _bracketsCheck($term) {
    $this->_profBegin('_bracketsCheck');
    $ret = parent::_bracketsCheck($term);
    $this->_profEnd('_bracketsCheck');
    return $ret;
  }

  # _searchString from line 306
  function _searchString($term, $expression) {
    $this->_profBegin('_searchString');
    $ret = parent::_searchString($term, $expression);
    $this->_profEnd('_searchString');
    return $ret;
  }

  # _bracketExplode from line 338
  function _bracketExplode($separator, $term) {
    $this->_profBegin('_bracketExplode');
    $ret = parent::_bracketExplode($separator, $term);
    $this->_profEnd('_bracketExplode');
    return $ret;
  }

  # _getEndGroups from line 416
  function _getEndGroups($string, $open='[', $close=']') {
    $this->_profBegin('_getEndGroups');
    $ret = parent::_getEndGroups($string, $open, $close);
    $this->_profEnd('_getEndGroups');
    return $ret;
  }

  # _prestr from line 502
  function _prestr(&$string, $delimiter, $offset=0) {
    $this->_profBegin('_prestr');
    $ret = parent::_prestr(&$string, $delimiter, $offset);
    $this->_profEnd('_prestr');
    return $ret;
  }

  # _afterstr from line 520
  function _afterstr($string, $delimiter, $offset=0) {
    $this->_profBegin('_afterstr');
    $ret = parent::_afterstr($string, $delimiter, $offset);
    $this->_profEnd('_afterstr');
    return $ret;
  }

  # setVerbose from line 539
  function setVerbose($levelOfVerbosity = 1) {
    $this->_profBegin('setVerbose');
    $ret = parent::setVerbose($levelOfVerbosity);
    $this->_profEnd('setVerbose');
    return $ret;
  }

  # getLastError from line 558
  function getLastError() {
    $this->_profBegin('getLastError');
    $ret = parent::getLastError();
    $this->_profEnd('getLastError');
    return $ret;
  }

  # _setLastError from line 577
  function _setLastError($message='', $line='-', $file='-') {
    $this->_profBegin('_setLastError');
    $ret = parent::_setLastError($message, $line, $file);
    $this->_profEnd('_setLastError');
    return $ret;
  }

  # _displayError from line 594
  function _displayError($message, $lineNumber='-', $file='-', $terminate=TRUE) {
    $this->_profBegin('_displayError');
    $ret = parent::_displayError($message, $lineNumber, $file, $terminate);
    $this->_profEnd('_displayError');
    return $ret;
  }

  # _displayMessage from line 611
  function _displayMessage($message, $lineNumber='-', $file='-') {
    $this->_profBegin('_displayMessage');
    $ret = parent::_displayMessage($message, $lineNumber, $file);
    $this->_profEnd('_displayMessage');
    return $ret;
  }

  # _beginDebugFunction from line 630
  function _beginDebugFunction($functionName) {
    $this->_profBegin('_beginDebugFunction');
    $ret = parent::_beginDebugFunction($functionName);
    $this->_profEnd('_beginDebugFunction');
    return $ret;
  }

  # _closeDebugFunction from line 656
  function _closeDebugFunction($aStartTime, $returnValue = "") {
    $this->_profBegin('_closeDebugFunction');
    $ret = parent::_closeDebugFunction($aStartTime, $returnValue);
    $this->_profEnd('_closeDebugFunction');
    return $ret;
  }

  # _profileFunction from line 681
  function _profileFunction($aStartTime, $alertString) {
    $this->_profBegin('_profileFunction');
    $ret = parent::_profileFunction($aStartTime, $alertString);
    $this->_profEnd('_profileFunction');
    return $ret;
  }

  # _printContext from line 694
  function _printContext($context) {
    $this->_profBegin('_printContext');
    $ret = parent::_printContext($context);
    $this->_profEnd('_printContext');
    return $ret;
  }

  # _treeDump from line 706
  function _treeDump($node, $indent = '') {
    $this->_profBegin('_treeDump');
    $ret = parent::_treeDump($node, $indent);
    $this->_profEnd('_treeDump');
    return $ret;
  }

} // END Class 
?>