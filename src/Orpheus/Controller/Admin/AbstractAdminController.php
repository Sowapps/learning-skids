<?php

namespace Orpheus\Controller\Admin;

use App\Controller\AbstractHttpController;
use App\Entity\User;
use DateTime;
use Exception;
use Orpheus\Rendering\HTMLRendering;

abstract class AbstractAdminController extends AbstractHttpController {
	
	const OPTION_CONTENT_TITLE = 'contentTitle';
	const OPTION_CONTENT_LEGEND = 'contentLegend';
	
	protected string $scope = self::SCOPE_ADMIN;
	
	protected $breadcrumb = [];
	protected array $notifications = [];
	
	public function addNotification($title, $text, $isWarning = false, ?DateTime $date = null) {
		$this->notifications[] = (object) [
			'title' => $title,
			'text'  => $text,
			'type'  => $isWarning ? 'warning' : 'normal',
			'date'  => $date,
		];
	}
	
	public function hasNotification() {
		return !!$this->notifications;
	}
	
	public function getNotifications() {
		return $this->notifications;
	}
	
	public function addThisToBreadcrumb($label = null, $link = false) {
		$this->addRouteToBreadcrumb($this->getRouteName(), $label, $link);
	}
	
	/**
	 * Add given route to breadcrumb
	 * Label is optional, else we translate the route name
	 * Link could be
	 *  - disabled using false
	 *  - auto-generated using true or an array of value (passed as values)
	 *  - Specified using string
	 *
	 * @param $route
	 * @param string|null $label
	 * @param string|bool|array $link
	 * @throws Exception
	 */
	public function addRouteToBreadcrumb($route, $label = null, $link = true) {
		if( !$link ) {
			$link = null;
			
		} elseif( typeOf($link) !== 'string' ) {
			// Could be true => generate with no args
			// Could be an array => generate using args
			$params = $this->getValues();
			if( is_array($link) ) {
				$params += $link;
			}
			$link = u($route, $params);
		}
		$this->addBreadcrumb($label ? $label : t($route), $link);
	}
	
	public function addBreadcrumb($label, $link = null) {
		$this->breadcrumb[] = (object) ['label' => $label, 'link' => $link];
	}
	
	public function preRun($request) {
		parent::preRun($request);
		
		HTMLRendering::setDefaultTheme('admin');
		$user = User::getLoggedUser();
		
		// Else Admin only
		if( $user->isAdminUser() ) {
			$this->addRouteToBreadcrumb(getHomeRoute());
		}
		
		return null;
	}
	
	public function render($response, $layout, $values = []) {
		$values['breadcrumb'] = $this->breadcrumb;
		
		return parent::render($response, $layout, $values);
	}
	
	/**
	 * Set content title displayed to user
	 * Default is null, the content is auto-generated from route name
	 * String set the title
	 * False hides the title
	 *
	 * @param string|false|null $title
	 */
	public function setContentTitle($title) {
		$this->setOption(self::OPTION_CONTENT_TITLE, $title);
	}
	
}
