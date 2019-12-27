<?php

namespace Drupal\monitoring_tool_client\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class ModuleCollectorService.
 */
class ModuleCollectorService implements ModuleCollectorServiceInterface {

  /**
   * Configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * CollectModulesService constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Configuration manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    ModuleHandlerInterface $module_handler
  ) {
    $this->configFactory = $config_factory;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getModules() {
    $result = [];
    $configuration = $this->configFactory->get('monitoring_tool_client.settings');
    $skip_list = $configuration->get('skip_updates');
    /** @var \Drupal\Core\Extension\Extension[] $module_list */
    $module_list = array_filter(
      // The "extension.list.module" service is not supported for < Drupal 8.6.
      system_rebuild_module_data(),
      [static::class, 'filterContribModules']
    );

    $result['drupal'] = [
      'machine_name' => 'drupal',
      'name' => 'Drupal core',
      'core' => \Drupal::CORE_COMPATIBILITY,
      'status' => TRUE,
      'version' => \Drupal::VERSION,
      'skip_updates' => FALSE,
    ];

    foreach ($module_list as $module_name => $module) {
      $info = isset($module->info) ? $module->info : [];
      $result[$module_name] = [
        'machine_name' => $module->getName(),
        'name' => $info['name'],
        'core' => \Drupal::CORE_COMPATIBILITY,
        'version' => $info['version'],
        'status' => $this->moduleHandler->moduleExists($module->getName()),
        'skip_updates' => !empty($skip_list[$module->getName()]),
      ];
    }

    return $result;
  }

  /**
   * Will filter the custom and child modules.
   *
   * @param \Drupal\Core\Extension\Extension $module
   *   Module item.
   *
   * @return bool
   *   Filter or not.
   */
  public static function filterContribModules(Extension $module) {
    $info = isset($module->info) ? $module->info : [];

    if (
      isset($info['project']) &&
      // Will ignore exaction not modules.
      $module->getType() === 'module' &&
      // Will ignore the drupal core modules.
      $info['project'] !== 'drupal' &&
      // Will ignore the modules that are located in the same folder.
      $module->getName() === $info['project'] &&
      // Will ignore the child modules.
      basename(dirname($module->getPathname())) === $info['project']
    ) {
      return TRUE;
    }

    return FALSE;
  }

}
