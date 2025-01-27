<?php
namespace OCA\FinanceTracker\Controller;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

use OCA\FinanceTracker\Db\InvestmentMapper;

class InvestmentController extends BaseController {
    /** @var InvestmentMapper */
    private $mapper;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        IRequest $request,
        InvestmentMapper $mapper,
        LoggerInterface $logger
    ) {
        parent::__construct('finance_tracker', $request);
        $this->mapper = $mapper;
        $this->logger = $logger;
    }

    /**
     * Get all investments
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $filters Optional filters for investments
     * @return JSONResponse
     */
    public function index(array $filters = []): JSONResponse {
        try {
            $investments = $this->mapper->findAll($filters);
            return $this->success($investments);
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve investments', [
                'exception' => $e,
                'filters' => $filters
            ]);
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get investment performance
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $filters Optional filters for performance
     * @return JSONResponse
     */
    public function performance(array $filters = []): JSONResponse {
        try {
            $performance = $this->mapper->getPerformance($filters);
            return $this->success($performance);
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve investment performance', [
                'exception' => $e,
                'filters' => $filters
            ]);
            return $this->error($e->getMessage());
        }
    }

    /**
     * Create a new investment
     *
     * @NoAdminRequired
     * @param array $investmentData Investment details
     * @return JSONResponse
     */
    public function create(array $investmentData): JSONResponse {
        try {
            $investment = $this->mapper->insert($investmentData);
            return $this->success($investment, Http::STATUS_CREATED);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Invalid investment data', [
                'message' => $e->getMessage(),
                'data' => $investmentData
            ]);
            return $this->error($e->getMessage(), Http::STATUS_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create investment', [
                'exception' => $e,
                'data' => $investmentData
            ]);
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update an existing investment
     *
     * @NoAdminRequired
     * @param int $id Investment ID
     * @param array $investmentData Updated investment details
     * @return JSONResponse
     */
    public function update(int $id, array $investmentData): JSONResponse {
        try {
            $investment = $this->mapper->update($id, $investmentData);
            return $this->success($investment);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update investment', [
                'exception' => $e,
                'id' => $id,
                'data' => $investmentData
            ]);
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete an investment
     *
     * @NoAdminRequired
     * @param int $id Investment ID to delete
     * @return JSONResponse
     */
    public function delete(int $id): JSONResponse {
        try {
            $this->mapper->delete($id);
            return $this->success(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete investment', [
                'exception' => $e,
                'id' => $id
            ]);
            return $this->error($e->getMessage());
        }
    }
}
