<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Db\Investment;
use OCA\FinanceTracker\Db\InvestmentMapper;
use OCA\FinanceTracker\Service\InvestmentAnalysisService;
use OCP\AppFramework\Http;

class InvestmentController extends Controller {
    private $investmentMapper;
    private $analysisService;
    private $userId;

    public function __construct(
        $AppName,
        IRequest $request,
        InvestmentMapper $investmentMapper,
        InvestmentAnalysisService $analysisService,
        $UserId
    ) {
        parent::__construct($AppName, $request);
        $this->investmentMapper = $investmentMapper;
        $this->analysisService = $analysisService;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function index() {
        return new DataResponse(
            $this->investmentMapper->findAll($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     * @param string $type
     * @return DataResponse
     */
    public function listByType(string $type) {
        return new DataResponse(
            $this->investmentMapper->findByType($this->userId, $type)
        );
    }

    /**
     * @NoAdminRequired
     * @param string $name
     * @param string $type
     * @param float $purchasePrice
     * @param float $quantity
     * @param string $sector
     * @param string $riskLevel
     * @return DataResponse
     */
    public function create(
        string $name, 
        string $type, 
        float $purchasePrice, 
        float $quantity,
        string $sector,
        string $riskLevel
    ) {
        try {
            $investment = new Investment();
            $investment->setUserId($this->userId);
            $investment->setName($name);
            $investment->setType($type);
            $investment->setPurchasePrice($purchasePrice);
            $investment->setQuantity($quantity);
            $investment->setCurrentPrice($purchasePrice);
            $investment->setPurchaseDate(new \DateTime());
            $investment->setLastUpdateDate(new \DateTime());
            $investment->setSector($sector);
            $investment->setRiskLevel($riskLevel);

            $savedInvestment = $this->investmentMapper->insert($investment);

            return new DataResponse($savedInvestment, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @param float $currentPrice
     * @return DataResponse
     */
    public function updatePrice(int $id, float $currentPrice) {
        try {
            $updatedInvestment = $this->investmentMapper->updateCurrentPrice($id, $currentPrice);
            return new DataResponse($updatedInvestment);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @return DataResponse
     */
    public function destroy(int $id) {
        try {
            $investment = $this->investmentMapper->find($id);
            
            // Ensure the investment belongs to the user
            if ($investment->getUserId() !== $this->userId) {
                throw new \Exception('Not authorized to delete this investment');
            }

            $this->investmentMapper->delete($investment);
            return new DataResponse(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function portfolioRiskAnalysis() {
        try {
            $riskAnalysis = $this->analysisService->analyzePortfolioRisk($this->userId);
            return new DataResponse($riskAnalysis);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function portfolioRebalancing() {
        try {
            $rebalancingRecommendations = $this->analysisService->recommendPortfolioRebalancing($this->userId);
            return new DataResponse($rebalancingRecommendations);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
