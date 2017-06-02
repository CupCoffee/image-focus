<?php

namespace ImageFocus;

class FocusPoint
{
    public function __construct()
    {
        $this->addHooks();
    }

    /**
     * Make sure all hooks are being executed.
     */
    private function addHooks()
    {
        add_action('wp_ajax_initialize-crop', [$this, 'initializeCrop']);
        add_action('admin_enqueue_scripts', [$this, 'loadScripts']);
    }

    /**
     * Enqueues all necessary CSS and Scripts
     */
    public function loadScripts()
    {
        wp_enqueue_script('focuspoint-js', IMAGEFOCUS_ASSETS . 'js/focuspoint.min.js', ['jquery']);
        wp_localize_script('focuspoint-js', 'focusPointL10n', $this->focusPointL10n());
        wp_enqueue_script('focuspoint-js');

        wp_enqueue_style('image-focus-css', IMAGEFOCUS_ASSETS . 'css/style.min.css');
    }

    /**
     * Return all the translation strings necessary for the javascript
     *
     * @return array
     */
    private function focusPointL10n()
    {
        return [
            'cropButton' => __('Crop image', IMAGEFOCUS_TEXTDOMAIN),
        ];
    }

    /**
     * Initialize a new crop
     */
    public function initializeCrop()
    {
        // Check if we've got all the data
        $image = $_POST['image'];

        if (null === $image['focus']['x'] || null === $image['focus']['y']) {
            die(
            json_encode(
                [
                    'success' => false,
                ]
            )
            );
        }

        $crop = new Crop();
        $crop->cropImage($image['attachmentId'], $image['focus']['x'], $image['focus']['y']);

        // Return success
        die(
        json_encode(
            [
                'success' => true,
            ]
        )
        );
    }
}