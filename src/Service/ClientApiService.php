<?php

namespace Drupal\monitoring_tool_client\Service;

/**
 * Class ClientApiService.
 */
class ClientApiService implements ClientApiServiceInterface {

  /**
   * Connector service, will send data to monitoring tool server.
   *
   * @var \Drupal\monitoring_tool_client\Service\ServerConnectorServiceInterface
   */
  protected $serverConnector;

  /**
   * Will gets the list of contribution modules.
   *
   * @var \Drupal\monitoring_tool_client\Service\ModuleCollectorServiceInterface
   */
  protected $moduleCollector;

  /**
   * ClientApiService constructor.
   *
   * @param \Drupal\monitoring_tool_client\Service\ServerConnectorServiceInterface $server_connector
   *   Connector service, will send data to monitoring tool server.
   * @param \Drupal\monitoring_tool_client\Service\ModuleCollectorServiceInterface $module_collector
   *   Will gets the list of contribution modules.
   */
  public function __construct(
    ServerConnectorServiceInterface $server_connector,
    ModuleCollectorServiceInterface $module_collector
  ) {
    $this->serverConnector = $server_connector;
    $this->moduleCollector = $module_collector;
  }

  /**
   * {@inheritdoc}
   */
  public function sendModules() {
    return $this->serverConnector->send([
      'modules' => $this->moduleCollector->getModules(),
    ]);
  }

}
