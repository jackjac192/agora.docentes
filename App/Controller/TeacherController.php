<?php

namespace App\Controller;

use App\Config\View as View;
use App\Config\Session as Session;
use App\Model\GroupModel as Group;
use App\Model\PeriodModel as Period;
use App\Model\TeacherModel as Teacher;
use App\Model\AsignatureModel as Asignature;
use App\Model\EvaluationPeriodModel as Evaluation;
/**
* 
*/
class TeacherController
{
	
	private $_group;
	private $_teacher;
	private $_periods;
	private $_session;
	private $_asignature;
	private $_evaluation;

	function __construct()
	{	

		if(Session::check('authenticated')):
			$this->_group = new Group(Session::get('db'));
			$this->_periods = new Period(Session::get('db'));
			$this->_teacher = new Teacher(Session::get('db'));	
			$this->_asignature = new Asignature(Session::get('db'));
			$this->_evaluation = new Evaluation(Session::get('db'));
		else:
			$view = new View(
				'http',
				'404'
			);

			$view->execute();
		endif;
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function HomeAction($db='', $idTeacher='')
	{

		$asginatures = $this->_teacher->getAsignaturesAndGroups($_SESSION['id_teacher'])['data'];

		$view = new View(
			'teacher',
			'home',
			[
				'asginatures'	=>	$asginatures
			]
		);

		$view->execute();
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function indexAction($db='', $idTeacher='')
	{	

		if(Session::check('authenticated')):
			$subheader = array(
			'title'	=>	'Inicio',
			'icon'	=>	'fa fa-home',
				'items'	=>	array()
			);

			$view = new View(
				'teacher',
				'index',
				[
					'include'		=>	'partials/home.tpl.php',
					'subheader'		=>	$subheader,
					'institution'	=>	$_SESSION['institution']
				]
			);
			$view->execute();
		endif;
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function evaluationAction()
	{	
		if(Session::check('authenticated')):
			$groupsAndAsign = $this->_teacher->getAsignaturesAndGroups($_SESSION['id_teacher'])['data'];
				
			// Creamos el subheader para los menus horizontal
			$subheader = array(
				'title'	=>	'Evaluación',
				'icon'	=>	'fa fa-check',
				'items'	=>	array(
					1	=>	array(
						'title'	=>	'Evaluar',
						'link'	=>	'/teacher/evaluate',
						'active' =>	'active'
					)
				),
			);

			// Preguntamos si el docente es director de algun grupo
			if($this->_teacher->isDirector($_SESSION['id_teacher']))
			{
				array_push($subheader['items'], array(
					'title'	=>	'Observaciones Generales',
					'link'	=>	'/generalObservation/index/teacher',
					'active' =>	''
				));
				array_push($subheader['items'], array(
					'title'	=>	'Informe General de Periodo',
					'link'	=>	'/generalReportPeriod/index/teacher',
					'active' =>	''
				));
			}

			$view = new View(
				'teacher',
				'index',
				[
					'tittle_panel'		=>	'',
					'include'			=>	'partials/evaluation/home.tpl.php',
					'subheader'			=>	$subheader,
					'groupsAndAsign'	=>	$groupsAndAsign,
					'institution'	=>	$_SESSION['institution']
				]
			);

			$view->execute();
		endif;
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function sheetsAction()
	{	
		if(Session::check('authenticated')):
			$periods = $this->_periods->all()['data'];
			$asignatures = $this->_teacher->getAsignaturesAndGroups($_SESSION['id_teacher'])['data'];

			$subheader = array(
				'title'	=>	'Planillas',
				'icon'	=>	'fa fa-file-text-o',
				'items'	=>	array()
			);
			$view = new View(
				'teacher',
				'index',
				[
					'include'		=>	'partials/sheets/home.tpl.php',
					'tittle_panel'	=>	'Planillas',
					'institution'	=>	$_SESSION['institution'],
					'subheader'		=>	$subheader,
					'asignatures'	=>	$asignatures,
					'periods'		=>	$periods
				]
			);

			$view->execute();
		endif;
	}


	/**
	 * 
	 * @param
	 * @return
	*/
	public function StatisticsAction()
	{
		if(Session::check('authenticated')):
			// Creamos el subheader para los menus horizontal
			$subheader = array(
				'title'	=>	'Estadisticas',
				'icon'	=>	'fa fa-line-chart',
				'items'	=>	array(
					1	=>	array(
						'title'	=>	'Consolidado',
						'link'	=>	'/statistic/consolidateEvaluation',
						'active' =>	'active'
					),
					2	=>	array(
						'title'		=>	'Estudiante',
						'link'		=>	'#',
						'active'	=>	'',
						'subItem'	=>	array(
							1	=>	array(
								'tittle'	=>	'Matriculado por sexo',
								'link'		=>	'/statistic/studentSexRegistered'
							),
							2	=>	array(
								'tittle'	=>	'Eficiencia',
								'link'		=>	'/statistic/studentEfficiency'
							)
						)
					),
					3	=>	array(
						'title'		=>	'Reprobados',
						'link'		=>	'#',
						'active'	=>	'',
						'subItem'	=>	array(
							1	=>	array(
								'tittle'	=>	'Estudianes Reprobados',
								'link'		=>	'/statistic/studentDisapproved'
							),
							2	=>	array(
								'tittle'	=>	'Reprobrados Detallados',
								'link'		=>	'/statistic/detailDisapproved'
							)
						)
					),
					4	=>	array(
						'title'	=>	'Desempeño por Docente',
						'link'	=>	'/statistic/performanceByTeacher',
						'active' =>	''
					)
				),
			);

			$view = new View(
				'teacher',
				'index',
				[
					'tittle_panel'		=>	'',
					'include'			=>	'partials/statistic/consolidate/consolidate.tpl.php',
					'subheader'			=>	$subheader,
					'institution'	=>	$_SESSION['institution']
				]
			);

			$view->execute();
		endif;
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function settingsAction()
	{	
		if(Session::check('authenticated')):
			// 
			$info = $this->_teacher->find(Session::get('id_teacher'));

			// Creamos el subheader para los menus horizontal
			$subheader = array(
				'title'	=>	'Configuracio',
				'icon'	=>	'fa fa-cog',
				'items'	=>	array(
					1	=>	array(
						'title'	=>	'Configuracion de la cuenta',
						'link'	=>	'/settings/index/teacher',
						'active' =>	'active'
					),
					// 2	=>	array(
					// 	'title'		=>	'Seguridad e inicio de sesión',
					// 	'link'		=>	'/settings/security/teacher',
					// 	'active'	=>	''
					// )
				),
			);

			$view = new View(
				'teacher',
				'index',
				[
					'tittle_panel'	=>	'',
					'subheader'		=>	$subheader,
					'include'		=>	'partials/settings/account/general.tpl.php',
					'info'			=>	$info['data'][0],
					'institution'	=>	$_SESSION['institution']

				]
			);

			$view->execute();
		endif;
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function evaluateAction()
	{	

		$groupsAndAsign = $this->_teacher->getAsignaturesAndGroups($_SESSION['id_teacher'])['data'];

		$view = new View(
			'teacher/partials/evaluation',
			'home',
			[
				'tittle_panel'		=>	'Evaluar Periodo',
				'groupsAndAsign'	=>	$groupsAndAsign
			]
		);

		$view->execute();
	}


	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function showFormEvaluatePeriodAction()
	{
		// Validamos la peticion GET
		if(isset($_GET['options']['request']) && $_GET['options']['request']== 'spa'):
			
			$infoGroup = $this->_group->find($_GET['id_group'])['data'][0];
			$infoAsignature = $this->_asignature->find($_GET['id_asignature'])['data'][0];

			$view = new View(
				'teacher/partials/evaluation',
				'formEvaluatePeriod',
				[
					'tittle_panel'	=>	'Evaluar periodo pendiente',
					'group'			=> 	$infoGroup,
					'asignature'	=>	$infoAsignature,
					'back'			=>	$_GET['options']['back']
				]
			);

			$view->execute();

		else:
			echo "404 no se puede mostrar esta pagina";
		endif;
	}


	/**
	 *
	 *
	 *
	*/
	public function performanceByGroup()
	{
		
	} 

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function getStudentWithoutPeriodEvaluationAction(
		$column,
		$id_asignature, 
		$id_group
	){

		$students = $this->_evaluation->getPeriodsWithOutEvaluating($column, $id_asignature, $id_group)['data'];

		$view = new View(
			'teacher/partials/evaluation',
			'formEvaluatePeriodRender',
			[
				'students'	=> $students,
				'periodo'	=>	$column
			]
		);

		$view->execute();
	}

	/**
	 *
	 *	@param
	 *  @return
	*/ 
	public function updatePeriodAction($period, $id_student, $id_asignature, $value)
	{
		echo $this->_evaluation->updatePeriod($period, $id_student, $id_asignature, $value);
	}
}
?>