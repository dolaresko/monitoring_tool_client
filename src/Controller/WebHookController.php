<?php

namespace Drupal\monitoring_tool_client\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\monitoring_tool_client\Functional\AccessCheckTrait;
use Drupal\monitoring_tool_client\Service\ClientApiServiceInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class WebHookController.
 */
class WebHookController implements ContainerInjectionInterface {

  use AccessCheckTrait;

  /**
   * Monitoring tool client API.
   *
   * @var \Drupal\monitoring_tool_client\Service\ClientApiServiceInterface
   */
  protected $clientApi;

  /**
   * WebHookController constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request stack.
   * @param \Drupal\monitoring_tool_client\Service\ClientApiServiceInterface $client_api
   *   HTTP Guzzle client.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    RequestStack $request_stack,
    ClientApiServiceInterface $client_api
  ) {
    $this->setConfigFactory($config_factory);
    $this->setRequestStack($request_stack);
    $this->clientApi = $client_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('request_stack'),
      $container->get('monitoring_tool_client.client_api')
    );
  }

  /**
   * WebHook route callback.
   *
   * @param string $project_hash
   *   The project ID from Monitoring tool Server.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Http response.
   */
  public function sendModules($project_hash) {
    try {
      $this->clientApi->sendModules();
    }
    catch (GuzzleException $exception) {
      return new Response(NULL, Response::HTTP_SERVICE_UNAVAILABLE);
    }
    catch (HttpExceptionInterface $exception) {
      return new Response(NULL, $exception->getStatusCode(), $exception->getHeaders());
    }

    return new Response(NULL, Response::HTTP_NO_CONTENT);
  }

}
