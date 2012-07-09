<?php
interface IData {
 	public static function getById( $id );
	public static function getAll();
	public static function insert( $value );
	public static function update( $value );
	public static function delete( $id );
}
?>