<?php
namespace MYSQL {
/*------------------------------------------------------------*\
 * Accessor : PDO MySQL Connection Package
- 2014.04.26 kj@iis.sinica
 *  Accessor(SERVER,DATABASE,USERNAME,PASSWORD)
-      使用 USERNAME 與 PASSWORD 連結到 SERVER 上的 DATABASE
-      預設使用 UTF-8 編碼
 *  writeLogFile(MESSAGE)
-      在執行目錄下建立 (日期)_(IP).log 檔案，記錄 MESSAGE
 *  _query(SQL[,ARRAY OF VARIABLES])
-      return: PDO::PDOStatement
-      進行 SELECT SQL 語法，並回傳 PDO::PDOStatement
 *  _execute(SQL[,ARRAY OF VARIABLES])
-      return: 最後修改的 ID
-      執行 SQL 語法，並回傳最後修改的 ID
-      有錯誤時不會有回傳值
\*------------------------------------------------------------*/
	define( 'DB_SERVER', 'localhost' );
	define( 'DB_USERNAME', 'joanne3634' );
	define( 'DB_PASSWORD', '369369' );
	define( 'DB_DATABASE', 'appledaily' );
	define( 'DB_ENCODING', 'utf8' );

	class Accessor {
		private $pdo = null;
		public $RECORD_SESSION = false;

		function __construct() {
			$options = array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.DB_ENCODING);
			$this->pdo = new \PDO('mysql:host='.DB_SERVER.';dbname='.DB_DATABASE,DB_USERNAME,DB_PASSWORD,$options);
			if (!$this->pdo) {$this->writeLogFile('Create PDO fail.');}
		}

		function writeLogFile($message) {
			$prefix = '';
			if ($this->RECORD_SESSION) {
				$sid = session_id();
				if ($sid == '') {
					session_start();
					$sid = session_id();
				}
				$prefix = 'SESSION(' . $sid . ') ';
			}
			$ipaddr = isset($_SERVER['REMOTE_ADDR']) ? str_replace('.', '-', $_SERVER['REMOTE_ADDR']) : null;
			if (empty($ipaddr)) {$ipaddr = 'UNKNOWN';}
			$fp = fopen('../../www-data/log_errors/' . date('Y-m-d') . '_' . $ipaddr . '.log', 'a');
			fwrite($fp, $prefix . $message . PHP_EOL);
			fclose($fp);
		}

		public function _query($sql, $args = null) {
			// print( $sql );
			$result = null;
			if ($args != null) {
				$stmt = $this->pdo->prepare($sql);
				if ($stmt->execute($args)) {$result = $stmt;}
			} else { $result = $this->pdo->query($sql);}
			if ($result === null) {$this->writeLogFile('Invalid Argument: MYSQL\Accessor::_query.');}
			if ($result === false) {
				$errinfo = $this->pdo->errorInfo();
				$this->writeLogFile('PDO Exception: ' . $errinfo[2]);
				$this->writeLogFile('SQL: ' . $sql);
				$this->writeLogFile('DATA: ' . print_r($args, true));
				return false;
			}
			return $result;
		}

		public function _execute($sql, $args = null) {
			if ($args == null) {$args = array();}
			$stmt = $this->pdo->prepare($sql);
			if ($stmt->execute($args)) {
				try
				{
					$lastid = $this->pdo->lastInsertId();
					return $lastid;
				} catch (PDOException $e) {$this->writeLogFile('Fail to get last ID: ' . $e->getMessage());}
			} else {
				$errinfo = $stmt->errorInfo();
				$this->writeLogFile('PDO Exception: ' . $errinfo[2]);
				$this->writeLogFile('SQL: ' . $sql);
				$this->writeLogFile('DATA: ' . print_r($args, true));
			}
		}

	}
}