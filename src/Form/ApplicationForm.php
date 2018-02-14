<?php

namespace Drupal\application\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\application\Exception\ApplicationNullException;
use Drupal\application\Form\Step\ApplicationFormStepInterface;
use Drupal\application\Form\Step\ContactDetailsStep;
use Drupal\application\Form\Step\PersonalDetailsStep;
use Drupal\application\Form\Step\ReviewStep;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Application module's application form.
 */
class ApplicationForm extends FormBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Create an ApplicationForm object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      // Get the current user for dependeny injection.
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'application_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $application = NULL) {

    // In order to conform to FormInterface::buildForm, $application must be
    // defaulted to NULL. However, the form requires this object. Therefore a
    // check is made to ensure an application object has been passed.
    if (is_null($application)) {
      // A custom exception is thrown. This exception extends \Exception.
      throw new ApplicationNullException();
    }

    // On initial load, $application will be passed to the form. On subsequent
    // steps, it will come from the form state. FormStateInterface::get()
    // retrieves arbitrary data from the form state.
    $application = $form_state->get('application') ?? $application;
    // The application node is saved to the form state. This ensures that it is
    // available anywhere in the form process.
    $form_state->set('application', $application);

    // The current step is retrieved from the form state.
    $step = $form_state->get('application_step');
    // A check is made to see if the step is null - which happens on initial
    // form load.
    if (is_null($step)) {
      // If $application has an ID, it means that an application is bening
      // edited.
      if ($application->id()) {
        // On edit, go directly to the review step.
        $step = 'review';
      }
      else {
        // This is a new application, so go to the first step (personal details)
        $step = 'personal_details';
      }
    }
    // Save the application to the form state, for use anywhere in the form
    // process.
    $form_state->set('application_step', $step);

    // Add the fields for the current step of the form.
    $this->addFields($form, $form_state);
    // Add the navigation (previous/next etc) to the form.
    $this->addNavigation($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Only validate form values when moving to the next step of the form.
    if ($form_state->getTriggeringElement()['#value'] === $form['actions']['next']['#value']) {
      // Get the object that represents the current step of the form.
      $form_step_object = $this->getCurrentFormStepObject($form_state);
      // Validate the current step of the form.
      $form_step_object->validateStep($form, $form_state);
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Get the object that represents the current step of the form.
    $form_step_object = $this->getCurrentFormStepObject($form_state);

    // If the next button is clicked:
    if ($form_state->getTriggeringElement()['#value'] === $form['actions']['next']['#value']) {
      // Submit the current step of the form.
      $form_step_object->submitStep($form, $form_state);
      // Set the step of the form that will be loaded after submission is
      // complete.
      $this->setStep($form_step_object->getNextStep(), $form_state);
      // Rebuild the form.
      $form_state->setRebuild(TRUE);
    }
    // If the previous button is clicked:
    elseif ($form_state->getTriggeringElement()['#value'] === $form['actions']['previous']['#value']) {

      // Submit the current step of the form. Note that these values have not
      // been validated, but that's ok, as they will be validated after they
      // return to this step, and proceed forward.
      $form_step_object->submitStep($form, $form_state);
      // Set the step of the form that will be loaded after submission is
      // complete.
      $this->setStep($form_step_object->getPreviousStep(), $form_state);
      // Rebuild the form.
      $form_state->setRebuild(TRUE);
    }
    // If the 'submit application' button is clicked:
    elseif ($form_state->getTriggeringElement()['#value'] === $form['actions']['save']['#value']) {
      // Save the application.
      $this->saveApplication($form_state);
      // Note - the form is not rebuit, as it is loaded fresh. If a redirect
      // is required, it would be added here.
    }
  }

  /**
   * Retrieve the object that defines the current step of the form.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return \Drupal\application\Form\Step\ApplicationFormStepInterface
   *   The object defining the current step of the form.
   */
  protected function getCurrentFormStepObject(FormStateInterface $form_state): ApplicationFormStepInterface {

    // Get the current step of the form.
    $step = $form_state->get('application_step');

    // Retrieve the object that represents the current step.
    switch ($step) {
      case 'personal_details':
        $form_step_object = new PersonalDetailsStep();

        break;

      case 'contact_details':
        $form_step_object = new ContactDetailsStep();

        break;

      case 'review':
        $form_step_object = new ReviewStep();

        break;

    }

    return $form_step_object;
  }

  /**
   * Adds the form fields for the current step, to the form.
   */
  protected function addFields(array &$form, FormStateInterface $form_state): VOID {
    // Retrieve the object that represents the current step of the form.
    $form_step_object = $this->getCurrentFormStepObject($form_state);
    // Attach the form fields for the current step of the form.
    $form_step_object->attachFormFields($form, $form_state);
  }

  /**
   * Adds the navigation buttons (previous/next etc) to the form.
   */
  protected function addNavigation(array &$form, FormStateInterface $form_state): VOID {

    // Retrieve the current step of the form.
    $step = $form_state->get('application_step');

    // Create a wrapper for the buttons.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add the previous button.
    $form['actions']['previous'] = [
      '#type' => 'submit',
      '#value' => $this->t('Previous'),
      // The button needs to be disabled on the first step.
      '#disabled' => $step === 'personal_details',
    ];

    // Add the next button.
    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
      // This button needs to be removed on the review step.
      '#access' => $step !== 'review',
    ];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Application'),
      // This button is only shown on the review step.
      '#access' => $step === 'review',
    ];

  }

  /**
   * Sets the next step to be used in the form.
   *
   * @param string $step
   *   The next step to be used in the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  private function setStep($step, FormStateInterface $form_state): VOID {
    $form_state->set('application_step', $step);
  }

  /**
   * Save the application, and show a message to the user.
   */
  private function saveApplication(FormStateInterface $form_state): VOID {

    // Retrieve the application from the form state.
    $application = $form_state->get('application');

    // Set the current user as the owner of the application node.
    $application->setOwnerId($this->currentUser->id());
    // Nodes require a title. The current user:s account name is arbitrarily
    // used as a default.
    $application->setTitle($this->currentUser->getAccountName());
    // The application is saved.
    $application->save();

    // A Url object to edit the new application is created.
    $url = Url::fromRoute('application.application_edit_page', ['application' => $application->id()]);
    // A message is shown to the user informing them the applicaiton has been
    // saved, with a link to edit the application.
    drupal_set_message($this->t('Thank you, your application has been saved. You can edit it <a href=":application_url">here</a>.', [':application_url' => $url->toString()]));
  }

}
