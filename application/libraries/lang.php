<?php
class Lang extends Laravel\Lang {
	/**
	 * Parse a language key into its file and line segments.
	 *
	 * Language keys are formatted similarly to configuration keys. The first
	 * segment represents the language file, while the second segment
	 * represents a language line within that file.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function parse($key)
	{
		if (count($segments = explode('.', $key)) > 1)
		{
			return array($segments[0], implode('.', array_slice($segments, 1)));
		}

		throw new \InvalidArgumentException("Invalid language line [$key].");
	}
}
?>