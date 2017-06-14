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

    public function getName() {
         return Craft::t('Unsplash');
    }

    public function getDescription() {
        return Craft::t('Unsplash integration for CraftCMS');
    }

    public function getDocumentationUrl() {
        return 'https://github.com/studioespresso/unsplash/blob/master/README.md';
    }

    public function getReleaseFeedUrl() {
        return 'https://raw.githubusercontent.com/studioespresso/unsplash/master/releases.json';
    }

    public function getVersion() {
        return '0.1';
    }

    public function getSchemaVersion() {
        return '0.1';
    }

    public function getDeveloper() {
        return 'Studio Espresso';
    }

    public function getDeveloperUrl() {
        return 'https://studioespresso.co';
    }

    public function hasCpSection() {
        return true;
    }

    public function registerCpRoutes() {
        return array(
            'unsplash' => array( 'action' => 'Unsplash/index' ),
            'unsplash/popular' => array( 'action' => 'Unsplash/popular' ),
        );
    }

    /**
     * Defines the attributes that model your plugin’s available settings.
     *
     * @return array
     */
    protected function defineSettings() {
        return array(
            'someSetting' => array(AttributeType::String, 'label' => 'Some Setting', 'default' => ''),
        );
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
    public function getSettingsHtml() {

    }

    public function prepSettings($settings) {
        // Modify $settings here...

        return $settings;
    }

}