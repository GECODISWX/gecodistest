<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Doofinder
 * @copyright Doofinder
 * @license   GPLv3
 */

use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;

class DoofinderFacetsURLSerializer
{

    public function addFilterToFacetFilters(array $facetFilters, Filter $facetFilter, $facet)
    {
        if ($facet->getProperty('range')) {
            $facetFilters[$facet->getLabel()] = array(
                $facetFilter->getProperty('symbol'),
                $facetFilter->getValue()['from'],
                $facetFilter->getValue()['to'],
            );
        } else {
            $facetFilters[$facet->getLabel()][$facetFilter->getLabel()] = $facetFilter->getLabel();
        }
        return $facetFilters;
    }

    public function removeFilterFromFacetFilters(array $facetFilters, Filter $facetFilter, $facet)
    {
        if ($facet->getProperty('range')) {
            unset($facetFilters[$facet->getLabel()]);
        } else {
            unset($facetFilters[$facet->getLabel()][$facetFilter->getLabel()]);
            if (empty($facetFilters[$facet->getLabel()])) {
                unset($facetFilters[$facet->getLabel()]);
            }
        }
        return $facetFilters;
    }

    public function getActiveFacetFiltersFromFacets(array $facets)
    {
        $facetFilters = array();
        foreach ($facets as $facet) {
            if ($facet->getProperty('range')) {
                foreach ($facet->getFilters() as $facetFilter) {
                    if ($facetFilter->isActive()) {
                        $facetFilters[$facet->getLabel()] = array(
                            $facetFilter->getProperty('symbol'),
                            $facetFilter->getValue()['from'],
                            $facetFilter->getValue()['to'],
                        );
                    }
                }
            } else {
                foreach ($facet->getFilters() as $facetFilter) {
                    if ($facetFilter->isActive()) {
                        $facetFilters[$facet->getLabel()][$facetFilter->getLabel()] = $facetFilter->getLabel();
                    }
                }
            }
        }

        return $facetFilters;
    }

    public function serialize(array $facets)
    {
        $facetFilters = $this->getActiveFacetFiltersFromFacets($facets);
        $urlSerializer = new URLFragmentSerializer();
        return $urlSerializer->serialize($facetFilters);
    }

    public function setFiltersFromEncodedFacets(array $facets, $encodedFacets)
    {
        $urlSerializer = new URLFragmentSerializer();
        $facetAndFiltersLabels = $urlSerializer->unserialize($encodedFacets);

        foreach ($facetAndFiltersLabels as $facetLabel => $filters) {
            foreach ($facets as $facet) {
                if ($facet->getLabel() === $facetLabel) {
                    if (true === $facet->getProperty('range')) {
                        $symbol = $filters[0];
                        $from = $filters[1];
                        $to = $filters[2];
                        $found = false;

                        foreach ($facet->getFilters() as $filter) {
                            if ($from >= $filter->getValue()['from'] && $to <= $filter->getValue()['to']) {
                                $filter->setActive(true);
                                $found = true;
                            }
                        }

                        if (!$found) {
                            $filter = new Filter();
                            $filter->setValue(array(
                                'from' => $from,
                                'to' => $to,
                            ))->setProperty('symbol', $symbol);
                            $filter->setActive(true);
                            $facet->addFilter($filter);
                        }
                    } else {
                        foreach ($filters as $filterLabel) {
                            foreach ($facet->getFilters() as $filter) {
                                if ($filter->getLabel() === $filterLabel) {
                                    $filter->setActive(true);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $facets;
    }
}
