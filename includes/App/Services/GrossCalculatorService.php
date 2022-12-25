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

    public static function createCalculation(array $calculationData)
    {

        try {
            $service = new self($calculationData);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }

        $postData = [
            'post_title' => $service->productName,
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

        return sprintf('Cena produktu %s, wynosi: %.2f zł brutto, kwota podatku to %.2f zł.'
            , $service->productName
            , $service->grossValue,
            $service->taxValue);
    }

    private function validateFields(array $calculationData): array
    {

        $result = array();

        if (empty($calculationData)) {
            throw new \Exception('Wrong calculation data.');
        }

        if (!isset($calculationData['product_name']) || !$calculationData['product_name']) {
            throw new \Exception('Wrong product name.');
        }

        $result['product_name'] = sanitize_text_field($calculationData['product_name']);

        if (!isset($calculationData['net_amount']) || !$calculationData['net_amount']) {
            throw new \Exception('Wrong net amount.');
        }

        $result['net_amount'] = (float)$calculationData['net_amount'];


        if (!isset($calculationData['vat_rate']) || !$calculationData['vat_rate']) {
            throw new \Exception('Wrong VAT rate.');
        }

        $result['vat_rate'] = (float)$calculationData['vat_rate'];

        return $result;
    }


    private function calculate(): void
    {
        $this->taxValue = 0;

        if($this->vatRate != 0){
            $this->taxValue = ($this->vatRate / 100) * $this->netAmount;
        }

        $this->grossValue = $this->netAmount + $this->taxValue;

    }

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
}
