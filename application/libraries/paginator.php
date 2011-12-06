<?php
class Paginator extends Laravel\Paginator {
	
	/**
	 * The URL used to create links
	 *
	 * @var string
	 */
	protected $base_url;
	
	/**
	 * Create a HTML page link.
	 *
	 * @param  int     $page
	 * @param  string  $text
	 * @param  string  $attributes
	 * @return string
	 */
	protected function link($page, $text, $class)
	{
		$query = '?page='.$page.$this->appendage($this->appends);

		return HTML::link((empty($this->base_url) ? URI::current() : $this->base_url).$query, $text, compact('class'), Request::secure());
	}
	
	/**
	 * Create a HTML page link.
	 *
	 * @param  string  $base_url
	 * @return Paginator
	 */
	public function from($base_url)
	{
		$this->base_url = $base_url;
		return $this;
	}
}