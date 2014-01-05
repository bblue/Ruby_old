<?php
namespace Lib\Db;

class MysqlAdapter implements DatabaseAdapterInterface
{
    protected $_config = array();
    protected $_link;
    protected $_result;
   
    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        if (count($config) !== 4) {
            throw new MysqlAdapterException('Invalid number of connection parameters.');  
        }
        $this->_config = $config;
    }
   
    /**
     * Connect to MySQL
     */
    public function connect()
    {
        // connect only once
        if ($this->_link !== null) {
            return $this->_link;
        }
        list($host, $user, $password, $database) = $this->_config;
        if (($this->_link = @mysqli_connect($host, $user, $password, $database))) {
            unset($host, $user, $password, $database);
            return $this->_link;
        }
        throw new MySQLAdapterException('Error connecting to the server : ' . mysqli_connect_error());
    }

    /**
     * Execute the specified query
     */
    public function query($query)
    {
    	if(PRINT_SQL_QUERY === true)
    	{
    		echo '<pre>'; print_r($query); echo "</pre>\n";
    	}
    	
        if (!is_string($query) || empty($query)) {
            throw new MySQLAdapterException('The specified query is not valid.');  
        }
        // lazy connect to MySQL
        $this->connect();
        if ($this->_result = mysqli_query($this->_link, $query)) {
            return $this->_result;
        }
        throw new MySQLAdapterException('Error executing the specified query ' . $query . mysqli_error($this->_link));
    }
   
    /**
     * Perform a SELECT statement
     */
    public function select(array $aTables, $where = '', $fields = '*', $order = '', $limit = null, $offset = null)
    {
        $query = 'SELECT ' . $fields . ' FROM ' . implode(', ', $aTables)
               . (($where) ? ' WHERE ' . $where : '')
               . (($limit) ? ' LIMIT ' . $limit : '')
               . (($offset && $limit) ? ' OFFSET ' . $offset : '')
               . (($order) ? ' ORDER BY ' . $order : '');
        $this->query($query);
        return $this->countRows();
    }
   
    /**
     * Perform an INSERT statement
     */ 
    public function insert(array $aTables, array $data)
    {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_map(array($this, 'quoteValue'), array_values($data)));
        $query = 'INSERT INTO ' . implode(', ', $aTables) . ' (' . $fields . ') ' . 'VALUES (' . $values . ')';
        $this->query($query);
        return $this->getInsertId();
    }
   
    /**
     * Perform an UPDATE statement
     */
    public function update(array $aTables, array $data, $where = '')
    {
        $set = array();
        foreach ($data as $field => $value) {
            $set[] = $field . '=' . $this->quoteValue($value);
        }
        $set = implode(', ', $set);
        $query = 'UPDATE ' . implode(', ', $aTables) . ' SET ' . $set
               . (($where) ? ' WHERE ' . $where : '');

        $this->query($query);

        return $this->getAffectedRows();
    }
   
    /**
     * Perform a DELETE statement
     */
    public function delete(array $aTables, $where = '')
    {
        $query = 'DELETE FROM ' . implode(', ', $aTables)
               . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }
   
    /**
     * Escape the specified value
     */
    public function quoteValue($value)
    {
        $this->connect();
        if ($value === null) {
            $value = 'NULL';
        }
        else if (!is_numeric($value)) {
            $value = "'" . mysqli_real_escape_string($this->_link, $value) . "'";
        }
        return $value;
    }
   
    /**
     * Fetch a single row from the current result set (as an associative array)
     */
    public function fetch()
    {
        if ($this->_result !== null) {
            if (($row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC)) !== false) {
                return $row;
            }
            $this->freeResult();
            return false;
        }
        return null;
    }

    /**
     * Get the insertion ID
     */
    public function getInsertId()
    {
        return $this->_link !== null ?
               mysqli_insert_id($this->_link) :
               null;
    }
   
    /**
     * Get the number of rows returned by the current result set
     */ 
    public function countRows()
    {
        return $this->_result !== null ?
               mysqli_num_rows($this->_result) :
               0;
    }
   
    /**
     * Get the number of affected rows
     */
    public function getAffectedRows()
    {
        return ($this->_link !== null) ? mysqli_affected_rows($this->_link) : 0;
    }
   
    /**
     * Free up the current result set
     */
    public function freeResult()
    {
        if ($this->_result !== null) {
            mysqli_free_result($this->_result);
            return true;
        }
        return false;
    }
   
    /**
     * Close explicitly the database connection
     */
    public function disconnect()
    {
        if ($this->_link !== null) {
            mysqli_close($this->_link);
            $this->_link = null;
            return true;
        }
        return false;
    }
   
    /**
     * Close automatically the database connection when the instance of the class is destroyed
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}