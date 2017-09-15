<?php

namespace Mchljams\Gcia;

class GciaTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
  }

  public function tearDown()
  {
  }

  public function testConstruct()
  {
      $this->expectExceptionMessage('Your API key is required and must be a string.');
      $this->expectException('Exception');

      $spun = new Gcia();
  }
}
