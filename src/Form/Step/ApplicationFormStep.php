<?php

namespace Drupal\application\Form\Step;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * Absract class to be extended for each step of the Application Form.
 */
abstract class ApplicationFormStep implements ApplicationFormStepInterface {

  /**
   * Retrieve the application node used to store the data for the form.
   *
   * @return \Drupal\node\NodeInterface
   *   The application node used to store the data for the form.
   */
  protected function getApplication(FormStateInterface $form_state): NodeInterface {
    // Retrieve the node from the form state.
    return $form_state->get('application');
  }

  /**
   * Test if the submitted value for a given field is empty.
   *
   * @param string $elementKey
   *   The key of the element to be checked.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state interface.
   *
   * @return bool
   *   TRUE if the field is empty, FALSE if it has a value.
   */
  protected function fieldIsEmpty($elementKey, FormStateInterface $form_state): bool {
    return (bool) !strlen($form_state->getValue($elementKey));
  }

  /**
   * Sets the next step to be used in the form.
   *
   * @param string $step
   *   The next step to be used in the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected static function setStep($step, FormStateInterface $form_state): VOID {
    // Save the step to the form state.
    $form_state->set('application_step', $step);
    // Set the form to be rebuilt so the step is shown.
    $form_state->setRebuild(TRUE);
  }

}
