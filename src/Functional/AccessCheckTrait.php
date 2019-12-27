<?php

namespace Drupal\monitoring_tool_client\Functional;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\monitoring_tool_client\Service\ServerConnectorServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Trait AccessCheckTrait.
 */
trait AccessCheckTrait {

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Checking of the access by header Token.
   *
   * @param string $project_hash
   *   The project ID from Monitoring tool Server.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function checkAccess($project_hash) {
    $config = $this->configFactory->get('monitoring_tool_client.settings');
    $secure_token = $this->requestStack->getCurrentRequest()->headers->get(ServerConnectorServiceInterface::MONITORING_TOOL_ACCESS_HEADER);

    if (
      $config->get('webhook') === TRUE &&
      !empty($project_hash) &&
      !empty($secure_token) &&
      $project_hash === $config->get('project_id') &&
      $secure_token === $config->get('secure_token')
    ) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

  /**
   * Will set the request stack.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  protected function setRequestStack(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * Will set the config factory.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  protected function setConfigFactory(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

}
