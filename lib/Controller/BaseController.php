<?php
namespace OCA\FinanceTracker\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

abstract class BaseController extends Controller {
    public function __construct(
        string $appName,
        IRequest $request
    ) {
        parent::__construct($appName, $request);
    }

    /**
     * Create a successful JSON response
     *
     * @param mixed $data Response data
     * @param int $status HTTP status code
     * @return JSONResponse
     */
    protected function success($data = null, $status = Http::STATUS_OK) {
        return new JSONResponse([
            'status' => 'success',
            'data' => $data
        ], $status);
    }

    /**
     * Create an error JSON response
     *
     * @param string $message Error message
     * @param int $status HTTP status code
     * @return JSONResponse
     */
    protected function error($message, $status = Http::STATUS_BAD_REQUEST) {
        return new JSONResponse([
            'status' => 'error',
            'message' => $message
        ], $status);
    }
}
