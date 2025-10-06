<?php

namespace Tests\Feature\ErrorHandling;

use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomExceptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_logic_exception_returns_correct_status(): void
    {
        $this->withoutExceptionHandling();
        
        $exception = new BusinessLogicException('Custom business error', 400, 'CUSTOM_ERROR');
        
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals('CUSTOM_ERROR', $exception->getErrorCode());
        $this->assertEquals('Custom business error', $exception->getMessage());
    }

    public function test_resource_not_found_exception_returns_404(): void
    {
        $exception = new ResourceNotFoundException('Resource not found', 'RESOURCE_MISSING');
        
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('RESOURCE_MISSING', $exception->getErrorCode());
        $this->assertEquals('Resource not found', $exception->getMessage());
    }

    public function test_business_logic_exception_has_default_values(): void
    {
        $exception = new BusinessLogicException();
        
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals('BUSINESS_LOGIC_ERROR', $exception->getErrorCode());
        $this->assertEquals('Business logic error occurred', $exception->getMessage());
    }

    public function test_resource_not_found_exception_has_default_values(): void
    {
        $exception = new ResourceNotFoundException();
        
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals('RESOURCE_NOT_FOUND', $exception->getErrorCode());
        $this->assertEquals('Resource not found', $exception->getMessage());
    }

    public function test_custom_exception_can_be_thrown_and_caught(): void
    {
        try {
            throw new BusinessLogicException('Test error', 422, 'TEST_ERROR');
        } catch (BusinessLogicException $e) {
            $this->assertEquals('Test error', $e->getMessage());
            $this->assertEquals(422, $e->getStatusCode());
            $this->assertEquals('TEST_ERROR', $e->getErrorCode());
        }
    }
}
