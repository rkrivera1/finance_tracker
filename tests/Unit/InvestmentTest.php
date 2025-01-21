<?php
namespace OCA\FinanceTracker\Tests\Unit;

use OCA\FinanceTracker\Db\Investment;
use PHPUnit\Framework\TestCase;

class InvestmentTest extends TestCase {
    private $investment;

    protected function setUp(): void {
        $this->investment = new Investment();
    }

    public function testCreateInvestment() {
        $this->investment->setUserId('testuser');
        $this->investment->setName('Tech Stock');
        $this->investment->setType('stock');
        $this->investment->setPurchasePrice(100.00);
        $this->investment->setQuantity(10);
        $this->investment->setCurrentPrice(110.00);
        $this->investment->setPurchaseDate(new \DateTime('2023-01-01'));
        $this->investment->setSector('technology');
        $this->investment->setRiskLevel('medium');

        $this->assertEquals('Tech Stock', $this->investment->getName());
        $this->assertEquals('stock', $this->investment->getType());
    }

    public function testInvestmentValueCalculations() {
        $this->investment->setPurchasePrice(100.00);
        $this->investment->setQuantity(10);
        $this->investment->setCurrentPrice(110.00);

        $this->assertEquals(1100.00, $this->investment->getCurrentValue());
        $this->assertEquals(100.00, $this->investment->getGainLoss());
        $this->assertEquals(10, $this->investment->getGainLossPercentage());
    }

    public function testInvalidInvestmentQuantity() {
        $this->expectException(\InvalidArgumentException::class);
        $this->investment->setQuantity(-5);
    }

    public function testInvestmentRiskLevels() {
        $validRiskLevels = ['low', 'medium', 'high'];

        foreach ($validRiskLevels as $riskLevel) {
            $this->investment->setRiskLevel($riskLevel);
            $this->assertEquals($riskLevel, $this->investment->getRiskLevel());
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->investment->setRiskLevel('extreme');
    }

    public function testInvestmentJsonSerialization() {
        $this->investment->setUserId('testuser');
        $this->investment->setName('Tech Stock');
        $this->investment->setType('stock');
        $this->investment->setPurchasePrice(100.00);
        $this->investment->setQuantity(10);
        $this->investment->setCurrentPrice(110.00);
        $this->investment->setSector('technology');

        $jsonData = $this->investment->jsonSerialize();

        $this->assertArrayHasKey('id', $jsonData);
        $this->assertArrayHasKey('name', $jsonData);
        $this->assertArrayHasKey('type', $jsonData);
        $this->assertArrayHasKey('currentValue', $jsonData);
        $this->assertArrayHasKey('gainLoss', $jsonData);
        $this->assertArrayHasKey('gainLossPercentage', $jsonData);
    }

    public function testInvestmentSectorValidation() {
        $validSectors = [
            'technology', 'finance', 'healthcare', 
            'energy', 'consumer_goods', 'real_estate'
        ];

        foreach ($validSectors as $sector) {
            $this->investment->setSector($sector);
            $this->assertEquals($sector, $this->investment->getSector());
        }
    }
}
