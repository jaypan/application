<?php

namespace Drupal\application\Form\Step;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for each step of the Application Form.
 */
interface ApplicationFormStepInterface {

  /**
   * Attach fields to be shown on the current step of the form.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   */
  public function attachFormFields(array &$form, FormStateInterface $form_state): VOID;

  /**
   * Validates any form fields added in ::attachFormFields().
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   */
  public function validateStep(array $form, FormStateInterface $form_state): VOID;

  /**
   * Submits any form fields added in ::attachFormFields().
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   */
  public function submitStep(array $form, FormStateInterface $form_state): VOID;

  /**
   * Retrieve the step of the form that follows the current step.
   *
   * @return string
   *   The key representing the next step in the application form after the
   *   current step.
   */
  public function getNextStep(): string;

  /**
   * Retrieve the step of the form that is previous to the current step.
   *
   * @return string
   *   The key representing the previous step in the application form.
   */
  public function getPreviousStep(): string;

}
