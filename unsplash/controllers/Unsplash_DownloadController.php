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
	    HttpClient::init(array(
		    'applicationId'	=> craft()->config->get('apiKey', 'Unsplash'),
		    'utmSource' => 'SplashingImages_CraftCMS',
	    ));

        if(!craft()->request->isAjaxRequest()) {
            return false;
        }

        $path = new PathService();
        $dir = $path->getTempPath();
        if(!is_dir($dir)){ mkdir($dir); }

	    $id = craft()->request->getPost('id');
	    $credit = craft()->request->getPost('attr');
	    $photo = Photo::find($id);
	    $payload = $photo->download();

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
        if($settings->assetFolder) {
        	$criteria = new FolderCriteriaModel();
        	$criteria->name = $settings->assetFolder;
        	$criteria->sourceId = $settings->assetSource;
			$assetSource = craft()->assets->findFolder($criteria);
        } else {
 	        $assetSource = craft()->assets->getRootFolderBySourceId($settings->assetSource);
        }

	    $result = craft()->assets->insertFileByLocalPath($tmp, $credit . '-' .rand() . '.jpg', $assetSource->id, true);

	    if($settings->creditsField) {
		    // Get the asset we just created
		    $savedImage = craft()->assets->getFileById($result->getDataItem('fileId'));
		    $savedImage->setContentFromPost(array(
			    $settings->creditsField => 'Photo by ' . $credit,
		    ));
		    $result = craft()->elements->saveElement($savedImage);

	    }

	    // Delete the file we just uploaded from the tmp dir.

	    if(file_exists($tmp)) {
		    unlink($tmp);
	    }

	    exit;


    }

}