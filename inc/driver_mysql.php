<?php

//File created to be full compatible the select instructions with adoDB
//Created by Armando Urquiola Cabrera, Julio 2005.


//that style was copied from Biocase (Markus Döring)....
$syntax = array();
# full reference to an attribute with its table-alias. #1=table, #2=attribute
$syntax['attribute'] = '#1.#2';
# reference to a table alone
$syntax['table'] = '#1';
# reference to a field alone
$syntax['field'] = '#1';
# represent a numeric value
$syntax['number'] = '#1';
# represent a string value
$syntax['string'] = "'#1'";
# the wildcard character
$syntax['wildcard'] = '%';
# brackets used for grouping statements
$syntax['brackets'] = '(#1)';
# a select statement. #1=select part, #2=from part
$syntax['select'] = 'SELECT #1 FROM #2';
# a select statement. #1=select part, #2=from(+where+orderby) part, #3=limit/top number
	$syntax['select-limit'] = 'SELECT DISTINCT #1 FROM #2 LIMIT #3';
	$syntax['select-distinct'] = 'SELECT DISTINCT #1 FROM #2';
# a count function. #1=list of count attributes
$syntax['count'] = 'COUNT(#1)';
# the where clause of a select statement. #1=select+from part, #2=where part
$syntax['where'] = '#1 WHERE #2';
# the order by clause of a select statement. #1=select+from(+where) part, #2=order by part
	$syntax['order-by'] = '#1 ORDER BY #2';
# the left-join syntax of a DBMS. #1=table-alias_1, #2=table-alias_2, #3=comparison/logical operator string joining the tables
$syntax['join'] = '#1 LEFT JOIN #2 ON #3';
# the left-join syntax of a DBMS. #1=table-alias_1, #2=table-alias_2, #3=comparison/logical operator string joining the tables
$syntax['full-join'] = '#1, #2';
# syntax to create an alias of a table. #1=real table name, #2=alias name
$syntax['alias'] = '#1 AS #2';
	$syntax['concatenation-string'] = '||';
	
# LOGICAL OPERATORS
# -----------------
$unaryLOP  =array("not" => "NOT");	
$binaryLOP =array("and" => "AND", "or" => "OR");

# COMPARISON OPERATORS
# --------------------
$unaryCOP	=array('isnull'=>"IS NULL", 'isnotnull'=>"IS NOT NULL");
$binaryCOP   	=array('equals'=>"=", 'notequals'=>"<>", 'like'=>"LIKE", 'lessthan'=>"<", 'lessthanorequals'=>"<=", 'greaterthan'=>">", 'greaterthanorequals'=>">=");
$multipleCOP 	=array('in'=>"IN");
# only for output. this is the real spelling which is case sensitive.
$COPout		=array('isnull'=>"isNull", 'isnotnull'=>"isNotNull", 'equals'=>"equals", 'notequals'=>"notEquals", 'like'=>"like", 'lessthan'=>"lessThan", 'lessthanorequals'=>"lessThanOrEquals", 'greaterthan'=>"greaterThan", 'greaterthanorequals'=>"greaterThanOrEquals", 'in'=>"in");

# ESCAPE CHARACTERS
# -----------------
# characters that need to be escaped in SQL
$escape = array();
$escape["'"] = "''";

	
//specification to firebird

/* SELECT
    [ALL | DISTINCT | DISTINCTROW ]
      [HIGH_PRIORITY]
      [STRAIGHT_JOIN]
      [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
      [SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
    select_expr, ...
    [INTO OUTFILE 'file_name' export_options
      | INTO DUMPFILE 'file_name']
    [FROM table_references
      [WHERE where_definition]
      [GROUP BY {col_name | expr | position}
        [ASC | DESC], ... [WITH ROLLUP]]
      [HAVING where_definition]
      [ORDER BY {col_name | expr | position}
        [ASC | DESC] , ...]
      [LIMIT {[offset,] row_count | row_count OFFSET offset}]
      [PROCEDURE procedure_name(argument_list)]
      [FOR UPDATE | LOCK IN SHARE MODE]]
*/

$syntax['alias'] = '#1 "#2"';
$syntax['alias-char'] = ""; //for example: AS in firebird
$syntax['attribute'] = '#1.#2';
$syntax['table'] = '#1';
$syntax['field'] = '#1';
$syntax['concatenation-string'] = 'concat(#1, #2)';


return;
// use the Firebird 'SELECT FIRST x SKIP x' syntax (fastest, but available
// only with the firebird server);

// use the Interbase6.5 'ROWS x TO y' syntax (untestet, because I don't have
// access to an ib65 server  *** please report any errors or success with this ***)


?>


