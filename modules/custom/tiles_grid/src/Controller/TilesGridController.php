<?php
namespace Drupal\tiles_grid\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;
use Drupal\taxonomy\Entity\Term;

/**
 * Return Tiles Grid to display in home page.
 *
 * @author Jayendra
 *        
 */
class TilesGridController extends ControllerBase
{

    /**
     * Provide a list of Tiles which can displayed in home page.
     */
    function ListTiles()
    {

        // getting list of entity from entity queue
        /**
         * $entity_subqueue = \Drupal::entityTypeManager()->getStorage('node')->load('tile');
         * $items = $entity_subqueue->get('items')->getValue();*
         */
        $nids = \Drupal::entityTypeManager()->getStorage('node')
            ->getQuery()
            ->condition('type', 'tile')
            ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

        $tiles_list = [];

        /**
         * Loading details of entity queue item.
         */
        foreach ($nodes as $node) {

            // nid of node;
            $nid = $node->id();
            $videoFiledRender = '';
            $linkUrl = '';

            /**
             * Getting fields of tile.
             */

            $tileTitle = $node->getTitle();
            $imageUurl = $node->get('field_image')->entity->uri->value;
            $tileTagline = (! empty($node->get('field_tagline')->getValue()) ? $node->get('field_tagline')->getValue()[0]['value'] : '');                        
            $linkUrl = (! empty($node->get('field_link')->getValue()) ? $node->get('field_link')->getValue()[0]['uri'] : '');

            /**
             * Loading taxonomy term of tags
             *
             * @var Ambiguous $tags_id
             */
            $termEntity = $node->get('field_tags')
                ->first()
                ->get('entity')
                ->getTarget()
                ->getValue();
            $iconUrl = $termEntity->get('field_icon')->entity->uri->value;

            /**
             * Getting Video Field information.
             */

            if (! $node->get('field_video')->isEmpty()) {

                $videoFiledRender = $node->get('field_video')->view([
                    'type' => 'video_embed_field_colorbox',
                    'label' => 'hidden',
                    'settings' => [
                        'image_style' => 'tile_image'
                    ]
                ]);
            }

            /**
             * Getting Article information
             * If article is attached to tile, then we will show information of article.
             */

            if (! ($node->get('field_article')->isEmpty())) {

                $articleNode = $node->get('field_article')
                    ->first()
                    ->get('entity')
                    ->getTarget()
                    ->getValue();

                $tileTitle = $articleNode->getTitle();
                $imageUurl = $articleNode->get('field_image')->entity->uri->value;

                $termEntity = $articleNode->get('field_tags')
                    ->first()
                    ->get('entity')
                    ->getTarget()
                    ->getValue();
                $iconUrl = $termEntity->get('field_icon')->entity->uri->value;
            }

            /**
             * Preparing array for template file.
             */

            $tiles_list[$nid]['title'] = $tileTitle;
            $tiles_list[$nid]['tag_line'] = $tileTagline;

            $tiles_list[$nid]['image'] = ImageStyle::load('tile_image')->buildUrl($imageUurl);
            $tiles_list[$nid]['icon'] = ImageStyle::load('icon_style')->buildUrl($iconUrl);
            $tiles_list[$nid]['term_name'] = $termEntity->getName();
            $tiles_list[$nid]['video_field'] = $videoFiledRender;
            $tiles_list[$nid]['link_url'] = $linkUrl;
        }

        return array(
            '#theme' => 'tiles_grid',
            '#twig_list' => $tiles_list
        );
    }
}