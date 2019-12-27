<?php

namespace Drupal\monitoring_tool_client\Service;

/**
 * Interface ServerConnectorServiceInterface.
 */
interface ServerConnectorServiceInterface {

  /**
   * Monitoring Tool server API version.
   */
  const MONITORING_TOOL_API_VERSION = 'v1';

  /**
   * HTTP header access token name.
   */
  const MONITORING_TOOL_ACCESS_HEADER = 'monitoring-tool-token';

  /**
   * Will send data to the common Monitoring Tool server.
   *
   * @param array $data
   *   Data for sending to Monitoring Tool server.
   * @param string $method
   *   HTTP Method.
   * @param string $action
   *   Action hook.
   *
   * @return \Psr\Http\Message\ResponseInterface|null
   *   Guzzle response.
   */
  public function send(array $data, $method = 'POST', $action = 'input');

}
