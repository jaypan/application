<?php

namespace Drupal\application\Form\Step;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Creates the contact details step of the application form.
 */
class ContactDetailsStep extends ApplicationFormStep {

  // String translation is required in this calls, so the trait is used.
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function attachFormFields(array &$form, FormStateInterface $form_state): VOID {

    $application = $this->getApplication($form_state);

    // Add the address field.
    $form['field_address'] = [
      '#title' => $this->t('Address'),
      '#type' => 'textfield',
      // Retrieve the default value from the application node, if it exists.
      '#default_value' => $application->get('field_address')->value ?? FALSE,
    ];

    // Add the telephone field.
    $form['field_telephone'] = [
      '#title' => $this->t('Telephone'),
      '#type' => 'tel',
      // Retrieve the default value from the application node, if it exists.
      '#default_value' => $application->get('field_telephone')->value ?? FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateStep(array $form, FormStateInterface $form_state): VOID {

    // Required fields are tested for emptiness. This is done instead of using
    // #required to give more control over when the validation occurs, and the
    // message that is shown when validation is failed.
    if ($this->fieldIsEmpty('field_address', $form_state)) {
      $form_state->setError($form['field_address'], $this->t('Please enter your address'));
    }

    if ($this->fieldIsEmpty('field_telephone', $form_state)) {
      $form_state->setError($form['field_telephone'], $this->t('Please enter your telephone number'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitStep(array $form, FormStateInterface $form_state): VOID {

    // Retrieve the application from the form state.
    $application = $this->getApplication($form_state);

    // Save the value submitted for the address to the application.
    $application->set('field_address', $form_state->getValue('field_address'));
    // Save the value submitted for telephone number to the application.
    $application->set('field_telephone', $form_state->getValue('field_telephone'));

  }

  /**
   * {@inheritdoc}
   */
  public function getNextStep(): string {
    // The next step of the form is the review step.
    return 'review';
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousStep(): string {
    // This previous step of the form is the personal details step.
    return 'personal_details';
  }

}
