<?php
/**
 * Plugin Name:     Gross Calculator
 * Description:     Adds custom shortcode {gross_calculation} with a form where you can create a post with gross and tax value calculation. The newly added post type will have a custom post type: gross-calculation
 * Author:          Jakub Owczarek
 * Text Domain:     GC
 * Version:         0.1
 */


use GrossCalculator\Wordpress\PostTypes\GrossCalculation;
use GrossCalculator\Wordpress\Shortcodes\GrossCalculationForm;

if (!defined('ABSPATH')) {
    exit;
}

define('APP_DIR', dirname(__FILE__));

require_once dirname(__FILE__) . '/vendor/autoload.php';

class GrossCalculator{

    const MINIMUM_PHP_VERSION = '7.4.0';

    public function init(){

        register_activation_hook(__FILE__, [$this, 'activation']);

        add_action('init', [$this,'registerCPTs'], 0);

        add_action('init',[$this,'registerShortCodes'], 0);

    }

    /**
     * Register custom post type classes
     *
     * @return void
     */
    public function registerCPTs(): void
    {
        GrossCalculation::register();
    }

    /**
     * Register shortcode classes
     *
     * @return void
     */
    public function registerShortCodes(): void
    {
       GrossCalculationForm::register();
    }


    public function activation()
    {
        if(version_compare(phpversion(),self::MINIMUM_PHP_VERSION,'<')){
            die('Plugin Gross Calculator wymaga PHP w wersji większej lub równej ' . self::MINIMUM_PHP_VERSION);
        }
    }

}

$grossCalculator = new GrossCalculator();
$grossCalculator->init();
