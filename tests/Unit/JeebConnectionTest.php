<?php

namespace Tests\Unit;

use Jeeb\JeebConnection;
use PHPUnit\Framework\TestCase;

class JeebConnectionTest extends TestCase
{
    private $apiKey;
    private $options;
    public function setUp(): void
    {
        $this->apiKey = $_ENV['API_KEY'];
        $this->options = [
            'orderNo' => 'TestOrderNumber',
            'client' => 'Internal',
            "baseCurrencyId" => "USD",
            "baseAmount" => 100,
        ];
    }
    public function testCorrectIssue(): array
    {
        $jeebConnection = new JeebConnection($this->apiKey);
        try{
            $result = $jeebConnection->issue($this->options);
            $this->assertTrue($result['succeed']);
            return $result;
        }catch (\Exception $e){
            $this->expectException($e);
        }
    }

    /**
     * @depends testCorrectIssue
     * @param array $result
     */
    public function testCorrectStatus(array $result): void
    {
        $jeebConnection = new JeebConnection($this->apiKey);
        try{
            $result = $jeebConnection->status(['token' => $result['result']['token']]);
            $this->assertTrue($result['succeed']);
        }catch (\Exception $e){
            $this->expectException($e);
        }
    }

    /**
     * @depends testCorrectIssue
     * @param array $result
     */
    public function testSeal(array $result): void
    {
        $jeebConnection = new JeebConnection($this->apiKey);
        $token = $result['result']['token'];
        try{
            $result = $jeebConnection->seal(['token' => $token]);
            $this->assertStringContainsString('پرداخت هنوز نهایی نشده است.', $result['message']) ;
        }catch (\Exception $e){
            $this->expectException($e);
        }
    }
    public function testFailedIssue(): void
    {
        $jeebConnection = new JeebConnection(uniqid());
        try{
            $result = $jeebConnection->issue($this->options);
            $this->assertStringContainsString('Api key is required!', $result) ;
        }catch (\Exception $e){
            $this->expectException($e);
        }
    }
    public function testFailedStatus(): void
    {
        $jeebConnection = new JeebConnection($this->apiKey);
        try{
            $result = $jeebConnection->status(['token' => uniqid()]);
            $this->assertNotTrue($result['succeed']);
        }catch (\Exception $e){
            $this->expectException($e);
        }
    }
}
