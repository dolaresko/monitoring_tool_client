<?php

namespace Drupal\monitoring_tool_client\Service;

/**
 * Interface ClientApiServiceInterface.
 */
interface ClientApiServiceInterface {

  /**
   * Will do HTTP request with list of the modules.
   */
  public function sendModules();

}
