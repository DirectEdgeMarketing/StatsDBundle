<?php
namespace SM\StatsDBundle;

/**
 * Static wrapper for StatsD.
 *
 * @example
 *
 * use SM\StatsDBundle\StatsDWrapper as StatsD;
 * StatsD::increment('my.important.stat');
 *
 * @author Brian Feaver <brian.feaver@sellingsource.com>
 */
class StatsDWrapper
{
	/**
	 * @var StatsD
	 */
	private static $instance;

	/**
	 * Sets the instance to use for the wrapper.
	 *
	 * @param StatsD $instance
	 */
	public static function setInstance(StatsD $instance)
	{
		static::$instance = $instance;
	}

	/**
	 * Log timing information.
	 *
	 * @param string $stat
	 * @param float $time
	 * @param float $sampleRate
	 */
	public static function timing($stat, $time, $sampleRate = 1.0)
	{
		static::$instance->timing($stat, $time, $sampleRate);
	}

	/**
	 * Increments one or more stats counters.
	 *
	 * @param $stat
	 * @param float $sampleRate
	 */
	public static function increment($stat, $sampleRate = 1.0)
	{
		static::$instance->increment($stat, $sampleRate);
	}

	/**
	 * Decrements one or more stats counters.
	 *
	 * @param string $stat
	 * @param float $sampleRate
	 */
	public static function decrement($stat, $sampleRate = 1.0)
	{
		static::$instance->decrement($stat, $sampleRate);
	}
}
