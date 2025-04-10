
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
        if (getenv('APP_ENV') !== 'production' && file_exists(__DIR__ . '/../.env')) {
            $dotenv = parse_ini_file(__DIR__ . '/../.env');
            foreach ($dotenv as $key => $value) {
                putenv("$key=$value");
            }
        }

        // Set configuration with fallback values
        $this->host = 'dpg-cvqn0he3jp1c73dsfnvg-a.ohio-postgres.render.com';  // Your database host
        $this->port = '5432'; // Database port
        $this->db_name = 'emr_platform'; // Database name
        $this->username = 'emr_platform_user'; // Database username
        $this->password = 'rBirGywJYnVMuJHFFuc8pYvTJIyrJXik'; // Database password

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

        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";

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