<?php

namespace DWBD\RistauranteBundle\Services;

/**
 * Class Pager
 * @package DWBD\RistauranteBundle\Services
 */
class Pager
{
	/** @var array */
	private $entities;

	/** @var int */
	private $page;

	/** @var int */
	private $limit;

	/** @var int */
	private $start;

	/** @var int */
	private $last;

	/** @var int */
	private $total;

	/** @var  string */
	private $route;

	/**
	 * Paginator constructor.
	 *
	 * @param array $entities
	 * @param int $page
	 * @param int $limit
	 * @param string $route
	 */
	public function __construct(array $entities, $page, $limit, $route)
	{
		if (!is_array($entities)) {
			throw new \InvalidArgumentException('$entities must be an array');
		}
		if (!is_numeric($page) || $page < 0) {
			throw new \InvalidArgumentException('$page must be an int positive or null');
		}
		if (!is_int($limit) || $limit < 1) {
			throw new \InvalidArgumentException('$limit must be an int strictly positive');
		}
		if (!is_string($route) || empty($route)) {
			throw new \InvalidArgumentException('$route must a not empty string');
		}

		$page = intval($page);
		$this->route = $route;
		$this->page = $page < 1 ? 1 : $page;
		$this->limit = $limit;
		$this->start = ($this->page - 1) * $limit;
		$this->total = count($entities);
		$last = ceil($this->total / $this->limit);
		$this->last = $last == 0 ? 1 : $last;

		if (empty($entities)) {
			$this->entities = array();
		} else {
			$this->entities = array_slice($entities, $this->start, $this->limit);
		}

	}

	/**
	 * Return all entities on the page given in constructor
	 *
	 * @return array
	 */
	public function getEntities()
	{
		return $this->entities;
	}

	/**
	 * @param array $entities
	 * @return Pager
	 */
	public function setEntities($entities)
	{
		$this->entities = $entities;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @param int $page
	 * @return Pager
	 */
	public function setPage($page)
	{
		$this->page = $page;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @param int $limit
	 * @return Pager
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * @param int $start
	 * @return Pager
	 */
	public function setStart($start)
	{
		$this->start = $start;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLast()
	{
		return $this->last;
	}

	/**
	 * @param int $last
	 * @return Pager
	 */
	public function setLast($last)
	{
		$this->last = $last;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * @param int $total
	 * @return Pager
	 */
	public function setTotal($total)
	{
		$this->total = $total;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @param string $route
	 * @return Pager
	 */
	public function setRoute($route)
	{
		$this->route = $route;
		return $this;
	}

	/**
	 * Returns the start offset of the page
	 *
	 * @return int
	 */
	public function getOffsetStart()
	{
		return $this->start + 1;
	}

	/**
	 * Returns the end offset of the page
	 *
	 * @return int
	 */
	public function getOffsetEnd()
	{
		return $this->start + count($this->entities);
	}

	/**
	 * Returns a formatted string with the start and end offsets on total entries
	 *
	 * @return string
	 * 		{start} to {end} / {total} entries
	 */
	public function getFormattedOffsets()
	{
		return $this->getOffsetStart() . ' to ' . $this->getOffsetEnd() . ' / ' . $this->total . ' entries';
	}

}
