<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\pjaxfilter;


use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * PjaxFilter is an action filter that ensures an action has been called using pjax calls and to configure what to do
 * in case the controller's action has been called differently.
 *
 * To use PjaxFilter, declare it in `behaviors()` method of your controller class. For example, the following
 * declarations will define full pjax filter for all controller's actions but the index action.
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *         'verbs' => [
 *             'class' => \dosamigos\pjaxfilter\PjaxFilter::className(),
 *             'actions' => [
 *                 '*'  => ['url' => ['index']],
 *             ],
 *             'exclude' => ['index']
 *         ],
 *     ];
 * }
 * ~~~
 *
 * The next example, sets pjax filtering for controller's delete method and if not called via pjax, it will fire an
 * error. The rest of the actions will not be affected.
 *
 * ~~~
 * public function behaviors()
 * {
 *     return [
 *         'verbs' => [
 *             'class' => \dosamigos\pjaxfilter\PjaxFilter::className(),
 *             'actions' => [
 *                 'error' => ['code' => 404, 'msg' => 'Not found']
 *             ]
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\pjaxfilter
 */
class PjaxFilter extends Behavior
{

	/**
	 * @var array this property defines the action names to filter for valid Pjax requests.
	 * For each action that you add an entry with the action id as array key and an array of
	 * the url to redirect if fails to be a valid Pjax request.
	 * If you wish to throw an error, setup an Http error code and its message instead.
	 * If an action is not listed no action will be taken.
	 *
	 * You can use '*' to stand for all actions. When an action is explicitly
	 * specified, it takes precedence over the specification given by '*'.
	 *
	 * For example,
	 *
	 * ~~~
	 * [
	 *   'update' => ['url' => ['index']],
	 *   'delete' => ['error' => ['code' => 404, 'msg' => 'Page not found'],
	 * 	 '*' => [
	 * 		'url' => ['index'], // or ['error' => [....]],
	 * 	 ]
	 * ]
	 * ~~~
	 */
	public $actions = [];
	/**
	 * @var array this property defines the actions to exclude to filter
	 */
	public $exclude = [];


	/**
	 * Declares event handlers for the [[owner]]'s events.
	 * @return array events (array keys) and the corresponding event handler methods (array values).
	 */
	public function events()
	{
		return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
	}

	/**
	 * @param \yii\base\ActionEvent $event
	 * @return boolean
	 * @throws \yii\web\HttpException when the request method is not allowed.
	 */
	public function beforeAction($event)
	{
		$action = $event->action->id;

		if (isset($this->actions[$action])) {
			$reaction = $this->actions[$action];
		} elseif (isset($this->actions['*'])) {
			$reaction = $this->actions['*'];
		} else {
			return $event->isValid;
		}

		if (!\Yii::$app->getRequest()->getIsPjax() && !in_array($action, $this->exclude)) {
			$url = ArrayHelper::getValue($reaction, 'url');
			if($url) {
				$event->isValid = false;
				\Yii::$app->controller->redirect($url);
			}
			$error = ArrayHelper::getValue($reaction, 'error');
			if($error) {
				throw new HttpException(ArrayHelper::getValue($error, 'code'), ArrayHelper::getValue($error, 'msg'));
			}
		}
		return $event->isValid;
	}
}