<?php

class Database {

	private $host = "localhost"; // local
	private $dbname = "ecomm"; // nombre de la base de datos que creaste en localhost
	private $username = "root"; // usuario predeterminado de XAMPP
	private $password = ""; // sin contraseña por defecto
	private $options  = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	);
	protected $conn;

	public function open() {
		try {
			$this->conn = new PDO(
				"mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
				$this->username,
				$this->password,
				$this->options
			);
			return $this->conn;
		} catch (PDOException $e) {
			echo "Error de conexión: " . $e->getMessage();
		}
	}

	public function close() {
		$this->conn = null;
	}
}

$pdo = new Database();
?>
