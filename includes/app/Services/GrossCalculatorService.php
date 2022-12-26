<?php

namespace GrossCalculator\Services;

use GrossCalculator\Wordpress\PostTypes\GrossCalculation;
use GrossCalculator\Wordpress\Shortcodes\GrossCalculationForm;

class GrossCalculatorService
{

    private string $productName;
    private float $netAmount;
    private string $currency;
    private float $vatRate;
    private float $grossAmount;
    private float $taxAmount;


    /**
     * Validate calculation data and set service properties. Throws exception if any of calculation data is wrong.
     *
     * @param array $calculationData
     * @throws \Exception
     */
    public function __construct(array $calculationData)
    {
        try {
            $calculationData = $this->validateFields($calculationData);
        }catch(\Exception $e){
            throw new \Exception('Wystąpił błąd podczas podczas wyliczania wartości brutto: ' . $e->getMessage());
        }

        $this->productName = $calculationData['product_name'];
        $this->netAmount = $calculationData['net_amount'];
        $this->currency = GrossCalculationForm::getCalculationCurrency();
        $this->vatRate = $calculationData['vat_rate'];

        $this->calculate();
    }

    /**
     * Insert new post (cpt: gross_calculation) with all calculated data as meta custom fields.
     * Returns message about calculation on success or throws exception on failure.
     *
     * @param array $calculationData
     * @return string
     * @throws \Exception
     */
    public static function createCalculation(array $calculationData)
    {

        try {
            $service = new self($calculationData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $postData = [
            'post_title' => $service->productName,
            'post_content' => $service->getCalculationSuccessMessage(),
            'post_status' => 'publish',
            'post_type' => GrossCalculation::getGrossCalculationCPTName(),
            'meta_input' => $service->getCalculationMetaInput(),
        ];
        try {
            wp_insert_post($postData);
        } catch (\Exception $e) {
            error_log('Gross calculation can not be saved: ' . $e->getMessage());
            throw new \Exception('Wystąpił błąd. Wyliczenie nie może zostać zapisane.');
        }

        return $service->getCalculationSuccessMessage();
    }

    /**
     * Validate all calculation data fields.
     *
     * @param array $calculationData
     * @return array
     * @throws \Exception
     */
    private function validateFields(array $calculationData): array
    {

        $result = array();

        if (empty($calculationData)) {
            throw new \Exception('błąd danych');
        }

        if (!isset($calculationData['product_name']) || !$calculationData['product_name']) {
            throw new \Exception('błędna nazwa produktu');
        }

        $result['product_name'] = sanitize_text_field($calculationData['product_name']);

        if (!isset($calculationData['net_amount']) || !$calculationData['net_amount']) {
            throw new \Exception('błędna wartość netto.');
        }

        $result['net_amount'] = (float)$calculationData['net_amount'];


        if (!isset($calculationData['vat_rate']) || $calculationData['vat_rate'] == null) {
            throw new \Exception('błedna stawka VAT.');
        }

        $result['vat_rate'] = (float)$calculationData['vat_rate'];

        return $result;
    }

    /**
     * Perform calculation for gross value and tax value by given product data.
     *
     * @return void
     */
    private function calculate(): void
    {
        $this->taxAmount = 0;

        if($this->vatRate != 0){
            $this->taxAmount = ($this->vatRate / 100) * $this->netAmount;
        }

        $this->grossAmount = $this->netAmount + $this->taxAmount;

    }

    /**
     * Returns array of prepared meta input for custom post insertion.
     *
     * @return array
     */
    private function getCalculationMetaInput(): array
    {
        $result = [];

        $result['product_name'] = $this->productName;
        $result['net_value'] = $this->netAmount;
        $result['gross_value'] = $this->grossAmount;
        $result['tax'] = $this->taxAmount;
        $result['currency'] = $this->currency;
        $result['vat_rate'] = $this->vatRate;
        $result['ip_address'] = $this->getCurrentUserIP();
        $result['calculation_date'] = date('d.m.y');

        return $result;
    }

    /**
     * Returns current user ip.
     *
     * @return mixed
     */
    private function getCurrentUserIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

            $ip = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {

            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Returns string with finished calculation data.
     *
     * @return string
     */
    private function getCalculationSuccessMessage(): string
    {

        return sprintf('Cena produktu <b>%s</b>, wynosi: <b>%.2f zł</b> brutto, kwota podatku to <b>%.2f zł</b>.'
            , $this->productName
            , $this->grossAmount,
            $this->taxAmount);

    }
}
