<?php

namespace GrossCalculator\Wordpress\Shortcodes;

use GrossCalculator\Services\GrossCalculatorService;

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

    /**
     * Register custom shortcode with calculation form.
     *
     * @return void
     */
    public static function register(): void
    {
        $form = new self();
        add_shortcode('gross_calculator', [$form, 'getCalculationFormHtml']);
    }

    /**
     * Returns html string with calculation form, and handles calculation output message.
     *
     * @return string
     *
     */
    public function getCalculationFormHtml(): string
    {
        $messageContent = '';
        $success = true;

        if (!empty($_POST['calculation'])) {
            try {
                $messageContent = GrossCalculatorService::createCalculation($_POST['calculation']);
            } catch (\Exception $e) {
                $messageContent = $e->getMessage();
                $success = false;
            }
        }

        return sprintf('
            %s
            <div class="gross-calc-form-center">
             <span class="dashicons dashicons-money-alt"></span>
             <p>Gross Calculator</p>
            <form action="" method="post">
            %s
            <input type="text" class="gross-calc-input"  name="calculation[product_name]" placeholder="Product Name" required/>
            <br>
            <input type="number" class="gross-calc-input" name="calculation[net_amount]" placeholder="Net Amount" step=".01" required/>
            <br>
            <input type="text"  class="gross-calc-input" name="calculation[currency]" value="%s" disabled/>
            <br>
            <select class="gross-calc-input" name="calculation[vat_rate]" required>
            %s
            </select>
            <br>
            <button class="gross-calc-submit" value="calculate" type="submit">Oblicz</button>
            </form>
            </div>',
            $this->getMessageHtml($messageContent, $success),
            wp_nonce_field('gross_calculation', 'gross_calculation_nonce'),
            self::CALCULATION_CURRENCY,
            self::getPossibleVatRatesOptions());
    }

    /**
     * Return html string with all possible tax rate options for select.
     *
     * @return string
     */
    private static function getPossibleVatRatesOptions(): string
    {
        $html = '';

        foreach (self::POSSIBLE_VAT_RATES as $rate_label => $rate_value) {
            $html .= sprintf('<option value="%d">%s</option>', $rate_value, $rate_label);
        }

        return $html;
    }

    /**
     * Returns calculation currency set by class constant.
     *
     * @return string
     */
    public static function getCalculationCurrency(): string
    {
        return self::CALCULATION_CURRENCY;
    }

    /**
     * Returns string html with output message from creating calculation.
     *
     * @param string $messageContent
     * @param bool $success
     * @return string
     */
    private function getMessageHtml(string $messageContent, bool $success)
    {
        if (!$messageContent) {
            return '';
        }

        if ($success) {
            return sprintf('
        <p class="gross-calc-success">%s</p>
        ', $messageContent);
        }

        return sprintf('
        <p class="gross-calc-failed">%s</p>
        ', $messageContent);
    }
}
