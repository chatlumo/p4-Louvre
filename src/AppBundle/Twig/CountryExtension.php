<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 14/02/2018
 * Time: 11:48
 */

namespace AppBundle\Twig;

use Symfony\Component\Intl\Intl;


class CountryExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('countryname', array($this, 'countryName')),
        );
    }

    public function countryName($countryCode)
    {
        $countryName = Intl::getRegionBundle()->getCountryName($countryCode);

        return $countryName;
    }
}