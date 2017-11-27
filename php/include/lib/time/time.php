<?php
	class Timer
	{
		private $timer_value= 0.0;
		private $ival_start = 0.0;
		private $ival_stop	= 0.0;
		private $state		= "";

		public function __construct()
		{
			$this->start();
		}

		public function getTicks()
		{
			return microtime();
		}

		public function sleep($microseconds = 0)
		{
			usleep($microseconds);
		}

		public function getTime()
		{
			$this->pause();
			$this->resume();
			return $this->timer_value;
		}

		public function start()
		{
			if($this->state != "started")
			{
				$this->timer_value = 0;
				$this->ival_start = $this->getTicks();
				$this->state = "started";
			}
			return $this->ival_start;
		}

		public function pause()
		{
			if($this->state == "started")
			{
				$this->ival_stop = $this->getTicks();
				$this->state = "paused";
				$this->timer_value += ($this->ival_stop - $this->ival_start);
			}
			return $this->ival_stop;
		}

		public function resume()
		{
			if($this->state == "paused")
			{
				$this->ival_start = $this->getTicks();
				$this->state = "started";
			}
			return $this->ival_start;
		}

		public function stop()
		{
			if($this->state != "idle" && $this->state != "stopped")
			{
				$this->ival_stop = $this->getTicks();
				$this->state = "stopped";
				$this->timer_value += ($this->ival_stop - $this->ival_start);
			}
			return $this->timer_value;
		}

		public function getTimeToString($time = 0, $format = "H:i:s u")
		{
			if($time == 0)
			{
				return $this->getTime();
				//return date($format, $this->getTime());
			}
			else
			{
				return $time;
				//return date($format, $time);
			}
		}
	}
?>