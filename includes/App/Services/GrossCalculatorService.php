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
        var_dump($this->validateFields($calculationData));
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
}
