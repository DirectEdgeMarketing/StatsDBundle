<?php
namespace SM\StatsDBundle;
/**
 * Sends statistics to the stats daemon over UDP
 * @author: foss@etsy.com (initial implementation, all the hard stuff)
 * @author: Tarjei Huse (tarjei@scanmine.com)
 * 
 * */
class StatsD
{
	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var int
	 */
	private $port;

	/**
	 * @var bool
	 */
	private $noop;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @param $host
	 * @param $port
	 * @param bool $noop
	 * @param string $prefix
	 * @return \SM\StatsDBundle\StatsD
	 *
	 */
    public function __construct($host, $port, $noop = false, $prefix = '') {
        $this->host = $host;
        $this->port = $port;
        $this->noop = $noop;

		if (!empty($prefix)) {
			$prefix = trim($prefix, '.');
			$prefix .= '.';
		}

		$this->prefix = $prefix;
    }

    /**
     * Log timing information
     *
     * @param string $stats The metric to in log timing info for.
     * @param float $time The ellapsed time (ms) to log
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * */
    public function timing($stat, $time, $sampleRate=1)
    {
        $this->send(array($stat => "$time|ms"), $sampleRate);
    }


    /**
     * Increments one or more stats counters
     *
     * @param string|array $stats The metric(s) to increment.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     * */
    public function increment($stats, $sampleRate=1)
    {
        $this->updateStats($stats, 1, $sampleRate);
    }


    /**
     * Decrements one or more stats counters.
     *
     * @param string|array $stats The metric(s) to decrement.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     * */
    public function decrement($stats, $sampleRate=1)
    {
        $this->updateStats($stats, -1, $sampleRate);
    }


    /**
     * Updates one or more stats counters by arbitrary amounts.
     *
     * @param string|array $stats The metric(s) to update. Should be either a string or array of metrics.
     * @param int|1 $delta The amount to increment/decrement each metric by.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     * */
    public function updateStats($stats, $delta=1, $sampleRate=1)
    {
        if (!is_array($stats)) {
            $stats = array($stats);
        }
        $data = array();
        foreach ($stats as $stat) {
            $data[$stat] = "$delta|c";
        }

        $this->send($data, $sampleRate);
    }


    /*
     * Squirt the metrics over UDP
     * */

    public function send($data, $sampleRate=1)
    {
        if ($this->noop) {
            return;
        }

        // sampling
        $sampledData = array();

        if ($sampleRate < 1) {
            foreach ($data as $stat => $value) {
                if ((mt_rand() / mt_getrandmax()) <= $sampleRate) {
                    $sampledData[$stat] = "$value|@$sampleRate";
                }
            }
        } else {
            $sampledData = $data;
        }

        if (empty($sampledData)) {
            return;
        }

        // Wrap this in a try/catch - failures in any of this should be silently ignored
        try {
            $host =$this->host;
            $port = $this->port;

            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if (!$socket) {
                throw new \Exception("Could not open statd connection to $host:$port");
            }
            foreach ($sampledData as $stat => $value) {
                $msg = "{$this->getPrefixedStat($stat)}:$value";
                socket_sendto($socket, $msg, strlen($msg), 0, $host, $port); 
            }
            socket_close($socket);
        } catch (\Exception $e) {
            //print "Exception in StatsD: " . $e->getMessage();
        }
    }

	private function getPrefixedStat($stat)
	{
		return $this->prefix . $stat;
	}
}
