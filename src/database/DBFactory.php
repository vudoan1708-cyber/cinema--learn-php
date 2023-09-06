<?php
  namespace cinema\database;

  /**
   * This class is a factory - a factory provides an interface with which its corresponding an instance of a class is interacted
   */
  final class DBFactory {
    public $connection;

    private $query = '';
    private $parameters = [];

    public function __construct() {
      try {
        $dbInstance = new Database();
        // Create a connection to the database
        $this->connection = $dbInstance->connect();
      } catch (\Exception $ex) {
        throw $ex;
      }
    }

    private function resetConnection() {
      $this->removeParameters();
      $this->resetQuery();
    }

    public function removeParameters(): self {
      $this->parameters = [];
      return $this;
    }

    /**
     * Adds parameters to be bound to the query.
     *
     * The parameters array must have the bind names set as keys with the corresponding value as array value
     * e.g. ["email" => "example@voly.co.uk", ":secondaryEmail" => "secondary@voly.co.uk"]
     * The bind name can inclue or omit the ":" required by SQL syntax
     *
     * @param array $parameters Array of parameters to be bound.
     *
     * @return self
     */
    public function addParameters(array $parameters): self {
      if (!empty($parameters)) {
        foreach ($parameters as $bind => $value) {
          if (is_array($value)) {
            foreach ($value as $key => $val) {
              $this->addParameters(["{$bind}_{$key}" => $val]);
            }
            continue;
          }
          if ($bind[0] !== ":") {
            $bind = ":{$bind}";
          }
          $this->parameters[$bind] = $value;
        }
      }
      return $this;
    }

    private function prepare() {
      try {
        $statement = $this->connection->prepare($this->query);
        $statement->execute($this->parameters);
        return $statement;
      } catch (\PDOException $ex) {
        throw $ex;
      }
    }

    # Execution
    /**
     * Executes the set query and returns the first matched result.
     */
    public function fetchOne() {
      try {
        $statement = $this->prepare();
        $results = $statement->fetch(\PDO::FETCH_ASSOC);
        $this->resetConnection();
        return $results;
      } catch (\Exception $e) {
        ErrorLog($e);
        $this->resetConnection();
        throw $e;
      }
    }
    /**
     * Executes the set query and returns the all matched results.
     */
    public function fetchAll() {
      try {
        $statement = $this->prepare();
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->resetConnection();
        return $results;
      } catch (\Exception $e) {
        ErrorLog($e);
        $this->resetConnection();
        throw $e;
      }
    }
    /**
     * Executes the set query and returns the first matched result by ID.
     * @param string $tableName The table name to select columns from.
     * @param string $id The id of a row.
     */
    public function fetchById(string $tableName, string $id) {
      try {
        if (count($this->parameters) > 0) {
          $this->prepare();
        }
        // Fetch newly created data
        return $this
          ->removeParameters()
          ->addParameters([ ':id' => $id ])
          ->select('*')
          ->from($tableName)
          ->where('id')
          ->fetchOne();
      } catch (\Exception $e) {
        ErrorLog($e);
        $this->resetConnection();
        throw $e;
      }
    }
    /**
     * Executes the set query and returns the first matched result.
     * @param string $tableName The table name to select columns from.
     */
    public function fetchLastInserted(string $tableName) {
      try {
        if (count($this->parameters) > 0) {
          $this->prepare();
        }
        $lastInsertedId = $this->connection->lastInsertId();
        // Fetch newly created data
        return $this
          ->removeParameters()
          ->fetchById($tableName, $lastInsertedId);
      } catch (\Exception $e) {
        ErrorLog($e);
        $this->resetConnection();
        throw $e;
      }
    }

    # Transaction
    public function inTransaction() {
      return $this->connection->inTransaction();
    }

    public function beginTransaction() {
      if ($this->inTransaction()) return;

      $maxAttempts = 3;
      $attempt = 0;

      while (TRUE) {
        try {
          $this->connection->beginTransaction();
          break;
        } catch (\Exception $ex) {
          if ($attempt < $maxAttempts) {
            $attempt += 1;
            continue;
          }
          $this->resetConnection();
          throw $ex;
        }
      }
    }

    # Build Query methods
    public function resetQuery() {
      $this->query = '';
    }

    /**
     * SELECT mysql statement.
     *
     * @param string[] $keys Array of string to be selected.
     * @example - select('id', 'name'), select('id', 'COUNT(*) AS count')
     *
     * @return self
     */
    public function select(string ...$keys) {
      $joinedKeys = implode(', ', $keys);
      $this->query = "SELECT {$joinedKeys}";
      return $this;
    }
    /**
     * FROM mysql statement.
     *
     * @param string $tableName The table name to select columns from.
     * @example - from('user_table')
     *
     * @return self
     */
    public function from(string $tableName) {
      $this->query .= " FROM {$tableName}";
      return $this;
    }
    /**
     * AS mysql statement.
     *
     * @param string $stageName The name to be called.
     * @example - as('userId')
     *
     * @return self
     */
    public function as(string $stageName) {
      $this->query .= " AS {$stageName}";
      return $this;
    }
    /**
     * OR mysql statement.
     *
     * @param string $column The affected column.
     * @example - or('movieId')
     *
     * @return self
     */
    public function or(string $column) {
      $this->query .= " OR {$column} = :{$column}";
      return $this;
    }
    /**
     * IN mysql statement.
     *
     * @param array $values The values to match.
     * @example - in(1, 2, 3)
     *
     * @return self
     */
    public function in(array $values) {
      $formatted = implode(', ', $values);
      $this->query .= " IN ({$formatted})";
      return $this;
    }
    /**
     * WHERE mysql statement. This will follow the standardised secure procedure to avoid SQL injection
     * The parameterised placeholder will be the name of columns with a colon (:) prefix
     *
     * @param string $column The affected column.
     * @example - where('id')
     *
     * @return self
     */
    public function where(string $column) {
      $this->query .= " WHERE {$column} = :{$column}";
      return $this;
    }
    /**
     * WHERE ... AND mysql statement. This will follow the standardised secure procedure to avoid SQL injection
     * The parameterised placeholder will be the name of columns with a colon (:) prefix
     *
     * @param array $columnsAndValues The affected columns and the values as placeholders.
     * @example - whereAnd([ 'id' => 1, 'name' => 'Vu' ]) or whereAnd([ 'id' => ':id1', 'name' => ':nameVu' ])
     *
     * @return self
     */
    public function whereAnd(array $columnsAndValues) {
      $keys = array_keys($columnsAndValues); // [ 'id', 'name' ]
      $values = array_values($columnsAndValues); // [ 1, 'Vu' ] or [ ':id1', ':nameVu' ]
      // Ensure parameterisation
      $placeholders = array_map(function ($key, $value) { // [ ':id1', ':nameVu' ]
        if ($value[0] === ':') return $value;
        return ":$key$value";
      }, $keys, $values);
      // keys and parameters array
      $formattedArray = array_map(function ($key, $placeholder) {
        return "$key = $placeholder"; // [ 'id = :id1', 'name = ":nameVu"' ]
      }, $keys, $placeholders);
      // joined string
      $formattedString = implode(' AND ', $formattedArray); // id = :id1 AND name = :nameVu
      $this->query .= " WHERE {$formattedString}";
      return $this;
    }
    /**
     * WHERE ... IN mysql statement. This will follow the standardised secure procedure to avoid SQL injection
     * The parameterised placeholder will be the name of columns with a colon (:) prefix
     *
     * @param string $column The affected column.
     * @param array $values The values to match, this could already be placeholders.
     * @example - whereIn('id', [ 1, 2, 3, 4, 5 ]) or whereIn('id', [ ':id1', ':id2', ':id3', ':id4', ':id5'])
     *
     * @return self
     */
    public function whereIn(string $column, array $values) {
      $placeholders = array_map(function($val) {
        if ($val[0] === ':') return $val;
        return ":id{$val}"; // [ :id1, :id2, :id3, ... ]
      }, $values);

      $formatted = implode(', ', $placeholders);
      $this->query .= " WHERE {$column} IN ($formatted)";
      return $this;
    }
    /**
     * INSERT INTO mysql statement. This will follow the standardised secure procedure to avoid SQL injection
     * The parameterised placeholders will be the names of the columns with a colon (:) prefix
     *
     * @param string $tableName The table that new data will be inserted into.
     * @param iterable $columns The inserted columns.
     * @example - as('userId')
     *
     * @return self
     */
    public function insert(string $tableName, iterable $columns) {
      $placeholders = array_map(function($col) {
        return ":{$col}";
      }, $columns);

      $joinedColumns = implode(', ', $columns);
      $joinedPlaceholders = implode(', ', $placeholders);
      $this->query = "INSERT INTO {$tableName} ({$joinedColumns}) VALUES ({$joinedPlaceholders})";
      return $this;
    }
    /**
     * UPDATE ... SET mysql statement. This will follow the standardised secure procedure to avoid SQL injection
     * The parameterised placeholders will be the names of the columns with a colon (:) prefix
     *
     * @param string $tableName The table that data will be updated.
     * @param iterable $columns The updated columns.
     * @example - as('userId')
     *
     * @return self
     */
    public function update(string $tableName, iterable $columns) {
      $placeholders = array_map(function($col) {
        return "$col=:{$col}";
      }, $columns);

      $joinedPlaceholders = implode(', ', $placeholders);
      $this->query = "UPDATE {$tableName} SET {$joinedPlaceholders}";
      return $this;
    }
    /**
     * DELETE FROM mysql statement.
     *
     * @param string $tableName The table that data will be deleted.
     * @example - as('userId')
     *
     * @return self
     */
    public function delete(string $tableName) {
      $this->query = "DELETE FROM {$tableName}";
      return $this;
    }
  }
