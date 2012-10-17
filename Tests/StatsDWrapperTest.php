<?php
namespace SM\StatsDBundle\Tests;

use SM\StatsDBundle\StatsDWrapper;

class StatsDWrapperTest extends \PHPUnit_Framework_TestCase
{
	private $statsd;

	protected function setUp()
	{
		$this->statsd = $this->getMockBuilder('SM\StatsDBundle\StatsD')
			->disableOriginalConstructor()
			->getMock();
		StatsDWrapper::setInstance($this->statsd);
	}

	public function testIntegerUsedForDefaultSampleRateIncrement()
	{
		$this->statsd->expects($this->once())
			->method('increment')
			->with($this->anything(), $this->isType('int'));

		StatsDWrapper::increment('junk.test');
	}

	public function testIntegerUsedForDefaultSampleRateDecrement()
	{
		$this->statsd->expects($this->once())
			->method('decrement')
			->with($this->anything(), $this->isType('int'));

		StatsDWrapper::decrement('junk.test');
	}

	public function testIntegerUsedForDefaultSampleRateTiming()
	{
		$this->statsd->expects($this->once())
			->method('timing')
			->with($this->anything(), $this->isType('int'));

		StatsDWrapper::timing('junk.test', 1);
	}
}
