<?php
/**
 * Ramayan CMS — Database Layer (Patal)
 * finggu.com | Author: sudarshanpjadhav
 * Part of the Ramayan CMS Ecosystem
 */

declare(strict_types=1);

namespace Ram\Database;

/**
 * PatalDB — Database connection and fluent query builder.
 *
 * All queries go through PDO with prepared statements.
 * Raw SQL is never executed directly — structural SQL injection immunity.
 *
 * Usage:
 *   PatalDB::ramFn_table('posts')->ramFn_where('status','=','publish')->ramFn_get();
 *   PatalDB::ramFn_query("SELECT * FROM `ram_posts` WHERE id = ?", [42]);
 */
final class PatalDB
{
    private static ?PatalDB $ramVar_instance = null;
    private static ?\PDO    $ramVar_pdo      = null;

    /** @var string Table prefix (e.g. 'ram_') */
    private static string $ramVar_prefix = 'ram_';

    /** @var int Total queries executed (for debug) */
    private static int $ramVar_queryCount = 0;

    // ── Builder state (per chain instance) ───────────────────────────────────

    private string $ramVar_table      = '';
    private string $ramVar_fullTable  = '';

    /** @var string[] SELECT columns */
    private array $ramVar_selects     = ['*'];

    /** @var array<int, array{column: string, op: string, value: mixed, boolean: string}> */
    private array $ramVar_wheres      = [];

    /** @var array<int, array{column: string, direction: string}> */
    private array $ramVar_orders      = [];

    private ?int $ramVar_limit        = null;
    private ?int $ramVar_offset       = null;

    /** @var string[] JOIN clauses (raw) */
    private array $ramVar_joins       = [];

    /** @var mixed[] Bound parameters accumulated during build */
    private array $ramVar_bindings    = [];

    // ─── Connection ───────────────────────────────────────────────────────────

    /**
     * Establish the PDO connection.
     *
     * @param array<string, string> $ramVar_config Keys: host, dbname, user, password, charset, prefix
     * @throws \RuntimeException on connection failure
     */
    public static function ramFn_connect(array $ramVar_config): void
    {
        if (self::$ramVar_pdo !== null) {
            return; // Already connected
        }

        $ramVar_host    = $ramVar_config['host']     ?? 'localhost';
        $ramVar_dbname  = $ramVar_config['dbname']   ?? '';
        $ramVar_user    = $ramVar_config['user']      ?? '';
        $ramVar_pass    = $ramVar_config['password']  ?? '';
        $ramVar_charset = $ramVar_config['charset']   ?? 'utf8mb4';
        self::$ramVar_prefix = $ramVar_config['prefix'] ?? 'ram_';

        $ramVar_dsn = "mysql:host={$ramVar_host};dbname={$ramVar_dbname};charset={$ramVar_charset}";

        try {
            self::$ramVar_pdo = new \PDO($ramVar_dsn, $ramVar_user, $ramVar_pass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$ramVar_charset} COLLATE {$ramVar_charset}_unicode_ci",
            ]);
        } catch (\PDOException $ramVar_e) {
            throw new \RuntimeException(
                'Ramayan CMS — PatalDB: Could not connect to MySQL. ' . $ramVar_e->getMessage()
            );
        }

        self::$ramVar_instance = new self();
    }

    /**
     * Get the raw PDO connection.
     */
    public static function ramFn_getPdo(): \PDO
    {
        if (self::$ramVar_pdo === null) {
            throw new \RuntimeException('PatalDB: Not connected. Call PatalDB::ramFn_connect() first.');
        }
        return self::$ramVar_pdo;
    }

    /**
     * Check whether a database connection is active.
     */
    public static function ramFn_isConnected(): bool
    {
        return self::$ramVar_pdo !== null;
    }

    // ─── Query Builder entry point ────────────────────────────────────────────

    /**
     * Start a fluent query on a table.
     *
     * @param string $ramVar_table Table name WITHOUT prefix (e.g. 'posts', 'users')
     * @return static New builder instance
     */
    public static function ramFn_table(string $ramVar_table): static
    {
        $ramVar_builder              = new static();
        $ramVar_builder->ramVar_table     = $ramVar_table;
        $ramVar_builder->ramVar_fullTable = self::$ramVar_prefix . $ramVar_table;
        return $ramVar_builder;
    }

    // ─── SELECT ───────────────────────────────────────────────────────────────

    /**
     * Set the columns to SELECT.
     *
     * @param string[] $ramVar_columns Column names (e.g. ['id','title','slug'])
     */
    public function ramFn_select(array $ramVar_columns): static
    {
        $this->ramVar_selects = $ramVar_columns;
        return $this;
    }

    // ─── WHERE ───────────────────────────────────────────────────────────────

    /**
     * Add a WHERE clause.
     *
     * @param string $ramVar_column Column name
     * @param string $ramVar_op     Operator: '=', '!=', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS NULL', 'IS NOT NULL'
     * @param mixed  $ramVar_value  Comparison value
     */
    public function ramFn_where(string $ramVar_column, string $ramVar_op, mixed $ramVar_value = null): static
    {
        $this->ramVar_wheres[] = [
            'column'  => $ramVar_column,
            'op'      => strtoupper($ramVar_op),
            'value'   => $ramVar_value,
            'boolean' => 'AND',
        ];
        return $this;
    }

    /**
     * Add an OR WHERE clause.
     */
    public function ramFn_orWhere(string $ramVar_column, string $ramVar_op, mixed $ramVar_value = null): static
    {
        $this->ramVar_wheres[] = [
            'column'  => $ramVar_column,
            'op'      => strtoupper($ramVar_op),
            'value'   => $ramVar_value,
            'boolean' => 'OR',
        ];
        return $this;
    }

    /**
     * Add a WHERE IN clause.
     *
     * @param string  $ramVar_column Column name
     * @param mixed[] $ramVar_values Array of values
     */
    public function ramFn_whereIn(string $ramVar_column, array $ramVar_values): static
    {
        $this->ramVar_wheres[] = [
            'column'  => $ramVar_column,
            'op'      => 'IN',
            'value'   => $ramVar_values,
            'boolean' => 'AND',
        ];
        return $this;
    }

    /**
     * Add a WHERE NULL clause.
     */
    public function ramFn_whereNull(string $ramVar_column): static
    {
        $this->ramVar_wheres[] = [
            'column'  => $ramVar_column,
            'op'      => 'IS NULL',
            'value'   => null,
            'boolean' => 'AND',
        ];
        return $this;
    }

    /**
     * Add a WHERE NOT NULL clause.
     */
    public function ramFn_whereNotNull(string $ramVar_column): static
    {
        $this->ramVar_wheres[] = [
            'column'  => $ramVar_column,
            'op'      => 'IS NOT NULL',
            'value'   => null,
            'boolean' => 'AND',
        ];
        return $this;
    }

    // ─── ORDER, LIMIT, OFFSET ─────────────────────────────────────────────────

    /**
     * Add ORDER BY.
     *
     * @param string $ramVar_column    Column to sort by
     * @param string $ramVar_direction 'ASC' or 'DESC'
     */
    public function ramFn_orderBy(string $ramVar_column, string $ramVar_direction = 'ASC'): static
    {
        $this->ramVar_orders[] = [
            'column'    => $ramVar_column,
            'direction' => strtoupper($ramVar_direction) === 'DESC' ? 'DESC' : 'ASC',
        ];
        return $this;
    }

    /**
     * Set LIMIT.
     */
    public function ramFn_limit(int $ramVar_limit): static
    {
        $this->ramVar_limit = $ramVar_limit;
        return $this;
    }

    /**
     * Set OFFSET.
     */
    public function ramFn_offset(int $ramVar_offset): static
    {
        $this->ramVar_offset = $ramVar_offset;
        return $this;
    }

    // ─── Execute ─────────────────────────────────────────────────────────────

    /**
     * Execute SELECT and return all matching rows.
     *
     * @return array<int, array<string, mixed>>
     */
    public function ramFn_get(): array
    {
        [$ramVar_sql, $ramVar_bindings] = $this->ramFn_buildSelect();
        $ramVar_stmt = self::ramFn_prepare($ramVar_sql, $ramVar_bindings);
        return $ramVar_stmt->fetchAll() ?: [];
    }

    /**
     * Execute SELECT and return the first row or null.
     *
     * @return array<string, mixed>|null
     */
    public function ramFn_first(): ?array
    {
        $this->ramVar_limit = 1;
        [$ramVar_sql, $ramVar_bindings] = $this->ramFn_buildSelect();
        $ramVar_stmt = self::ramFn_prepare($ramVar_sql, $ramVar_bindings);
        $ramVar_row  = $ramVar_stmt->fetch();
        return $ramVar_row !== false ? $ramVar_row : null;
    }

    /**
     * Find a single row by primary key.
     *
     * @param int|string $ramVar_id Primary key value
     * @return array<string, mixed>|null
     */
    public function ramFn_find(int|string $ramVar_id): ?array
    {
        return $this->ramFn_where('id', '=', $ramVar_id)->ramFn_first();
    }

    /**
     * Count rows matching the current WHERE clauses.
     */
    public function ramFn_count(): int
    {
        $this->ramVar_selects = ['COUNT(*) AS ram_count'];
        [$ramVar_sql, $ramVar_bindings] = $this->ramFn_buildSelect();
        $ramVar_stmt = self::ramFn_prepare($ramVar_sql, $ramVar_bindings);
        $ramVar_row  = $ramVar_stmt->fetch();
        return (int) ($ramVar_row['ram_count'] ?? 0);
    }

    /**
     * Check if any rows match the current WHERE clauses.
     */
    public function ramFn_exists(): bool
    {
        return $this->ramFn_count() > 0;
    }

    // ─── INSERT ───────────────────────────────────────────────────────────────

    /**
     * Insert a row and return the new auto-increment ID.
     *
     * @param array<string, mixed> $ramVar_data Column => value pairs
     * @return int Last insert ID
     */
    public function ramFn_insert(array $ramVar_data): int
    {
        $ramVar_columns  = array_keys($ramVar_data);
        $ramVar_placeholders = implode(', ', array_fill(0, count($ramVar_columns), '?'));
        $ramVar_colList  = implode(', ', array_map(
            static fn(string $c): string => '`' . $c . '`',
            $ramVar_columns
        ));

        $ramVar_sql = "INSERT INTO `{$this->ramVar_fullTable}` ({$ramVar_colList}) VALUES ({$ramVar_placeholders})";
        self::ramFn_prepare($ramVar_sql, array_values($ramVar_data));
        return (int) self::ramFn_getPdo()->lastInsertId();
    }

    /**
     * Insert or update on duplicate key.
     *
     * @param array<string, mixed> $ramVar_data      Data to insert
     * @param array<string, mixed> $ramVar_updateData Data to update on conflict (if null, uses $ramVar_data)
     */
    public function ramFn_upsert(array $ramVar_data, ?array $ramVar_updateData = null): int
    {
        $ramVar_updateData ??= $ramVar_data;
        $ramVar_columns      = array_keys($ramVar_data);
        $ramVar_placeholders = implode(', ', array_fill(0, count($ramVar_columns), '?'));
        $ramVar_colList      = implode(', ', array_map(static fn(string $c): string => '`' . $c . '`', $ramVar_columns));
        $ramVar_updateList   = implode(', ', array_map(
            static fn(string $c): string => '`' . $c . '` = VALUES(`' . $c . '`)',
            array_keys($ramVar_updateData)
        ));

        $ramVar_sql      = "INSERT INTO `{$this->ramVar_fullTable}` ({$ramVar_colList}) VALUES ({$ramVar_placeholders}) ON DUPLICATE KEY UPDATE {$ramVar_updateList}";
        $ramVar_bindings = array_values($ramVar_data);
        self::ramFn_prepare($ramVar_sql, $ramVar_bindings);
        return (int) self::ramFn_getPdo()->lastInsertId();
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    /**
     * Update rows matching current WHERE clauses.
     *
     * @param array<string, mixed> $ramVar_data Column => value pairs
     * @return int Number of affected rows
     */
    public function ramFn_update(array $ramVar_data): int
    {
        $ramVar_setParts = array_map(
            static fn(string $c): string => '`' . $c . '` = ?',
            array_keys($ramVar_data)
        );
        $ramVar_bindings = array_values($ramVar_data);

        $ramVar_sql = "UPDATE `{$this->ramVar_fullTable}` SET " . implode(', ', $ramVar_setParts);
        [$ramVar_whereClause, $ramVar_whereBindings] = $this->ramFn_buildWhereClause();

        if ($ramVar_whereClause !== '') {
            $ramVar_sql .= ' WHERE ' . $ramVar_whereClause;
            $ramVar_bindings = array_merge($ramVar_bindings, $ramVar_whereBindings);
        }

        $ramVar_stmt = self::ramFn_prepare($ramVar_sql, $ramVar_bindings);
        return $ramVar_stmt->rowCount();
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────

    /**
     * Delete rows matching current WHERE clauses.
     *
     * @return int Number of deleted rows
     */
    public function ramFn_delete(): int
    {
        $ramVar_sql = "DELETE FROM `{$this->ramVar_fullTable}`";
        [$ramVar_whereClause, $ramVar_whereBindings] = $this->ramFn_buildWhereClause();

        if ($ramVar_whereClause !== '') {
            $ramVar_sql .= ' WHERE ' . $ramVar_whereClause;
        }

        $ramVar_stmt = self::ramFn_prepare($ramVar_sql, $ramVar_whereBindings);
        return $ramVar_stmt->rowCount();
    }

    // ─── Raw Queries ──────────────────────────────────────────────────────────

    /**
     * Execute a raw SQL query with prepared-statement bindings.
     *
     * ALWAYS use this instead of mysqli_query. Never interpolate user data.
     *
     * @param string  $ramVar_sql      SQL with ? placeholders
     * @param mixed[] $ramVar_bindings Values to bind
     * @return \PDOStatement
     */
    public static function ramFn_query(string $ramVar_sql, array $ramVar_bindings = []): \PDOStatement
    {
        return self::ramFn_prepare($ramVar_sql, $ramVar_bindings);
    }

    /**
     * Execute a raw SELECT and return all rows.
     *
     * @param string  $ramVar_sql
     * @param mixed[] $ramVar_bindings
     * @return array<int, array<string, mixed>>
     */
    public static function ramFn_selectRaw(string $ramVar_sql, array $ramVar_bindings = []): array
    {
        return self::ramFn_prepare($ramVar_sql, $ramVar_bindings)->fetchAll() ?: [];
    }

    /**
     * Execute a raw SELECT and return the first row.
     *
     * @return array<string, mixed>|null
     */
    public static function ramFn_selectOne(string $ramVar_sql, array $ramVar_bindings = []): ?array
    {
        $ramVar_row = self::ramFn_prepare($ramVar_sql, $ramVar_bindings)->fetch();
        return $ramVar_row !== false ? $ramVar_row : null;
    }

    // ─── Transactions ─────────────────────────────────────────────────────────

    public static function ramFn_beginTransaction(): void
    {
        self::ramFn_getPdo()->beginTransaction();
    }

    public static function ramFn_commit(): void
    {
        self::ramFn_getPdo()->commit();
    }

    public static function ramFn_rollback(): void
    {
        if (self::ramFn_getPdo()->inTransaction()) {
            self::ramFn_getPdo()->rollBack();
        }
    }

    /**
     * Execute a callback inside a transaction with automatic rollback on exception.
     *
     * @template T
     * @param callable(): T $ramVar_callback
     * @return T
     */
    public static function ramFn_transaction(callable $ramVar_callback): mixed
    {
        self::ramFn_beginTransaction();
        try {
            $ramVar_result = $ramVar_callback();
            self::ramFn_commit();
            return $ramVar_result;
        } catch (\Throwable $ramVar_e) {
            self::ramFn_rollback();
            throw $ramVar_e;
        }
    }

    // ─── Utility ─────────────────────────────────────────────────────────────

    /**
     * Get the full prefixed table name.
     */
    public static function ramFn_tableName(string $ramVar_table): string
    {
        return self::$ramVar_prefix . $ramVar_table;
    }

    /**
     * Get total query count (debug).
     */
    public static function ramFn_getQueryCount(): int
    {
        return self::$ramVar_queryCount;
    }

    /**
     * Get the configured table prefix.
     */
    public static function ramFn_getPrefix(): string
    {
        return self::$ramVar_prefix;
    }

    // ─── Internal SQL builders ────────────────────────────────────────────────

    /**
     * @return array{0: string, 1: mixed[]}
     */
    private function ramFn_buildSelect(): array
    {
        $ramVar_cols = implode(', ', array_map(
            static fn(string $c): string => ($c === '*' || str_contains(strtoupper($c), ' AS ') || str_contains($c, '(')) ? $c : '`' . $c . '`',
            $this->ramVar_selects
        ));

        $ramVar_sql      = "SELECT {$ramVar_cols} FROM `{$this->ramVar_fullTable}`";
        $ramVar_bindings = [];

        // JOINs
        if (!empty($this->ramVar_joins)) {
            $ramVar_sql .= ' ' . implode(' ', $this->ramVar_joins);
        }

        // WHERE
        [$ramVar_whereClause, $ramVar_whereBindings] = $this->ramFn_buildWhereClause();
        if ($ramVar_whereClause !== '') {
            $ramVar_sql      .= ' WHERE ' . $ramVar_whereClause;
            $ramVar_bindings  = array_merge($ramVar_bindings, $ramVar_whereBindings);
        }

        // ORDER BY
        if (!empty($this->ramVar_orders)) {
            $ramVar_orderParts = array_map(
                static fn(array $o): string => '`' . $o['column'] . '` ' . $o['direction'],
                $this->ramVar_orders
            );
            $ramVar_sql .= ' ORDER BY ' . implode(', ', $ramVar_orderParts);
        }

        // LIMIT / OFFSET
        if ($this->ramVar_limit !== null) {
            $ramVar_sql .= ' LIMIT ' . $this->ramVar_limit;
        }
        if ($this->ramVar_offset !== null) {
            $ramVar_sql .= ' OFFSET ' . $this->ramVar_offset;
        }

        return [$ramVar_sql, $ramVar_bindings];
    }

    /**
     * @return array{0: string, 1: mixed[]}
     */
    private function ramFn_buildWhereClause(): array
    {
        if (empty($this->ramVar_wheres)) {
            return ['', []];
        }

        $ramVar_parts    = [];
        $ramVar_bindings = [];
        $ramVar_first    = true;

        foreach ($this->ramVar_wheres as $ramVar_where) {
            $ramVar_prefix = $ramVar_first ? '' : $ramVar_where['boolean'] . ' ';
            $ramVar_first  = false;
            $ramVar_col    = '`' . $ramVar_where['column'] . '`';
            $ramVar_op     = $ramVar_where['op'];

            if (in_array($ramVar_op, ['IS NULL', 'IS NOT NULL'], true)) {
                $ramVar_parts[] = $ramVar_prefix . $ramVar_col . ' ' . $ramVar_op;
            } elseif ($ramVar_op === 'IN' || $ramVar_op === 'NOT IN') {
                $ramVar_vals   = (array) $ramVar_where['value'];
                $ramVar_phs    = implode(', ', array_fill(0, count($ramVar_vals), '?'));
                $ramVar_parts[]   = $ramVar_prefix . $ramVar_col . ' ' . $ramVar_op . ' (' . $ramVar_phs . ')';
                $ramVar_bindings  = array_merge($ramVar_bindings, $ramVar_vals);
            } else {
                $ramVar_parts[]   = $ramVar_prefix . $ramVar_col . ' ' . $ramVar_op . ' ?';
                $ramVar_bindings[] = $ramVar_where['value'];
            }
        }

        return [implode(' ', $ramVar_parts), $ramVar_bindings];
    }

    /**
     * Prepare, execute, and return a PDOStatement.
     *
     * @param string  $ramVar_sql
     * @param mixed[] $ramVar_bindings
     * @throws \RuntimeException on failure
     */
    private static function ramFn_prepare(string $ramVar_sql, array $ramVar_bindings): \PDOStatement
    {
        self::$ramVar_queryCount++;

        try {
            $ramVar_stmt = self::ramFn_getPdo()->prepare($ramVar_sql);
            $ramVar_stmt->execute($ramVar_bindings);
            return $ramVar_stmt;
        } catch (\PDOException $ramVar_e) {
            throw new \RuntimeException(
                'PatalDB Query Error: ' . $ramVar_e->getMessage() . ' | SQL: ' . $ramVar_sql
            );
        }
    }
}
