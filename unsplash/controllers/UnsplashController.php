<?php
/**
 * Unsplash plugin for Craft CMS
 *
 * Unsplash Controller
 *
 * --snip--
 * Generally speaking, controllers are the middlemen between the front end of the CP/website and your plugin’s
 * services. They contain action methods which handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering post data, saving it on a model,
 * passing the model off to a service, and then responding to the request appropriately depending on the service
 * method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what the method does (for example,
 * actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 * --snip--
 *
 * @author    Studio Espresso
 * @copyright Copyright (c) 2017 Studio Espresso
 * @link      https://studioespresso.co
 * @package   Unsplash
 * @since     0.1
 */

namespace Craft;

use Crew\Unsplash\HttpClient;
use Crew\Unsplash\Photo;
use Crew\Unsplash\Search;

class UnsplashController extends BaseController
{

    protected $allowAnonymous = array('actionIndex',
        );

    public function actionLatest() {
        $this->pluginIsConfigured();
        if(craft()->cache->get('UnplashLatest')) {
            $this->renderTemplate('Unsplash/_latest', craft()->cache->get('UnplashLatest'));
        } else {
            $this->setup();
            $images = Photo::all($page = 1, $per_page = 25, $orderby = 'latest');
            craft()->cache->add('UnplashLatest', array('images' => $images), (60*60*12));
            $this->renderTemplate('Unsplash/_latest', array('images' => $images));
        }
    }

    public function actionIndex() {
        $this->pluginIsConfigured();
        if(craft()->cache->get('UnsplashPopular')) {
            $this->renderTemplate('Unsplash/_index', craft()->cache->get('UnsplashPopular'));
        } else {
            $this->setup();
            $images = Photo::curated(1, 25);
            craft()->cache->add('UnsplashPopular', array('images' => $images), (60*60*12));
            $this->renderTemplate('Unsplash/_index', array('images' => $images));
        }
    }

    public function actionRandom() {
        $this->pluginIsConfigured();
        if(craft()->cache->get('UnsplashRandom')) {
            $this->renderTemplate('Unsplash/_random', craft()->cache->get('UnsplashRandom'));
        } else {
            $this->setup();
            $images = Photo::random(
                array(
                    'count' => 25
                )
            );
            craft()->cache->add('UnsplashRandom', array('images' => $images), (60*10));
            $this->renderTemplate('Unsplash/_random', array('images' => $images));
        }
    }

    public function actionSearch() {
        $this->pluginIsConfigured();
        if(!craft()->request->getParam('q')) {
            return false;
        }

        $query = craft()->request->getParam('q');
        $page = craft()->request->getParam('page');

        if(craft()->cache->get('UnsplashSearch-' . $query . '-' . $page )) {
            $this->renderTemplate('Unsplash/_search', craft()->cache->get('UnsplashSearch-' . $query . '-' . $page ));
        } else {
            $this->setup();
            $search = Search::photos($query, $page);
            $data = [];
            $data['images'] = $search->getResults();
            $data['pagination']['total_pages'] = $search->getTotalPages();
            $data['pagination']['pages'] = range(1, $search->getTotalPages());
            $data['pagination']['total_results'] = $search->getTotal();
            craft()->cache->add('UnsplashSearch-' . $query . '-' . $page, array('results' => $data));
            $this->renderTemplate('Unsplash/_search', array('results' => $data));
        }
    }

    private function pluginIsConfigured() {
        $settings = craft()->plugins->getPlugin('Unsplash')->getSettings();
        if($settings->assetSource) {
            return true;
        }
        $this->renderTemplate('Unsplash/_noConfig');
    }

    private function setup() {
        return HttpClient::init(array(
            'applicationId'	=> craft()->config->get('apiKey', 'Unsplash'),
            'utmSource' => 'SplashingImages_CraftCMS',
        ));
    }
}