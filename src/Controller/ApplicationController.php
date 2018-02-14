<?php

namespace Drupal\application\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles route callbacks for the Application module.
 */
class ApplicationController extends ControllerBase implements ApplicationControllerInterface {

  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Creates an ApplicationController object.
   *
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder service.
   */
  public function __construct(FormBuilderInterface $formBuilder) {
    $this->formBuilder = $formBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      // Retrieve the form builder for depdency injection.
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function applicationPage(): array {

    // Create an application node object to be used in the form.
    $application = Node::create([
      'type' => 'application',
    ]);

    // Return the form with a wrapper.
    return [
      '#prefix' => '<div id="application_page">',
      '#suffix' => '</div>',
      // $application is passed to the form builder.
      'form' => $this->formBuilder->getForm('Drupal\application\Form\ApplicationForm', $application),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function applicationEditPage(NodeInterface $application): array {

    return [
      '#prefix' => '<div id="application_edit_page">',
      '#suffix' => '</div>',
      // $application is passed to the form builder.
      'form' => $this->formBuilder->getForm('Drupal\application\Form\ApplicationForm', $application),
    ];

  }

}
