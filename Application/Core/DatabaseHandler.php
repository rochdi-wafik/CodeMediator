<?php
namespace Core;

use Core\Interfaces\IDatabase;
use Core\Interfaces\IDatabaseHandler;
use PDO;
use PDOException;

/**
 * @todo add bool return to insert() delete() to indicate status
 */
class DatabaseHandler implements IDatabaseHandler, IDatabase{
    
    /**
	 * SQL 
	 * 
	 * @var query hold queries to be executed
	 * @var bind hold bind values
	 * @var flag added to last
	 */

	private $query = null;
	private $bind_params = array();
	private $query_flag = null;

	/**
	 * PDO 
	 * 
	 * @var fetch_mode Fetch Data As : OBJ | ASSOC 
	 * @var returnType Return boolean | int | mixed | etc
	 */

	private $fetch_mode = OBJ;
	private $returnType;


	/**
	 * DATABASE
	 * 
	 * @var db hold reference of this class
	 * @var connection hold db connection object
	 */
	protected $db;
	private $connection;
	private $complete = false;

	/**
	 * @var message used to set / get messages 
	 */
	protected $message;
	

	function __construct()
	{
		/**
		 * Store methods in $db to be inherited
		 * The developer can access methods using var $db
		 * Example : $this->db->get('users')
		 */
		$this->db = $this;

		// initialize db connection
		$this->connection = Database::getInstance()->getConnection();
	}

	/**
	 * Just a $db getter
	 * passed by reference so that changes applied to the original property
	 */
	public function &db()
	{
		return $this->db;
	}
	
	//----------------------------- General Methods -------------------------------

	/**
	 * Reset class after operation complete
	 */
	private function clean()
	{
		if($this->complete == true)
		{
			$this->bind_params = array();
			$this->query = null;
			$this->query_flag = null;
		}
	}

	/**
	 * Get Result
	 * @param string $query to be executed
	 * @return mix object,array,int,etc
	 */
	public function result($query=null)
	{
		$sql = ($query != null) ? $query : $this->query;
		$MODE = ($this->fetch_mode == OBJ ) ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
		try {	
			$stmt = $this->connection->prepare($sql);
			$stmt->setFetchMode($MODE);
			// Check Return Type
			if($this->returnType == BOOLEAN)
			{
				return $stmt->execute($this->bind_params);
			}
			else{
				$stmt->execute($this->bind_params);
			    return $stmt->fetchAll();
			}
				
		} 
		catch (PDOException $e) {
			CodeMediator::show_error(array(
				"title" => "Database Exception",
				"content" => $e->getMessage()
			));
			return null;
		}
		finally{
			$this->complete = true;
		}

	}


	/**
	 * Fetch all elements (alias of result)
	 * @return ?array 
	 */
	public function fetchAll()
	{
		return $this->result($this->query);
	}


	/**
	 * Fetch current element
	 * @return mix array|object
	 */
	public function fetch()
	{
		$MODE = ($this->fetch_mode == OBJ ) ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
		try {	
			$stmt = $this->connection->prepare((string)$this->query);
			$stmt->setFetchMode($MODE);
			$stmt->execute($this->bind_params);
			return $stmt->fetch();
		} 
		catch (PDOException $e) {
			CodeMediator::show_error(array(
				"title" => "Database Exception",
				"content" => $e->getMessage()
			));
			return null;
		}
		finally{
			$this->complete = true;
		}
	}


	//----------------------------- PDO Methods -------------------------------------
	
	/**
	 * @param obj $mode =  OBJ OR ASSOC
	 */
	public function fetchMode($mode = OBJ)
	{
		$this->fetch_mode = $mode;
	}

	/**
	 * @return obj pdo connection object
	 */
	public function getConnection()
	{
		return $this->connection;
	}
	

	/**
	 * Set which dataType return (default = mixed)
	 * @param CONSTANT $dataType
	 */
	public function returnType($dataType = MIXED)
	{
		if(!in_array($dataType,array(MIXED,BOOLEAN,INTEGER,STRING)))
		{
			CodeMediator::show_error(array(
				"title" => "Error In Model Method",
				"content" => 'method <a>returnType()</a> has wrong parameter',
				"level" => WARNING,
				"description" => 'only following parameters are allowed: MIXED,BOOLEAN,INTEGER,STRING',
				"track" => ' xxxModel{..} $this->db->returnType()'
			));
		}
		$this->returnType = $dataType;
	}
	
	//----------------------------- CRUD Methods ------------------------------------
	
	/**
	 * Execute Direct Sql Statement
	 * @param string $sql
	 * @param array-string $setup 
	 * @var array: ["mode" => "assoc|obj"] ["action" => "execute|count|fetch|fetchAll"]
	 * @var string: "assoc|obj|execute|count|fetch|fetchAll"
	 * 
	 * @api
	 * $this->db()->sql('...');
	 */
	public function sql($sql=null, $setup=null)
	{
	    $MODE = PDO::FETCH_OBJ;
		try {
			$stmt = $this->connection->prepare($sql);	
			// IF Array Given
			if(is_array($setup))
			{
				extract($setup);
				if(isset($mode))
				{
					$MODE = (strtolower($mode) == "assoc") ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;
				}
				if(isset($action))
				{
					if($action== self::FLAG_EXECUTE)
					{
						return $stmt->execute();
					}
						
					if($action == self::FLAG_COUNT)
					{
						$stmt->execute();
						return $stmt->rowCount();
					}
					if($action== self::FLAG_FETCH)
					{
						$stmt->execute();
						return $stmt->fetch($MODE);
					}
					if($action== self::FLAG_FETCH_ALL)
					{
						$stmt->execute();
						return $stmt->fetchAll($MODE);
					}
				}
			}
			// IF String Given
			if(is_string($setup))
			{
				$MODE = ($setup == "assoc") ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;
				if($setup == self::FLAG_EXECUTE)
				{
					return $stmt->execute();
				}
					
				if($setup == self::FLAG_COUNT)
				{
					$stmt->execute();
					return $stmt->rowCount();
				}
				if($setup == self::FLAG_FETCH)
				{
					$stmt->execute();
					return $stmt->fetch($MODE);
				}
				if($setup == self::FLAG_FETCH_ALL)
				{
					$stmt->execute();
					return $stmt->fetchAll($MODE);
				}
			}
			// IF Null -> Default action
			$stmt->execute();
			return $stmt->fetchAll($MODE);	
		} 
		catch (PDOException $e) {
			CodeMediator::show_error(array(
				"title" => "Database Exception",
				"content" => $e->getMessage(),
				"track" => 'xxxModel{} using this->db->sql()'
			));
			return null;
		}
	}

	/**
	 * @param string $table
	 * @param array $condition
	 * @param string $and_or AND OR
	 * @return mixed
	 */
	public function getOk($table, $condition=null, $and_or = 'AND'){
		return $this->get($table, $condition, $and_or);
	}

	public function get($table, $condition=null, $and_or = 'AND')
	{	
		$this->clean();

		if(!is_array($condition))
		{
			$this->query = " SELECT * FROM $table ";
		}
		else{
			// Execution
			$columns = array_keys($condition);

			// Generate Where Condition
			$where=null;
			foreach($columns as $column)
			{
				$where .= " $column = ? $and_or"; 	
			}

			// Check flags
			$stmt_flag = null;
			if($this->query_flag!=null){
				if($this->query_flag== self::FLAG_UPDATE_FIRST){
					$stmt_flag = self::FLAG_UPDATE_FIRST;
				}
				else if($this->query_flag == self::FLAG_UPDATE_LAST){
					$stmt_flag = self::FLAG_UPDATE_LAST;
				}
			}
			// Trim last AND
			$where = rtrim($where, $and_or);
			$this->query .= "SELECT * FROM $table WHERE $where $stmt_flag";
			$this->bind_params = array_merge($this->bind_params,array_values($condition));
		}
		return $this->result();
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @return boolean
	 */
	public function insert($table = null,$data = null)
	{
		$this->clean();
		// Validation
		if(!is_array($data))
		{
			CodeMediator::show_error(array(
				"title" => "Error In Model Method",
				"content" => 'method <a>insert()</a> has wrong parameter',
				"level" => DANGER,
				"description" => 'insert($table,$data) parameter $data must be an associative array <br>example: $this->db->insert("table",array("colum"=>"value"))',
				"track" => ' xxxModel{..} $this->db->insert()'
			));
			$this->message = 'insert($table,$data) accept only associative array';
			return false;
		}
		// Execution
		$keys = array_keys($data);
	    $values = array_values($data);
	    $columns = implode(',' ,$keys);
	    $bindvalues = str_repeat("?,", count($keys)-1); // (-1) => shift last (,)
		
		$this->query = " INSERT INTO $table($columns) VALUES ({$bindvalues}?) ";
		$this->bind_params = array_merge($this->bind_params,$values);
		$this->returnType = BOOLEAN;
		return $this->result($this->query);
	}

	/** 
	 * @param string $table
	 * @param array $data if data[] not set, the method will delete all items
	 * @param string $and_or AND OR
	 * @return boolean
	 */
	public function delete($table, $condition=null, $and_or='AND')
	{
		$this->clean();
		/**
		 * Note: Because DELETE return true even if where doesn't match
		 * we will check if colum exist first, if not, return false
		 */
		if(!is_array($condition))
		{
			$this->query = "DELETE FROM $table ";
			$this->returnType = BOOLEAN;
		}
		else{
			// Execution
			$columns = array_keys($condition);
			$where=null;
			foreach($columns as $column)
			{
				$where .= " $column = ? $and_or"; 	
			}

			// Check flags
			$stmt_flag = null;
			if($this->query_flag!=null){
				if($this->query_flag== self::FLAG_UPDATE_FIRST){
					$stmt_flag = self::FLAG_UPDATE_FIRST;
				}
				else if($this->query_flag == self::FLAG_UPDATE_LAST){
					$stmt_flag = self::FLAG_UPDATE_LAST;
				}
			}

			// Trim last AND
			$where = rtrim($where, $and_or);
			$this->query = " DELETE FROM $table WHERE $where $stmt_flag";
			$this->bind_params = array_merge($this->bind_params,array_values($condition));	

			$this->returnType = BOOLEAN;
		}
		return $this->result();
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @return boolean
	 */
	public function update($table, $data = null)
	{
		$this->clean();
		// Validation
		if(!is_array($data))
		{
			CodeMediator::show_error(array(
				"title" => "Error In Model Method",
				"content" => 'method <a>update()</a> has wrong parameter',
				"level" => DANGER,
				"description" => 'update($table,$data) parameter $data must be an associative array <br>example: $this->db->insert("table",array("colum"=>"value"))',
				"track" => ' xxxModel{..} $this->db->update()'
			));
			$this->message = 'update($table,$data) arg $data accept only associative array';
			return false;
		}

		// Execution
		$columns = array_keys($data);    // ex: array('fullname','username')
	    $values = array_values($data);  // ex: array('Mr Samy','mr.sam11')
		$stmt=null;
		foreach($columns as $column)
		{
			$stmt .= "$column = ? , ";  // ex: fullname = ? , username = ? ,
		}
		
		$stmt = rtrim($stmt, ' ,'); // remove last (,)

		// Check flags
		$stmt_flag = null;
		if($this->query_flag!=null){
			if($this->query_flag== self::FLAG_UPDATE_FIRST){
				$stmt_flag = self::FLAG_UPDATE_FIRST;
			}
			else if($this->query_flag == self::FLAG_UPDATE_LAST){
				$stmt_flag = self::FLAG_UPDATE_LAST;
			}
		}

		$this->query = " UPDATE $table SET $stmt $stmt_flag"; 
		$this->bind_params = array_merge($this->bind_params,(array)$values);
		$this->returnType = BOOLEAN;
	}

	/** Check if item(s) exists
	 * @param string $table
	 * @param array $condition
	 * @param string $and_or AND OR 
	 * @return boolean-mix
	 */
	public function exists($table, $condition=null, $and_or = 'AND')
	{
		$this->clean();
		// Validation
		if(!is_array($condition))
		{
			CodeMediator::show_error(array(
				"title" => "Error In Model Method",
				"content" => 'method <a>exists()</a> has wrong parameter',
				"level" => DANGER,
				"description" => 'exists($table,$data) parameter $data must be an associative array <br>example: $this->db->exists("table",array("colum"=>"value"))',
				"track" => ' xxxModel{..} $this->db->exists()'
			));
			$this->message = 'exists($table,$data) arg $data accept only associative array';
			return false;
		}

		// Generate Where Condition
		$where=null;
		$columns = array_keys($condition);
		foreach($columns as $column)
		{
			$where .= " $column = ? $and_or"; 
		}
		// Trim last AND
		$where = rtrim($where, $and_or);

		$query = "SELECT * FROM $table WHERE $where ";
		$MODE = ($this->fetch_mode == OBJ ) ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
		try {	
			$stmt = $this->connection->prepare($query);
			$stmt->execute(array_values($condition));
			$row = $stmt->fetch($MODE);
			return ($stmt->rowCount() >=1 ) ? $row : false;
		} 
		catch (PDOException $e) {
			$exception = ($stmt->errorInfo()[2] != null) ? $stmt->errorInfo()[2] : $e->getMessage();
			CodeMediator::show_error(array(
				"title" => "Database Exception",
				"content" => $exception
			));
			return false;
		}

	}

	/** Search in table
	 * @param string $table
	 * @param array $data 
	 * @return mixed
	 */
	public function search($table, $data=null)
	{
		$this->clean();
		// Validation
		if(!is_array($data))
		{
			CodeMediator::show_error(array(
				"title" => "Error In Model Method",
				"content" => 'method <a>like()</a> has wrong parameter',
				"level" => DANGER,
				"description" => 'like($table,$data) parameter $data must be an associative array <br>example: $this->db->like("table",array("colum"=>"value"))',
				"track" => ' xxxModel{..} $this->db->like()'
			));
			$this->message = 'like($table,$data) arg $data accept only associative array';
			return null;
		}

		// Execution
		foreach($data as $column => $value)
		{
			$sql = " SELECT * FROM $table WHERE lower($column) LIKE lower('%".filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS)."%') ";
		}
		$this->query = $sql;
		return $this->result();
	}

	//----------------------------- Conditions Methods --------------------------------

	/**
	 * @param array $condition
	 * @param string $and_or AND OR
	 */
	public function where($condition=null, $and_or='AND')
	{
		// Validation
		if(!is_array($condition))
		{
			CodeMediator::show_error(array(
				"title" => "Error In Model Method",
				"content" => "method <a>where()</a> accept only associative array",
				"level" => DANGER,
				"description" => 'example: $this->db->where(array("status"=>1))',
				"track" => ' xxxModel{..} $this->db->where()'
			));
			$this->message = "Where() accept only associative array";
			return null;
		}

		// Execution
		$columns = array_keys($condition);

		// Generete Where Condition
		$where=null;
		foreach($columns as $column)
		{
			$where .= " $column = ? $and_or"; 
		}

		// Trim last AND
		$where = rtrim($where, $and_or);
		/** 
		 * Check if query allready has WHERE Statement
		 * IF true start with AND else start with WHERE
		 */
		$arr = explode(' ',(string)$this->query);
		if(in_array("WHERE", $arr))
		{
			$this->query .= " $and_or $where ";
		}
		else{
			$this->query .= " WHERE $where ";
		}
		$this->bind_params = array_merge($this->bind_params,array_values($condition));
	}
	/**
	 * @param int $number
	 */
	public function limit($number=null)
	{
		if(filter_var($number, FILTER_VALIDATE_INT))
		{
			$this->query .= " Limit $number ";
		}
	}

		/**
	 * @param int $number
	 */
	public function offset($number=null)
	{
		if(filter_var($number, FILTER_VALIDATE_INT))
		{
			$this->query .= " OFFSET $number ";
		}
	}

	/**
	 * @api orderBy must not be after limit or offset
	 *      it must be before limit, or offset
	 */
	public function orderBy($key, $order='DESC')
	{
		$this->query .= " ORDER BY $key $order ";
	}

    /** Count all rows in a table
	 * @param string $table
	 * @return boolean
	 */
	public function countRows($table, $more=null)
	{
		$this->clean();
		$query = "SELECT * FROM $table ";
		if(is_string($more)){
			$query .= " ".$more;
		}
		try {	
			$stmt = $this->connection->prepare($query);
			$stmt->execute();
			return $stmt->rowCount();
		} 
		catch (PDOException $e) {
			CodeMediator::show_error(array(
				"title" => "Database Exception",
				"content" => $e->getMessage()
			));
			return null;
		}
	}


	/**
	 * @api flag must be used before fetch() fetchAll() result()
	 *      flag must be used with limit() and orderBy() 
	 */
	private function testFlags($param, $flags){
		if($flags & self::FLAG_FETCH){
			// todo
		}
		if($flags & self::FLAG_COUNT){
			// todo
		}
		if($flags & self::FLAG_EXECUTE){
			// todo
		}
	}
	public function flag($flag)
	{
		$flags = [
			self::FLAG_FETCH_FIRST, self::FLAG_FETCH_LAST,
			self::FLAG_UPDATE_FIRST, self::FLAG_UPDATE_LAST,
			self::FLAG_REMOVE_FIRST, self::FLAG_REMOVE_LAST
		];
		if(in_array($flag, $flags)){
			$this->query_flag = $flag;
		}
	}
	//----------------------------- HELPER Methods ----------------------------------

	/**
	 * @method setMessage() used in Models to pass messages to the Controllers
	 * @example userModel{} $this->setMessage('user not found')
	 */
	public function setMessage($msg=null)
	{
		$this->message = $msg;
	}

	/**
	 * @method getMessage() used in Controllers to messages exceptions from Models
	 * @example userController{} $this->childModel->getMessage()
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * Get New instance From A Class [Model]
	 * @param className Name of target model
	 */
	public function get_instance($className){
        /**
         * Set property named with className
         * set property's value as className instence
         */
        if(is_array($className))
        {
            foreach($className as $name){
                $this->{$name} = new $name;
            }
        }
        else{
            $this->{$className} = new $className;
        }
        
    }


}
