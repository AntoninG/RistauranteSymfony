<?php

namespace DWBD\RistauranteBundle\Services;


class PagerFactory
{
	public function createPager($entities, $page, $limit, $route)
	{
		return new Pager($entities, $page, $limit, $route);
	}
}