<?php
declare(strict_types = 1);
/**
 * Weave example app Hello Controller.
 */

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Weave example app Hello Controller.
 */
class Hello
{
	/**
	 * The template renderer instance.
	 *
	 * @var \PHPTAL
	 */
	protected $view;

	/**
	 * The message string provided on init.
	 *
	 * Look in App\Config to see how this is provided from the app config.
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Constructor.
	 *
	 * If you needed a ResponseFactory in this class you could define a
	 * constructor parameter of type \Weave\Http\ResponseFactoryInterface
	 * and the DIC would provide it for you.
	 *
	 * @param \PHPTAL $view    The phptal renderer instance.
	 * @param string  $message The message value from the app config.
	 */
	public function __construct(\PHPTAL $view, $message)
	{
		$this->view = $view;
		$this->message = $message;
	}

	/**
	 * The Hello contoller method.
	 *
	 * In this app we are using a double-pass middleware stack so
	 * both a Request and a Response are provided. In a single-pass stack
	 * you would need to create a response for yourself.
	 *
	 * @param Request  $request  The Request.
	 * @param Response $response The Response.
	 *
	 * @return Response|\PHPTAL
	 */
	public function hello(Request $request, Response $response)
	{
		$owner = $request->getAttribute('owner');
		$this->view->message = $this->message . ", " . $owner;
		return $this->view;
	}
}
