<?php

namespace Drupal\client_url\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;

/**
 * Plugin implementation of the 'client_url_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "client_url_widget_type",
 *   module = "client_url",
 *   label = @Translation("Client Url"),
 *   field_types = {
 *     "client_url_field_type"
 *   },
 *   multiple_values = TRUE
 * )
 */
class ClientUrlWidgetType extends OptionsWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Read file and get url lists.
    $module_path = \Drupal::service('extension.list.module')->getPath('client_url');
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $module_path . '/client_url.txt';
    $client_urls = file($file_path, FILE_IGNORE_NEW_LINES);

    // Make an associative array.
    $client_urls = array_combine($client_urls, $client_urls);

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $options = $client_urls;
    $selected = [];
    foreach ($items as $item) {
      $value = $item->{$this->column};
      $selected[] = $value;
    }

    // If required and there is one single option, preselect it.
    if ($this->required && count($options) == 1) {
      reset($options);
      $selected = [key($options)];
    }

    if ($this->multiple) {
      $element += [
        '#type' => 'checkboxes',
        '#default_value' => $selected,
        '#options' => $options,
      ];
    }
    else {
      $element += [
        '#type' => 'radios',
        // Radio buttons need a scalar value. Take the first default value, or
        // default to NULL so that the form element is properly recognized as
        // not having a default value.
        '#default_value' => $selected ? reset($selected) : NULL,
        '#options' => $options,
      ];
    }

    return $element;
  }

}
