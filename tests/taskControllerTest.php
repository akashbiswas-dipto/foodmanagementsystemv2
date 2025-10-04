<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MongoDB\Collection;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use MongoDB\DeleteResult;
use MongoDB\BSON\ObjectId;

// Adjust path to your controller
require_once __DIR__ . '/../src/controller/taskController.php';

class TaskControllerTest extends TestCase
{
    private $mockDb;
    private $mockCollection;
    private $controller;
    private $base_url = 'http://localhost/foodmanagementsystem/';
    private $userId = '64f123abc4567890def12345';
    private $redirectedUrl = null;

    protected function setUp(): void
    {
        // Mock the MongoDB collection
        $this->mockCollection = $this->createMock(Collection::class);

        // Mock the DB object as stdClass and assign collection
        $this->mockDb = new stdClass();
        $this->mockDb->food = $this->mockCollection;

        // Mock redirect handler to capture URL instead of exiting
        $redirectHandler = function($url) {
            $this->redirectedUrl = $url;
        };

        // Instantiate controller with mock DB and redirect handler
        $this->controller = new FoodController(
            $this->mockDb,
            $this->base_url,
            $this->userId,
            'Test User',
            2,
            $redirectHandler
        );

        // Mock session
        $_SESSION['user_id'] = $this->userId;
    }

    public function testShareFoodSuccess()
    {
        $taskData = [
            'food_item' => 'Test Food',
            'food_category' => 'Fruits',
            'quantity' => 5,
            'pickup_time' => '2025-10-10 12:00',
            'location' => 'Test Location'
        ];

        // Mock insertOne to return a proper InsertOneResult
        $mockResult = $this->createMock(InsertOneResult::class);

        $this->mockCollection->expects($this->once())
            ->method('insertOne')
            ->with($this->callback(function ($arg) use ($taskData) {
                return $arg['donor_id'] === $_SESSION['user_id']
                    && $arg['food_item'] === $taskData['food_item'];
            }))
            ->willReturn($mockResult);

        // Call the method
        $this->controller->shareFood($taskData);

        $this->assertStringContainsString('success=Food shared successfully', $this->redirectedUrl);
    }

    public function testUpdateFoodSuccess()
    {
        $foodId = (string)new ObjectId();
        $taskData = [
            'food_id' => $foodId,
            'food_item' => 'Updated Food',
            'food_category' => 'Vegetables',
            'quantity' => 10,
            'pickup_time' => '2025-10-15 14:00',
            'location' => 'Updated Location'
        ];

        $mockResult = $this->createMock(UpdateResult::class);
        $mockResult->method('getModifiedCount')->willReturn(1);

        $this->mockCollection->expects($this->once())
            ->method('updateOne')
            ->with(
                $this->callback(fn($filter) => (string)$filter['_id'] === $foodId && $filter['donor_id'] === $_SESSION['user_id']),
                $this->anything()
            )
            ->willReturn($mockResult);

        $this->controller->updateFood($taskData);

        $this->assertStringContainsString('success=Food updated successfully', $this->redirectedUrl);
    }

    public function testDeleteFoodSuccess()
    {
        $foodId = (string)new ObjectId();

        $mockResult = $this->createMock(DeleteResult::class);
        $mockResult->method('getDeletedCount')->willReturn(1);

        $this->mockCollection->expects($this->once())
            ->method('deleteOne')
            ->with($this->callback(fn($filter) => (string)$filter['_id'] === $foodId && $filter['donor_id'] === $_SESSION['user_id']))
            ->willReturn($mockResult);

        $this->controller->deleteFood($foodId);

        $this->assertStringContainsString('success=Food deleted successfully', $this->redirectedUrl);
    }
}
