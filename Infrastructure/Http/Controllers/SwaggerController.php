<?php
declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use App\Core\Exceptions\NotFoundException;

/**
 * @OA\Info(
 *     title="Fast Food Restaurant API",
 *     version="1.0.0",
 *     description="API for managing food orders"
 * )
 */
class SwaggerController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api-docs",
     *     tags={"Documentation"},
     *     summary="Get API documentation",
     *     description="Returns Swagger/OpenAPI documentation in JSON format",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Documentation not found"
     *     )
     * )
     */
    public function showDocs(): void
    {
        $docsFile = __DIR__. '/../../../public/swagger.json';

        if (!file_exists($docsFile)) {
            throw new NotFoundException('API documentation not found');
        }

        header('Content-Type: application/json');
        readfile($docsFile);
        exit;
    }

    /**
     * @OA\Post(
     *     path="/api-docs/generate",
     *     tags={"Documentation"},
     *     summary="Regenerate documentation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Documentation generated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function generateDocs(): false|string
    {
        exec('php bin/generate-swagger-docs.php', $output, $returnCode);

        if ($returnCode !== 0) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'Failed to generate docs',
                'output' => $output
            ], 500);
        }

        return $this->jsonResponse([
            'status' => 'success',
            'message' => 'Documentation regenerated'
        ]);
    }
}