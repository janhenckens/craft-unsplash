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

class UnsplashController extends BaseController
{

    protected $allowAnonymous = array('actionIndex',
        );


    public function actionIndex() {
        $this->renderTemplate('Unsplash/_index');
    }

    public function actionPopular() {

        if(craft()->cache->get('UnsplashPopular')) {


            $this->renderTemplate('Unsplash/_index', craft()->cache->get('UnsplashPopular'));
        } else {
            $this->setup();
            $images = Photo::curated(1, 25);
            craft()->cache->add('UnsplashPopular', array('images' => $images), (60*60*12));
            $this->renderTemplate('Unsplash/_index', array('images' => $images));
        }
    }

    private function setup() {
        return HttpClient::init(array(
            'applicationId'	=> craft()->config->get('apiKey', 'Unsplash'),
        ));
    }
}