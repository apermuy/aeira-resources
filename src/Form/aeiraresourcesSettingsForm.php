<?php

namespace Drupal\aeiraresources\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class aeiraresourcesSettingsForm extends ConfigFormBase {

  /** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'aeiraresources.settings';

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aeiraresources_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['mapa'] = [
        '#type' => 'details',
        '#title' => $this->t('Configuración mapa'),
        '#open' => TRUE,
    ];

    $form['mapa']['aeira_lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#description' => $this->t('Latitude da ubicación a empregar como centro do mapa.<b>Exemplo formato</b>: 60.39429'),
      '#required' => TRUE,
      '#default_value' => $config->get('aeira_lat'),
    ];
    
    $form['mapa']['aeira_lon'] = [
       '#type' => 'textfield',
       '#title' => $this->t('Lonxitude'),
       '#description' => $this->t('Lonxitude da ubicación a empregar como centro do mapa.<b>Exemplo formato</b>: 5.32653'),
       '#required' => TRUE,
       '#default_value' => $config->get('aeira_lon'),
    ];
    
    $form['mapa']['aeira_zoom'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Nivel de zoom'),
        '#description' => $this->t('Nivel de zoom inicial do mapa. Un valor maior, aumenta o zoom no mapa. <b>Valores</b>: Entre 1 e 19.'),
        '#required' => TRUE,
        '#default_value' => $config->get('aeira_zoom'),
     ];
     $form['capa'] = [
      '#type' => 'details',
      '#title' => $this->t('Configuración capa base para mapa'),
      '#open' => FALSE,
     ];
     $form['capa']['aeira_base_map_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL capa base para mapa'),
      '#description' => $this->t('URL capa base para mapa. <a href="https://leaflet-extras.github.io/leaflet-providers/preview/" target="_blank">Consultar os dispoñibles para Leaflet</a>.'),
      '#required' => TRUE,
      '#default_value' => $config->get('aeira_base_map_uri'),
     ];
     $form['capa']['aeira_base_map_attribution'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Atribución'),
      '#size' => 60,
      '#maxlength' => 192,
      '#description' => $this->t('Cadea de texto da atribución da capa base o do mapa.'),
      '#required' => TRUE,
      '#default_value' => $config->get('aeira_base_map_attribution'),
     ];      

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('aeiraresources.settings');

    $config
      ->set('aeira_lat', $form_state->getValue('aeira_lat'))
      ->set('aeira_lon', $form_state->getValue('aeira_lon'))
      ->set('aeira_zoom', $form_state->getValue('aeira_zoom'))
      ->set('aeira_base_map_uri', $form_state->getValue('aeira_base_map_uri'))
      ->set('aeira_base_map_attribution', $form_state->getValue('aeira_base_map_attribution'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}