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
    private float $grossValue;
    private float $taxValue;


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
            throw new \Exception('An error occured while creating new gross calculation: ' . $e->getMessage());
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
            throw new \Exception('Gross calculation can not be saved.');
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
            throw new \Exception('wrong calculation data.');
        }

        if (!isset($calculationData['product_name']) || !$calculationData['product_name']) {
            throw new \Exception('wrong product name.');
        }

        $result['product_name'] = sanitize_text_field($calculationData['product_name']);

        if (!isset($calculationData['net_amount']) || !$calculationData['net_amount']) {
            throw new \Exception('wrong net amount.');
        }

        $result['net_amount'] = (float)$calculationData['net_amount'];


        if (!isset($calculationData['vat_rate']) || $calculationData['vat_rate'] == null) {
            throw new \Exception('wrong VAT rate.');
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
        $this->taxValue = 0;

        if($this->vatRate != 0){
            $this->taxValue = ($this->vatRate / 100) * $this->netAmount;
        }

        $this->grossValue = $this->netAmount + $this->taxValue;

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
        $result['gross_value'] = $this->grossValue;
        $result['tax'] = $this->taxValue;
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

        return sprintf('Cena produktu %s, wynosi: %.2f zł brutto, kwota podatku to %.2f zł.'
            , $this->productName
            , $this->grossValue,
            $this->taxValue);

    }
}
