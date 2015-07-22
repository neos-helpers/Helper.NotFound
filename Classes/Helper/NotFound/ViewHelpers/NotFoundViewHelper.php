<?php
namespace Helper\NotFound\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "WM.StarterKit".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Http\RequestHandler;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Http\Uri;
use TYPO3\Flow\Http\Component\ComponentContext;
use TYPO3\Flow\I18n\Detector;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Mvc\Routing\Router;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Neos\Domain\Service\ContentDimensionPresetSourceInterface;


/**
 * Loads the content of a given URL
 */
class NotFoundViewHelper extends AbstractViewHelper {

  /**
   * @Flow\Inject
   * @var Detector
   */
  protected $localeDetector;

  /**
   * @Flow\Inject
   * @var \TYPO3\Flow\Mvc\Dispatcher
   */
  protected $dispatcher;

  /**
   * @Flow\Inject(lazy = false)
   * @var Bootstrap
   */
  protected $bootstrap;

  /**
   * @Flow\Inject(lazy = false)
   * @var Router
   */
  protected $router;

  /**
   * @Flow\Inject
   * @var ConfigurationManager
   */
  protected $configurationManager;

  /**
   * @Flow\Inject
   * @var ContentDimensionPresetSourceInterface
   */
  protected $contentDimensionPresetSource;

  /**
   * Initialize this engine
   *
   * @return void
   */
  public function initializeObject() {
    $this->router->setRoutesConfiguration($this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_ROUTES));
  }
  /**
   * @param string $path
   * @return string
   * @throws \Exception
   */
  public function render($path = NULL) {
    if ($path === NULL) {
      $path = '404';
    }

    /** @var RequestHandler $activeRequestHandler */
    $activeRequestHandler = $this->bootstrap->getActiveRequestHandler();
    $parentHttpRequest = $activeRequestHandler->getHttpRequest();

    $requestPath = $parentHttpRequest->getUri()->getPath();
    $language = explode('/', ltrim($requestPath, '/'))[0];

    if ($language === 'neos') {
      throw new \Exception('NotFoundViewHelper can not be used for neos-routes.', 1435648210);
    }

    $language = $this->localeDetector->detectLocaleFromLocaleTag($language)->getLanguage();
    if ($this->contentDimensionPresetSource->findPresetByUriSegment('language', $language) === NULL) {
      $language = '';
    }
    if ($language !== '') {
      $language .= '/';
    }

    $request = Request::create(new Uri(rtrim($parentHttpRequest->getBaseUri(), '/') . '/' . $language . $path));
    $matchingRoute = $this->router->route($request);
    if (!$matchingRoute) {
      throw new \Exception(sprintf('Uri with path "%s" could not be found.', rtrim($parentHttpRequest->getBaseUri(), '/') . '/' . $language . $path), 1426446160);
    }
    $response = new Response();

    $objectManager = $this->bootstrap->getObjectManager();
    $baseComponentChain = $objectManager->get('TYPO3\Flow\Http\Component\ComponentChain');

    $componentContext = new ComponentContext($request, $response);
    $baseComponentChain->handle($componentContext);

    return $response->getContent();
  }
}
