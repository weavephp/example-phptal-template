<?php
declare(strict_types = 1);
/**
 * Weave example app for templated controller output via a custom dispatcher.
 */
namespace App;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \Weave\Dispatch\DispatchAdaptorInterface;

/**
 * Dispatcher that also handles global templating.
 */
class TemplateDispatch implements DispatchAdaptorInterface
{
	/**
	 * Call the callable, providing parameters and returning the returned value.
	 *
	 * In this class, if the callable is a controller then expect an array
	 * of key/value pairs rather than a Response object and render a template
	 * for returning in the Response.
	 *
	 * Rather than an array, you could return something like an Aura.Payload
	 * instance which gives more structure and richer information transfer.
	 *
	 * @param callable $dispatchable   The callable to be called.
	 * @param string   $resolutionType Are we dispatching to a static, invokable etc.
	 * @param string   $dispatchSource Where the dispatch request came from.
	 * @param Request  $request        The request.
	 * @param mixed    ...$rest        Any remaining parameters passed to the callable.
	 *
	 * @return mixed Some form of PSR-7 style response.
	 */
	public function dispatch(
		$dispatchable,
		$resolutionType,
		$dispatchSource,
		Request $request,
		...$rest
	) {

		// Do the dispatch. $returnedValue could be a Response or a PHPTAL instance.
		// Rather than directly returning the PHPTAL instance, you
		// could opt for something more decoupled such as an
		// Aura.Payload but this example just keeps things simple.
		$returnedValue = $dispatchable($request, ...$rest);

		if ($dispatchSource === DispatchAdaptorInterface::SOURCE_MIDDLEWARE_STACK
			|| ! ($returnedValue instanceof \PHPTAL)) {
			// Middleware isn't templated.
			return $returnedValue;
		}

		// Just makes reading the code later easier.
		$renderer = $returnedValue;

		// Here we use Aura router's route name attribute
		// but you could use any request attribute or some totally different
		// approach for indentifying a template.
		$templateName = $request->getAttribute('route.name') . '.xhtml';
		$renderer->setTemplate($templateName);
		$templateResult = $renderer->execute();

		// We know this is a doublepass middleware stack based app so we
		// know the first value in $rest is the Response object.
		// For a singlepass stack you would need to provide a ResponseFactory
		// as an injected constructor parameter and create a new Response here.
		$response = array_shift($rest);

		// Write the template output to the Response body.
		$response->getBody()->write($templateResult);
		return $response;
	}
}
