<?php

namespace Lib;

class Stack
{
	private $data = [];
	private $count = 0;

	/**
	 * Impinge un element in capul stivei.
	 *
	 * @param  mixed $element
	 * @return mixed
	 */
	public function push($element)
	{
		return $this->data[$this->count++] = $element;
	}

	/**
	 * Inlatura un element din stiva, returnandu-i valoarea.
	 * 	
	 * @return mixed
	 */
	public function pop()
	{
		if ( ! $this->isEmpty())
		{
			$this->count--;
			return array_pop($this->data);
		}
		throw new EmptyStackAccessException;
	}

	/**
	 * Returneaza elementul din capul stivei.
	 * 
	 * @return mixed
	 */
	public function top()
	{
		if ( ! $this->isEmpty())
		{
			return $this->data[$this->size() - 1];
		}
		throw new EmptyStackAccessException;
	}

	/**
	 * Returneaza numarul de elemente din stiva.
	 * 
	 * @return int
	 */
	public function size()
	{
		return $this->count;
	}

	/**
	 * Returneaza true daca stiva este goala.
	 * @return boolean
	 */
	public function isEmpty()
	{
		return $this->size() == 0 ? true : false;
	}

	/**
	 * Indeparteaza toate elementele stivei.
	 * 
	 * @return void
	 */
	public function clear()
	{
		$this->count = 0;
		$this->data = [];
	}
	
	/**
	 * Afiseaza continutul stack-ului
	 * return string
	 */
	
	public function debug()
	{
		$str = '<table border="1"><tr><td>Continut:</td></tr>';
		for ($i = 0; $i < $this->size(); $i++)
			$str .= '<tr><td>'.$this->data[$i].'</td></tr>';
		$str .= '</table>';
		return $str;
	}
}

class EmptyStackAccessException extends \Exception {}