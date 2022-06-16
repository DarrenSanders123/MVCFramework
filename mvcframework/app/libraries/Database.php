<?php

class Database
{
    private string $host;
    private string $dbName;
    private string $user;
    private string $pass;

    private PDO $dbh;
    private string $error;

    private PDOStatement $stmt;

    private mixed $bindArr;

    /**
     * The constructor is where the database connection is made.
     */

    public function __construct()
    {
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->dbName = DB_NAME;

        //dsn for mysql
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbName;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            return $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } //catch any errors
        catch (PDOException $e) {
            return $this->error = $e->getMessage();
        }
    }

    /**
     * The query() method takes a string as an argument and prepares the query for execution
     * @param mixed $query
     * @return PDOStatement|false
     */

    public function query(mixed $query): PDOStatement|false
    {
        return $this->stmt = $this->dbh->prepare($query);
    }

    /**
     * The bind() method binds the value to the parameter marker and stores the value in an array for later execution.
     * @param string $param
     * The first argument is the parameter marker (e.g. ': userid') to be replaced.
     * @param mixed $value
     * The second argument is the value to be bound to the parameter marker.
     * @param null $type
     * [optional]
     * The third argument is the type of the value.
     */

    public function bind(string $param, mixed $value, $type = null)
    {
        $this->bindArr[$param] = $value;
        if (is_null($type)) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * The print_query() method prints the query string with the parameter markers and their corresponding values.
     * @return array|string $queryStr
     */

    public function print_query(): stdClass|string
    {
        $queryStr = $this->stmt->queryString;

        if (!empty($this->bindArr)) {
            foreach ($this->bindArr as $key => $val) {
                $queryStr = match (true) {
                    is_int($val), is_bool($val), is_null($val) => str_replace($key, $val, $queryStr),
                    default => str_replace($key, '"' . $val . '"', $queryStr),
                };
            }
        }

        return print($queryStr);
    }

    /**
     * The single() method returns the first row of the resultset as an associative array.
     * @return array|bool and array of the first row.
     * @uses resultset, PDOStatement::execute(), PDOStatement::fetch()
     */
    public function single(): stdClass|bool
    {
        self::resultset();
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * The resultset() method returns the results of the query as an array of associative arrays.
     *
     * [example] resultset()[0]['TABLE_COLLUM'] (gets the collum from the first result)
     * @return array|bool an array of the results, or an error message.
     * @uses execute, PDOStatement::fetchAll()
     */

    public function resultset(): bool|array
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * The execute() method executes the query and returns true if successful.
     * @return bool|array true if statement succeeded or an error message.
     * @uses PDOStatement::execute(), PDOStatement::$queryString
     */

    public function execute(): bool|stdClass
    {
        return $this->stmt->execute() ?? $this->queryError();
    }

    /**
     * The queryError() method returns the error message of the last query.
     * @return array|bool
     * @uses PDO::errorInfo()
     */

    public function queryError(): stdClass|bool
    {
        $qError = $this->dbh->errorInfo();
        if (!is_null($qError[2])) {
            return $qError[2];
        }
        return false;
    }

    /**
     * The rowCount() method returns the number of rows in the resultset.
     * @return int Number of rows.
     * @uses PDOStatement::rowCount()
     */

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * The lastInsertId() method returns the ID of the last inserted row.
     * @return string
     * @uses PDO::lastInsertId()
     */

    public function lastInsertId(): string
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * Initiates a transaction
     *
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO object instance are not committed until you end the transaction by calling PDO::commit(). Calling PDO::rollBack() will roll back all changes to the database and return the connection to autocommit mode.
     *
     * @return bool
     * @uses PDO::beginTransaction()
     */

    public function beginTransaction(): bool
    {
        return $this->dbh->beginTransaction();
    }

    /**
     * The endTransaction() method commits the transaction.
     * @return bool
     * @uses PDO::commit()
     */

    public function endTransaction(): bool
    {
        return $this->dbh->commit();
    }

    /**
     * The cancelTransaction() method cancels the transaction.
     *
     * basically does rollback()
     * @return bool
     * @uses PDO::rollBack()
     */

    public function cancelTransaction(): bool
    {
        return $this->dbh->rollBack();
    }

    /**
     * The debugDumpParams() method prints the prepared query and the bound parameters.
     *
     *
     * Dumps the information contained by a prepared statement directly on the output. It will provide the SQL query in use, the number of parameters used (Params), the list of parameters with their key name or position, their name, their position in the query (if this is supported by the PDO driver, otherwise, it will be -1), type (param_type) as an integer, and a boolean value is_param.
     *
     * This is a debug function, which dumps the data directly to the normal output.
     * @uses PDOStatement::debugDumpParams()
     */

    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

}