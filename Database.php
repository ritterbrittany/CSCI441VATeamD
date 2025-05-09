
<?php
class Database {
    private $conn;

    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;

    public function __construct() {
        // Load .env file if not in production (e.g., local development)
        if (getenv('APP_ENV') !== 'production' && file_exists(__DIR__ . '/.env')) {
            $dotenv = parse_ini_file(__DIR__ . '/.env');
            foreach ($dotenv as $key => $value) {
                putenv("$key=$value");
            }
        }

        $this->host = getenv('DATABASE_HOST') ?: 'localhost';
        $this->port = getenv('DATABASE_PORT') ?: '5432';
        $this->db_name = getenv('DATABASE_NAME');
        $this->username = getenv('DATABASE_USER');
        $this->password = getenv('DATABASE_PASSWORD');

        // Validate required credentials
        if (empty($this->db_name) || empty($this->username)) {
            throw new RuntimeException(
                "Missing required database credentials. " .
                "Verify DATABASE_NAME and DATABASE_USER are set in your environment."
            );
        }
    }

    public function connect() {
        $this->conn = null;

        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=require";


        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            // Test connection immediately
            $this->conn->query("SELECT 1")->fetch();

        } catch (PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            
            // More detailed error for development
            if (getenv('APP_ENV') === 'development') {
                throw new RuntimeException(
                    "Failed to connect to database: " . $e->getMessage() . 
                    "\nDSN: " . str_replace($this->password, '*****', $dsn)
                );
            } else {
                throw new RuntimeException("Database connection failed");
            }
        }

        return $this->conn;
    }

    public function getConnection() {
        return $this->conn ?? $this->connect();
    }
}