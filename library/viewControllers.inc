<?php
/**
 * 	The base view controller class that is to be extended by any view controller.
 */
abstract class BaseViewController {
	private $viewString;
	private $viewTemplate;
	private $node;

	private function __construct() {
	}

	public abstract function getView($node);

}

/**
 * 	The standard page view class.  Extends ViewController.
 */
class ViewController extends BaseViewController {
	public function getView($node) {
		$sql = 'SELECT * FROM nodes WHERE id=:id';
		$stmt = ConnectionClass::getInstance() -> prepare($sql);
		$stmt -> execute(array(':id' => $node));
		$row = $stmt -> fetch(PDO::FETCH_ASSOC);

		$pageText = file_get_contents($row['base_file']);

		$title = $row['title'];
		$cssName = $row['css_file'];
		$content = $row['content'];

		/**
		 * If the user is an admin, add the admin menu to the file.
		 */
		$displayText = str_replace('{pTopLogin}', file_get_contents('templates/lineStyleLoginForm.html'), $pageText);
		if (Member::getById(1)) {
			$displayText = str_replace('{pAdminMenu}', file_get_contents('templates/adminMenu.html'), $displayText);
		} else {
			$displayText = str_replace('{pAdminMenu}', '', $displayText);
		}
		$displayText = str_replace('{pAction}', 'index.php', $displayText);
		$displayText = str_replace('{pTitle}', $title, $displayText);
		$displayText = str_replace('{pCssRef}', $cssName, $displayText);
		$displayText = str_replace('{pContent}', $content, $displayText);
		$displayText .= '</br>';
		$displayText .= Member::register( array( 'username' => 'adminBill', 'email' => 'adminBill@manoutdoors.com' ));
		return $displayText;
	}
}
?>