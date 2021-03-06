<?php
namespace App\Controller;

use App\Config\Session as Session;
use App\Model\TeacherModel as Teacher;
use App\Model\InstitutionModel as Institution;
/**
* 
*/
class AuthController
{
	
	private $_session;

	function __construct()
	{
	}

	/**
	* @params
	*
	* @return
	*/
	public function indexAction()
	{

	}

	/**
	 *
	 * @param
	 * @return
	*/
	public function loginAction()
	{
		if(isset($_POST)):

			// Validamos la session
		if(!Session::check('authenticated')):

			$teacher = new Teacher($_POST['db']);
			$institution = new Institution($_POST['db']);
			$resp = $teacher->find($_POST['id_teacher']);

			// Preguntamos si hay resultados
			if($resp['state']):

				// Creamos las variables de session
				Session::set('authenticated', true);
				Session::set('db', $_POST['db']);
				Session::set('id_teacher', $resp['data'][0]['id_docente']);
				Session::set('institution', $institution->getInfo()['data']);
				Session::set('rol', 'teacher');

				// Redireccionamos al home
				header("Location: /");
			endif;
			
		else:
			echo "404";
		endif;

		endif;
	}

	/**
	* @params
	*
	* @return
	*/
	public function loginTestAction($db='', $id_teacher)
	{	
		// Validamos la session
		if(!Session::check('authenticated')):

			$teacher = new Teacher($db);
			$resp = $teacher->find($id_teacher);
			$institution = new Institution($db);

			// Preguntamos si hay resultados
			if($resp['state']):

				// Creamos las variables de session
				Session::set('db', $db);
				Session::set('rol', 'teacher');
				Session::set('authenticated', true);
				Session::set('id_teacher', $resp['data'][0]['id_docente']);
				Session::set('institution', $institution->getInfo()['data']);

				// Redireccionamos al home
				header("Location: /");
			endif;
			
		else:
			echo "404";
		endif;
	}
}
?>