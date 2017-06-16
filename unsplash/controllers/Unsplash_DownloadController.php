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

class Unsplash_DownloadController extends BaseController
{

    public function actionSave() {
        if(!craft()->request->isAjaxRequest()) {
            return false;
        }

        $path = new PathService();
        $dir = $path->getTempPath();
        if(!is_dir($dir)){ mkdir($dir); }

        $payload = trim(stripslashes($_POST['source']));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payload);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $picture = curl_exec($ch);
        curl_close($ch);

        $tmpImage = 'photo-' . rand() . '.jpg';
        $tmp = $dir . $tmpImage;

        $saved = file_put_contents($tmp, $picture);
        $settings = craft()->plugins->getPlugin('Unsplash')->getSettings();
        craft()->assets->insertFileByLocalPath($tmp, 'photo-' . rand() . '.jpg', $settings->assetSource, true);
        exit;
    }

}