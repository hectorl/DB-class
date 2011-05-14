<?php
	/**
	 * Clase modelo para MySQL usando las funciones mysql_* de php
	 * 
	 * @author		Héctor Laura
	 * @version		0.52
	 * 
	 */
	 class DB{

		private static $_instance = null;
		
		const HOST = '';
		const USER = '';
		const PASS = '';
		const DB   = '';
		
		private $_bdLink = null;
		private $_type = null;
		private $_tables = array();
		private $_fields = array();
		private $_join = array();
		private $_where = array();		
		private $_group = array();
		private $_order = array();
		private $_limit = null;
		private $_sql = array();
		private $_if = null;
		private $_keys = array();
		private $_tail = null;
		private $_debug = false;


		/**
		 * Conecta con la base de datos y devuelve
		 * 
		 * @param	string	$host	Host donde se encuentra la base de datos
		 * @param	string	$user	Usuario de la base de datos
		 * @param	string	$pass	Contraseña de acceso a la base de datos
		 * @param	string	$debug	Indica si se muestra la información de TODAS las sql's ejecutadas
		 * 
		 * @return	_bdLink
		 */
		private function __construct(){

			$this->_bdLink = @mysql_connect(self::HOST, self::USER, self::PASS);		

			if($this->_bdLink){

				if(@mysql_select_db(self::DB, $this->_bdLink)){

					mysql_set_charset('utf8', $this->_bdLink);

				}else{

					throw new Exception('La selección de la base de datos ha fallado:<br />' . mysql_error());

				}//fin else

			}else{

				throw new Exception('La conexión a la base de datos ha fallado:<br />' . mysql_error());

			}//fin else

			//$this->_debug = $debug;

		}//fin __construct


		/**
		 * Inicializa la clase en caso de ser necesario
		 */
		public static function init(){

			if (!(self::$_instance instanceof self)){

				self::$_instance = new self();
	      
			}//fin if

			return self::$_instance;

		}//fin init


		/**
		 * Establece una conexión con la base de datos
		 */
		public function set_db_connection(){

			return @mysql_connect(self::HOST, self::USER, self::PASS);

		}//fin set_db_connection


		/**
		 * 
		 */
		public function select_db($bdLink){

			if($bdLink){

				if(@mysql_select_db(self::DB, $bdLink)){

					mysql_set_charset('utf8', $bdLink);

				}else{

					return array('type' => 'error', 
							   'text' => 'La selección de la base de datos ha fallado:<br />' . mysql_error(), 
							   'title' => 'Error con la base de datos');

				}//fin else

			}else{

				return array('type' => 'error', 
						   'text' => 'La conexión a la base de datos ha fallado:<br />' . mysql_error(), 
						   'title' => 'Error con la base de datos');

			}//fin else

		}//fin select_db


		/**
		 * 
		 */
		public function __destruct(){

			$this->unset_everything();

		}//fin __destruct


		/**
		 * 
		 */
		private function unset_everything(){
/*
			unset($this->_type);
			unset($this->_tables);
			unset($this->_fields);
			unset($this->_values);
			unset($this->_join);
			unset($this->_where);		
			unset($this->_group);
			unset($this->_order);
			unset($this->_limit);
			unset($this->_sql);
			unset($this->_tail);
			unset($this->_keys);
			unset($this->_if);
*/
			$this->_type = null;
			$this->_tables = array();
			$this->_fields = array();
			$this->_values = array();
			$this->_join = array();
			$this->_where = null;
			$this->_group = array();
			$this->_order = array();
			$this->_limit = null;
			$this->_sql  = array();
			$this->_tail = null;
			$this->_keys = array();
			$this->_if = null;

		}//fin unset_everything


		/**
		 * 
		 */
		public function select($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'SELECT';

		}//fin select

		
		/**
		 *
		 */ 
		public function insert($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'INSERT';

		}//fin insert

		
		/**
		 * 
		 */
		public function update($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'UPDATE';

		}//fin update


		/**
		 * 
		 */
		public function delete($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'DELETE';

		}//fin delete


		/**
		 * Realiza un show; 
		 * Posibles valores en fields: FULL COLUMNS, TABLE STATUS, INDEX, etc.
		 */
		public function show($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'SHOW';

		}//fin show
		
		
		public function create($if, $bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'CREATE';
			$this->_if = $if;

		}//fin create


		/**
		 * 
		 */
		public function prepare($sql, $bdLink = null){


			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			$this->_type = 'PREPARE';
			$this->_sql = $sql;

		}//fin prepare

		/**
		 * 
		 */
		public function begin($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if
			
			mysql_query('BEGIN', $this->_bdLink);

		}//fin begin


		/**
		 * 
		 */
		public function commit($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			mysql_query('COMMIT', $this->_bdLink);

		}//fin commit


		/**
		 * 
		 */
		public function rollback($bdLink = null){

			if($bdLink != null){

				$this->_bdLink = $bdLink;

			}//fin if

			mysql_query('ROLLBACK', $this->_bdLink);

		}//fin rollback		


		/**
		 * Organiza los campos dependiendo del tipo de SQL
		 * 
		 * @param		$fields		Array con los campos. En update e insert, el formato es:
		 * 						array('campo_db1' => 'valor1', 'campo_db2' => 'valor2', etc.)
		 * 
		 * @param		$quotes		Valor bool para indicar si se deben o no añadir comillas en un campo.
		 * 							Útil para campos que se quieren incrementar en un update.
		 */
		public function fields($fields = array('*'), $quotes = true){

			reset($fields);

			while(list($key, $val) = each($fields)){		

				$key = $this->escape($key);				

				if($this->_type == 'INSERT'){

					$this->_fields[] = $key;
					$this->_values[] = $this->escape($val);

				}elseif($this->_type == 'UPDATE'){

					if($quotes){

						$this->_fields[] = $key . '="' .  $this->escape($val) . '"';
						
					}else{

						$this->_fields[] = $key . '=' .  $this->escape($val);

					}//fin else

				}elseif($this->_type == 'SELECT' || $this->_type == 'SHOW'){

					$this->_fields[] = $this->escape($val);

				}elseif($this->_type == 'CREATE'){

					$this->_fields[] = '`' . $this->escape($val[0]) . '` ' . 
								    stripslashes($this->escape($val[1])) . ' ' . 
								    stripslashes($this->escape($val[2])) . 
								    ' COMMENT "' . $this->escape($val[3]) . '"';

				}//fin elseif

			}//fin while

		}//fin fields


		/**
		 * Establece las tablas que se van a usar
		 * 
		 * @param		$tables		Array con las tablas
		 */
		public function tables($tables){

			if(is_array($tables)){

				while(list($key, $val) = each($tables)){

					$this->_tables[] = $this->escape($val);

				}//fin while

			}else{

				$this->_tables[] = $this->escape($tables);

			}//fin else

		}//fin tables


		/**
		 * Añade una línea para hacer un join
		 * 
		 * @param		$table		Tabla con la que se hace el join
		 * @param		$fields		Comparación de los campos
		 * @param		$side		Tipo de join: INNER, LEFT, RIGHT, etc.			
		 */
		public function join($table, $fields, $side = 'INNER'){

			$this->_join[] = $this->escape($side) . ' JOIN ' . $this->escape($table) . ' ON ' . $this->escape($fields); 

		}//fin join


		/**
		 * Establece una línea para hacer un where
		 * 
		 * @param		$comp		Condición
		 * @param		$conc		Tipo de concatenación: AND, OR
		 * @param		$bracket		Parentesis de inicio o final para agrupar condiciones
		 * @param		$escape		Índica si debe o no escapar la condición
		 */
		public function where($comp, $conc = '', $bracket = '', $escape = true){

			$where = '';

			if($bracket == '('){

				$where .= $bracket;

			}//fin if

			$where .= (($escape) ? $this->escape($conc) : $conc) . ' ' . $comp;
			
			if($bracket == ')'){

				$where .= $bracket;

			}//fin if
			
			$this->_where[] = $where;

		}//fin where


		/**
		 * Establece los groups
		 * 
		 * @param		$group		Array con las agrupaciones deseadas
		 */
		public function group($group){

			while(list($key, $val) = each($group)){
				$this->_group[] = $this->escape($val);
			}//fin while
		}//fin group


		/**
		 * Estblece el orden y el tipo en el que serán mostrados los resultados
		 * 
		 * @param		$order		Campo por el que queremos el orden
		 * @param		$orderType	Tipo de orden: ASC, DES
		 */
		public function order($order, $orderType = 'ASC'){
			$this->_order[] = $this->escape($order) . ' ' . $this->escape($orderType);
		}//fin order


		/**
		 * Rango de registros que queremos sacar
		 * 
		 * @param		$from		Desde que registro vamos a sacar resultados
		 * @param		$length		Número de registros que vamos a sacar
		 */
		public function limit($from, $length){
			$this->_limit = $this->escape($from) . ', ' . $this->escape($length);
		}//fin limit


		/**
		 * 
		 */
		public function keys($keys){

			while(list($key, $val) = each($keys)){

				if($val[1] != 'PRIMARY KEY' && $val[1] != 'UNIQUE'){

					$this->_keys[] = 'KEY `' . $this->escape($val[0]) . '` (`' . $this->escape($val[1]) . '`)';

				}else{

					$this->_keys[] = $val[1] . ' (`' . $this->escape($val[0]) . '`)';

				}//fin else

			}//fin while

		}//fin keys


		/**
		 * 
		 */
		public function tail($engine, $charset, $comment = '', $increment = null){			

			$this->_tail = 'ENGINE=' . $engine . ' DEFAULT CHARSET=' . $charset . ' COMMENT="' . $comment . '"';

			if($increment != null){

				$this->_tail .= ' AUTO_INCREMENT=' . $increment;				

			}//fin if

		}//fin tail



		/**
		 * Ejecuta la consulta
		 * 
		 * @param		$debug		Valor bool que índica si se muestra o no la consulta
		 */		
		public function execute($debug = false, $debug_ajax = false){

			switch($this->_type){

				case 'SELECT': $resp = $this->do_select();
					break;
				case 'INSERT': $resp = $this->do_insert();
					break;
				case 'UPDATE': $resp = $this->do_update();
					break;
				case 'DELETE': $resp = $this->do_delete();
					break;
				case 'SHOW': $resp = $this->do_show();
					break;
				case 'CREATE': $resp = $this->do_create();
					break;
				case 'PREPARE': $resp = $this->do_prepare();
					break;					

			}//fin switch

			if($this->_debug || $debug){

				$this->do_debug($debug_ajax);

			}//fin if
			
			$this->unset_everything();

			return $resp;

		}//fin execute;

		
		/**
		 * Realiza el SELECT en base a todos los parametros dados
		 * 
		 * @return	$rows		Array con los registros devueltos
		 */
		private function do_select(){

			$this->_sql[] = 'SELECT';
			$this->_sql[] = implode(', ', $this->_fields);
			$this->_sql[] = 'FROM';
			$this->_sql[] = implode(', ', $this->_tables);

			if(sizeof($this->_join) > 0){

				$this->_sql[] = implode(' ', $this->_join);

			}//fin if

			if(sizeof($this->_where) > 0){

				$this->_sql[] = 'WHERE';
				$this->_sql[] = implode(' ', $this->_where);

			}//fin if

			if(sizeof($this->_group) > 0){

				$this->_sql[] = 'GROUP BY';
				$this->_sql[] = implode(', ', $this->_group);

			}//fin if

			if(sizeof($this->_order) > 0){

				$this->_sql[] = 'ORDER BY';
				$this->_sql[] = implode(', ', $this->_order);

			}//fin if

			if(strlen($this->_limit) > 0){

				$this->_sql[] = 'LIMIT ' . $this->_limit; 	

			}//fin if		

			$sql = implode(' ', $this->_sql);

			$result = mysql_query($sql) or die(mysql_error());

			$rows = array();

			while($fetch = mysql_fetch_assoc($result)){

				while(list($field, $val) = each($fetch)){			    

					$row[$field] = str_replace('\\', '', $val);

				}//fin while

				$rows[] = $row;

			}//fin while

			return $rows;

		}//fin do_select


		/**
		 * 
		 */
		private function do_insert(){

			$this->_sql[] = 'INSERT INTO';
			$this->_sql[] = implode(', ', $this->_tables);
			$this->_sql[] = '(' . implode(', ', $this->_fields) . ')';
			$this->_sql[] = 'VALUES';
			$this->_sql[] = '("' . implode('", "', $this->_values) . '")';

			$sql= implode(' ', $this->_sql);

			return mysql_query($sql) ? true : false;

		}//fin 


		/**
		 * 
		 */
		private function do_update(){

			$this->_sql[] = 'UPDATE';
			$this->_sql[] = implode(', ', $this->_tables);
			$this->_sql[] = 'SET';
			$this->_sql[] = implode(', ', $this->_fields);

			if(sizeof($this->_where) > 0){

				$this->_sql[] = 'WHERE';
				$this->_sql[] = implode(' ', $this->_where);

			}//fin if

			$sql= implode(' ', $this->_sql);

			return mysql_query($sql) ? true : false;

		}//fin do_update


		/**
		 * 
		 */
		private function do_delete(){

			$this->_sql[] = 'DELETE FROM';
			$this->_sql[] = implode(', ', $this->_tables);

			if(sizeof($this->_where) > 0){

				$this->_sql[] = 'WHERE';
				$this->_sql[] = implode(' ', $this->_where);

			}//fin if

			$sql= implode(' ', $this->_sql);

			return mysql_query($sql) ? true : false;

		}//fin do_delete


		/**
		 * 
		 */
		private function do_show(){

			$this->_sql[] = 'SHOW';
			$this->_sql[] = implode(', ', $this->_fields);
			$this->_sql[] = 'FROM';
			
			if(sizeof($this->_tables) > 0){

				$this->_sql[] = implode(' ', $this->_tables);

			}else{

				$this->_sql[] = self::DB;

			}//fin else

			if(sizeof($this->_where) > 0){

				$this->_sql[] = 'WHERE';
				$this->_sql[] = implode(' ', $this->_where);

			}//fin if

			$sql = implode(' ', $this->_sql);

			$result = mysql_query($sql) or die(mysql_error());

			while($fetch = mysql_fetch_assoc($result)){

				while(list($field, $val) = each($fetch)){			    

					$row[$field] = $val;		

				}//fin while

				$rows[] = $row;

			}//fin while

			return $rows;

		}//fin do_show


		/**
		 * 
		 */
		private function do_create(){

			$this->_sql[] = 'CREATE TABLE';
			$this->_sql[] = $this->_if;
			$this->_sql[] = implode(' ', $this->_tables);
			$this->_sql[] = '(';
			$this->_sql[] = implode(', ', $this->_fields);
			$this->_sql[] = ', ' . implode(', ', $this->_keys);
			$this->_sql[] = ')';
			$this->_sql[] = $this->_tail;

			$sql = implode(' ', $this->_sql);

			return mysql_query($sql) ? true : false;

		}//fin do_create


		/**
		 * Ejecuta una consulta cualquiera
		 * 
		 * @return	bool		true -> Consulta correcta; false -> Consulta incorrecta
		 */
		private function do_prepare(){

			return mysql_query($this->_sql) ? true : false;

		}//fin do_prepare
		

		/**
		 * Comprueba si la conexión es correcta
		 * 
		 * @param	string	$host		Nombre del servidor MySQL
		 * @param	string	$user		Nombre del usuario de la bd
		 * @param	string	$pass		Contraseña de la bd
		 * @param	string	$db			Base de datos con la que se quiere conectar
		 * 
		 * @return	bool			true -> correcto; false -> incorrecto
		 */
		public function check_connection($host, $user, $pass, $db){

			$bdLink =  mysql_connect($host, $user, $pass);
			return (mysql_select_db($db, $bdLink)) ? true : false;

		}//fin check_connection



		/**
		 * Escapa una variable por medio de la función mysql_real_escape_string
		 * 
		 * @param		$var		Valor a escapar
		 * 
		 * @return	Valor escapado
		 */
		public function escape($var){

			return mysql_real_escape_string($var, $this->_bdLink);

		}//fin escape

		
		/**
		 * Genera un identificador único
		 * 
		 * @param		string	$prefix		Prefijo del identificadro
		 * 
		 * @return	string	El identificador único
		 */
		public function get_uid($prefix = null){

			return $prefix . md5(uniqid(mt_rand(), true));

		}//fin get_uid



		/**
		 * Saca el último id insertado
		 */
		public function last_id(){

			return mysql_insert_id($this->_bdLink);

		}//fin last_id


		/**
		 * 
		 */
		public function do_debug($debug_ajax){

			$this->_sql = array();

			switch($this->_type){

				case 'SELECT': $info = $this->do_select_debug($debug_ajax);
					break;
				case 'INSERT': $info = $this->do_insert_debug($debug_ajax);
					break;
				case 'UPDATE': $info = $this->do_update_debug($debug_ajax);
					break;
				/*
				case 'DELETE': $info = $this->do_delete_debug($debug);
					break;
				case 'SHOW': $info = $this->do_show_debug($debug);
					break;
				case 'CREATE': $info = $this->do_create_debug($debug);
					break;
				case 'PREPARE': $info = $this->do_prepare_debug($debug);
					break;					
				*/

			}//fin switch

			if($debug_ajax){

				echo $info;

			}else{

				echo '<script type="text/javascript">
						debug = window.open("","DB_Debug","width=400,height=200,resizable,scrollbars=yes,titlebar=1,top=600,left=1500");
						debug.document.write("<div style=\"font-size:10px; font-family:sans-serif\"><h1>SQL debug</h1>' . str_replace('"', '&quot', $info) . '</div>");							
					</script>';

			}//fin else		 

		}//fin do_debug


		private function do_select_debug($debug_ajax){

			$this->_sql[] = '<b>SELECT</b>';
			$this->_sql[] = implode(', ', $this->_fields);
			$this->_sql[] = '<b>FROM</b>';
			$this->_sql[] = implode(', ', $this->_tables);

			if(sizeof($this->_join) > 0){

				$this->_sql[] = implode(' ', $this->_join);

			}//fin if

			if(sizeof($this->_where) > 0){

				$this->_sql[] = '<b>WHERE</b>';
				$this->_sql[] = implode(' ', $this->_where);

			}//fin if

			if(sizeof($this->_group) > 0){

				$this->_sql[] = '<b>GROUP BY</b>';
				$this->_sql[] = implode(', ', $this->_group);

			}//fin if

			if(sizeof($this->_order) > 0){

				$this->_sql[] = '<b>ORDER BY</b>';
				$this->_sql[] = implode(', ', $this->_order);

			}//fin if

			if(strlen($this->_limit) > 0){

				$this->_sql[] = '<b>LIMIT</b> ' . $this->_limit; 	

			}//fin if		

			return implode('<br/>', $this->_sql);
			
		}//fin do_debug_select


		/**
		 * 
		 */
		private function do_insert_debug(){

			$this->_sql[] = '<b>INSERT INTO</b>';
			$this->_sql[] = implode(', ', $this->_tables);
			$this->_sql[] = '<b>(</b>' . implode(', ', $this->_fields) . '<b>)</b>';
			$this->_sql[] = '<b>VALUES</b>';
			$this->_sql[] = '<b>(</b>"' . implode('", "', $this->_values) . '"<b>)</b>';

			return implode(' ', $this->_sql);

		}//fin do_debug_insert


		/**
		 * 
		 */
		private function do_update_debug(){

			$this->_sql[] = '<b>UPDATE</b>';
			$this->_sql[] = implode(', ', $this->_tables);
			$this->_sql[] = '<b>SET</b>';
			$this->_sql[] = implode(', ', $this->_fields);

			if(sizeof($this->_where) > 0){

				$this->_sql[] = '<b>WHERE</b>';
				$this->_sql[] = implode(' ', $this->_where);

			}//fin if

			return implode(' ', $this->_sql);

		}//fin do_debug_insert

	}//fin MODELO
