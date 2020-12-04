<?php

namespace Base;

class InputFilterer
{
	protected $stringCleaning = [
		"\x00" => '',
		"\x01" => '',
		"\x02" => '',
		"\x03" => '',
		"\x04" => '',
		"\x05" => '',
		"\x06" => '',
		"\x07" => '',
		"\x08" => '',
		"\x0B" => '',
		"\x0C" => '',
		"\x0D" => '',
		"\x0E" => '',
		"\x0F" => '',
		"\x10" => '',
		"\x11" => '',
		"\x12" => '',
		"\x13" => '',
		"\x14" => '',
		"\x15" => '',
		"\x16" => '',
		"\x17" => '',
		"\x18" => '',
		"\x19" => '',
		"\x1A" => '',
		"\x1B" => '',
		"\x1C" => '',
		"\x1D" => '',
		"\x1E" => '',
		"\x1F" => '',
		"\x7F" => '',

		"\xC2\x80" => '',
		"\xC2\x81" => '',
		"\xC2\x82" => '',
		"\xC2\x83" => '',
		"\xC2\x84" => '',
		"\xC2\x85" => '',
		"\xC2\x86" => '',
		"\xC2\x87" => '',
		"\xC2\x88" => '',
		"\xC2\x89" => '',
		"\xC2\x8A" => '',
		"\xC2\x8B" => '',
		"\xC2\x8C" => '',
		"\xC2\x8D" => '',
		"\xC2\x8E" => '',
		"\xC2\x8F" => '',
		"\xC2\x90" => '',
		"\xC2\x91" => '',
		"\xC2\x92" => '',
		"\xC2\x93" => '',
		"\xC2\x94" => '',
		"\xC2\x95" => '',
		"\xC2\x96" => '',
		"\xC2\x97" => '',
		"\xC2\x98" => '',
		"\xC2\x99" => '',
		"\xC2\x9A" => '',
		"\xC2\x9B" => '',
		"\xC2\x9C" => '',
		"\xC2\x9D" => '',
		"\xC2\x9E" => '',
		"\xC2\x9F" => '',

		"\xC2\xA0" => ' ',
		"\xC2\xAD" => '',
		"\xE2\x80\x8B" => '',
		"\xEF\xBB\xBF" => ''
	];

	protected $fullUnicode = false;

	public function __construct($fullUnicode = false)
	{
		$this->fullUnicode = $fullUnicode;
	}

	public function filterArray(array $array, array $filters)
	{
		$output = [];

		foreach ($filters AS $key => $type)
		{
			$value = array_key_exists($key, $array) ? $array[$key] : null;

			if (is_array($type))
			{
				if (!is_array($value))
				{
					$value = [];
				}
				$output[$key] = $this->filterArray($value, $type);
			}
			else
			{
				$output[$key] = $this->filter($value, $type);
			}
		}

		return $output;
	}

	public function filter($value, $type, array $options = null)
	{
		if (!is_array($options))
		{
			$optionParts = explode(',', $type);
			$type = array_shift($optionParts);
			$options = [];

			foreach ($optionParts AS $part)
			{
				$option = explode(':', trim($part), 2);
				if (!isset($option[1]))
				{
					$option[1] = true;
				}
				else
				{
					$option[1] = trim($option[1]);
				}
				$options[trim($option[0])] = $option[1];
			}
		}

		$type = trim(strtolower($type));

		if ($type && $type[0] === '?')
		{
			$nullable = true;
			$type = substr($type, 1);
		}
		else
		{
			$nullable = false;
		}

		if (!$type)
		{
			throw new \LogicException("No filter type provided");
		}

		if ($nullable && $value === null)
		{
			return null;
		}

		return $this->cleanInternal($value, $type, $options);
	}

	protected function cleanInternal($value, $type, array $options)
	{
		switch ($type)
		{
			case 'str':
			case 'string':
				if (is_scalar($value))
				{
					$value = str_replace("\r\n", "\n", strval($value));
					if (!preg_match('/^./us', $value))
					{
						$value = '';
					}
				}
				else
				{
					$value = '';
				}

				if (empty($options['no-clean']))
				{
					$value = $this->cleanString($value, false);
				}

				if (empty($options['no-trim']))
				{
					$value = trim($value);
				}
				break;

			case 'num':
				if (is_scalar($value))
				{
					$value = $this->normalizeDecimalSeparator($value);
					$value = strval(floatval($value)) + 0;
				}
				else
				{
					$value = 0;
				}
				break;

			case 'unum':
				if (is_scalar($value))
				{
					$value = $this->normalizeDecimalSeparator($value);
					$value = strval(floatval($value)) + 0;
					if ($value < 0)
					{
						$value = 0;
					}
				}
				else
				{
					$value = 0;
				}
				break;

			case 'int':
			case 'integer':
				if (is_scalar($value))
				{
					$value = intval($value);
				}
				else
				{
					$value = 0;
				}
				break;

			case 'uint':
			case 'unsigned':
				if (is_scalar($value))
				{
					$value = intval($value);
					if ($value < 0)
					{
						$value = 0;
					}
				}
				else
				{
					$value = 0;
				}
				break;

			case 'posint':
			case 'positive-integer':
				if (is_scalar($value))
				{
					$value = intval($value);
					if ($value < 1)
					{
						$value = 1;
					}
				}
				else
				{
					$value = 1;
				}
				break;

			case 'float':
				if (is_scalar($value))
				{
					$value = $this->normalizeDecimalSeparator($value);
					$value = floatval($value);
				}
				else
				{
					$value = 0;
				}
				break;

			case 'bool':
			case 'boolean':
				$value = (bool)$value;
				break;

			case 'array':
				if (!is_array($value))
				{
					$value = [];
				}

				if (empty($options['no-clean']))
				{
					$value = $this->cleanArrayStrings($value);
				}
				break;
            case 'datetime':
                $tz = BaseApp::date()->getTimeZone();
                $useDtObject = !empty($options['obj']);

                if (is_scalar($value) && $value)
                {
                    $value = trim(strval($value));
                    if (!$value || is_numeric($value))
                    {
                        $value = intval($value);
                    }
                    else
                    {
                        try
                        {
                            $dt = new \DateTime($value, $tz);
                            if (!empty($options['end']))
                            {
                                $dt->setTime(23, 59, 59);
                            }

                            $value = $useDtObject ? $dt : intval($dt->format('U'));
                        }
                        catch (\Exception $e)
                        {
                            // probably a formatting issue, ignore
                            $value = empty($options['obj']) ? 0 : null;
                        }
                    }
                }
                else
                {
                    $value = empty($options['obj']) ? 0 : null;
                }

                if ($useDtObject)
                {
                    if (!$value)
                    {
                        $value = null;
                    }
                    else if (!($value instanceof \DateTime))
                    {
                        $value = new \DateTime('@' . $value);
                    }
                }
                break;
			default:
				if (preg_match('/^array-(.*)$/', $type, $match))
				{
					if (!is_array($value))
					{
						$value = [];
					}
					else
					{
						foreach ($value AS &$innerValue)
						{
							$innerValue = $this->filter($innerValue, $match[1], $options);
						}
					}
				}
				else
				{
					throw new \InvalidArgumentException("Unknown filter type $type");
				}
		}

		return $value;
	}

	public function normalizeDecimalSeparator($value)
	{
		$decimalSep = '.';

		if (strpos($value, $decimalSep) !== false && $decimalSep !== '.')
		{
			$value = str_replace($decimalSep, '.', $value);
		}

		return $value;
	}

	public function cleanString($string, $trim = true)
	{
		if (!$this->fullUnicode)
		{
			$string = preg_replace('/[\xF0-\xF7].../', '', $string);
		}

		$string = strtr(strval($string), $this->stringCleaning);
		if ($trim)
		{
			$string = trim($string);
		}

		return $string;
	}

	public function cleanArrayStrings(array $input, $trim = true)
	{
		foreach ($input AS &$v)
		{
			if (is_string($v))
			{
				$v = str_replace("\r\n", "\n", $v);
				if (!preg_match('/^./us', $v))
				{
					$v = '';
				}
				$v = $this->cleanString($v, $trim);
			}
			else if (is_array($v))
			{
				$v = $this->cleanArrayStrings($v, $trim);
			}
		}

		return $input;
	}
}