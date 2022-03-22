<?php

class DB {

  // class variables
	private $_db = null;	// Database Object
	private $_stmt;				// Database Statements
  private $_connected = false;

  public function __construct( $type, $dbfile = null ) {
		if( $type === null ) return false;
		if( $type === 'FILE' && $dbfile === null ) return false; 
		# Create PDO object
		$PDO = '';
		$this->_dbType = $type;
		if( $type === 'FILE' ) {
			if( ! file_exists( $dbfile ) ) {
				echo "File $dbfile does not exists, abort.";
				return false;
				exit;
			}
			if( $this->checkSQLiteFile() ) exit;
			try {
				$PDO = 'sqlite:' . $dbfile;
				$this->_dbString = 'SQLite::' . $type . "::" . $dbfile;
			} catch ( PDOException $e ) {
				$this->connected = false;
				throw new Exception( $e->getMessage() );
			}
		} else if( $type === 'MEMORY' ) {
			$PDO = 'sqlite::memory:';
			$this->_dbString = 'SQLite::' . $type;
		}
		// create PDO Object
		//parent::__construct( $PDO );
		$this->_db = new PDO( $PDO );
		// Set errormode to exceptions
		$this->_db->setAttribute(
			PDO::ATTR_ERRMODE, 
		  PDO::ERRMODE_EXCEPTION
		);
		if( $this->connection() ) {
			$this->_connected = true;
		}
  }

	// check SQLite Database file
	private function checkSQLiteFile() {
		if( SQLITE_TYPE === 'FILE' && ! is_writeable( SQLITE_FILE ) ) {
		  $serverusername = posix_getpwuid(posix_geteuid());
			echo "SQLite file not writeable by webserver user, please add write permissions to file and Folder! <br />";
		  echo "sudo chown -R " . $serverusername['name'] . " " . dirname( SQLITE_FILE ). "<br />";
			return true;
		}
		return false;
	}

	// close DB connection
	public function close() {
		$this->finalize();
		if( $this->_db ) $this->_db = null;
		$this->connected = false;
	}

	// verifying database connection (write access, etc.)
	public function connection() {
    return $this->_dbString;
		$dbStrArr = explode( '::', $this->_dbString );
		if( $dbStrArr[1] === 'FILE' && $dbStrArr[2] !== null || $dbStrArr[2] !== "" ) {
			if( is_file( $dbStrArr[2] ) && is_writable( $dbStrArr[2] ) ) {
				return $this->_dbString;
			}
		} else if( $dbStrArr[1] === 'MEMORY' ) {
			return $this->_dbString;
		}
		return false;
	}

	// return the database object
	public function getDb() {
		return $this->_db;
	}

	// prepare query
	public function query( string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement {
		try {
			$this->_stmt = $this->_db->prepare( $query );
			//$this->_stmt = $this->_db->query( $query );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
		return $this->_stmt;
	}

	// bind parameter 
	public function bind( $param, $value, $type = null ) {
    try {
    	if( is_null( $type ) ) {
		    switch( true ) {
		      case is_int( $value ):
		        $type = PDO::PARAM_INT;
		        break;
		      case is_bool( $value ):
		        $type = PDO::PARAM_INT;
		        break;
		      case is_null( $value ):
		        $type = PDO::PARAM_NULL;
		        break;
		      default:
		        //$type = PDO::SQLITE3_TEXT;
            $type = PDO::PARAM_STR;
		    }
		  }
		  $this->_stmt->bindValue( $param, $value, $type );
			//$this->_stmt->bindParam( $param, $value, $type );
    } catch( PDOException $e ) {
    	throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
    }
	}

	// execute statement
	public function execute() {
		try {
			return $this->_stmt->execute();
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
	}

	// get result as array
	public function resultset() {
		$result = null;
		try {
			$result = $this->_stmt->fetchAll( PDO::FETCH_ASSOC );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

	// get result as object
	public function resultObj() {
    $result = null;
		try {
			$result = $this->_stmt->fetchAll( PDO::FETCH_OBJ );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

	// get a single result
	public function single() {
		$result = null;
		try {
			$result = $this->_stmt->querySingle( PDO::FETCH_ASSOC );
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

	// get the amount of rows
	public function rowCount( $table = 'users' ) {
		$result = 0;
		try {
			if( $this->_stmt !== null ) {
				//$result = $this->_stmt->rowCount();
				//$result = parent::rowCount( $table );
				$query = "SELECT COUNT(*) as count FROM " . $table;
				$this->_stmt = $this->_db->prepare( $query );
				$result = $this->_stmt->execute();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

  // // get the last insert id
  // public function lastInsertId(?string $table = null ): string {
  //   $result = 0;
  //   try {
  //     $result = parent::lastInsertId( $table );
  //   } catch( PDOException $e ) {
  //     throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
  //   }
  //     return $result;
  // }

  // // Transactions
  // public function beginTransaction(): bool {
  //   return parent::beginTransaction();
  // }
  //
  // public function endTransaction() {
  //   return parent::commit();
  // }
  //
  // public function cancelTransaction() {
  //   return parent::rollBack();
  // }

	// Debuging
	public function debugDumpParams() {
		$result = null;
		try {
			if( $this->_stmt !== null ) {
				$result = $this->_stmt->debugDumpParams();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

	public function queryString() {
    $result = null;
		try {
			if( $this->_stmt !== null ) {
				$result = $this->_stmt->queryString;
				//$result = parent::queryString();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

	public function errorInfo(): array {
    $result = null;
		try {
			if( $this->_stmt !== null ) {
				$result = $this->_stmt->errorInfo();
			}
		} catch( PDOException $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
    return $result;
	}

}

?>
