<?php
/**
 * Plugin Name:     Gross Calculator
 * Description:     Adds custom shortcode with a form where you can create a post with gross and tax value calculation. The newly added post type will have a custom post type: gross-calculation
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

    public function init(){

        add_action('init', [$this,'registerCPTs'], 0);

        add_action('init',[$this,'registerShortCodes'], 0);

    }

    public function registerCPTs(): void
    {
        GrossCalculation::register();
    }

    public function registerShortCodes(): void
    {
       GrossCalculationForm::register();
    }


}

$grossCalculator = new GrossCalculator();
$grossCalculator->init();
