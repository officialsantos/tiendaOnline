<?php

class Database {

	private $host = "sql107.infinityfree.com";
	private $dbname = "if0_39331618_ecomm"; // Reemplazá con el nombre correcto de tu base de datos
	private $username = "if0_39331618";
	private $password = "GewsKfWastFpra";
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
