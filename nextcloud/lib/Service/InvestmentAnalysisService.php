<?php
namespace OCA\FinanceTracker\Service;

use OCA\FinanceTracker\Db\InvestmentMapper;

class InvestmentAnalysisService {
    private $investmentMapper;

    public function __construct(InvestmentMapper $investmentMapper) {
        $this->investmentMapper = $investmentMapper;
    }

    /**
     * Analyze portfolio risk and diversification
     * @param string $userId
     * @return array
     */
    public function analyzePortfolioRisk(string $userId): array {
        $investments = $this->investmentMapper->findAll($userId);

        if (empty($investments)) {
            return [
                'totalRiskScore' => 0,
                'riskLevel' => 'no_investments',
                'diversification' => []
            ];
        }

        $riskScores = [];
        $sectorAllocation = [];
        $typeAllocation = [];
        $totalValue = 0;

        foreach ($investments as $investment) {
            $currentValue = $investment->getCurrentValue();
            $totalValue += $currentValue;

            // Risk score calculation
            $riskScore = $this->calculateInvestmentRiskScore($investment);
            $riskScores[] = $riskScore;

            // Sector allocation
            $sector = $investment->getSector();
            $sectorAllocation[$sector] = 
                ($sectorAllocation[$sector] ?? 0) + $currentValue;

            // Type allocation
            $type = $investment->getType();
            $typeAllocation[$type] = 
                ($typeAllocation[$type] ?? 0) + $currentValue;
        }

        // Normalize sector and type allocations
        $sectorAllocation = array_map(
            fn($value) => round(($value / $totalValue) * 100, 2), 
            $sectorAllocation
        );
        $typeAllocation = array_map(
            fn($value) => round(($value / $totalValue) * 100, 2), 
            $typeAllocation
        );

        // Calculate average risk score
        $averageRiskScore = array_sum($riskScores) / count($riskScores);

        return [
            'totalRiskScore' => round($averageRiskScore, 2),
            'riskLevel' => $this->determineRiskLevel($averageRiskScore),
            'diversification' => [
                'sectorAllocation' => $sectorAllocation,
                'typeAllocation' => $typeAllocation
            ],
            'portfolioValue' => $totalValue
        ];
    }

    /**
     * Calculate individual investment risk score
     * @param Investment $investment
     * @return float
     */
    private function calculateInvestmentRiskScore($investment): float {
        $riskFactors = [
            'volatility' => $this->calculateVolatility($investment),
            'gainLossPercentage' => abs($investment->getGainLossPercentage()),
            'riskLevel' => $this->mapRiskLevel($investment->getRiskLevel())
        ];

        // Weighted risk calculation
        return (
            ($riskFactors['volatility'] * 0.4) + 
            ($riskFactors['gainLossPercentage'] * 0.3) + 
            ($riskFactors['riskLevel'] * 0.3)
        );
    }

    /**
     * Simulate investment volatility
     * @param Investment $investment
     * @return float
     */
    private function calculateVolatility($investment): float {
        // This is a simplified volatility calculation
        $priceChange = abs($investment->getCurrentPrice() - $investment->getPurchasePrice());
        $originalPrice = $investment->getPurchasePrice();
        
        return min(($priceChange / $originalPrice) * 100, 100);
    }

    /**
     * Map risk level to numeric score
     * @param string $riskLevel
     * @return float
     */
    private function mapRiskLevel(string $riskLevel): float {
        $riskLevels = [
            'low' => 20,
            'medium_low' => 40,
            'medium' => 60,
            'medium_high' => 80,
            'high' => 100
        ];

        return $riskLevels[strtolower($riskLevel)] ?? 50;
    }

    /**
     * Determine overall risk level
     * @param float $riskScore
     * @return string
     */
    private function determineRiskLevel(float $riskScore): string {
        if ($riskScore <= 20) {
            return 'very_low';
        } elseif ($riskScore <= 40) {
            return 'low';
        } elseif ($riskScore <= 60) {
            return 'moderate';
        } elseif ($riskScore <= 80) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    /**
     * Recommend portfolio rebalancing
     * @param string $userId
     * @return array
     */
    public function recommendPortfolioRebalancing(string $userId): array {
        $riskAnalysis = $this->analyzePortfolioRisk($userId);

        $recommendations = [];

        // Sector diversification recommendations
        foreach ($riskAnalysis['diversification']['sectorAllocation'] as $sector => $allocation) {
            if ($allocation > 50) {
                $recommendations[] = [
                    'type' => 'sector_concentration',
                    'sector' => $sector,
                    'allocation' => $allocation,
                    'advice' => 'Consider diversifying away from this sector'
                ];
            }
        }

        // Risk level recommendations
        switch ($riskAnalysis['riskLevel']) {
            case 'very_high':
                $recommendations[] = [
                    'type' => 'risk_management',
                    'advice' => 'Your portfolio is very high risk. Consider adding more stable investments.'
                ];
                break;
            case 'very_low':
                $recommendations[] = [
                    'type' => 'growth_potential',
                    'advice' => 'Your portfolio is very conservative. Consider adding some growth-oriented investments.'
                ];
                break;
        }

        return [
            'riskAnalysis' => $riskAnalysis,
            'recommendations' => $recommendations
        ];
    }
}
