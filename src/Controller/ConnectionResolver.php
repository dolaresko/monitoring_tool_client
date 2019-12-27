<?php

namespace Drupal\monitoring_tool_client\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\monitoring_tool_client\Functional\AccessCheckTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConnectionResolver.
 */
class ConnectionResolver implements ContainerInjectionInterface {

  use AccessCheckTrait;

  /**
   * WebHookController constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request stack.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    RequestStack $request_stack
  ) {
    $this->setConfigFactory($config_factory);
    $this->setRequestStack($request_stack);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('request_stack')
    );
  }

  /**
   * Will return the success in case connection is ok.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Http response.
   */
  public function testConnection() {
    return new Response(NULL, Response::HTTP_NO_CONTENT);
  }

}
