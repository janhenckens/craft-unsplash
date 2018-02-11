<?php
/**
 * Unsplash plugin for Craft CMS
 *
 * @author    Studio Espresso
 * @copyright Copyright (c) 2017 Studio Espresso
 * @link      https://studioespresso.co
 * @package   Unsplash
 * @since     0.1
 */

namespace Craft;

require 'vendor/autoload.php';

class UnsplashPlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Splashing Images');
    }

    public function getDescription()
    {
        return Craft::t('Unsplash integration for CraftCMS');
    }

    public function getDocumentationUrl()
    {
        return 'https://github.com/studioespresso/unsplash/blob/master/README.md';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/studioespresso/craft-unsplash/master/releases.json';
    }

    public function getVersion()
    {
        return '1.2.0';
    }

    public function getSchemaVersion()
    {
        return '0.1';
    }

    public function getDeveloper()
    {
        return 'Studio Espresso';
    }

    public function getDeveloperUrl()
    {
        return 'https://studioespresso.co';
    }

    public function hasCpSection()
    {
        return true;
    }

    public function registerCpRoutes()
    {
        return array(
            'unsplash' => array( 'action' => 'Unsplash/index' ),
            'unsplash/latest' => array( 'action' => 'Unsplash/latest' ),
            'unsplash/random' => array( 'action' => 'Unsplash/random' ),
            'unsplash/search' => array( 'action' => 'Unsplash/search' ),
        );
    }

    protected function defineSettings()
    {
        return array(
            'assetSource' => array(AttributeType::String, 'label' => 'Asset Source', 'default' => ''),
            'assetFolder' => array(AttributeType::String, 'label' => 'Asset Folder', 'default' => ''),
            'creditsField' => array(AttributeType::String, 'label' => 'Credits Field', 'default' => ''),

        );
    }

    public function onAfterInstall()
    {
        craft()->request->redirect(UrlHelper::getCpUrl('settings/plugins/unsplash'));
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
    public function getSettingsHtml()
    {
        $settings = craft()->plugins->getPlugin('Unsplash')->getSettings();

        $sourceOptions[] = array('label' => '---', 'value' => "");
        foreach (craft()->assetSources->getAllSources() as $source) {
            $sourceOptions[] = array('label' => $source->name, 'value' => $source->id);
        }

        $sourceFields[] = array('label' => '---', 'value' => "");

        if ($settings->assetSource) {
            $source = craft()->assetSources->getSourceById($settings->assetSource);
            $fieldLayout = craft()->fields->getLayoutById($source->getAttribute('fieldLayoutId'));
            $textFields = [ 'PlainText' , 'RichText' ];
            foreach ($fieldLayout->getFieldIds() as $field) {
                $field = craft()->fields->getFieldById($field);
                if (in_array($field->getFieldType()->getClassHandle(), $textFields)) {
                    $sourceFields[$field->getAttribute('handle')] = $field->getAttribute('name');
                };
            }
        }

        return craft()->templates->render('unsplash/_settings', array(
            'settings' => $this->getSettings(),
            'sourceFields' => $sourceFields,
            'assetSources' => $sourceOptions,
        ));
    }

    public function prepSettings($settings)
    {
    	if($settings['assetFolder']) {
    	    $assetService = craft()->assets;
    	    $assetService->createFolder($settings['assetSource'], $settings['assetFolder']);
	    }
        return $settings;
    }
}
