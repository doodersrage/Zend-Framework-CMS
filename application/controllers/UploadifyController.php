<?PHP
/**
 * @name UploadifyController
 * @desc The controller to serve the main page for our upload example.
 *
 * @author Andrei
 * @filesource application/controllers/UploadifyController.php
 * @version 1.0.0
 */

Class UploadifyController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->layout()->disableLayout();
    }

	public function indexAction(){}

	public function uploadAction()
	{
		if (! empty ( $_FILES ))
		{
			$tempFile = $_FILES ['Filedata'] ['tmp_name'];
			$targetFile = APP_BASE_PATH . $_REQUEST['folder'] . $_FILES ['Filedata'] ['name'];

			move_uploaded_file ( $tempFile, $targetFile );
			$this->view->op = str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
		}
		else
		{
			echo 'No files sent';
		}
	}
}