<?php defined('SYSPATH') OR die('No direct access.');
/**
 * Riddle captcha class.
 *
 * @package		Kohana/Captcha
 * @category	Driver
 */
class Kohana_Captcha_Driver_Riddle extends Captcha_Driver {

	/**
	 * @var string Captcha riddle
	 */
	private $riddle;

	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return string The challenge answer
	 */
	public function generate_challenge()
	{
		// Load riddles from the current language
		$riddles = Kohana::$config->load('captcha.riddles');
		// Pick a random riddle
		$riddle = $riddles[array_rand($riddles)];
		// Store the question for output
		$this->riddle = $riddle[0];
		// Return the answer
		return (string) $riddle[1];
	}

	/**
	 * Outputs the Captcha riddle.
	 *
	 * @param boolean $html HTML output
	 * @return mixed
	 */
	public function render($html = TRUE)
	{
		return $this->riddle;
	}

} // End Captcha Riddle Driver Class
