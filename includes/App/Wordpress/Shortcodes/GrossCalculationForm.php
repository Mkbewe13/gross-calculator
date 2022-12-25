<?php

namespace GrossCalculator\Wordpress\Shortcodes;

class GrossCalculationForm
{
    private const CALCULATION_CURRENCY = 'PLN';

    private const POSSIBLE_VAT_RATES = [
        '23%' => 23,
        '22%' => 22,
        '8%' => 8,
        '7%' => 7,
        '5%' => 5,
        '3%' => 3,
        '0%' => 0,
        'zw.' => 0,
        'np.' => 0,
        'o.o.' => 0,
    ];

    public static function register(): void
    {
        $form = new self();
        add_shortcode('gross_calculator', [$form,'getCalculationFormHtml']);
    }

    public function getCalculationFormHtml(): string
    {

        return sprintf('
            <form action="" method="post">
            <h4>Gross Calculator</h4>
            %s
            <input type="text"  name="calculation[product_name]" placeholder="Product Name" required/>
            <br>
            <input type="number" name="calculation[net_amount]" placeholder="Net Amount" step=".01" required/>
            <br>
            <input type="text"  name="calculation[currency]" value="%s" disabled/>
            <br>
            <select name="calculation[vat_rate]" required>
            %s
            </select>
            <br>
            <button value="calculate" type="submit">Oblicz</button>
            </form>',
            wp_nonce_field('gross_calculation', 'gross_calculation_nonce'),
            self::CALCULATION_CURRENCY,
            self::getPossibleVatRatesOptions());
    }

    private static function getPossibleVatRatesOptions(): string
    {
        $html = '';

        foreach (self::POSSIBLE_VAT_RATES as $rate_label => $rate_value){
            $html .= sprintf('<option value="%d">%s</option>',$rate_value,$rate_label);
        }

        return $html;
    }

    public static function getCalculationCurrency(): string
    {
        return self::CALCULATION_CURRENCY;
    }
}
