<?php

namespace Drupal\client_url\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'client_url_formatter_type' formatter.
 *
 * @FieldFormatter(
 *   id = "client_url_formatter_type",
 *   label = @Translation("Client Url"),
 *   field_types = {
 *     "client_url_field_type"
 *   }
 * )
 */
class ClientUrlFormatterType extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $domains = [];
    foreach ($items as $delta => $item) {
      $domain = $this->getDomain($this->addScheme($this->viewValue($item)));
      if ($domain) {
        if (!in_array($domain, $domains)) {
          $domains[] = $domain;
          $elements[$delta] = ['#markup' => $this->viewValue($item)];
        }
      }
      else {
        $elements[$delta] = ['#markup' => $this->viewValue($item)];
      }
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

  /**
   * Apply scheme in URL.
   */
  protected function addScheme($url, $scheme = 'https://') {
    return parse_url($url, PHP_URL_SCHEME) === NULL ?
    $scheme . $url : $url;
  }

  /**
   * Get Url domain.
   */
  protected function getDomain($url) {
    $pieces = parse_url($url);
    $domain = $pieces['host'] ?? '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
      return $regs['domain'];
    }
    return FALSE;
  }

}
