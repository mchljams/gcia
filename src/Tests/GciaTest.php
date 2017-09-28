<?php

namespace Mchljams\Gcia;

use PHPUnit\Framework\TestCase;

class GciaTest extends \PHPUnit_Framework_TestCase
{
    public $key = '123ABC';

    public $gcia;

    public function setUp()
    {
        $this->gcia = new gcia();

        $this->gcia->setKey($this->key);
    }

    public function tearDown()
    {
        $this->gcia = null;
    }

    private function getPrivate($object, $property)
    {
        $reflection = new \ReflectionProperty(get_class($object), $property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }

    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    public function testKeyIsSet()
    {
        $gcia = new Gcia();

        $gcia->setKey($this->key);

        $setKey = self::getPrivate($gcia, 'key');

        $this->assertEquals($this->key, $setKey);
    }

    public function testUrlIsSet()
    {
        $electionQuery = $this->gcia->electionQuery();

        $url = self::getPrivate($this->gcia, 'url');

        $this->assertInternalType('string', $url);
    }

    public function testBuildRequestURLException()
    {
        $this->expectExceptionMessage('Your API key is required and must be a string.');

        $this->expectException('Exception');

        $this->invokeMethod(new Gcia(), 'buildRequestURL');
    }

    public function testGetRequestURLException()
    {
        $this->expectExceptionMessage('Request URL Not Set.');

        $this->expectException('Exception');

        $this->gcia->getRequestURL();
    }

    public function testElectionQuery()
    {
        $electionQuery = $this->gcia->electionQuery();

        $this->assertInternalType('string', $electionQuery);
    }

    public function testVoterInfoQuery()
    {
        $address = '123 Main St.';

        $voterInfoQuery = $this->gcia->voterInfoQuery($address);

        $this->assertInternalType('string', $voterInfoQuery);
    }

    public function testVoterInfoQueryWithElectionId()
    {
        $address = 'string';

        $electionID = 'string';

        $voterInfoQuery = $this->gcia->voterInfoQuery($address, $electionID);

        $this->assertInternalType('string', $voterInfoQuery);
    }

    public function testVoterInfoQueryWithOfficialOnlyIsTrue()
    {
        $address = 'string';

        $officialOnly = true;

        $voterInfoQuery = $this->gcia->voterInfoQuery($address, null, $officialOnly);

        $this->assertInternalType('string', $voterInfoQuery);
    }

    public function testVoterInfoQueryEcxeption()
    {
        $this->expectExceptionMessage('Address is required.');

        $this->expectException('Exception');

        $address = null;

        $voterInfoQuery = $this->gcia->voterInfoQuery($address);
    }

    public function testRepresentativeInfoByAddress()
    {
        $address = '123 Main St.';

        $representativeInfoByAddress = $this->gcia->representativeInfoByAddress($address);

        $this->assertInternalType('string', $representativeInfoByAddress);
    }

    public function testRepresentativeInfoByAddressException()
    {
        $this->expectExceptionMessage('Address is required.');

        $this->expectException('Exception');

        $address = null;

        $representativeInfoByAddress = $this->gcia->representativeInfoByAddress($address);
    }

    public function testRepresentativeInfoByDivision()
    {
        $ocdID = 'string';

        $representativeInfoByDivision = $this->gcia->representativeInfoByDivision($ocdID);

        $this->assertInternalType('string', $representativeInfoByDivision);
    }

    public function testRepresentativeInfoByDivisionException()
    {
        $this->expectExceptionMessage('ocdID is required.');

        $this->expectException('Exception');

        $ocdID = null;

        $representativeInfoByDivision = $this->gcia->representativeInfoByDivision($ocdID);
    }

    public function testSearch()
    {
        $query = 'string';

        $search = $this->gcia->search($query);

        $this->assertInternalType('string', $search);
    }

    public function testSearchException()
    {
        $this->expectExceptionMessage('Query is required.');

        $this->expectException('Exception');

        $query = null;

        $search = $this->gcia->search($query);
    }
}
