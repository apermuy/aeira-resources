<?php

namespace Drupal\aeiraresources\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the aeiraresouces module.
 */

class AeiraResourcesController extends ControllerBase {

  /**
   * Returns map page
   *
   * @return array
   *   A simple renderable array.
   */
  public function aeiraMapa() {
    return [
      '#markup' => '<div id="frontpagemap"></div>',
    ];
  }
}
