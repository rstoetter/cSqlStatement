<?php


/*

TODO: IsJoin( $table_name )

*/

if ( (int) phpversion( ) < 5 ) die("Sorry, The class cSqlStatement is written for PHP 5");

define( '_SQL_GENERATOR_QUERY_TYPE_SELECT', 'SELECT' );
define( '_SQL_GENERATOR_QUERY_TYPE_INSERT', 'INSERT' );
define( '_SQL_GENERATOR_QUERY_TYPE_UPDATE', 'UPDATE' );
define( '_SQL_GENERATOR_QUERY_TYPE_DELETE', 'DELETE' );

namespace rstoetter\cSqlStatement;


/**
  *
  * The class cSqlStatement helps to construct SQL statements. The namespace is rstoetter\cSqlStatement.
  *
  * @author Rainer Stötter
  * @copyright 2016-2017 Rainer Stötter
  * @license MIT
  * @version =1.0
  *
  */

class cSqlStatement {

	/**
	 * Turns error messages on and off, useful for debugging
	 *
	 * @var boolean
	 */
	 
	private $m_debug= true;

	/**
	 * Holds the query type (select, insert, update, delete)
	 * Only SELECT is supported yet
	 *
	 * @var string
	 */
	 
	 
	public $m_query_type = "SELECT"; // Default Query Type

	/**
	 * the part of the select statement between select and field list
	 *
	 * @var string
	 */

	public $m_extra = '';		// Extra-Anteil zwischen Select und Feldliste
	
	/**
	 * the query 
	 *
	 * @var string
	 */	

	protected $m_query = '';
	
	/**
	 *
	 * @var string the name of the table
	 */	
	
	
	protected $m_table = '';
	
	/**
	 *
	 * @var string the group by clause of the query
	 */	
	
	
	protected $m_group_by = '';
	
	/**
	 *
	 * @var string the having by clause of the query
	 */	
	
	
	protected $m_having = '';
	
	
	/**
	 *
	 * @var string the order by clause of the query
	 */	
	
	
	protected $m_order_by = '';
	
	/**
	 *
	 * @var string the where clause of the query
	 */	
	
	//protected $m_limit = '';
	protected $m_where = '';

	/**
	 *
	 * @var string the line counter of the limit clause of the query
	 */	
	
	
	public $m_limit_count = '';
	
	/**
	 *
	 * @var string the from part of the limit clause of the query
	 */	
	
	
	public $m_limit_from = '';

	/**
	 * 
	 * @var array array with the column names, dimensions must match with the values array
	 */
	protected $m_a_columns = array();
	
	
	/**
	 * 
	 * @var array array with the field values, dimensions must match with the columns array
	 */
	
	protected $m_a_values  = array();
	
	
    /**
      *
      * The method Reset( ) resets the internal state of the instance
      *
      * Example:
      *
      *
      * @param string the query type ( select, insert, update, delete )
      *
      */    	

	public function Reset( $query_type ) {

	    $this->m_queryType = strtoupper( $query_type);

	    $this->m_extra = '';		// Extra-Anteil zwischen Select und Feldliste
	    $this->m_query = '';
	    $this->m_table = '';
	    $this->m_group_by = '';
	    $this->m_having = '';
	    $this->m_order_by = '';
	    $this->m_where = '';
	    $this->m_limit_count = '';
	    $this->m_limit_from = '';
	    $this->m_a_columns = array( );
	    $this->m_a_values  = array( );


	}

	/**
	 *
	 * the constructor for an object of type cSQL, which defines the query type
	 *
	 * @param string query_type the query type ( select, insert, delete, update )
	 *
	 */
	 
	public function __construct( $query_type) {

		if( ! strlen( $query_type ) ) {
		
            throw new \Exception("\n constructor of cSQL without query type");
		
		}

		$this->m_queryType = strtoupper( $query_type);

	}

	/**
	 * the destructor of object of type cSQL
	 *
	 */
	
	
	public function __destruct( ) {


	}
	
    /**
      *
      * The method GetLimit( ) returns the LIMIT clause of the query
      *
      * Example:
      *
      *
      * @return string the LIMIT clause
      *
      */    	
	


	public function GetLimit( ) {

	    return $this->m_limit_from . ( $this->m_limit_count ? ',' . $this->m_limit_count  : '');

	}	// function GetLimit( )
	
	
    /**
      *
      * The method GetLimits( ) returns the from and count part of the LIMIT clause of the query
      *
      * Example:
      *
      *
      * @param string the returned from part of the query 
      * @param string the returned line count part of the query
      *
      */    	
	

	public function GetLimits( &$from, &$count ) {

	    $from = $this->m_limit_from;

	    $count = $this->m_limit_count;

	}	// function GetLimits( )

	/**
	 * The method SetTable( ) defines the table to work on
	 *
	 * @param string $tableName the name of the table
	 */
	 
	public function SetTable($tableName) {
		$this->m_table = $tableName;
	}
	
	/**
	 * The method GetTable( ) returns the name of the table we are working on
	 *
	 * @return string the name of the table
	 *
	 */
	

	public function GetTable( ) {
		return $this->m_table;	// TODO: Feld liefern, da hier mehrere Tabellennamen oder Joins stehen können
	}

	/**
	 * The method SetExtra( ) sets the extra part beween SELECT and the field list
	 *
	 * @param string the extra part of the query
	 *
	 */


	public function SetExtra( $extra ) {
		$this->m_extra = $extra;
	}

	/**
	 * Adds a column to the list in m_a_columns
	 *
	 * @param string the name of the column
	 */
	 
	public function AddColumn($col_name) {
	
		$this->m_a_columns[] = $col_name;


	}	// function AddColumn( )
	
	/**
	 * resets the values in m_a_values (empty the array )
	 *
	 */
	

	public function ResetValues( ) {
		$this->m_a_values = array();


	}	// function ResetValues( )
	
	/**
	 * resets the column names in m_a_columns (empty the array )
	 *
	 */
	

	public function ResetColumns( ) {
		$this->m_a_columns = array( );


	}	// function ResetColumns( )
	
	
	/**
	 * The method RemoveColumn( ) removes the column with the name $col from the list of columns in $m_a_columns
	 *
	 * Example:
	 *
	 *
	 * @param string the name of the column
	 * 
	 * @return bool true, if $col was found and removed
	 *
	 */
	
	
	

	public function RemoveColumn( $col ) {

	    for ( $i = 0; $i < count( $this->m_a_columns ); $i++ ) {

		if ( $this->m_a_columns[ $i ] == $col ) {

		    unset( $this->m_a_columns[ $i ] );
		    $this->m_a_columns = array_values( $this->m_a_columns );

		    return true;

		}

	    }

	    return false;

	}	// function RemoveColumn( )


	/**
	 * The method GetFieldCount( ) returns the number of managed field names
	 *
	 * Example:
	 *
	 *
	 * @return int the number of managed field names in $m_a_columns
	 *
	 */
	
	
	public function GetFieldCount( ) {

	    return count( $this->m_a_columns );

	}	// function GetFieldCount( )

	
	/**
	 * The method GetFields( ) returns in $ary an array with the managed field names
	 *
	 * Example:
	 *
	 *
	 * @param array the array with the managed field names
	 * 
	 */
	

	public function GetFields( &$ary ) {

	    $ary = array( );

	    for ( $i = 0; $i < count( $this->m_a_columns ); $i ++ ) {

            $ary[]= $this->m_a_columns[ $i ];

	    }

	}	// function GetFields( )
	
	
	/**
	 * The method GetField( ) returns the the managed field name with the index $index
	 *
	 * Example:
	 *
	 * @param int the index of the desired field name
	 *
	 * @return string the managed field name with the index $index
	 * 
	 */
	

	public function GetField( $index ) {

	    return $this->m_a_columns[ $index ];

	}	// function GetField( )
	
	/**
	 * The method GetFieldName( ) returns the managed field name with the index $index
	 * it is an alias for GetField( )
	 *
	 * Example:
	 *
	 * @param int the index of the desired field name
	 *
	 * @return the managed field name with the index $index
	 * 
	 */
	
	

	public function GetFieldName( $index ) {

	    return $this->GetField( $index );;

	}	// function GetField( )
	

	/**
	 * The method SetColumn( ) resets the column names and values and sets the first column name $column_name
	 *
	 * Example:
	 *
	 * @param string the name of the first column
	 *
	 */
	
	
	public function SetColumn($col_name) {

		$this->m_a_columns = array( );
		$this->m_a_values = array( );

		$this->m_a_columns[] = $col_name;
	}

	/**
	 * Adds a string value to the list
	 *
	 * @param string $value
	 */
	 
	public function AddValue( $value ) {
	
        $value = ( get_magic_quotes_gpc( ) ) ? $value : addslashes($value);
        
		$this->m_a_values[] = "'".$value."'";
		
	}

	/**
	 * Sets the where clause to $where
	 *
	 * @param string the where clause
	 */
	 
	public function SetWhere( $where ) {
		$this->m_where = $where;
	}
	
	/**
	 * Gets the where clause 
	 *
	 * @return string the where clause
	 */
	

	public function GetWhere(  ) {
		return $this->m_where;
	}
	
	
	/**
	 * Sets the group by clause to $group_by
	 *
	 * @param string the where clause
	 */
	

	public function SetGroupBy($group_by) {

		$this->m_group_by = $group_by;
	}

	
	/**
	 * Gets the group by clause 
	 *
	 * @return string the group by clause
	 */
	

	public function GetGroupBy(  ) {
		return $this->m_group_by;
	}
	
	/**
	 * Adds $group_by to the group by clause 
	 *
	 * @param string the group by clause to add
	 */
	
	
	
	public function AddGroupBy($group_by) {

		if ( strlen( $this->m_group_by ) ) $this->m_group_by .= ' , ';
		$this->m_group_by .= $group_by;
	}
	
	
	/**
	 * Sets the having clause to $having
	 *
	 * @param string the having clause
	 */
	

	public function SetHaving($having) {
		$this->m_having = $having;
	}

	
	/**
	 * Gets the having clause 
	 *
	 * @return string the having clause
	 */
	

	public function GetHaving(  ) {
		return $this->m_having;
	}
	
	
	/**
	 * Sets the limit clause to the values
	 *
	 * @param string the from-part
	 * @param string the count-part
	 *
	 */
	
	
	
	public function SetLimits( $from, $count) {

		$this->m_limit_from = $from;
		$this->m_limit_count = $count;

	}	// function SetLimits( )
	
	
	/**
	 * Gets the order by clause 
	 *
	 * @return string the order by clause
	 */
	
	

	public function GetOrderBy( ) {

	    return $this->m_order_by;

	}	// function GetOrderBy( )

	/**
	 * Sets the order by clause to $order_by
	 *
	 * @param string the having clause
	 */

	public function SetOrderBy($order_by) {

		$this->m_order_by = $order_by;

	}	// function SetOrderBy( )
	
	/**
	 * Adds $order_by to the order by clause 
	 *
	 * @param string the order by clause to add
	 */
	

	public function AddOrderBy( $order_by ) {

		if ( strlen( $this->m_order_by ) ) $this->m_order_by .= ' , ';

		$this->m_order_by .= $order_by;

	}	// function AddOrderBy( )

	/**
	 * Sets the whole query
	 *
	 * @param string the new query
	 *
	 */


	public function SetQuery($query) {
		$this->m_query = $query;
	}
	
	/**
	 * Sets the debug mode
	 *
	 * @param bool whether errors should be displayed
	 *
	 */	

	public function DisplayErrors( $display_errors ) {
		$this->m_debug= $display_errors;
	}

	/**
	 *
	 *  display an error message
	 *
	 * @param string the message to display 
	 *
	 */
	protected function DisplayError($message) {
		if ($this->m_debug) {
			echo "<font size='10' face='arial' color='red'>\n";
			echo "<p>cSqlStatement v." . self::VERSION . "</p>";
			echo "</font>";
			echo "<font size='8' face='arial' color='yellow>\n";
			echo "<p>$message</p>";
			echo "</font>";
		}
	}




	/**
	 * Generates and returns the query as a string
	 *
	 * @param bool whether to print the statement or not - defaults to false
	 * @return string the generated query or an empty string
	 *
	 */
	 
	public function GetQuery( $display_statement = false ) {
	
		if (empty($this->m_table) and ($this->m_queryType != "QUERY")) { $this->DisplayError("Error - No table selected"); return ''; };

		$sql = '';
		
		switch ($this->m_queryType) {
			case "SELECT":
				$sql = 'SELECT ';
				$sql.= ' ' . $this->m_extra . ' ';
				$sql.= implode(', ', $this->m_a_columns);

				$sql.=" FROM {$this->m_table}";

				if (strlen($this->m_where))   $sql.= " WHERE $this->m_where";
				if (strlen($this->m_group_by)) $sql.= " GROUP BY $this->m_group_by";
				if (strlen($this->m_having))  $sql.= " HAVING $this->m_having";
				if (strlen($this->m_order_by)) $sql.= " ORDER BY $this->m_order_by";
				
				if (
				      (strlen( trim( $this->m_limit_from ) ) ) &&
				      (strlen( trim( $this->m_limit_count ) ) ) &&
				      ( intval( $this->m_limit_count ) > 0 ) )  {
				    $sql.= " LIMIT {$this->m_limit_from}, {$this->m_limit_count}";
				}  elseif ( (strlen( trim( $this->m_limit_from ) ) ) && ( intval( $this->m_limit_from ) > 0 ) )  {
				    $sql.= " LIMIT {$this->m_limit_from}";
				}


				break;
			case 'INSERT':
				if (count($this->m_a_columns) != count($this->m_a_values)) { $this->DisplayError("Error - Column count does not match the value count"); return ''; };
				$sql.= "INSERT INTO {$this->m_table} ";

				$sql.= '(';
				$sql.= implode(', ', $this->m_a_columns);
				$sql.= ') ';

				$sql.= 'VALUES';

				$sql.= ' (';
				$sql.= implode(', ', $this->m_a_values);
				$sql.= ')';
				break;
			case 'UPDATE':
				if (count($this->m_a_columns) != count($this->m_a_values)) { $this->DisplayError("Error - Column count does not match the value count"); return '';} ;
				$sql.= "UPDATE {$this->m_table} SET ";

				$col_count = count($this->m_a_columns);
				for ($i=0;$i<$col_count;$i++) {
					$sql.= "{$this->m_a_columns[$i]} = {$this->m_a_values[$i]}";
					if ($i < $col_count-1) $sql.= ", ";
				}


				if (strlen($this->m_wwhere)) $sql.= " WHERE $this->where";
				if ($this->m_limit) $sql.= " LIMIT $this->m_limit";

				break;
			case 'DELETE':
				$sql.= "DELETE FROM {$this->m_table} ";

				if (strlen($this->m_wwhere)) $sql.= "WHERE $this->where";
				break;
			case 'QUERY':
				if (!strlen($this->m_query)) $this->DisplayError("Warning - No SQL detected");
				$sql.= $this->m_query;

		}

		$sql .= ';';

		if ( $display_statement ) {

		    echo $sql( );

		}


		return $sql;
		
	}  // function GetQuery( )
	
    /**
      *
      * The method SetColumn( ) adds the column $fieldlist to $m_columns
      *
      * Example:
      *
      * @param string $fieldlist the name of the column
      *
      */     

	public function SetColumn( $fieldlist ) {

		$this->m_columns = array( );
		$this->m_values = array( );

		$this->m_columns[]= $fieldlist;

	}	
	
}   // class cSqlStatement

?>
