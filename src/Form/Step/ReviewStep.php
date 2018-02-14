<?php

namespace Drupal\application\Form\Step;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Creates the review step of the application form.
 */
class ReviewStep extends ApplicationFormStep {

  // String translation is required in this calls, so the trait is used.
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function attachFormFields(array &$form, FormStateInterface $form_state): VOID {

    // Retrieve the application node used to store the form data.
    $application = $this->getApplication($form_state);

    // Create a wrapper.
    $form['personal_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Personal Details'),
      '#open' => TRUE,
    ];

    // Display the value submitted for the first name.
    $form['personal_details']['first_name'] = [
      '#prefix' => '<p>',
      '#suffic' => '</p>',
      '#markup' => $this->t('First Name: @first_name', ['@first_name' => $application->get('field_first_name')->value]),
    ];

    // Display the value submitted for the last name.
    $form['personal_details']['last_name'] = [
      '#prefix' => '<p>',
      '#suffic' => '</p>',
      '#markup' => $this->t('Last Name: @last_name', ['@last_name' => $application->get('field_last_name')->value]),
    ];

    // Display the value submitted for the birthdate.
    $form['personal_details']['birthdate'] = [
      '#prefix' => '<p>',
      '#suffic' => '</p>',
      '#markup' => $this->t('Birthdate: @birthdate', ['@birthdate' => $application->get('field_birthdate')->value]),
    ];

    // Add a wrapper for the edit button.
    $form['personal_details']['actions'] = [
      '#type' => 'actions',
    ];

    // Add the personal details edit button.
    $form['personal_details']['actions']['edit_personal_details'] = [
      '#type' => 'submit',
      '#value' => $this->t('Edit personal details'),
      '#submit' => [__CLASS__ . '::editPersonalDetailsSubmit'],
    ];

    // Create a wrapper.
    $form['contact_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Contact Details'),
      '#open' => TRUE,
    ];

    // Display the value submitted for the address.
    $form['contact_details']['address'] = [
      '#prefix' => '<p>',
      '#suffic' => '</p>',
      '#markup' => $this->t('Address: @address', ['@first_name' => $application->get('field_address')->value]),
    ];

    // Display the value submitted for the telephone number.
    $form['contact_details']['telephone'] = [
      '#prefix' => '<p>',
      '#suffic' => '</p>',
      '#markup' => $this->t('Telephone: @telephone', ['@telephone' => $application->get('field_telephone')->value]),
    ];

    // Add a wrapper for the edit button.
    $form['contact_details']['actions'] = [
      '#type' => 'actions',
    ];

    // Add the contact details edit button.
    $form['contact_details']['actions']['edit_contact_details'] = [
      '#type' => 'submit',
      '#value' => $this->t('Edit contact details'),
      '#submit' => [__CLASS__ . '::editContactDetailsSubmit'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateStep(array $form, FormStateInterface $form_state): VOID {}

  /**
   * {@inheritdoc}
   */
  public function submitStep(array $form, FormStateInterface $form_state): VOID {}

  /**
   * {@inheritdoc}
   */
  public function getNextStep(): string {
    // This function is required by ApplicationFormStepInterface, however it is
    // never actually called, as the next button is disabled on the review step.
    // An empty string is retured as the return value has been typehinted as a
    // string.
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousStep(): string {
    // This contact details step is the step previous to the current step.
    return 'contact_details';
  }

  /**
   * Submit handler for the edit personal details submit button.
   */
  public static function editPersonalDetailsSubmit(&$form, FormStateInterface $form_state): VOID {
    // Set the personal details step to be loaded when the form is rebuilt.
    self::setStep('personal_details', $form_state);
  }

  /**
   * Submit handler for the edit personal details submit button.
   */
  public static function editContactDetailsSubmit(&$form, FormStateInterface $form_state): VOID {
    // Set the contact details step to be loaded when the form is rebuilt.
    self::setStep('contact_details', $form_state);
  }

}
