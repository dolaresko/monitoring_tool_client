<?php

namespace Drupal\monitoring_tool_client\Service;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Site\Settings;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class ServerConnectorService.
 */
class ServerConnectorService implements ServerConnectorServiceInterface {

  /**
   * Guzzle HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Settings for the HTTP client.
   *
   * @var array
   */
  protected $settings;

  /**
   * Configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * ServerConnectorService constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   Guzzle HTTP client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Configuration manager.
   */
  public function __construct(
    ClientInterface $http_client,
    ConfigFactoryInterface $config_factory
  ) {
    $this->httpClient = $http_client;
    $this->settings = Settings::get('monitoring_tool', []) + ['options' => []];
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function send(array $data, $method = 'POST', $action = 'input') {
    if (!empty($this->settings['base_url'])) {
      $config = $this->configFactory->getEditable('monitoring_tool_client.settings');
      $url = rtrim($this->settings['base_url'], '/');
      $url .= '/monitoring-tool/api/' . static::MONITORING_TOOL_API_VERSION . '/' . $config->get('project_id') . '/' . $action;
      $default_options = [
        RequestOptions::JSON => !empty($data) ? $data : NULL,
        RequestOptions::QUERY => [
          'time' => time(),
        ],
        RequestOptions::ALLOW_REDIRECTS => TRUE,
        RequestOptions::VERIFY => FALSE,
        RequestOptions::HTTP_ERRORS => FALSE,
        RequestOptions::HEADERS => [
          static::MONITORING_TOOL_ACCESS_HEADER => $config->get('secure_token'),
        ],
      ];
      $options = NestedArray::mergeDeep($default_options, $this->settings['options']);

      try {
        return $this->httpClient->request($method, $url, $options);
      } catch (GuzzleException $exception) {
        watchdog_exception('monitoring_tool_client', $exception);
      }
    }

    return NULL;
  }

}
