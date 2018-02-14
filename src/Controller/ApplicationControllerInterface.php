<?php

namespace Drupal\application\Controller;

use Drupal\node\NodeInterface;

/**
 * Interface for ApplicationController.
 */
interface ApplicationControllerInterface {

  /**
   * Creates the application page.
   *
   * @return array
   *   A render array representing the application page.
   */
  public function applicationPage(): array;

  /**
   * Creates the application edit page.
   *
   * @return array
   *   A render array representing the application page.
   */
  public function applicationEditPage(NodeInterface $application): array;

}
