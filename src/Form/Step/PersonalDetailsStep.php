<?php

namespace Drupal\application\Form\Step;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Creates the personal details step of the application form.
 */
class PersonalDetailsStep extends ApplicationFormStep {

  // String translation is required in this calls, so the trait is used.
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function attachFormFields(array &$form, FormStateInterface $form_state): VOID {

    // Get the application object used to store the form date.
    $application = $this->getApplication($form_state);

    // Create the first name field.
    $form['field_first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      // Retrieve the default value from the application node if it exists.
      '#default_value' => $application->get('field_first_name')->value ?? FALSE,
    ];

    // Create the last name field.
    $form['field_last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      // Retrieve the default value from the application node if it exists.
      '#default_value' => $application->get('field_last_name')->value ?? FALSE,
    ];

    // Create the birthdate field.
    $form['field_birthdate'] = [
      '#type' => 'date',
      '#title' => $this->t('Birthdate'),
      // Retrieve the default value from the application node if it exists.
      '#default_value' => $application->get('field_birthdate')->value ?? FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateStep(array $form, FormStateInterface $form_state): VOID {

    // Required fields are tested for emptiness. This is done instead of using
    // #required to give more control over when the validation occurs, and the
    // message that is shown when validation is failed.
    if ($this->fieldIsEmpty('field_first_name', $form_state)) {
      $form_state->setError($form['field_first_name'], $this->t('Please enter your first name'));
    }

    if ($this->fieldIsEmpty('field_last_name', $form_state)) {
      $form_state->setError($form['field_last_name'], $this->t('Please enter your last name'));
    }

    if ($this->fieldIsEmpty('field_birthdate', $form_state)) {
      $form_state->setError($form['field_birthdate'], $this->t('Please enter your birthdate'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitStep(array $form, FormStateInterface $form_state): VOID {

    // Retrieve the application node used to save the form data.
    $application = $this->getApplication($form_state);

    // Save the value submitted for first name to the application node.
    $application->set('field_first_name', $form_state->getValue('field_first_name'));
    // Save the value submitted for last name to the application node.
    $application->set('field_last_name', $form_state->getValue('field_last_name'));
    // Save the value submitted for birthdate to the application node.
    $application->set('field_birthdate', $form_state->getValue('field_birthdate'));
  }

  /**
   * {@inheritdoc}
   */
  public function getNextStep(): string {
    // The next step of the form is the contact details step.
    return 'contact_details';
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousStep(): string {
    // This is the first step in the form, so there is no previous step.
    // However, this function has the return value typehinted as a string, so
    // an empty string is returned. This function will never actually be called
    // however, as the previous button is disabled on this step.
    return '';
  }

}
