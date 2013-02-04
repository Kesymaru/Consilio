<?php
/*******************************************
					MySQL Session Manager
										by
							Peter Haza
					<peter.haza@gmail.com>

						Released under the
 Creative Commons Attribution 2.5 License
http://creativecommons.org/licenses/by/2.5/
*******************************************/

// Mysql user
define( 'root', '' );

// Mysql password
define( 'DB_PASSWORD', '' );

// Mysql database name
define( 'matriz', '' );

// Mysql host
define( 'localhost', null );

// Mysql host port
define( 'DB_PORT', null );

// Name of the session table
define( 'session', 'sessions' );


/**
 * This class will save sessions in mysql instead
 * of potentialy more insecure files.
 */
class MysqlSessionManager
{
	private $link = null;
	private $DB_SESSION_TABLE = 'session';

	public function __construct( $db_username, $db_password, $db_name, $db_host = null, $db_port = null )
	{
		$host = $db_host == null ? 'localhost' : $db_host;
		$host .= ':'. ( $db_port == null ? 3306 : $db_port );

		$link = mysql_connect( $host, $db_username, $db_password );

		if( $link )
		{
			if( mysql_select_db( $db_name, $link ) )
			{
				$this->link = $link;
				return true;
			}
			else
			{
				throw new Exception( 'DB error in '.__CLASS__.'. Could not select database. The error was: '.@mysql_error( $link ) );
			}
		}
		else
		{
			throw new Exception( 'DB error in '.__CLASS__.'. Could not connect. The error was: '.@mysql_error( $link ) );
		}

		return false;
	}

	public function __destruct()
	{
		session_write_close();
	}

	public function close()
	{
		if( $this->link != null )
		{
			return mysql_close( $this->link );
		}
	}

	public function destroy( $session_id )
	{
		return mysql_query( "DELETE * FROM ".$this->DB_SESSION_TABLE." WHERE session_id = '".$session_id."'", $this->link );
	}

	public function gc( $not_in_use )
	{
		return mysql_query( 'DELETE * FROM '.$this->DB_SESSION_TABLE.' WHERE session_expires < '.time(), $this->link );
	}

	public function read( $session_id )
	{
		$res = mysql_query( "SELECT session_data FROM ".$this->DB_SESSION_TABLE." WHERE session_id = '".$session_id."'" );

		if( $res )
		{
			if( $row = mysql_fetch_assoc( $res ) )
			{
				return stripslashes( $row['session_data'] );
			}
		}

		return '';
	}

	public function open( $session_name )
	{
		$this->gc( ini_get( 'session.gc_maxlifetime' ) );
		return $this->link != null;
	}

	public function write( $session_id, $session_data )
	{
		$data = mysql_real_escape_string( $session_data, $this->link );
		$time = time() + ini_get( 'session.gc_maxlifetime' );

		$res = mysql_query( "INSERT INTO ".$this->DB_SESSION_TABLE." (session_id, session_data, session_expires) VALUES ('".
			$session_id."', '".$data."', ".$time.") ON DUPLICATE KEY UPDATE session_data = '".$data."', session_expires = ".$time );

		return $res;
	}
}

$msm = new MysqlSessionManager( 'root', '', 'matriz', 'localhost','');
//$msm = new MysqlSessionManager( DB_USERNAME, DB_PASSWORD, DB_NAME, DB_HOST, DB_PORT );

session_set_save_handler(
	array( &$msm, "open" ),
	array( &$msm, "close" ),
	array( &$msm, "read" ),
	array( &$msm, "write" ),
	array( &$msm, "destroy" ),
	array( &$msm, "gc" ) );
?>