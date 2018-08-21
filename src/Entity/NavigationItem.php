<?php


namespace App\Entity;


class NavigationItem
{
    /** @var string */
    private $routeName;

    /** @var string */
    private $label;

    /** @var NavigationItem[] */
    private $children = [];

    /**
     * NavigationItem constructor.
     * @param string $routeName
     * @param string $label
     * @param NavigationItem[] $children
     */
    public function __construct(string $routeName, string $label, array $children = [])
    {
        $this->routeName = $routeName;
        $this->label = $label;
        $this->children = $children;
    }
}