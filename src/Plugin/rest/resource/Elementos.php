<?php

namespace Drupal\aeiraresources\Plugin\rest\resource;

use Drupal\Core\Session\AccountInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;


/**
 * Provides REST Resource
 *
 * @RestResource(
 *   id = "elementos",
 *   label = @Translation("Elementos"),
 *   uri_paths = {
 *     "canonical" = "/api/1.0/elementos"
 *   }
 * )
 */
class Elementos extends ResourceBase {

    /**
     * Responds to GET requests.
     *
     * Returns a list of bundles for specified entity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function get() {
        $db = \Drupal::database();
        $listado = array();
    
        $query = $db->query("SELECT tid, name FROM taxonomy_term_field_data WHERE vid = 'tipo'");
        $result = $query->fetchAll();

        $classification_items = array();

        foreach($result as $row){
               
            $query1 = $db->query("SELECT n.nid,nd.title as elemento,x.field_xeolocalizacion_elemento_lat as lat, x.field_xeolocalizacion_elemento_lon as lon, td.name as tipo FROM node n 
            INNER JOIN node_field_data nd ON n.nid = nd.nid 
            INNER JOIN node__field_xeolocalizacion_elemento x ON n.nid = x.entity_id
            INNER JOIN node__field_tipo_elemento t ON n.nid = t.entity_id
            INNER JOIN taxonomy_term_field_data td ON t.field_tipo_elemento_target_id = td.tid
            WHERE n.type = 'elemento' and nd.status = 1 AND t.field_tipo_elemento_target_id = $row->tid");
            $result1 = $query1->fetchAll();

            $aux = array();
            $aux['type'] = 'FeatureCollection';
            $aux['features'] = array();
            $type = 'Point';

            // Obter tid para tipoloxía
            $tipoloxia = Term::load($row->tid);
           
            // Xerar url da icona
            
            if(is_string($tipoloxia->get('field_icona_tipo')->getValue()[0]['target_id'])){
                $urlfile = File::load($tipoloxia->get('field_icona_tipo')->getValue()[0]['target_id']);
                $urlicona = file_create_url($urlfile->get('uri')->getString());
            }
            else{
                $urlicona = '/profiles/contrib/aeira-profile/content/demo/images/marker-icon.png';
            }
            
                foreach($result1 as $row1){
                    $lon = floatval($row1->lon);
                    $lan = floatval($row1->lat);

                    $type = 'Point';
                    $url = \Drupal::request()->getSchemeAndHttpHost() . \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $row1->nid);
                    $aux1 = array(
                        'type' => 'Feature',
                        'geometry' => array(
                            'type' => $type,
                            'coordinates' => ["$lon","$lan"],
                        ),
                        'properties' => array(
                            'nid' => $row1->nid,
                            'url'=> $url,
                            'title' => $row1->elemento,
                            'icon' => $urlicona,
                            'tipo' => $row1->tipo,
                         )
                    );
                    $aux['features'][] = $aux1;
                }
            array_push($listado, $aux);
        }

        $response = new ResourceResponse($listado);
        $response->addCacheableDependency($listado);
        return $response;
    }
}